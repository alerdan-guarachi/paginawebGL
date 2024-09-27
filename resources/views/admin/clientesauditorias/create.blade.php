@extends('adminlte::page')

@section('content_header')
<h1>Nuevo Cliente Auditoria</h1>
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

                        {!! Form::open(['route' => 'admin.clientesauditorias.store', 'method'=>'POST']) !!}
                
                            {!! Form::hidden('usuarioid', auth()->user()->id) !!}
                            {!! Form::hidden('usuarioregistro', auth()->user()->name) !!}
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        {!! Form::label('nombrecompleto', 'Nombre completo:') !!}
                                        {!! Form::text('nombrecompleto', null, ['class' => 'form-control', 'placeholder' => '']) !!}
                                        @error('nombrecompleto')
                                            <small class="text-danger fas fa-exclamation-circle">
                                                {{$message}}
                                            </small>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="form-group">
                                        {!! Form::label('ci', 'CI:') !!}
                                        {!! Form::text('ci', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '9', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']) !!}
                                        @error('ci')
                                            <small class="text-danger fas fa-exclamation-circle">
                                                {{$message}}
                                            </small>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="form-group">
                                        {!! Form::label('genero', 'Género:') !!}
                                        {!! Form::select('genero', $genero, null, ['class' => 'form-control', 'placeholder' => '']) !!}
                                        @error('genero')
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
                                        {!! Form::label('lugarnacimiento', 'Lugar nacimiento:') !!}
                                        {!! Form::select('lugarnacimiento', $departamentos, null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '45']) !!}
                                        @error('lugarnacimiento')
                                            <small class="text-danger fas fa-exclamation-circle">
                                                {{$message}}
                                            </small>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="form-group">
                                        {!! Form::label('fechanacimiento', 'Fecha de nacimiento:') !!}
                                        {!! Form::date('fechanacimiento', \Carbon\Carbon::now(), ['class' => 'form-control', 'id' => 'fecha_nacimiento', 'max' => \Carbon\Carbon::now()->format('Y-m-d')]) !!}
                                        @error('fechanacimiento')
                                            <small class="text-danger fas fa-exclamation-circle">
                                                {{ $message }}
                                            </small>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="form-group">
                                        {!! Form::label('edad', 'Edad:') !!}
                                        {!! Form::text('edad', null, ['class' => 'form-control', 'readonly' => true, 'id' => 'edad']) !!}
                                        @error('edad')
                                            <small class="text-danger fas fa-exclamation-circle">
                                                {{ $message }}
                                            </small>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-3">
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
                                <div class="col-lg-3">
                                    <div class="form-group">
                                        {!! Form::label('estadocivil', 'Estado civil:') !!}
                                        {!! Form::select('estadocivil', $estciv, null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '45']) !!}
                                        @error('estadocivil')
                                            <small class="text-danger fas fa-exclamation-circle">
                                                {{$message}}
                                            </small>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="form-group">
                                        {!! Form::label('lugarresidencia', 'Lugar de residencia:') !!}
                                        {!! Form::select('lugarresidencia', $departamentos, null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '45']) !!}
                                        @error('lugarresidencia')
                                            <small class="text-danger fas fa-exclamation-circle">
                                                {{$message}}
                                            </small>
                                        @enderror
                                    </div>
                                </div>
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
                            </div>
                            <div class="row">
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        {!! Form::label('gradoinstruccion', 'Grado de instrucción:') !!}
                                        {!! Form::select('gradoinstruccion', $gradoins, null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '45']) !!}
                                        @error('estadocivil')
                                            <small class="text-danger fas fa-exclamation-circle">
                                                {{$message}}
                                            </small>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        {!! Form::label('actividadlaboral', 'Actividad laboral:') !!}
                                        {!! Form::select('actividadlaboral', $actlab, null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '45']) !!}
                                        @error('actividadlaboral')
                                            <small class="text-danger fas fa-exclamation-circle">
                                                {{$message}}
                                            </small>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        {!! Form::label('ocupacionprofesion', 'Ocupación / Profesión:') !!}
                                        {!! Form::text('ocupacionprofesion', null, ['class' => 'form-control', 'placeholder' => '']) !!}
                                        @error('ocupacionprofesion')
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
                                        {!! Form::label('entgestorsalud', 'Ent. gestor salud:') !!}
                                        {!! Form::text('entgestorsalud', null, ['class' => 'form-control', 'placeholder' => '']) !!}
                                        @error('entgestorsalud')
                                            <small class="text-danger fas fa-exclamation-circle">
                                                {{$message}}
                                            </small>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        {!! Form::label('motivoconsulta', 'Motivo de consulta:') !!}
                                        {!! Form::text('motivoconsulta', null, ['class' => 'form-control', 'placeholder' => '']) !!}
                                        @error('motivoconsulta')
                                            <small class="text-danger fas fa-exclamation-circle">
                                                {{$message}}
                                            </small>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            {!! Form::submit('Crear cliente', ['class' => 'btn btn-crear']) !!}
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@stop
@section('js')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script type="text/javascript" src="https://jeremyfagis.github.io/dropify/dist/js/dropify.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://jeremyfagis.github.io/dropify/dist/css/dropify.min.css"> 
<script>
//VALIDAR QUE FECHA DE NACIMIENTO NO SEA POSTERIOR A LA FECHA ACTUAL
    var fechaNacimiento = document.getElementById('fecha_nacimiento');
    fechaNacimiento.addEventListener('change', function() {
        var selectedDate = new Date(this.value);
        var currentDate = new Date();
        if (selectedDate > currentDate) {
            this.value = '{{ \Carbon\Carbon::now()->format("Y-m-d") }}';
            if (!document.getElementById('errorMensaje')) {
                var errorMensaje = document.createElement('div');
                errorMensaje.id = 'errorMensaje';
                errorMensaje.classList.add('mensaje-error');
                var iconoError = document.createElement('i');
                iconoError.classList.add('fas', 'fa-exclamation-circle');
                errorMensaje.appendChild(iconoError);
                
                var textoError = document.createElement('span');
                textoError.textContent = ' La fecha de nacimiento no puede ser posterior a la fecha actual.';
                errorMensaje.appendChild(textoError);
                this.parentNode.appendChild(errorMensaje);
            }
        } else {
            var mensajeError = document.getElementById('errorMensaje');
            if (mensajeError) {
                mensajeError.remove();
            }
        }
    });

