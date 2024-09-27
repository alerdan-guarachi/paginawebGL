@extends('adminlte::page')

@section('content_header')
<h1>NUEVO ASOCIADO</h1>
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
                <div class="row ">
                    <div class="col-lg-12">
                        {!! Form::open(['route' => 'admin.asociados.store', 'method'=>'POST']) !!}
                
                        {!! Form::hidden('usuarioid', auth()->user()->id) !!}
                        {!! Form::hidden('usuarioregistro', auth()->user()->name) !!}
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    {!! Form::label('asociado', 'Asociado:') !!}
                                    {!! Form::text('asociado', null, ['class' => 'form-control', 'placeholder' => '']) !!}
                                    @error('asociado')
                                        <small class="text-danger fas fa-exclamation-circle">
                                            {{$message}}
                                        </small>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    {!! Form::label('direccion', 'Dirección:') !!}
                                    {!! Form::text('direccion', null, ['class' => 'form-control', 'placeholder' => '']) !!}
                                    @error('direccion')
                                        <small class="text-danger fas fa-exclamation-circle">
                                            {{$message}}
                                        </small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    {!! Form::label('telefono', 'Teléfono:') !!}
                                    {!! Form::text('telefono', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '8', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']) !!}
                                    @error('telefono')
                                        <small class="text-danger fas fa-exclamation-circle">
                                            {{$message}}
                                        </small>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-4">
                                <div class="form-group">
                                    {!! Form::label('nit', 'NIT:') !!}
                                    {!! Form::text('nit', null, ['class' => 'form-control', 'placeholder' => '','maxlength' => '20', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']) !!}
                                    @error('nit')
                                        <small class="text-danger fas fa-exclamation-circle">
                                            {{$message}}
                                        </small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group">
                                    {!! Form::label('ciudad', 'Ciudad:') !!}
                                    {!! Form::select('ciudad', $departamentos, null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '45']) !!}
                                    @error('ciudad')
                                        <small class="text-danger fas fa-exclamation-circle">
                                            {{$message}}
                                        </small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group">
                                    {!! Form::label('estadoproveedor', 'Estado:') !!}
                                    {!! Form::select('estadoproveedor', $estadoasociado, null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '45']) !!}
                                    @error('estadoproveedor')
                                        <small class="text-danger fas fa-exclamation-circle">
                                            {{$message}}
                                        </small>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-3">
                                <div class="form-group">
                                    {!! Form::label('mododepago', 'Modo de pago:') !!}
                                    {!! Form::select('mododepago', $mododepago, null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '45']) !!}
                                    @error('mododepago')
                                        <small class="text-danger fas fa-exclamation-circle">
                                            {{$message}}
                                        </small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    {!! Form::label('banco', 'Banco:') !!}
                                    {!! Form::select('banco', $bancos, null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '45']) !!}
                                    @error('banco')
                                        <small class="text-danger fas fa-exclamation-circle">
                                            {{$message}}
                                        </small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    {!! Form::label('cuenta', 'Número de cuenta:') !!}
                                    {!! Form::text('cuenta', null, ['class' => 'form-control', 'placeholder' => '','maxlength' => '20', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']) !!}
                                    @error('cuenta')
                                        <small class="text-danger fas fa-exclamation-circle">
                                            {{$message}}
                                        </small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    {!! Form::label('tipocuenta', 'Tipo de cuenta:') !!}
                                    {!! Form::text('tipocuenta', null, ['class' => 'form-control', 'placeholder' => '']) !!}
                                    @error('tipocuenta')
                                        <small class="text-danger fas fa-exclamation-circle">
                                            {{$message}}
                                        </small>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        {!! Form::submit('CREAR ASOCIADO', ['class' => 'btn btn-crear']) !!}
                    </div>
                {!! Form::close() !!}
                </div>        
            </div>
        </div>
    </div>
</div>
{{-- <div class="col-lg-4">
    @foreach($areas as $id => $nombreArea)
        <div class="form-group acciones" id="acciones_{{ $id }}" style="display: none;">
            {!! Form::label('accion', 'Acción:') !!}
            <br>
            @foreach($accionesPorArea[$id] as $key => $accion)
                <div class="form-check">
                    {!! Form::checkbox('accion[]', $accion, null, ['class' => 'form-check-input', 'id' => 'accion_'.$key]) !!}
                    {!! Form::label('accion_'.$key, $accion, ['class' => 'form-check-label']) !!}
                </div>
            @endforeach
            @error('accion')
                <small class="text-danger fas fa-exclamation-circle">
                    {{$message}}
                </small>
            @enderror
        </div>
    @endforeach
</div> --}}

@stop
@section('js')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script type="text/javascript" src="https://jeremyfagis.github.io/dropify/dist/js/dropify.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://jeremyfagis.github.io/dropify/dist/css/dropify.min.css"> 
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    $(document).ready(function() {
        $('#area').change(function() {
            var areaId = $(this).val();
            $('.acciones').hide();
            $('#acciones_' + areaId).show();
        });
    });
</script>

<script>
//CALCULAR LOS HORARIOS
    $(document).ready(function() {
        $('#horarioinicial').change(function() {
            $('#cantidadatencion').val('');
        });
        $('#horariofinal').change(function() {
            var horarioInicial = $('#horarioinicial').val();
            var horarioFinal = $('#horariofinal').val();
            var inicio = new Date("01/01/2024 " + horarioInicial);
            var fin = new Date("01/01/2024 " + horarioFinal);
            if (fin <= inicio) {
                /* alert("El horario final debe ser posterior al horario inicial."); */
                $('#horariofinal').val('');
                $('#cantidadatencion').val('');
            }
        });

//CALCULAR LA CANTIDAD DE ATENCION SEGUN LOS HORARIOS
        $('#tiempoatencion').change(function() {
            var horarioInicial = $('#horarioinicial').val();
            var horarioFinal = $('#horariofinal').val();
            var tiempoAtencion = $('#tiempoatencion').val();
            var inicio = new Date("01/01/2024 " + horarioInicial);
            var fin = new Date("01/01/2024 " + horarioFinal);
            var diff = (fin - inicio) / 1000 / 60;
            if (tiempoAtencion !== '00:00' && diff > 0) {
                var cantidadAtencion = diff / (parseInt(tiempoAtencion.split(':')[0]) * 60 + parseInt(tiempoAtencion.split(':')[1]));
                $('#cantidadatencion').val(Math.floor(cantidadAtencion));
            } else {
                $('#cantidadatencion').val('');
            }
        });
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
    h1 {
        color:#94c93b; 
        font-family: "Segoe UI";
        font-weight: 900;
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
</style>
@stop