//CALCULAR LA EDAD
function calcularEdad(fecha_nacimiento) {
    var fecha_actual = new Date();
    var fecha_nacimiento = new Date(fecha_nacimiento);
    
    if (isNaN(fecha_nacimiento.getFullYear()) || fecha_nacimiento.getFullYear() < 1000) {
        return '';
    }
    var edad = fecha_actual.getFullYear() - fecha_nacimiento.getFullYear();
    var mes = fecha_actual.getMonth() - fecha_nacimiento.getMonth();
    if (mes < 0 || (mes === 0 && fecha_actual.getDate() < fecha_nacimiento.getDate())) {
        edad--;
    }
    return edad;
}

//VALIDAR FECHA DE NACIMIENTO
document.getElementById('fecha_nacimiento').addEventListener('change', function() {
        var fecha_nacimiento = this.value;
        var fecha_actual = new Date();
        var selectedDate = new Date(fecha_nacimiento);
        if (selectedDate <= fecha_actual) {
            var edad = calcularEdad(fecha_nacimiento);
            document.getElementById('edad').value = edad;
        } else {
            document.getElementById('edad').value = '';
        }
    });
    var fecha_nacimiento = document.getElementById('fecha_nacimiento').value;
    var fecha_actual = new Date();
    var selectedDate = new Date(fecha_nacimiento);
    if (selectedDate <= fecha_actual) {
        var edad = calcularEdad(fecha_nacimiento);
        document.getElementById('edad').value = edad;
    } else {
        document.getElementById('edad').value = '';
    }

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