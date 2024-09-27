@extends('adminlte::page')
    
@section('content_header')
<h1>FICHA MEDICA DE "{{$clientecomun->nombrecompleto}}"</h1>
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
        <h3 style="text-align: center;" style="">FICHA GENERAL</h3>
        
        <div class="container">
            <div class="row">
                <div class="col-lg-4">
                    <div class="form-group">
                        {!! Form::label('Fecha de atención:') !!}
                        {!! Form::date('fechaatencion', \Carbon\Carbon::now(), ['class' => 'form-control', 'max' => \Carbon\Carbon::now()->format('Y-m-d')]) !!}
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="form-group">
                        {!! Form::label('Empresa:') !!}
                        {!! Form::text('empresa', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '45', 'readonly' => 'readonly']) !!}
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="form-group">
                        {!! Form::label('Regional:') !!}
                        {!! Form::text('regional', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '45', 'readonly' => 'readonly']) !!}
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="form-group">
                        {!! Form::label('Antecedentes patológicos:') !!}
                        {!! Form::text('antecedentespatologicos', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '200']) !!}
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-10">
                    <div class="form-group">
                        {!! Form::label('Nombre completo:') !!}
                        {!! Form::text('nombrecompleto', $clientecomun->nombrecompleto, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '45', 'readonly' => 'readonly']) !!}
                    </div>
                </div>
                <div class="col-lg-2">
                    <div class="form-group">
                        {!! Form::label('Código:') !!}
                        {!! Form::text('nombrecompleto', $clientecomun->id, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '45', 'readonly' => 'readonly']) !!}
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-lg-3">
                    <div class="form-group">
                        {!! Form::label('Género:') !!}
                        {!! Form::text('genero', $clientecomun->genero, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '45', 'readonly' => 'readonly']) !!}
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="form-group">
                        {!! Form::label('Fecha de nacimiento:') !!}
                        {!! Form::text('fechanacimiento', $clientecomun->fechanacimiento, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '45', 'readonly' => 'readonly']) !!}
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="form-group">
                        {!! Form::label('Edad:') !!}
                        {!! Form::text('edad', $clientecomun->edad, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '45', 'readonly' => 'readonly']) !!}
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="form-group">
                        {!! Form::label('Lugar de nacimiento:') !!}
                        {!! Form::text('lugarnacimiento', $clientecomun->lugarnacimiento, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '45', 'readonly' => 'readonly']) !!}
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-4">
                    <div class="form-group">
                        {!! Form::label('Residencia:') !!}
                        {!! Form::text('residencia', $clientecomun->lugarnacimiento, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '45', 'readonly' => 'readonly']) !!}
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="form-group">
                        {!! Form::label('Grado de instrucción:') !!}
                        {!! Form::text('gradoinstruccion', $clientecomun->gradoinstruccion, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '45', 'readonly' => 'readonly']) !!}
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="form-group">
                        {!! Form::label('Estado civil:') !!}
                        {!! Form::text('estadocivil', $clientecomun->estadocivil, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '45', 'readonly' => 'readonly']) !!}
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-3">
                    <div class="form-group">
                        {!! Form::label('CI:') !!}
                        {!! Form::text('ci', $clientecomun->ci, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '45', 'readonly' => 'readonly']) !!}
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="form-group">
                        {!! Form::label('Telefono del paciente:') !!}
                        {!! Form::text('telefono', $clientecomun->telefono, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '45', 'readonly' => 'readonly']) !!}
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-group">
                        {!! Form::label('Direccion domiciliaria:') !!}
                        {!! Form::text('domicilio', $clientecomun->domicilio, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '45', 'readonly' => 'readonly']) !!}
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-6">
                    <div class="form-group">
                        {!! Form::label('Motivo de consulta:') !!}
                        {!! Form::text('motivoconsulta', $clientecomun->motivoconsulta, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '45', 'readonly' => 'readonly']) !!}
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-group">
                        {!! Form::label('Ente gestor de salud:') !!}
                        {!! Form::text('entgestorsalud', $clientecomun->entgestorsalud, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '45', 'readonly' => 'readonly']) !!}
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-6">
                    <div class="form-group">
                        {!! Form::label('Profesion / Ocupacion:') !!}
                        {!! Form::text('ocupacion', $clientecomun->ocupacion, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '45', 'readonly' => 'readonly']) !!}
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-group">
                        {!! Form::label('Actividad laboral:') !!}
                        {!! Form::text('actividadlaboral', $clientecomun->actividadlaboral, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '45', 'readonly' => 'readonly']) !!}
                    </div>
                </div>
            </div>
            <h4 style="text-align: center;" style="">DATOS OCUPACIONALES</h4>
            <div class="row">
                <div class="col-lg-6">
                    <div class="form-group">
                        {!! Form::label('Periodo de tiempo laboral:') !!}
                        <div class="input-group">
                            {!! Form::text('periodotiempolaboral', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '3', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']) !!}
                            <div class="input-group-append">
                                {!! Form::select('periodo_tipo', ['' => '', 'DIAS' => 'DIAS', 'SEMANAS' => 'SEMANAS', 'MESES' => 'MESES', 'AÑOS' => 'AÑOS'], null, ['class' => 'form-control']) !!}
                            </div>
                        </div>
                    </div>
                </div>                
                <div class="col-lg-6">
                    <div class="form-group">
                        {!! Form::label('Cargo que desempeña o último cargo desempeñado:') !!}
                        {!! Form::text('actividadlaboral', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '45']) !!}
                    </div>
                </div>
            </div>
            <h4 style="text-align: center;" style="">ANTECEDENTES LABORALES</h4>
            <div class="row">
                <div class="col-lg-6">
                    <div class="form-group">
                        {!! Form::label('Lugar:') !!}
                        {!! Form::text('lugar', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '45']) !!}
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-group">
                        {!! Form::label('Periodo de tiempo laboral:') !!}
                        {!! Form::text('periodotiempolaboral', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '45']) !!}
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-group">
                        {!! Form::label('Direccion:') !!}
                        {!! Form::text('direccion', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '45']) !!}
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-group">
                        {!! Form::label('Edad:') !!}
                        {!! Form::text('edad', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '45']) !!}
                    </div>
                </div>
            </div>
            <div id="card01">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group">
                            {!! Form::label('', 'Cargo 1:') !!}
                            {!! Form::text('preguntas[1][cargo]', null, ['class' => 'form-control', 'placeholder' => '']) !!}
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="form-group">
                            {!! Form::label('', 'Desde:') !!}
                            {!! Form::date('preguntas[1][fechadesde]', null, ['class' => 'form-control', 'placeholder' => 'FECHA']) !!}
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="form-group">
                            {!! Form::label('', 'Hasta:') !!}
                            {!! Form::date('preguntas[1][fechahasta]', null, ['class' => 'form-control', 'placeholder' => 'FECHA']) !!}
                        </div>
                    </div>
                </div>
                <abbr title="Nuevo Formulario"><button type="button" class="btn btn-mas" onclick="mostrarcard02()"><i class="fas fa-plus"></i></button></abbr>
                <abbr id="menosformulario02" title="Menos Formulario" style="display: none;">
                    <button type="button" class="btn btn-menos" onclick="ocultarcard02()"><i class="fas fa-minus"></i></button></abbr>
            </div>
            <div id="card02" style="display: none;">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group">
                            {!! Form::label('', 'Cargo 2:') !!}
                            {!! Form::text('preguntas[1][cargo]', null, ['class' => 'form-control', 'placeholder' => '']) !!}
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="form-group">
                            {!! Form::label('', 'Desde:') !!}
                            {!! Form::date('preguntas[1][fechadesde]', null, ['class' => 'form-control', 'placeholder' => 'FECHA']) !!}
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="form-group">
                            {!! Form::label('', 'Hasta:') !!}
                            {!! Form::date('preguntas[1][fechahasta]', null, ['class' => 'form-control', 'placeholder' => 'FECHA']) !!}
                        </div>
                    </div>
                </div>
                <abbr title="Nuevo Formulario"><button type="button" class="btn btn-mas" onclick="mostrarcard03()"><i class="fas fa-plus"></i></button></abbr>
                <abbr id="menosformulario03" title="Menos Formulario" style="display: none;">
                    <button type="button" class="btn btn-menos" onclick="ocultarcard03()"><i class="fas fa-minus"></i></button></abbr>
            </div>
            <div id="card03" style="display: none;">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group">
                            {!! Form::label('', 'Cargo 3:') !!}
                            {!! Form::text('preguntas[1][cargo]', null, ['class' => 'form-control', 'placeholder' => '']) !!}
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="form-group">
                            {!! Form::label('', 'Desde:') !!}
                            {!! Form::date('preguntas[1][fechadesde]', null, ['class' => 'form-control', 'placeholder' => 'FECHA']) !!}
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="form-group">
                            {!! Form::label('', 'Hasta:') !!}
                            {!! Form::date('preguntas[1][fechahasta]', null, ['class' => 'form-control', 'placeholder' => 'FECHA']) !!}
                        </div>
                    </div>
                </div>
            </div>
            <script>
                function limpiarCampos01() {
                    document.getElementById('card01').querySelectorAll('input[type="text"]').forEach(function(input) {
                        input.value = '';
                    });
                    document.getElementById('card02').querySelectorAll('input[type="text"]').forEach(function(input) {
                        input.value = '';
                    });
                    document.getElementById('card03').querySelectorAll('input[type="text"]').forEach(function(input) {
                        input.value = '';
                    });
                    }
                function mostrarcard02() {
                    document.getElementById('card02').style.display = 'block';
                    document.getElementById('menosformulario02').style.display = 'inline';
                    }
                function ocultarcard02() {
                    var card02 = document.getElementById('card02');
                    if (card02.style.display === 'block') {
                        card02.style.display = 'none';
                        limpiarcard02();
                        document.getElementById('menosformulario02').style.display = 'none';
                        }
                    var card03 = document.getElementById('card03');
                    if (card03.style.display === 'block') {
                        card03.style.display = 'none';
                        limpiarcard03();
                        document.getElementById('menosformulario03').style.display = 'none';
                        }
                    }
                function limpiarcard02() {
                    document.getElementById('card02').querySelectorAll('input[type="text"]').forEach(function(input) {
                        input.value = '';
                        });
                    }
                function mostrarcard03() {
                    document.getElementById('card03').style.display = 'block';
                    document.getElementById('menosformulario03').style.display = 'inline';
                    }
                function ocultarcard03() {
                    var card03 = document.getElementById('card03');
                    if (card03.style.display === 'block') {
                        card03.style.display = 'none';
                        limpiarcard03();
                        document.getElementById('menosformulario03').style.display = 'none';
                        }
                    }
                function limpiarcard03() {
                    document.getElementById('card03').querySelectorAll('input[type="text"]').forEach(function(input) {
                        input.value = '';
                        });
                    }
            </script>
            <h4 style="text-align: center;" style="">IDENTIFICACIÓN DE PELIGROS</h4>
            <div class="row odd-row">
                <div class="col-lg-10">
                    <p>Peligros fisicos</p>
                </div>
                <div class="col-lg-2">
                    <div class="form-group">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="preguntas[1][respuesta]" value="si" id="rsi" onclick="mostrarFormulario01()">
                            <label class="form-check-label" for="rsi">SI</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="preguntas[1][respuesta]" value="no" id="rno" onclick="ocultarFormulario01()">
                            <label class="form-check-label" for="rno">NO</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-10">
                    <p>Peligros quimicos</p>
                </div>
                <div class="col-lg-2">
                    <div class="form-group">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="preguntas[2][respuesta]" value="si" id="rsi" onclick="mostrarFormulario01()">
                            <label class="form-check-label" for="rsi">SI</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="preguntas[2][respuesta]" value="no" id="rno" onclick="ocultarFormulario01()">
                            <label class="form-check-label" for="rno">NO</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row odd-row">
                <div class="col-lg-10">
                    <p>Peligros ergonomicos</p>
                </div>
                <div class="col-lg-2">
                    <div class="form-group">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="preguntas[3][respuesta]" value="si" id="rsi" onclick="mostrarFormulario01()">
                            <label class="form-check-label" for="rsi">SI</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="preguntas[3][respuesta]" value="no" id="rno" onclick="ocultarFormulario01()">
                            <label class="form-check-label" for="rno">NO</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-10">
                    <p>EPP'S</p>
                </div>
                <div class="col-lg-2">
                    <div class="form-group">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="preguntas[4][respuesta]" value="si" id="rsi" onclick="mostrarFormulario01()">
                            <label class="form-check-label" for="rsi">SI</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="preguntas[4][respuesta]" value="no" id="rno" onclick="ocultarFormulario01()">
                            <label class="form-check-label" for="rno">NO</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row odd-row">
                <div class="col-lg-10">
                    <p>Peligros biologicos</p>
                </div>
                <div class="col-lg-2">
                    <div class="form-group">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="preguntas[5][respuesta]" value="si" id="rsi" onclick="mostrarFormulario01()">
                            <label class="form-check-label" for="rsi">SI</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="preguntas[5][respuesta]" value="no" id="rno" onclick="ocultarFormulario01()">
                            <label class="form-check-label" for="rno">NO</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-10">
                    <p>Peligros mecanicos</p>
                </div>
                <div class="col-lg-2">
                    <div class="form-group">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="preguntas[6][respuesta]" value="si" id="rsi" onclick="mostrarFormulario01()">
                            <label class="form-check-label" for="rsi">SI</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="preguntas[6][respuesta]" value="no" id="rno" onclick="ocultarFormulario01()">
                            <label class="form-check-label" for="rno">NO</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row odd-row">
                <div class="col-lg-10">
                    <p>Peligros ambientales</p>
                </div>
                <div class="col-lg-2">
                    <div class="form-group">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="preguntas[7][respuesta]" value="si" id="rsi" onclick="mostrarFormulario01()">
                            <label class="form-check-label" for="rsi">SI</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="preguntas[7][respuesta]" value="no" id="rno" onclick="ocultarFormulario01()">
                            <label class="form-check-label" for="rno">NO</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-10">
                    <p>Peligros psicosociales</p>
                </div>
                <div class="col-lg-2">
                    <div class="form-group">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="preguntas[8][respuesta]" value="si" id="rsi" onclick="mostrarFormulario01()">
                            <label class="form-check-label" for="rsi">SI</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="preguntas[8][respuesta]" value="no" id="rno" onclick="ocultarFormulario01()">
                            <label class="form-check-label" for="rno">NO</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row odd-row">
                <div class="col-lg-10">
                    <p>Peligros fisicos</p>
                </div>
                <div class="col-lg-2">
                    <div class="form-group">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="preguntas[9][respuesta]" value="si" id="rsi" onclick="mostrarFormulario01()">
                            <label class="form-check-label" for="rsi">SI</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="preguntas[9][respuesta]" value="no" id="rno" onclick="ocultarFormulario01()">
                            <label class="form-check-label" for="rno">NO</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="form-group">
                        {!! Form::label('Otros:') !!}
                        {!! Form::text('otros', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '45']) !!}
                    </div>
                </div>
            </div>
            <h4 style="text-align: center;" style="">ANTECEDENTES PATOLOGICOS</h4>
            <div class="50">
                <div class="row">
                    <div class="col-lg-10">
                        <p>Cefalea</p>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[50][respuesta]" value="si" id="rsi" onclick="mostrarFormulario050()">
                                <label class="form-check-label" for="rsi">SI</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[50][respuesta]" value="no" id="rno" onclick="ocultarFormulario050()">
                                <label class="form-check-label" for="rno">NO</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="card050" style="display: none;">
                    <div class="row">
                        <div class="card-body">    
                            <div class="row">
                                <div class="col-lg-4">
                                    {!! Form::label('', 'Hace cuanto') !!}
                                    <div class="form-group">
                                        <div class="input-group">
                                            {!! Form::text('periodotiempolaboral50', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '3', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']) !!}
                                            <div class="input-group-append">
                                                {!! Form::select('periodo_tipo50', ['' => '', 'DIAS' => 'DIAS', 'SEMANAS' => 'SEMANAS', 'MESES' => 'MESES', 'AÑOS' => 'AÑOS'], null, ['class' => 'form-control']) !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                    function limpiarCampos050() {
                        document.getElementById('card050').querySelectorAll('input[type="text"]').forEach(function(input) {
                            input.value = '';
                        });
                        document.getElementById('card050').querySelectorAll('select').forEach(function(select) {
                            select.value = '';
                        });
                    }
                    function mostrarFormulario050() {
                        document.getElementById('card050').style.display = 'block';
                    }
                    function ocultarFormulario050() {
                        document.getElementById('card050').style.display = 'none';
                        limpiarCampos050();
                    }
                </script>
            </div>
            <div class="51">
                <div class="row odd-row">
                    <div class="col-lg-10">
                        <p>Defecto visual</p>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[50][respuesta]" value="si" id="rsi" onclick="mostrarFormulario050()">
                                <label class="form-check-label" for="rsi">SI</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[50][respuesta]" value="no" id="rno" onclick="ocultarFormulario050()">
                                <label class="form-check-label" for="rno">NO</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="card050" style="display: none;">
                    <div class="row odd-row">
                        <div class="card-body">    
                            <div class="row">
                                <div class="col-lg-4">
                                    {!! Form::label('', 'Hace cuanto') !!}
                                    <div class="form-group">
                                        <div class="input-group">
                                            {!! Form::text('periodotiempolaboral50', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '3', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']) !!}
                                            <div class="input-group-append">
                                                {!! Form::select('periodo_tipo50', ['' => '', 'DIAS' => 'DIAS', 'SEMANAS' => 'SEMANAS', 'MESES' => 'MESES', 'AÑOS' => 'AÑOS'], null, ['class' => 'form-control']) !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                    function limpiarCampos050() {
                        document.getElementById('card050').querySelectorAll('input[type="text"]').forEach(function(input) {
                            input.value = '';
                        });
                        document.getElementById('card050').querySelectorAll('select').forEach(function(select) {
                            select.value = '';
                        });
                    }
                    function mostrarFormulario050() {
                        document.getElementById('card050').style.display = 'block';
                    }
                    function ocultarFormulario050() {
                        document.getElementById('card050').style.display = 'none';
                        limpiarCampos050();
                    }
                </script>
            </div>
            <div class="52">
                <div class="row">
                    <div class="col-lg-10">
                        <p>Hipoacuasia</p>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[50][respuesta]" value="si" id="rsi" onclick="mostrarFormulario050()">
                                <label class="form-check-label" for="rsi">SI</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[50][respuesta]" value="no" id="rno" onclick="ocultarFormulario050()">
                                <label class="form-check-label" for="rno">NO</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="card050" style="display: none;">
                    <div class="row">
                        <div class="card-body">    
                            <div class="row">
                                <div class="col-lg-4">
                                    {!! Form::label('', 'Hace cuanto') !!}
                                    <div class="form-group">
                                        <div class="input-group">
                                            {!! Form::text('periodotiempolaboral50', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '3', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']) !!}
                                            <div class="input-group-append">
                                                {!! Form::select('periodo_tipo50', ['' => '', 'DIAS' => 'DIAS', 'SEMANAS' => 'SEMANAS', 'MESES' => 'MESES', 'AÑOS' => 'AÑOS'], null, ['class' => 'form-control']) !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                    function limpiarCampos050() {
                        document.getElementById('card050').querySelectorAll('input[type="text"]').forEach(function(input) {
                            input.value = '';
                        });
                        document.getElementById('card050').querySelectorAll('select').forEach(function(select) {
                            select.value = '';
                        });
                    }
                    function mostrarFormulario050() {
                        document.getElementById('card050').style.display = 'block';
                    }
                    function ocultarFormulario050() {
                        document.getElementById('card050').style.display = 'none';
                        limpiarCampos050();
                    }
                </script>
            </div>
            <div class="53">
                <div class="row odd-row">
                    <div class="col-lg-10">
                        <p>Otitis media</p>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[50][respuesta]" value="si" id="rsi" onclick="mostrarFormulario050()">
                                <label class="form-check-label" for="rsi">SI</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[50][respuesta]" value="no" id="rno" onclick="ocultarFormulario050()">
                                <label class="form-check-label" for="rno">NO</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="card050" style="display: none;">
                    <div class="row odd-row">
                        <div class="card-body">    
                            <div class="row">
                                <div class="col-lg-4">
                                    {!! Form::label('', 'Hace cuanto') !!}
                                    <div class="form-group">
                                        <div class="input-group">
                                            {!! Form::text('periodotiempolaboral50', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '3', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']) !!}
                                            <div class="input-group-append">
                                                {!! Form::select('periodo_tipo50', ['' => '', 'DIAS' => 'DIAS', 'SEMANAS' => 'SEMANAS', 'MESES' => 'MESES', 'AÑOS' => 'AÑOS'], null, ['class' => 'form-control']) !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                    function limpiarCampos050() {
                        document.getElementById('card050').querySelectorAll('input[type="text"]').forEach(function(input) {
                            input.value = '';
                        });
                        document.getElementById('card050').querySelectorAll('select').forEach(function(select) {
                            select.value = '';
                        });
                    }
                    function mostrarFormulario050() {
                        document.getElementById('card050').style.display = 'block';
                    }
                    function ocultarFormulario050() {
                        document.getElementById('card050').style.display = 'none';
                        limpiarCampos050();
                    }
                </script>
            </div>
            <div class="54">
                <div class="row">
                    <div class="col-lg-10">
                        <p>Sinusitis</p>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[50][respuesta]" value="si" id="rsi" onclick="mostrarFormulario050()">
                                <label class="form-check-label" for="rsi">SI</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[50][respuesta]" value="no" id="rno" onclick="ocultarFormulario050()">
                                <label class="form-check-label" for="rno">NO</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="card050" style="display: none;">
                    <div class="row">
                        <div class="card-body">    
                            <div class="row">
                                <div class="col-lg-4">
                                    {!! Form::label('', 'Hace cuanto') !!}
                                    <div class="form-group">
                                        <div class="input-group">
                                            {!! Form::text('periodotiempolaboral50', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '3', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']) !!}
                                            <div class="input-group-append">
                                                {!! Form::select('periodo_tipo50', ['' => '', 'DIAS' => 'DIAS', 'SEMANAS' => 'SEMANAS', 'MESES' => 'MESES', 'AÑOS' => 'AÑOS'], null, ['class' => 'form-control']) !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                    function limpiarCampos050() {
                        document.getElementById('card050').querySelectorAll('input[type="text"]').forEach(function(input) {
                            input.value = '';
                        });
                        document.getElementById('card050').querySelectorAll('select').forEach(function(select) {
                            select.value = '';
                        });
                    }
                    function mostrarFormulario050() {
                        document.getElementById('card050').style.display = 'block';
                    }
                    function ocultarFormulario050() {
                        document.getElementById('card050').style.display = 'none';
                        limpiarCampos050();
                    }
                </script>
            </div>
            <div class="55">
                <div class="row odd-row">
                    <div class="col-lg-10">
                        <p>Tinitus</p>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[50][respuesta]" value="si" id="rsi" onclick="mostrarFormulario050()">
                                <label class="form-check-label" for="rsi">SI</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[50][respuesta]" value="no" id="rno" onclick="ocultarFormulario050()">
                                <label class="form-check-label" for="rno">NO</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="card050" style="display: none;">
                    <div class="row odd-row">
                        <div class="card-body">    
                            <div class="row">
                                <div class="col-lg-4">
                                    {!! Form::label('', 'Hace cuanto') !!}
                                    <div class="form-group">
                                        <div class="input-group">
                                            {!! Form::text('periodotiempolaboral50', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '3', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']) !!}
                                            <div class="input-group-append">
                                                {!! Form::select('periodo_tipo50', ['' => '', 'DIAS' => 'DIAS', 'SEMANAS' => 'SEMANAS', 'MESES' => 'MESES', 'AÑOS' => 'AÑOS'], null, ['class' => 'form-control']) !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                    function limpiarCampos050() {
                        document.getElementById('card050').querySelectorAll('input[type="text"]').forEach(function(input) {
                            input.value = '';
                        });
                        document.getElementById('card050').querySelectorAll('select').forEach(function(select) {
                            select.value = '';
                        });
                    }
                    function mostrarFormulario050() {
                        document.getElementById('card050').style.display = 'block';
                    }
                    function ocultarFormulario050() {
                        document.getElementById('card050').style.display = 'none';
                        limpiarCampos050();
                    }
                </script>
            </div>
            <div class="56">
                <div class="row">
                    <div class="col-lg-10">
                        <p>Convulsiones</p>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[50][respuesta]" value="si" id="rsi" onclick="mostrarFormulario050()">
                                <label class="form-check-label" for="rsi">SI</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[50][respuesta]" value="no" id="rno" onclick="ocultarFormulario050()">
                                <label class="form-check-label" for="rno">NO</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="card050" style="display: none;">
                    <div class="row">
                        <div class="card-body">    
                            <div class="row">
                                <div class="col-lg-4">
                                    {!! Form::label('', 'Hace cuanto') !!}
                                    <div class="form-group">
                                        <div class="input-group">
                                            {!! Form::text('periodotiempolaboral50', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '3', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']) !!}
                                            <div class="input-group-append">
                                                {!! Form::select('periodo_tipo50', ['' => '', 'DIAS' => 'DIAS', 'SEMANAS' => 'SEMANAS', 'MESES' => 'MESES', 'AÑOS' => 'AÑOS'], null, ['class' => 'form-control']) !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                    function limpiarCampos050() {
                        document.getElementById('card050').querySelectorAll('input[type="text"]').forEach(function(input) {
                            input.value = '';
                        });
                        document.getElementById('card050').querySelectorAll('select').forEach(function(select) {
                            select.value = '';
                        });
                    }
                    function mostrarFormulario050() {
                        document.getElementById('card050').style.display = 'block';
                    }
                    function ocultarFormulario050() {
                        document.getElementById('card050').style.display = 'none';
                        limpiarCampos050();
                    }
                </script>
            </div>
            <div class="57">
                <div class="row odd-row">
                    <div class="col-lg-10">
                        <p>HTA</p>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[50][respuesta]" value="si" id="rsi" onclick="mostrarFormulario050()">
                                <label class="form-check-label" for="rsi">SI</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[50][respuesta]" value="no" id="rno" onclick="ocultarFormulario050()">
                                <label class="form-check-label" for="rno">NO</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="card050" style="display: none;">
                    <div class="row odd-row">
                        <div class="card-body">    
                            <div class="row">
                                <div class="col-lg-4">
                                    {!! Form::label('', 'Hace cuanto') !!}
                                    <div class="form-group">
                                        <div class="input-group">
                                            {!! Form::text('periodotiempolaboral50', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '3', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']) !!}
                                            <div class="input-group-append">
                                                {!! Form::select('periodo_tipo50', ['' => '', 'DIAS' => 'DIAS', 'SEMANAS' => 'SEMANAS', 'MESES' => 'MESES', 'AÑOS' => 'AÑOS'], null, ['class' => 'form-control']) !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                    function limpiarCampos050() {
                        document.getElementById('card050').querySelectorAll('input[type="text"]').forEach(function(input) {
                            input.value = '';
                        });
                        document.getElementById('card050').querySelectorAll('select').forEach(function(select) {
                            select.value = '';
                        });
                    }
                    function mostrarFormulario050() {
                        document.getElementById('card050').style.display = 'block';
                    }
                    function ocultarFormulario050() {
                        document.getElementById('card050').style.display = 'none';
                        limpiarCampos050();
                    }
                </script>
            </div>
            <div class="58">
                <div class="row">
                    <div class="col-lg-10">
                        <p>Enfermedad cardiaca</p>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[50][respuesta]" value="si" id="rsi" onclick="mostrarFormulario050()">
                                <label class="form-check-label" for="rsi">SI</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[50][respuesta]" value="no" id="rno" onclick="ocultarFormulario050()">
                                <label class="form-check-label" for="rno">NO</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="card050" style="display: none;">
                    <div class="row">
                        <div class="card-body">    
                            <div class="row">
                                <div class="col-lg-4">
                                    {!! Form::label('', 'Hace cuanto') !!}
                                    <div class="form-group">
                                        <div class="input-group">
                                            {!! Form::text('periodotiempolaboral50', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '3', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']) !!}
                                            <div class="input-group-append">
                                                {!! Form::select('periodo_tipo50', ['' => '', 'DIAS' => 'DIAS', 'SEMANAS' => 'SEMANAS', 'MESES' => 'MESES', 'AÑOS' => 'AÑOS'], null, ['class' => 'form-control']) !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                    function limpiarCampos050() {
                        document.getElementById('card050').querySelectorAll('input[type="text"]').forEach(function(input) {
                            input.value = '';
                        });
                        document.getElementById('card050').querySelectorAll('select').forEach(function(select) {
                            select.value = '';
                        });
                    }
                    function mostrarFormulario050() {
                        document.getElementById('card050').style.display = 'block';
                    }
                    function ocultarFormulario050() {
                        document.getElementById('card050').style.display = 'none';
                        limpiarCampos050();
                    }
                </script>
            </div>
            <div class="59">
                <div class="row odd-row">
                    <div class="col-lg-10">
                        <p>Hepatitis</p>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[50][respuesta]" value="si" id="rsi" onclick="mostrarFormulario050()">
                                <label class="form-check-label" for="rsi">SI</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[50][respuesta]" value="no" id="rno" onclick="ocultarFormulario050()">
                                <label class="form-check-label" for="rno">NO</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="card050" style="display: none;">
                    <div class="row odd-row">
                        <div class="card-body">    
                            <div class="row">
                                <div class="col-lg-4">
                                    {!! Form::label('', 'Hace cuanto') !!}
                                    <div class="form-group">
                                        <div class="input-group">
                                            {!! Form::text('periodotiempolaboral50', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '3', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']) !!}
                                            <div class="input-group-append">
                                                {!! Form::select('periodo_tipo50', ['' => '', 'DIAS' => 'DIAS', 'SEMANAS' => 'SEMANAS', 'MESES' => 'MESES', 'AÑOS' => 'AÑOS'], null, ['class' => 'form-control']) !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                    function limpiarCampos050() {
                        document.getElementById('card050').querySelectorAll('input[type="text"]').forEach(function(input) {
                            input.value = '';
                        });
                        document.getElementById('card050').querySelectorAll('select').forEach(function(select) {
                            select.value = '';
                        });
                    }
                    function mostrarFormulario050() {
                        document.getElementById('card050').style.display = 'block';
                    }
                    function ocultarFormulario050() {
                        document.getElementById('card050').style.display = 'none';
                        limpiarCampos050();
                    }
                </script>
            </div>
            <div class="60">
                <div class="row">
                    <div class="col-lg-10">
                        <p>Enfermedad de la tiroides</p>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[50][respuesta]" value="si" id="rsi" onclick="mostrarFormulario050()">
                                <label class="form-check-label" for="rsi">SI</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[50][respuesta]" value="no" id="rno" onclick="ocultarFormulario050()">
                                <label class="form-check-label" for="rno">NO</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="card050" style="display: none;">
                    <div class="row">
                        <div class="card-body">    
                            <div class="row">
                                <div class="col-lg-4">
                                    {!! Form::label('', 'Hace cuanto') !!}
                                    <div class="form-group">
                                        <div class="input-group">
                                            {!! Form::text('periodotiempolaboral50', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '3', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']) !!}
                                            <div class="input-group-append">
                                                {!! Form::select('periodo_tipo50', ['' => '', 'DIAS' => 'DIAS', 'SEMANAS' => 'SEMANAS', 'MESES' => 'MESES', 'AÑOS' => 'AÑOS'], null, ['class' => 'form-control']) !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                    function limpiarCampos050() {
                        document.getElementById('card050').querySelectorAll('input[type="text"]').forEach(function(input) {
                            input.value = '';
                        });
                        document.getElementById('card050').querySelectorAll('select').forEach(function(select) {
                            select.value = '';
                        });
                    }
                    function mostrarFormulario050() {
                        document.getElementById('card050').style.display = 'block';
                    }
                    function ocultarFormulario050() {
                        document.getElementById('card050').style.display = 'none';
                        limpiarCampos050();
                    }
                </script>
            </div>
            <div class="61">
                <div class="row odd-row">
                    <div class="col-lg-10">
                        <p>Hernia inguinal</p>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[50][respuesta]" value="si" id="rsi" onclick="mostrarFormulario050()">
                                <label class="form-check-label" for="rsi">SI</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[50][respuesta]" value="no" id="rno" onclick="ocultarFormulario050()">
                                <label class="form-check-label" for="rno">NO</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="card050" style="display: none;">
                    <div class="row odd-row">
                        <div class="card-body">    
                            <div class="row">
                                <div class="col-lg-4">
                                    {!! Form::label('', 'Hace cuanto') !!}
                                    <div class="form-group">
                                        <div class="input-group">
                                            {!! Form::text('periodotiempolaboral50', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '3', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']) !!}
                                            <div class="input-group-append">
                                                {!! Form::select('periodo_tipo50', ['' => '', 'DIAS' => 'DIAS', 'SEMANAS' => 'SEMANAS', 'MESES' => 'MESES', 'AÑOS' => 'AÑOS'], null, ['class' => 'form-control']) !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                    function limpiarCampos050() {
                        document.getElementById('card050').querySelectorAll('input[type="text"]').forEach(function(input) {
                            input.value = '';
                        });
                        document.getElementById('card050').querySelectorAll('select').forEach(function(select) {
                            select.value = '';
                        });
                    }
                    function mostrarFormulario050() {
                        document.getElementById('card050').style.display = 'block';
                    }
                    function ocultarFormulario050() {
                        document.getElementById('card050').style.display = 'none';
                        limpiarCampos050();
                    }
                </script>
            </div>
            <div class="62">
                <div class="row">
                    <div class="col-lg-10">
                        <p>Hernia umbilical</p>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[50][respuesta]" value="si" id="rsi" onclick="mostrarFormulario050()">
                                <label class="form-check-label" for="rsi">SI</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[50][respuesta]" value="no" id="rno" onclick="ocultarFormulario050()">
                                <label class="form-check-label" for="rno">NO</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="card050" style="display: none;">
                    <div class="row">
                        <div class="card-body">    
                            <div class="row">
                                <div class="col-lg-4">
                                    {!! Form::label('', 'Hace cuanto') !!}
                                    <div class="form-group">
                                        <div class="input-group">
                                            {!! Form::text('periodotiempolaboral50', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '3', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']) !!}
                                            <div class="input-group-append">
                                                {!! Form::select('periodo_tipo50', ['' => '', 'DIAS' => 'DIAS', 'SEMANAS' => 'SEMANAS', 'MESES' => 'MESES', 'AÑOS' => 'AÑOS'], null, ['class' => 'form-control']) !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                    function limpiarCampos050() {
                        document.getElementById('card050').querySelectorAll('input[type="text"]').forEach(function(input) {
                            input.value = '';
                        });
                        document.getElementById('card050').querySelectorAll('select').forEach(function(select) {
                            select.value = '';
                        });
                    }
                    function mostrarFormulario050() {
                        document.getElementById('card050').style.display = 'block';
                    }
                    function ocultarFormulario050() {
                        document.getElementById('card050').style.display = 'none';
                        limpiarCampos050();
                    }
                </script>
            </div>
            <div class="63">
                <div class="row odd-row">
                    <div class="col-lg-10">
                        <p>Artritis</p>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[50][respuesta]" value="si" id="rsi" onclick="mostrarFormulario050()">
                                <label class="form-check-label" for="rsi">SI</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[50][respuesta]" value="no" id="rno" onclick="ocultarFormulario050()">
                                <label class="form-check-label" for="rno">NO</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="card050" style="display: none;">
                    <div class="row odd-row">
                        <div class="card-body">    
                            <div class="row">
                                <div class="col-lg-4">
                                    {!! Form::label('', 'Hace cuanto') !!}
                                    <div class="form-group">
                                        <div class="input-group">
                                            {!! Form::text('periodotiempolaboral50', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '3', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']) !!}
                                            <div class="input-group-append">
                                                {!! Form::select('periodo_tipo50', ['' => '', 'DIAS' => 'DIAS', 'SEMANAS' => 'SEMANAS', 'MESES' => 'MESES', 'AÑOS' => 'AÑOS'], null, ['class' => 'form-control']) !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                    function limpiarCampos050() {
                        document.getElementById('card050').querySelectorAll('input[type="text"]').forEach(function(input) {
                            input.value = '';
                        });
                        document.getElementById('card050').querySelectorAll('select').forEach(function(select) {
                            select.value = '';
                        });
                    }
                    function mostrarFormulario050() {
                        document.getElementById('card050').style.display = 'block';
                    }
                    function ocultarFormulario050() {
                        document.getElementById('card050').style.display = 'none';
                        limpiarCampos050();
                    }
                </script>
            </div>
            <div class="64">
                <div class="row">
                    <div class="col-lg-10">
                        <p>Lumbalgia cronica</p>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[50][respuesta]" value="si" id="rsi" onclick="mostrarFormulario050()">
                                <label class="form-check-label" for="rsi">SI</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[50][respuesta]" value="no" id="rno" onclick="ocultarFormulario050()">
                                <label class="form-check-label" for="rno">NO</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="card050" style="display: none;">
                    <div class="row">
                        <div class="card-body">    
                            <div class="row">
                                <div class="col-lg-4">
                                    {!! Form::label('', 'Hace cuanto') !!}
                                    <div class="form-group">
                                        <div class="input-group">
                                            {!! Form::text('periodotiempolaboral50', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '3', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']) !!}
                                            <div class="input-group-append">
                                                {!! Form::select('periodo_tipo50', ['' => '', 'DIAS' => 'DIAS', 'SEMANAS' => 'SEMANAS', 'MESES' => 'MESES', 'AÑOS' => 'AÑOS'], null, ['class' => 'form-control']) !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                    function limpiarCampos050() {
                        document.getElementById('card050').querySelectorAll('input[type="text"]').forEach(function(input) {
                            input.value = '';
                        });
                        document.getElementById('card050').querySelectorAll('select').forEach(function(select) {
                            select.value = '';
                        });
                    }
                    function mostrarFormulario050() {
                        document.getElementById('card050').style.display = 'block';
                    }
                    function ocultarFormulario050() {
                        document.getElementById('card050').style.display = 'none';
                        limpiarCampos050();
                    }
                </script>
            </div>
            <div class="65">
                <div class="row odd-row">
                    <div class="col-lg-10">
                        <p>Silicosis</p>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[50][respuesta]" value="si" id="rsi" onclick="mostrarFormulario050()">
                                <label class="form-check-label" for="rsi">SI</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[50][respuesta]" value="no" id="rno" onclick="ocultarFormulario050()">
                                <label class="form-check-label" for="rno">NO</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="card050" style="display: none;">
                    <div class="row odd-row">
                        <div class="card-body">    
                            <div class="row">
                                <div class="col-lg-4">
                                    {!! Form::label('', 'Hace cuanto') !!}
                                    <div class="form-group">
                                        <div class="input-group">
                                            {!! Form::text('periodotiempolaboral50', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '3', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']) !!}
                                            <div class="input-group-append">
                                                {!! Form::select('periodo_tipo50', ['' => '', 'DIAS' => 'DIAS', 'SEMANAS' => 'SEMANAS', 'MESES' => 'MESES', 'AÑOS' => 'AÑOS'], null, ['class' => 'form-control']) !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                    function limpiarCampos050() {
                        document.getElementById('card050').querySelectorAll('input[type="text"]').forEach(function(input) {
                            input.value = '';
                        });
                        document.getElementById('card050').querySelectorAll('select').forEach(function(select) {
                            select.value = '';
                        });
                    }
                    function mostrarFormulario050() {
                        document.getElementById('card050').style.display = 'block';
                    }
                    function ocultarFormulario050() {
                        document.getElementById('card050').style.display = 'none';
                        limpiarCampos050();
                    }
                </script>
            </div>
            <div class="66">
                <div class="row">
                    <div class="col-lg-10">
                        <p>Convulsiones</p>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[50][respuesta]" value="si" id="rsi" onclick="mostrarFormulario050()">
                                <label class="form-check-label" for="rsi">SI</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[50][respuesta]" value="no" id="rno" onclick="ocultarFormulario050()">
                                <label class="form-check-label" for="rno">NO</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="card050" style="display: none;">
                    <div class="row">
                        <div class="card-body">    
                            <div class="row">
                                <div class="col-lg-4">
                                    {!! Form::label('', 'Hace cuanto') !!}
                                    <div class="form-group">
                                        <div class="input-group">
                                            {!! Form::text('periodotiempolaboral50', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '3', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']) !!}
                                            <div class="input-group-append">
                                                {!! Form::select('periodo_tipo50', ['' => '', 'DIAS' => 'DIAS', 'SEMANAS' => 'SEMANAS', 'MESES' => 'MESES', 'AÑOS' => 'AÑOS'], null, ['class' => 'form-control']) !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                    function limpiarCampos050() {
                        document.getElementById('card050').querySelectorAll('input[type="text"]').forEach(function(input) {
                            input.value = '';
                        });
                        document.getElementById('card050').querySelectorAll('select').forEach(function(select) {
                            select.value = '';
                        });
                    }
                    function mostrarFormulario050() {
                        document.getElementById('card050').style.display = 'block';
                    }
                    function ocultarFormulario050() {
                        document.getElementById('card050').style.display = 'none';
                        limpiarCampos050();
                    }
                </script>
            </div>
            <div class="67">
                <div class="row odd-row">
                    <div class="col-lg-10">
                        <p>Varices</p>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[50][respuesta]" value="si" id="rsi" onclick="mostrarFormulario050()">
                                <label class="form-check-label" for="rsi">SI</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[50][respuesta]" value="no" id="rno" onclick="ocultarFormulario050()">
                                <label class="form-check-label" for="rno">NO</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="card050" style="display: none;">
                    <div class="row odd-row">
                        <div class="card-body">    
                            <div class="row">
                                <div class="col-lg-4">
                                    {!! Form::label('', 'Hace cuanto') !!}
                                    <div class="form-group">
                                        <div class="input-group">
                                            {!! Form::text('periodotiempolaboral50', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '3', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']) !!}
                                            <div class="input-group-append">
                                                {!! Form::select('periodo_tipo50', ['' => '', 'DIAS' => 'DIAS', 'SEMANAS' => 'SEMANAS', 'MESES' => 'MESES', 'AÑOS' => 'AÑOS'], null, ['class' => 'form-control']) !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                    function limpiarCampos050() {
                        document.getElementById('card050').querySelectorAll('input[type="text"]').forEach(function(input) {
                            input.value = '';
                        });
                        document.getElementById('card050').querySelectorAll('select').forEach(function(select) {
                            select.value = '';
                        });
                    }
                    function mostrarFormulario050() {
                        document.getElementById('card050').style.display = 'block';
                    }
                    function ocultarFormulario050() {
                        document.getElementById('card050').style.display = 'none';
                        limpiarCampos050();
                    }
                </script>
            </div>
            <div class="68">
                <div class="row">
                    <div class="col-lg-10">
                        <p>Bronquitis</p>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[50][respuesta]" value="si" id="rsi" onclick="mostrarFormulario050()">
                                <label class="form-check-label" for="rsi">SI</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[50][respuesta]" value="no" id="rno" onclick="ocultarFormulario050()">
                                <label class="form-check-label" for="rno">NO</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="card050" style="display: none;">
                    <div class="row">
                        <div class="card-body">    
                            <div class="row">
                                <div class="col-lg-4">
                                    {!! Form::label('', 'Hace cuanto') !!}
                                    <div class="form-group">
                                        <div class="input-group">
                                            {!! Form::text('periodotiempolaboral50', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '3', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']) !!}
                                            <div class="input-group-append">
                                                {!! Form::select('periodo_tipo50', ['' => '', 'DIAS' => 'DIAS', 'SEMANAS' => 'SEMANAS', 'MESES' => 'MESES', 'AÑOS' => 'AÑOS'], null, ['class' => 'form-control']) !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                    function limpiarCampos050() {
                        document.getElementById('card050').querySelectorAll('input[type="text"]').forEach(function(input) {
                            input.value = '';
                        });
                        document.getElementById('card050').querySelectorAll('select').forEach(function(select) {
                            select.value = '';
                        });
                    }
                    function mostrarFormulario050() {
                        document.getElementById('card050').style.display = 'block';
                    }
                    function ocultarFormulario050() {
                        document.getElementById('card050').style.display = 'none';
                        limpiarCampos050();
                    }
                </script>
            </div>
            <div class="69">
                <div class="row odd-row">
                    <div class="col-lg-10">
                        <p>Asma</p>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[50][respuesta]" value="si" id="rsi" onclick="mostrarFormulario050()">
                                <label class="form-check-label" for="rsi">SI</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[50][respuesta]" value="no" id="rno" onclick="ocultarFormulario050()">
                                <label class="form-check-label" for="rno">NO</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="card050" style="display: none;">
                    <div class="row odd-row">
                        <div class="card-body">    
                            <div class="row">
                                <div class="col-lg-4">
                                    {!! Form::label('', 'Hace cuanto') !!}
                                    <div class="form-group">
                                        <div class="input-group">
                                            {!! Form::text('periodotiempolaboral50', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '3', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']) !!}
                                            <div class="input-group-append">
                                                {!! Form::select('periodo_tipo50', ['' => '', 'DIAS' => 'DIAS', 'SEMANAS' => 'SEMANAS', 'MESES' => 'MESES', 'AÑOS' => 'AÑOS'], null, ['class' => 'form-control']) !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                    function limpiarCampos050() {
                        document.getElementById('card050').querySelectorAll('input[type="text"]').forEach(function(input) {
                            input.value = '';
                        });
                        document.getElementById('card050').querySelectorAll('select').forEach(function(select) {
                            select.value = '';
                        });
                    }
                    function mostrarFormulario050() {
                        document.getElementById('card050').style.display = 'block';
                    }
                    function ocultarFormulario050() {
                        document.getElementById('card050').style.display = 'none';
                        limpiarCampos050();
                    }
                </script>
            </div>
            <div class="70">
                <div class="row">
                    <div class="col-lg-10">
                        <p>TBC</p>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[50][respuesta]" value="si" id="rsi" onclick="mostrarFormulario050()">
                                <label class="form-check-label" for="rsi">SI</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[50][respuesta]" value="no" id="rno" onclick="ocultarFormulario050()">
                                <label class="form-check-label" for="rno">NO</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="card050" style="display: none;">
                    <div class="row">
                        <div class="card-body">    
                            <div class="row">
                                <div class="col-lg-4">
                                    {!! Form::label('', 'Hace cuanto') !!}
                                    <div class="form-group">
                                        <div class="input-group">
                                            {!! Form::text('periodotiempolaboral50', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '3', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']) !!}
                                            <div class="input-group-append">
                                                {!! Form::select('periodo_tipo50', ['' => '', 'DIAS' => 'DIAS', 'SEMANAS' => 'SEMANAS', 'MESES' => 'MESES', 'AÑOS' => 'AÑOS'], null, ['class' => 'form-control']) !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                    function limpiarCampos050() {
                        document.getElementById('card050').querySelectorAll('input[type="text"]').forEach(function(input) {
                            input.value = '';
                        });
                        document.getElementById('card050').querySelectorAll('select').forEach(function(select) {
                            select.value = '';
                        });
                    }
                    function mostrarFormulario050() {
                        document.getElementById('card050').style.display = 'block';
                    }
                    function ocultarFormulario050() {
                        document.getElementById('card050').style.display = 'none';
                        limpiarCampos050();
                    }
                </script>
            </div>
            <div class="71">
                <div class="row odd-row">
                    <div class="col-lg-10">
                        <p>Enf. acido-peptica</p>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[50][respuesta]" value="si" id="rsi" onclick="mostrarFormulario050()">
                                <label class="form-check-label" for="rsi">SI</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[50][respuesta]" value="no" id="rno" onclick="ocultarFormulario050()">
                                <label class="form-check-label" for="rno">NO</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="card050" style="display: none;">
                    <div class="row odd-row">
                        <div class="card-body">    
                            <div class="row">
                                <div class="col-lg-4">
                                    {!! Form::label('', 'Hace cuanto') !!}
                                    <div class="form-group">
                                        <div class="input-group">
                                            {!! Form::text('periodotiempolaboral50', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '3', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']) !!}
                                            <div class="input-group-append">
                                                {!! Form::select('periodo_tipo50', ['' => '', 'DIAS' => 'DIAS', 'SEMANAS' => 'SEMANAS', 'MESES' => 'MESES', 'AÑOS' => 'AÑOS'], null, ['class' => 'form-control']) !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                    function limpiarCampos050() {
                        document.getElementById('card050').querySelectorAll('input[type="text"]').forEach(function(input) {
                            input.value = '';
                        });
                        document.getElementById('card050').querySelectorAll('select').forEach(function(select) {
                            select.value = '';
                        });
                    }
                    function mostrarFormulario050() {
                        document.getElementById('card050').style.display = 'block';
                    }
                    function ocultarFormulario050() {
                        document.getElementById('card050').style.display = 'none';
                        limpiarCampos050();
                    }
                </script>
            </div>
            <div class="72">
                <div class="row">
                    <div class="col-lg-10">
                        <p>Colon irritable</p>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[50][respuesta]" value="si" id="rsi" onclick="mostrarFormulario050()">
                                <label class="form-check-label" for="rsi">SI</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[50][respuesta]" value="no" id="rno" onclick="ocultarFormulario050()">
                                <label class="form-check-label" for="rno">NO</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="card050" style="display: none;">
                    <div class="row">
                        <div class="card-body">    
                            <div class="row">
                                <div class="col-lg-4">
                                    {!! Form::label('', 'Hace cuanto') !!}
                                    <div class="form-group">
                                        <div class="input-group">
                                            {!! Form::text('periodotiempolaboral50', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '3', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']) !!}
                                            <div class="input-group-append">
                                                {!! Form::select('periodo_tipo50', ['' => '', 'DIAS' => 'DIAS', 'SEMANAS' => 'SEMANAS', 'MESES' => 'MESES', 'AÑOS' => 'AÑOS'], null, ['class' => 'form-control']) !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                    function limpiarCampos050() {
                        document.getElementById('card050').querySelectorAll('input[type="text"]').forEach(function(input) {
                            input.value = '';
                        });
                        document.getElementById('card050').querySelectorAll('select').forEach(function(select) {
                            select.value = '';
                        });
                    }
                    function mostrarFormulario050() {
                        document.getElementById('card050').style.display = 'block';
                    }
                    function ocultarFormulario050() {
                        document.getElementById('card050').style.display = 'none';
                        limpiarCampos050();
                    }
                </script>
            </div>
            <div class="73">
                <div class="row odd-row">
                    <div class="col-lg-10">
                        <p>Colelitiasis</p>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[50][respuesta]" value="si" id="rsi" onclick="mostrarFormulario050()">
                                <label class="form-check-label" for="rsi">SI</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[50][respuesta]" value="no" id="rno" onclick="ocultarFormulario050()">
                                <label class="form-check-label" for="rno">NO</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="card050" style="display: none;">
                    <div class="row odd-row">
                        <div class="card-body">    
                            <div class="row">
                                <div class="col-lg-4">
                                    {!! Form::label('', 'Hace cuanto') !!}
                                    <div class="form-group">
                                        <div class="input-group">
                                            {!! Form::text('periodotiempolaboral50', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '3', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']) !!}
                                            <div class="input-group-append">
                                                {!! Form::select('periodo_tipo50', ['' => '', 'DIAS' => 'DIAS', 'SEMANAS' => 'SEMANAS', 'MESES' => 'MESES', 'AÑOS' => 'AÑOS'], null, ['class' => 'form-control']) !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                    function limpiarCampos050() {
                        document.getElementById('card050').querySelectorAll('input[type="text"]').forEach(function(input) {
                            input.value = '';
                        });
                        document.getElementById('card050').querySelectorAll('select').forEach(function(select) {
                            select.value = '';
                        });
                    }
                    function mostrarFormulario050() {
                        document.getElementById('card050').style.display = 'block';
                    }
                    function ocultarFormulario050() {
                        document.getElementById('card050').style.display = 'none';
                        limpiarCampos050();
                    }
                </script>
            </div>
            <div class="74">
                <div class="row">
                    <div class="col-lg-10">
                        <p>Urolotiasis</p>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[50][respuesta]" value="si" id="rsi" onclick="mostrarFormulario050()">
                                <label class="form-check-label" for="rsi">SI</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[50][respuesta]" value="no" id="rno" onclick="ocultarFormulario050()">
                                <label class="form-check-label" for="rno">NO</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="card050" style="display: none;">
                    <div class="row">
                        <div class="card-body">    
                            <div class="row">
                                <div class="col-lg-4">
                                    {!! Form::label('', 'Hace cuanto') !!}
                                    <div class="form-group">
                                        <div class="input-group">
                                            {!! Form::text('periodotiempolaboral50', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '3', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']) !!}
                                            <div class="input-group-append">
                                                {!! Form::select('periodo_tipo50', ['' => '', 'DIAS' => 'DIAS', 'SEMANAS' => 'SEMANAS', 'MESES' => 'MESES', 'AÑOS' => 'AÑOS'], null, ['class' => 'form-control']) !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                    function limpiarCampos050() {
                        document.getElementById('card050').querySelectorAll('input[type="text"]').forEach(function(input) {
                            input.value = '';
                        });
                        document.getElementById('card050').querySelectorAll('select').forEach(function(select) {
                            select.value = '';
                        });
                    }
                    function mostrarFormulario050() {
                        document.getElementById('card050').style.display = 'block';
                    }
                    function ocultarFormulario050() {
                        document.getElementById('card050').style.display = 'none';
                        limpiarCampos050();
                    }
                </script>
            </div>
            <div class="75">
                <div class="row odd-row">
                    <div class="col-lg-10">
                        <p>Urolotiasis</p>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[50][respuesta]" value="si" id="rsi" onclick="mostrarFormulario050()">
                                <label class="form-check-label" for="rsi">SI</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[50][respuesta]" value="no" id="rno" onclick="ocultarFormulario050()">
                                <label class="form-check-label" for="rno">NO</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="card050" style="display: none;">
                    <div class="row odd-row">
                        <div class="card-body">    
                            <div class="row">
                                <div class="col-lg-4">
                                    {!! Form::label('', 'Hace cuanto') !!}
                                    <div class="form-group">
                                        <div class="input-group">
                                            {!! Form::text('periodotiempolaboral50', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '3', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']) !!}
                                            <div class="input-group-append">
                                                {!! Form::select('periodo_tipo50', ['' => '', 'DIAS' => 'DIAS', 'SEMANAS' => 'SEMANAS', 'MESES' => 'MESES', 'AÑOS' => 'AÑOS'], null, ['class' => 'form-control']) !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                    function limpiarCampos050() {
                        document.getElementById('card050').querySelectorAll('input[type="text"]').forEach(function(input) {
                            input.value = '';
                        });
                        document.getElementById('card050').querySelectorAll('select').forEach(function(select) {
                            select.value = '';
                        });
                    }
                    function mostrarFormulario050() {
                        document.getElementById('card050').style.display = 'block';
                    }
                    function ocultarFormulario050() {
                        document.getElementById('card050').style.display = 'none';
                        limpiarCampos050();
                    }
                </script>
            </div>
            <div class="76">
                <div class="row">
                    <div class="col-lg-10">
                        <p>Infeccion urinaria</p>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[50][respuesta]" value="si" id="rsi" onclick="mostrarFormulario050()">
                                <label class="form-check-label" for="rsi">SI</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[50][respuesta]" value="no" id="rno" onclick="ocultarFormulario050()">
                                <label class="form-check-label" for="rno">NO</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="card050" style="display: none;">
                    <div class="row">
                        <div class="card-body">    
                            <div class="row">
                                <div class="col-lg-4">
                                    {!! Form::label('', 'Hace cuanto') !!}
                                    <div class="form-group">
                                        <div class="input-group">
                                            {!! Form::text('periodotiempolaboral50', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '3', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']) !!}
                                            <div class="input-group-append">
                                                {!! Form::select('periodo_tipo50', ['' => '', 'DIAS' => 'DIAS', 'SEMANAS' => 'SEMANAS', 'MESES' => 'MESES', 'AÑOS' => 'AÑOS'], null, ['class' => 'form-control']) !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                    function limpiarCampos050() {
                        document.getElementById('card050').querySelectorAll('input[type="text"]').forEach(function(input) {
                            input.value = '';
                        });
                        document.getElementById('card050').querySelectorAll('select').forEach(function(select) {
                            select.value = '';
                        });
                    }
                    function mostrarFormulario050() {
                        document.getElementById('card050').style.display = 'block';
                    }
                    function ocultarFormulario050() {
                        document.getElementById('card050').style.display = 'none';
                        limpiarCampos050();
                    }
                </script>
            </div>
            <div class="77">
                <div class="row odd-row">
                    <div class="col-lg-10">
                        <p>Venereas</p>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[50][respuesta]" value="si" id="rsi" onclick="mostrarFormulario050()">
                                <label class="form-check-label" for="rsi">SI</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[50][respuesta]" value="no" id="rno" onclick="ocultarFormulario050()">
                                <label class="form-check-label" for="rno">NO</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="card050" style="display: none;">
                    <div class="row odd-row">
                        <div class="card-body">    
                            <div class="row">
                                <div class="col-lg-4">
                                    {!! Form::label('', 'Hace cuanto') !!}
                                    <div class="form-group">
                                        <div class="input-group">
                                            {!! Form::text('periodotiempolaboral50', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '3', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']) !!}
                                            <div class="input-group-append">
                                                {!! Form::select('periodo_tipo50', ['' => '', 'DIAS' => 'DIAS', 'SEMANAS' => 'SEMANAS', 'MESES' => 'MESES', 'AÑOS' => 'AÑOS'], null, ['class' => 'form-control']) !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                    function limpiarCampos050() {
                        document.getElementById('card050').querySelectorAll('input[type="text"]').forEach(function(input) {
                            input.value = '';
                        });
                        document.getElementById('card050').querySelectorAll('select').forEach(function(select) {
                            select.value = '';
                        });
                    }
                    function mostrarFormulario050() {
                        document.getElementById('card050').style.display = 'block';
                    }
                    function ocultarFormulario050() {
                        document.getElementById('card050').style.display = 'none';
                        limpiarCampos050();
                    }
                </script>
            </div>
            <div class="78">
                <div class="row">
                    <div class="col-lg-10">
                        <p>Colesterol alto</p>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[50][respuesta]" value="si" id="rsi" onclick="mostrarFormulario050()">
                                <label class="form-check-label" for="rsi">SI</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[50][respuesta]" value="no" id="rno" onclick="ocultarFormulario050()">
                                <label class="form-check-label" for="rno">NO</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="card050" style="display: none;">
                    <div class="row">
                        <div class="card-body">    
                            <div class="row">
                                <div class="col-lg-4">
                                    {!! Form::label('', 'Hace cuanto') !!}
                                    <div class="form-group">
                                        <div class="input-group">
                                            {!! Form::text('periodotiempolaboral50', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '3', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']) !!}
                                            <div class="input-group-append">
                                                {!! Form::select('periodo_tipo50', ['' => '', 'DIAS' => 'DIAS', 'SEMANAS' => 'SEMANAS', 'MESES' => 'MESES', 'AÑOS' => 'AÑOS'], null, ['class' => 'form-control']) !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                    function limpiarCampos050() {
                        document.getElementById('card050').querySelectorAll('input[type="text"]').forEach(function(input) {
                            input.value = '';
                        });
                        document.getElementById('card050').querySelectorAll('select').forEach(function(select) {
                            select.value = '';
                        });
                    }
                    function mostrarFormulario050() {
                        document.getElementById('card050').style.display = 'block';
                    }
                    function ocultarFormulario050() {
                        document.getElementById('card050').style.display = 'none';
                        limpiarCampos050();
                    }
                </script>
            </div>
            <div class="79">
                <div class="row odd-row">
                    <div class="col-lg-10">
                        <p>Cancer</p>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[50][respuesta]" value="si" id="rsi" onclick="mostrarFormulario050()">
                                <label class="form-check-label" for="rsi">SI</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[50][respuesta]" value="no" id="rno" onclick="ocultarFormulario050()">
                                <label class="form-check-label" for="rno">NO</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="card050" style="display: none;">
                    <div class="row odd-row">
                        <div class="card-body">    
                            <div class="row">
                                <div class="col-lg-4">
                                    {!! Form::label('', 'Hace cuanto') !!}
                                    <div class="form-group">
                                        <div class="input-group">
                                            {!! Form::text('periodotiempolaboral50', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '3', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']) !!}
                                            <div class="input-group-append">
                                                {!! Form::select('periodo_tipo50', ['' => '', 'DIAS' => 'DIAS', 'SEMANAS' => 'SEMANAS', 'MESES' => 'MESES', 'AÑOS' => 'AÑOS'], null, ['class' => 'form-control']) !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                    function limpiarCampos050() {
                        document.getElementById('card050').querySelectorAll('input[type="text"]').forEach(function(input) {
                            input.value = '';
                        });
                        document.getElementById('card050').querySelectorAll('select').forEach(function(select) {
                            select.value = '';
                        });
                    }
                    function mostrarFormulario050() {
                        document.getElementById('card050').style.display = 'block';
                    }
                    function ocultarFormulario050() {
                        document.getElementById('card050').style.display = 'none';
                        limpiarCampos050();
                    }
                </script>
            </div>
            <div class="80">
                <div class="row">
                    <div class="col-lg-10">
                        <p>Varicocele</p>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[50][respuesta]" value="si" id="rsi" onclick="mostrarFormulario050()">
                                <label class="form-check-label" for="rsi">SI</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[50][respuesta]" value="no" id="rno" onclick="ocultarFormulario050()">
                                <label class="form-check-label" for="rno">NO</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="card050" style="display: none;">
                    <div class="row">
                        <div class="card-body">    
                            <div class="row">
                                <div class="col-lg-4">
                                    {!! Form::label('', 'Hace cuanto') !!}
                                    <div class="form-group">
                                        <div class="input-group">
                                            {!! Form::text('periodotiempolaboral50', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '3', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']) !!}
                                            <div class="input-group-append">
                                                {!! Form::select('periodo_tipo50', ['' => '', 'DIAS' => 'DIAS', 'SEMANAS' => 'SEMANAS', 'MESES' => 'MESES', 'AÑOS' => 'AÑOS'], null, ['class' => 'form-control']) !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                    function limpiarCampos050() {
                        document.getElementById('card050').querySelectorAll('input[type="text"]').forEach(function(input) {
                            input.value = '';
                        });
                        document.getElementById('card050').querySelectorAll('select').forEach(function(select) {
                            select.value = '';
                        });
                    }
                    function mostrarFormulario050() {
                        document.getElementById('card050').style.display = 'block';
                    }
                    function ocultarFormulario050() {
                        document.getElementById('card050').style.display = 'none';
                        limpiarCampos050();
                    }
                </script>
            </div>
            <div class="81">
                <div class="row odd-row">
                    <div class="col-lg-10">
                        <p>Dermatitis</p>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[50][respuesta]" value="si" id="rsi" onclick="mostrarFormulario050()">
                                <label class="form-check-label" for="rsi">SI</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[50][respuesta]" value="no" id="rno" onclick="ocultarFormulario050()">
                                <label class="form-check-label" for="rno">NO</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="card050" style="display: none;">
                    <div class="row odd-row">
                        <div class="card-body">    
                            <div class="row">
                                <div class="col-lg-4">
                                    {!! Form::label('', 'Hace cuanto') !!}
                                    <div class="form-group">
                                        <div class="input-group">
                                            {!! Form::text('periodotiempolaboral50', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '3', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']) !!}
                                            <div class="input-group-append">
                                                {!! Form::select('periodo_tipo50', ['' => '', 'DIAS' => 'DIAS', 'SEMANAS' => 'SEMANAS', 'MESES' => 'MESES', 'AÑOS' => 'AÑOS'], null, ['class' => 'form-control']) !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                    function limpiarCampos050() {
                        document.getElementById('card050').querySelectorAll('input[type="text"]').forEach(function(input) {
                            input.value = '';
                        });
                        document.getElementById('card050').querySelectorAll('select').forEach(function(select) {
                            select.value = '';
                        });
                    }
                    function mostrarFormulario050() {
                        document.getElementById('card050').style.display = 'block';
                    }
                    function ocultarFormulario050() {
                        document.getElementById('card050').style.display = 'none';
                        limpiarCampos050();
                    }
                </script>
            </div>
            <div class="82">
                <div class="row">
                    <div class="col-lg-10">
                        <p>Tunel de carpo</p>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[50][respuesta]" value="si" id="rsi" onclick="mostrarFormulario050()">
                                <label class="form-check-label" for="rsi">SI</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[50][respuesta]" value="no" id="rno" onclick="ocultarFormulario050()">
                                <label class="form-check-label" for="rno">NO</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="card050" style="display: none;">
                    <div class="row">
                        <div class="card-body">    
                            <div class="row">
                                <div class="col-lg-4">
                                    {!! Form::label('', 'Hace cuanto') !!}
                                    <div class="form-group">
                                        <div class="input-group">
                                            {!! Form::text('periodotiempolaboral50', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '3', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']) !!}
                                            <div class="input-group-append">
                                                {!! Form::select('periodo_tipo50', ['' => '', 'DIAS' => 'DIAS', 'SEMANAS' => 'SEMANAS', 'MESES' => 'MESES', 'AÑOS' => 'AÑOS'], null, ['class' => 'form-control']) !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                    function limpiarCampos050() {
                        document.getElementById('card050').querySelectorAll('input[type="text"]').forEach(function(input) {
                            input.value = '';
                        });
                        document.getElementById('card050').querySelectorAll('select').forEach(function(select) {
                            select.value = '';
                        });
                    }
                    function mostrarFormulario050() {
                        document.getElementById('card050').style.display = 'block';
                    }
                    function ocultarFormulario050() {
                        document.getElementById('card050').style.display = 'none';
                        limpiarCampos050();
                    }
                </script>
            </div>
            <div class="83">
                <div class="row odd-row">
                    <div class="col-lg-10">
                        <p>Varices pierna</p>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[50][respuesta]" value="si" id="rsi" onclick="mostrarFormulario050()">
                                <label class="form-check-label" for="rsi">SI</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[50][respuesta]" value="no" id="rno" onclick="ocultarFormulario050()">
                                <label class="form-check-label" for="rno">NO</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="card050" style="display: none;">
                    <div class="row odd-row">
                        <div class="card-body">    
                            <div class="row">
                                <div class="col-lg-4">
                                    {!! Form::label('', 'Hace cuanto') !!}
                                    <div class="form-group">
                                        <div class="input-group">
                                            {!! Form::text('periodotiempolaboral50', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '3', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']) !!}
                                            <div class="input-group-append">
                                                {!! Form::select('periodo_tipo50', ['' => '', 'DIAS' => 'DIAS', 'SEMANAS' => 'SEMANAS', 'MESES' => 'MESES', 'AÑOS' => 'AÑOS'], null, ['class' => 'form-control']) !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                    function limpiarCampos050() {
                        document.getElementById('card050').querySelectorAll('input[type="text"]').forEach(function(input) {
                            input.value = '';
                        });
                        document.getElementById('card050').querySelectorAll('select').forEach(function(select) {
                            select.value = '';
                        });
                    }
                    function mostrarFormulario050() {
                        document.getElementById('card050').style.display = 'block';
                    }
                    function ocultarFormulario050() {
                        document.getElementById('card050').style.display = 'none';
                        limpiarCampos050();
                    }
                </script>
            </div>
            <div class="84">
                <div class="row">
                    <div class="col-lg-10">
                        <p>Trombosis</p>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[50][respuesta]" value="si" id="rsi" onclick="mostrarFormulario050()">
                                <label class="form-check-label" for="rsi">SI</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[50][respuesta]" value="no" id="rno" onclick="ocultarFormulario050()">
                                <label class="form-check-label" for="rno">NO</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="card050" style="display: none;">
                    <div class="row">
                        <div class="card-body">    
                            <div class="row">
                                <div class="col-lg-4">
                                    {!! Form::label('', 'Hace cuanto') !!}
                                    <div class="form-group">
                                        <div class="input-group">
                                            {!! Form::text('periodotiempolaboral50', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '3', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']) !!}
                                            <div class="input-group-append">
                                                {!! Form::select('periodo_tipo50', ['' => '', 'DIAS' => 'DIAS', 'SEMANAS' => 'SEMANAS', 'MESES' => 'MESES', 'AÑOS' => 'AÑOS'], null, ['class' => 'form-control']) !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                    function limpiarCampos050() {
                        document.getElementById('card050').querySelectorAll('input[type="text"]').forEach(function(input) {
                            input.value = '';
                        });
                        document.getElementById('card050').querySelectorAll('select').forEach(function(select) {
                            select.value = '';
                        });
                    }
                    function mostrarFormulario050() {
                        document.getElementById('card050').style.display = 'block';
                    }
                    function ocultarFormulario050() {
                        document.getElementById('card050').style.display = 'none';
                        limpiarCampos050();
                    }
                </script>
            </div>
            <div class="85">
                <div class="row odd-row">
                    <div class="col-lg-10">
                        <p>Gota</p>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[50][respuesta]" value="si" id="rsi" onclick="mostrarFormulario050()">
                                <label class="form-check-label" for="rsi">SI</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[50][respuesta]" value="no" id="rno" onclick="ocultarFormulario050()">
                                <label class="form-check-label" for="rno">NO</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="card050" style="display: none;">
                    <div class="row odd-row">
                        <div class="card-body">    
                            <div class="row">
                                <div class="col-lg-4">
                                    {!! Form::label('', 'Hace cuanto') !!}
                                    <div class="form-group">
                                        <div class="input-group">
                                            {!! Form::text('periodotiempolaboral50', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '3', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']) !!}
                                            <div class="input-group-append">
                                                {!! Form::select('periodo_tipo50', ['' => '', 'DIAS' => 'DIAS', 'SEMANAS' => 'SEMANAS', 'MESES' => 'MESES', 'AÑOS' => 'AÑOS'], null, ['class' => 'form-control']) !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                    function limpiarCampos050() {
                        document.getElementById('card050').querySelectorAll('input[type="text"]').forEach(function(input) {
                            input.value = '';
                        });
                        document.getElementById('card050').querySelectorAll('select').forEach(function(select) {
                            select.value = '';
                        });
                    }
                    function mostrarFormulario050() {
                        document.getElementById('card050').style.display = 'block';
                    }
                    function ocultarFormulario050() {
                        document.getElementById('card050').style.display = 'none';
                        limpiarCampos050();
                    }
                </script>
            </div>
            <div class="86">
                <div class="row">
                    <div class="col-lg-10">
                        <p>E.P.O.C.</p>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[50][respuesta]" value="si" id="rsi" onclick="mostrarFormulario050()">
                                <label class="form-check-label" for="rsi">SI</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[50][respuesta]" value="no" id="rno" onclick="ocultarFormulario050()">
                                <label class="form-check-label" for="rno">NO</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="card050" style="display: none;">
                    <div class="row">
                        <div class="card-body">    
                            <div class="row">
                                <div class="col-lg-4">
                                    {!! Form::label('', 'Hace cuanto') !!}
                                    <div class="form-group">
                                        <div class="input-group">
                                            {!! Form::text('periodotiempolaboral50', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '3', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']) !!}
                                            <div class="input-group-append">
                                                {!! Form::select('periodo_tipo50', ['' => '', 'DIAS' => 'DIAS', 'SEMANAS' => 'SEMANAS', 'MESES' => 'MESES', 'AÑOS' => 'AÑOS'], null, ['class' => 'form-control']) !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                    function limpiarCampos050() {
                        document.getElementById('card050').querySelectorAll('input[type="text"]').forEach(function(input) {
                            input.value = '';
                        });
                        document.getElementById('card050').querySelectorAll('select').forEach(function(select) {
                            select.value = '';
                        });
                    }
                    function mostrarFormulario050() {
                        document.getElementById('card050').style.display = 'block';
                    }
                    function ocultarFormulario050() {
                        document.getElementById('card050').style.display = 'none';
                        limpiarCampos050();
                    }
                </script>
            </div>

            <div class="row">
                <div class="col-lg-6">
                    <div class="form-group">
                        {!! Form::label('Fracturas:') !!}
                        {!! Form::text('fracturas', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '45']) !!}
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-group">
                        {!! Form::label('Alergias:') !!}
                        {!! Form::text('alergias', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '45']) !!}
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-6">
                    <div class="form-group">
                        {!! Form::label('Transfusiones:') !!}
                        {!! Form::text('transfusiones', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '45']) !!}
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-group">
                        {!! Form::label('Intoxicaciones:') !!}
                        {!! Form::text('intoxicaciones', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '45']) !!}
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-6">
                    <div class="form-group">
                        {!! Form::label('Enfermedades de transmision sexual:') !!}
                        {!! Form::text('enfermedadessexual', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '45']) !!}
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-group">
                        {!! Form::label('Alteraciones en vision:') !!}
                        {!! Form::text('alteracionvision', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '45']) !!}
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-6">
                    <div class="form-group">
                        {!! Form::label('Alteraciones en oido:') !!}
                        {!! Form::text('alteracionoido', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '45']) !!}
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-group">
                        {!! Form::label('Enfermedades del aparato digestivo:') !!}
                        {!! Form::text('enfermedaddigestivo', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '45']) !!}
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-6">
                    <div class="form-group">
                        {!! Form::label('Enfermedades del aparato urogenital:') !!}
                        {!! Form::text('enfermedadurogenital', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '45']) !!}
                    </div>
                </div>
            </div>


            <h4 style="text-align: center;" style="">ANTECEDENTES PERSONALES NO PATOLOGICOS</h4>
            <h4 style="text-align: center;" style="">HABITOS TOXICOS</h4>
            <div class="section">
                <p><strong><span style="font-size: larger;">CIGARILLOS</span></strong></p>
                <div class="row">
                    <div class="col-lg-3">
                        <label>Estado</label>
                        <div class="form-group">
                            <select class="form-control estadocigarrillos">
                                <option value="nofuma">No fuma</option>
                                <option value="exfumador">Ex fumador</option>
                                <option value="fumador">Fumador</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-2 anossuspensionfumador hidden">
                        <label>Tiempo de suspension</label>
                        <div class="form-group">
                            <div class="input-group">
                                {!! Form::text('periodotiempolaboral', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '2', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57', 'disabled']) !!}
                                <div class="input-group-append">
                                    {!! Form::select('periodo_tipo', ['' => '', 'DIAS' => 'DIAS', 'SEMANAS' => 'SEMANAS', 'MESES' => 'MESES', 'AÑOS' => 'AÑOS'], null, ['class' => 'form-control', 'disabled']) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 frecuenciafumador hidden">
                        <label>Frecuencia</label>
                        <div class="form-group">
                            <div class="input-group">
                                {!! Form::text('periodotiempolaboral3', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '2', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57', 'disabled']) !!}
                                <div class="input-group-append">
                                    {!! Form::select('periodo_tipo3', ['' => '', 'AL DIA' => 'AL DIA', 'A LA SEMANA' => 'A LA SEMANA', 'AL MES' => 'AL MES', 'AL AÑO' => 'AL AÑO'], null, ['class' => 'form-control', 'disabled']) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-2 anosfumador hidden">
                        <label>Tiempo de consumo</label>
                        <div class="form-group">
                            <div class="input-group">
                                {!! Form::text('periodotiempolaboral2', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '2', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57', 'disabled']) !!}
                                <div class="input-group-append">
                                    {!! Form::select('periodo_tipo2', ['' => '', 'DIAS' => 'DIAS', 'SEMANAS' => 'SEMANAS', 'MESES' => 'MESES', 'AÑOS' => 'AÑOS'], null, ['class' => 'form-control', 'disabled']) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-2 numerocigarrillos hidden">
                        <label>Nro. de cigarrillos / Día</label>
                        <div class="form-group">
                            <input type="text" class="form-control numerocigarrillos-input" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 3);" disabled>
                        </div>
                    </div>
                    
                </div>
                <script>
                    document.addEventListener("DOMContentLoaded", function() {
                            var selectElements = document.querySelectorAll('.estadocigarrillos');
                            selectElements.forEach(function(selectElement) {
                                selectElement.addEventListener("change", function() {
                                    var parentSection = this.closest('.section');
                                    var relatedElements = parentSection.querySelectorAll('.form-control');

                                    relatedElements.forEach(function(element) {
                                        if (element !== selectElement) {
                                            element.disabled = true;
                                            if (this.value === "nofuma") {
                                                element.value = "";
                                            }
                                        }
                                    });
                                    if (this.value === "nofuma") {
                                        parentSection.querySelector('.anossuspensionfumador .form-control').value = "";
                                        parentSection.querySelector('.anossuspensionfumador select').value = "";
                                        parentSection.querySelector('.frecuenciafumador .form-control').value = "";
                                        parentSection.querySelector('.frecuenciafumador select').value = "";
                                        parentSection.querySelector('.numerocigarrillos .form-control').value = "";
                                        parentSection.querySelector('.anosfumador .form-control').value = "";
                                        parentSection.querySelector('.anosfumador select').value = "";
                                    } else if (this.value === "exfumador") {
                                        parentSection.querySelector('.anossuspensionfumador .form-control').disabled = false;
                                        parentSection.querySelector('.anossuspensionfumador select').disabled = false;
                                        parentSection.querySelector('.frecuenciafumador .form-control').value = "";
                                        parentSection.querySelector('.frecuenciafumador select').value = "";
                                        parentSection.querySelector('.numerocigarrillos .form-control').value = "";
                                        parentSection.querySelector('.anosfumador .form-control').value = "";
                                        parentSection.querySelector('.anosfumador select').value = "";
                                    } else if (this.value === "fumador") {
                                        parentSection.querySelector('.frecuenciafumador .form-control').disabled = false;
                                        parentSection.querySelector('.frecuenciafumador select').disabled = false;
                                        parentSection.querySelector('.numerocigarrillos .form-control').disabled = false;
                                        parentSection.querySelector('.anosfumador .form-control').disabled = false;
                                        parentSection.querySelector('.anosfumador select').disabled = false;
                                        parentSection.querySelector('.anossuspensionfumador .form-control').value = "";
                                        parentSection.querySelector('.anossuspensionfumador select').value = "";
                                    }
                                });
                            });
                        });
                </script>
            </div>
            <div class="section">
                <p><strong><span style="font-size: larger;">ALCOHOL</span></strong></p>
                <div class="row">
                    <div class="col-lg-3">
                        <label>Estado</label>
                        <div class="form-group">
                            <select class="form-control estadoalcohol">
                                <option value="nobebe">No bebe</option>
                                <option value="exbebedor">Ex bebedor</option>
                                <option value="bebedor">Bebedor</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-2 anossuspensionalcohol hidden">
                        <label>Tiempo de suspension</label>
                        <div class="form-group">
                            <div class="form-group">
                                <div class="input-group">
                                    {!! Form::text('periodotiempolaboral4', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '2', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57', 'disabled']) !!}
                                    <div class="input-group-append">
                                        {!! Form::select('periodo_tipo4', ['' => '', 'DIAS' => 'DIAS', 'SEMANAS' => 'SEMANAS', 'MESES' => 'MESES', 'AÑOS' => 'AÑOS'], null, ['class' => 'form-control', 'disabled']) !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 frecuenciaalcohol hidden">
                        <label>Frecuencia</label>
                        <div class="form-group">
                            <div class="input-group">
                                {!! Form::text('periodotiempolaboral5', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '2', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57', 'disabled']) !!}
                                <div class="input-group-append">
                                    {!! Form::select('periodo_tipo5', ['' => '', 'AL DIA' => 'AL DIA', 'A LA SEMANA' => 'A LA SEMANA', 'AL MES' => 'AL MES', 'AL AÑO' => 'AL AÑO'], null, ['class' => 'form-control', 'disabled']) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-2 anosalcohol hidden">
                        <label>Tiempo de consumo</label>
                        <div class="form-group">
                            <div class="input-group">
                                {!! Form::text('periodotiempolaboral6', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '2', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57', 'disabled']) !!}
                                <div class="input-group-append">
                                    {!! Form::select('periodo_tipo6', ['' => '', 'DIAS' => 'DIAS', 'SEMANAS' => 'SEMANAS', 'MESES' => 'MESES', 'AÑOS' => 'AÑOS'], null, ['class' => 'form-control', 'disabled']) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-2 tipobebida hidden">
                        <label>Tipo de bebida</label>
                        <div class="form-group">
                            <input type="text" class="form-control" disabled>
                        </div>
                    </div>
                </div>
                <script>
                    document.addEventListener("DOMContentLoaded", function() {
                            var selectElements = document.querySelectorAll('.estadoalcohol');
                            selectElements.forEach(function(selectElement) {
                                selectElement.addEventListener("change", function() {
                                    var parentSection = this.closest('.section');
                                    var relatedElements = parentSection.querySelectorAll('.form-control');
                
                                    relatedElements.forEach(function(element) {
                                        if (element !== selectElement) {
                                            element.disabled = true;
                                        }
                                    });
                                    if (this.value === "nobebe") {
                                        parentSection.querySelector('.anossuspensionfumador .form-control').value = "";
                                        parentSection.querySelector('.anossuspensionfumador select').value = "";
                                        parentSection.querySelector('.frecuenciafumador .form-control').value = "";
                                        parentSection.querySelector('.frecuenciafumador select').value = "";
                                        parentSection.querySelector('.numerocigarrillos .form-control').value = "";
                                        parentSection.querySelector('.anosfumador .form-control').value = "";
                                        parentSection.querySelector('.anosfumador select').value = "";
                                    } else if (this.value === "exbebedor") {
                                        parentSection.querySelector('.anossuspensionalcohol .form-control').disabled = false;
                                        parentSection.querySelector('.anossuspensionalcohol select').disabled = false;
                                        parentSection.querySelector('.frecuenciaalcohol .form-control').value = "";
                                        parentSection.querySelector('.frecuenciaalcohol select').value = "";
                                        parentSection.querySelector('.tipobebida .form-control').value = "";
                                        parentSection.querySelector('.anosalcohol .form-control').value = "";
                                        parentSection.querySelector('.anosalcohol select').value = "";
                                    }
                                    else if (this.value === "bebedor") {
                                        parentSection.querySelector('.frecuenciaalcohol .form-control').disabled = false;
                                        parentSection.querySelector('.frecuenciaalcohol select').disabled = false;
                                        parentSection.querySelector('.anosalcohol .form-control').disabled = false;
                                        parentSection.querySelector('.anosalcohol select').disabled = false;
                                        parentSection.querySelector('.tipobebida .form-control').disabled = false;
                                        parentSection.querySelector('.anossuspensionalcohol .form-control').value = "";
                                        parentSection.querySelector('.anossuspensionalcohol select').value = "";
                                    }
                                });
                            });
                        });
                </script>
            </div>
            <div class="section">
                <p><strong><span style="font-size: larger;">COCA</span></strong></p>
                <div class="row">
                    <div class="col-lg-3">
                        <label>Mastica coca</label>
                        <div class="form-group">
                            <select class="form-control estadococa">
                                <option value="cocano">No</option>
                                <option value="cocasi">Si</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-4 anoscoca hidden">
                        <label>Tiempo de consumo</label>
                        <div class="form-group">
                            <div class="input-group">
                                {!! Form::text('periodotiempolaboral8', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '2', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57', 'disabled']) !!}
                                <div class="input-group-append">
                                    {!! Form::select('periodo_tipo8', ['' => '', 'DIAS' => 'DIAS', 'SEMANAS' => 'SEMANAS', 'MESES' => 'MESES', 'AÑOS' => 'AÑOS'], null, ['class' => 'form-control', 'disabled']) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-5 frecuenciacoca hidden">
                        <label>Frecuencia</label>
                        <div class="form-group">
                            <div class="input-group">
                                {!! Form::text('periodotiempolaboral7', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '2', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57', 'disabled']) !!}
                                <div class="input-group-append">
                                    {!! Form::select('periodo_tipo7', ['' => '', 'AL DIA' => 'AL DIA', 'A LA SEMANA' => 'A LA SEMANA', 'AL MES' => 'AL MES', 'AL AÑO' => 'AL AÑO'], null, ['class' => 'form-control', 'disabled']) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <script>
                    document.addEventListener("DOMContentLoaded", function() {
                            var selectElements = document.querySelectorAll('.estadococa');
                
                            selectElements.forEach(function(selectElement) {
                                selectElement.addEventListener("change", function() {
                                    var parentSection = this.closest('.section');
                                    var relatedElements = parentSection.querySelectorAll('.form-control');
                
                                    relatedElements.forEach(function(element) {
                                        if (element !== selectElement) {
                                            element.disabled = true;
                                        }
                                    });
                
                                    if (this.value === "cocasi") {
                                        parentSection.querySelector('.anoscoca .form-control').disabled = false;
                                        parentSection.querySelector('.anoscoca select').disabled = false;
                                        parentSection.querySelector('.frecuenciacoca .form-control').disabled = false;
                                        parentSection.querySelector('.frecuenciacoca select').disabled = false;
                                    } else if (this.value === "cocano") {
                                        parentSection.querySelector('.anoscoca .form-control').value = "";
                                        parentSection.querySelector('.anoscoca select').value = "";
                                        parentSection.querySelector('.frecuenciacoca .form-control').value = "";
                                        parentSection.querySelector('.frecuenciacoca select').value = "";
                                    }
                                });
                            });
                        });
                </script>
            </div>
            <div class="section">
                <p><strong><span style="font-size: larger;">MEDICAMENTO</span></strong></p>
                <div class="row">
                    <div class="col-lg-3">
                        <label>Toma algun medicamento</label>
                        <div class="form-group">
                            <select class="form-control estadomedicamento">
                                <option value="medicamentono">No</option>
                                <option value="medicamentosi">Si</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-9 cualesmedicamentos hidden">
                        <label>Cuales</label>
                        <div class="form-group">
                            <input type="text" class="form-control" disabled>
                        </div>
                    </div>
                </div>
                <script>
                    document.addEventListener("DOMContentLoaded", function() {
                            var selectElements = document.querySelectorAll('.estadomedicamento');
                
                            selectElements.forEach(function(selectElement) {
                                selectElement.addEventListener("change", function() {
                                    var parentSection = this.closest('.section');
                                    var relatedElements = parentSection.querySelectorAll('.form-control');
                
                                    relatedElements.forEach(function(element) {
                                        if (element !== selectElement) {
                                            element.disabled = true;
                                        }
                                    });
                
                                    if (this.value === "medicamentosi") {
                                        parentSection.querySelector('.cualesmedicamentos .form-control').disabled = false;
                                    } else if (this.value === "medicamentono") {
                                        parentSection.querySelector('.cualesmedicamentos .form-control').value = "";
                                    }
                                });
                            });
                        });
                </script>
            </div>
            <h4 style="text-align: center;" style="">ADICIONAL HABITOS TOXICOS</h4>
            <div class="row">
                <div class="col-lg-6">
                    <div class="form-group">
                        {!! Form::label('Vivienda:') !!}
                        {!! Form::text('vivienda', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '200']) !!}
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-group">
                        {!! Form::label('Alimentacion:') !!}
                        {!! Form::text('alimentacion', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '200']) !!}
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-6">
                    <div class="form-group">
                        {!! Form::label('Drogas:') !!}
                        {!! Form::text('drogas', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '200']) !!}
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-group">
                        {!! Form::label('Deporte:') !!}
                        {!! Form::text('deporte', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '200']) !!}
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-6">
                    <div class="form-group">
                        {!! Form::label('Catarsis:') !!}
                        {!! Form::text('catarsis', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '200']) !!}
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-group">
                        {!! Form::label('Diuresis:') !!}
                        {!! Form::text('diuresis', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '200']) !!}
                    </div>
                </div>
            </div>
            <h4 style="text-align: center;" style="">ANTECEDENTES FAMILIARES</h4>
            <div class="Familiares">
                <div class="section">
                    <p><strong><span style="font-size: larger;">PADRE</span></strong></p>
                    <div class="row">
                        <div class="col-lg-2">
                            <label>Estado de salud</label>
                            <div class="form-group">
                                <select class="form-control estadosalud">
                                    <option value=""></option>
                                    <option value="vivo">Vivo</option>
                                    <option value="fallecido">Fallecido</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-2 edadvi hidden">
                            <label>Edad vivo</label>
                            <div class="form-group">
                                <input type="text" class="form-control edadvi-input" oninput="this.value = this.value.replace(/\D/g, '').slice(0, 3);" disabled>
                            </div>
                        </div>                        
                        <div class="col-lg-2 edadfa hidden">
                            <label>Edad al fallecer</label>
                            <div class="form-group">
                                <input type="text" class="form-control edadfa-input" oninput="this.value = this.value.replace(/\D/g, '').slice(0, 3);" disabled>
                            </div>
                        </div>
                        <div class="col-lg-3 causafa hidden">
                            <label>Causa de fallecimiento</label>
                            <div class="form-group">
                                <input type="text" class="form-control" disabled>
                            </div>
                        </div>
                        <div class="col-lg-3 obser hidden">
                            <label>Observaciones / Enfermedades</label>
                            <div class="form-group">
                                <input type="text" class="form-control" disabled>
                            </div>
                        </div>
                    </div>
                </div>

                <br>
                <div class="section">
                    <p><strong><span style="font-size: larger;">MADRE</span></strong></p>
                    <div class="row">
                        <div class="col-lg-2">
                            <label>Estado de salud</label>
                            <div class="form-group">
                                <select class="form-control estadosalud">
                                    <option value=""></option>
                                    <option value="vivo">Vivo</option>
                                    <option value="fallecido">Fallecido</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-2 edadvi hidden">
                            <label>Edad vivo</label>
                            <div class="form-group">
                                <input type="text" class="form-control edadvi-input" oninput="this.value = this.value.replace(/\D/g, '').slice(0, 3);" disabled>
                            </div>
                        </div>
                        <div class="col-lg-2 edadfa hidden">
                            <label>Edad al fallecer</label>
                            <div class="form-group">
                                <input type="text" class="form-control edadfa-input" oninput="this.value = this.value.replace(/\D/g, '').slice(0, 3);" disabled>
                            </div>
                        </div>
                        <div class="col-lg-3 causafa hidden">
                            <label>Causa de fallecimiento</label>
                            <div class="form-group">
                                <input type="text" class="form-control" disabled>
                            </div>
                        </div>
                        <div class="col-lg-3 obser hidden">
                            <label>Observaciones / Enfermedades</label>
                            <div class="form-group">
                                <input type="text" class="form-control" disabled>
                            </div>
                        </div>
                    </div>
                </div>

                <br>
                <div class="section">
                    <p><strong><span style="font-size: larger;">HERMANO/AS</span></strong></p>
                    <div class="row">
                        <div class="col-lg-2">
                            <label>Estado de salud</label>
                            <div class="form-group">
                                <select class="form-control estadosalud">
                                    <option value=""></option>
                                    <option value="vivo">Vivo</option>
                                    <option value="fallecido">Fallecido</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-2 edadvi hidden">
                            <label>Edad vivo</label>
                            <div class="form-group">
                                <input type="text" class="form-control edadvi-input" oninput="this.value = this.value.replace(/\D/g, '').slice(0, 3);" disabled>
                            </div>
                        </div>
                        <div class="col-lg-2 edadfa hidden">
                            <label>Edad al fallecer</label>
                            <div class="form-group">
                                <input type="text" class="form-control edadfa-input" oninput="this.value = this.value.replace(/\D/g, '').slice(0, 3);" disabled>
                            </div>
                        </div>
                        <div class="col-lg-3 causafa hidden">
                            <label>Causa de fallecimiento</label>
                            <div class="form-group">
                                <input type="text" class="form-control" disabled>
                            </div>
                        </div>
                        <div class="col-lg-3 obser hidden">
                            <label>Observaciones / Enfermedades</label>
                            <div class="form-group">
                                <input type="text" class="form-control" disabled>
                            </div>
                        </div>
                    </div>
                </div>

                <br>
                <div class="section">
                    <p><strong><span style="font-size: larger;">ESPOSA</span></strong></p>
                    <div class="row">
                        <div class="col-lg-2">
                            <label>Estado de salud</label>
                            <div class="form-group">
                                <select class="form-control estadosalud">
                                    <option value=""></option>
                                    <option value="vivo">Vivo</option>
                                    <option value="fallecido">Fallecido</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-2 edadvi hidden">
                            <label>Edad vivo</label>
                            <div class="form-group">
                                <input type="text" class="form-control edadvi-input" oninput="this.value = this.value.replace(/\D/g, '').slice(0, 3);" disabled>
                            </div>
                        </div>
                        <div class="col-lg-2 edadfa hidden">
                            <label>Edad al fallecer</label>
                            <div class="form-group">
                                <input type="text" class="form-control edadfa-input" oninput="this.value = this.value.replace(/\D/g, '').slice(0, 3);" disabled>
                            </div>
                        </div>
                        <div class="col-lg-3 causafa hidden">
                            <label>Causa de fallecimiento</label>
                            <div class="form-group">
                                <input type="text" class="form-control" disabled>
                            </div>
                        </div>
                        <div class="col-lg-3 obser hidden">
                            <label>Observaciones / Enfermedades</label>
                            <div class="form-group">
                                <input type="text" class="form-control" disabled>
                            </div>
                        </div>
                    </div>
                </div>

                <br>
                <div class="section">
                    <p><strong><span style="font-size: larger;">HIJOS</span></strong></p>
                    <div class="row">
                        <div class="col-lg-2">
                            <label>Estado de salud</label>
                            <div class="form-group">
                                <select class="form-control estadosalud">
                                    <option value=""></option>
                                    <option value="vivo">Vivo</option>
                                    <option value="fallecido">Fallecido</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-2 edadvi hidden">
                            <label>Edad vivo</label>
                            <div class="form-group">
                                <input type="text" class="form-control edadvi-input" oninput="this.value = this.value.replace(/\D/g, '').slice(0, 3);" disabled>
                            </div>
                        </div>
                        <div class="col-lg-2 edadfa hidden">
                            <label>Edad al fallecer</label>
                            <div class="form-group">
                                <input type="text" class="form-control edadfa-input" oninput="this.value = this.value.replace(/\D/g, '').slice(0, 3);" disabled>
                            </div>
                        </div>
                        <div class="col-lg-3 causafa hidden">
                            <label>Causa de fallecimiento</label>
                            <div class="form-group">
                                <input type="text" class="form-control" disabled>
                            </div>
                        </div>
                        <div class="col-lg-3 obser hidden">
                            <label>Observaciones / Enfermedades</label>
                            <div class="form-group">
                                <input type="text" class="form-control" disabled>
                            </div>
                        </div>
                    </div>
                </div>
                <script>
                    document.addEventListener("DOMContentLoaded", function() {
                            var selectElements = document.querySelectorAll('.estadosalud');
                
                            selectElements.forEach(function(selectElement) {
                                selectElement.addEventListener("change", function() {
                                    var parentSection = this.closest('.section');
                                    var relatedElements = parentSection.querySelectorAll('.form-control');
                
                                    relatedElements.forEach(function(element) {
                                        if (element !== selectElement) {
                                            element.disabled = true;
                                        }
                                    });
                
                                    if (this.value === "vivo") {
                                        parentSection.querySelector('.edadvi .form-control').disabled = false;
                                        parentSection.querySelector('.obser .form-control').disabled = false;
                                        parentSection.querySelector('.edadfa .form-control').value = "";
                                        parentSection.querySelector('.causafa .form-control').value = "";
                                    } else if (this.value === "fallecido") {
                                        parentSection.querySelector('.edadfa .form-control').disabled = false;
                                        parentSection.querySelector('.causafa .form-control').disabled = false;
                                        parentSection.querySelector('.edadvi .form-control').value = "";
                                        parentSection.querySelector('.obser .form-control').value = "";
                                    } else if (this.value === "") {
                                        parentSection.querySelector('.edadfa .form-control').value = "";
                                        parentSection.querySelector('.causafa .form-control').value = "";
                                        parentSection.querySelector('.edadvi .form-control').value = "";
                                        parentSection.querySelector('.obser .form-control').value = "";
                                    }
                                });
                            });
                        });
                </script>
            </div>
            <br>
            <h4 style="text-align: center;" style="">ANTECEDENTES FAMILIARES ADICIONALES</h4>
            <div class="30">
                <div class="row">
                    <div class="col-lg-10">
                        <p>HTA</p>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[30][respuesta]" value="si" id="rsi" onclick="mostrarFormulario030()">
                                <label class="form-check-label" for="rsi">SI</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[30][respuesta]" value="no" id="rno" onclick="ocultarFormulario030()">
                                <label class="form-check-label" for="rno">NO</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="card030" style="display: none;">
                    <div class="row">
                        <div class="card-body">    
                            <div class="row">
                                <div class="col-lg-4">
                                    {!! Form::label('', 'Hace cuanto') !!}
                                    <div class="form-group">
                                        <div class="input-group">
                                            {!! Form::text('periodotiempolaboral30', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '3', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']) !!}
                                            <div class="input-group-append">
                                                {!! Form::select('periodo_tipo30', ['' => '', 'DIAS' => 'DIAS', 'SEMANAS' => 'SEMANAS', 'MESES' => 'MESES', 'AÑOS' => 'AÑOS'], null, ['class' => 'form-control']) !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                    function limpiarCampos030() {
                        document.getElementById('card030').querySelectorAll('input[type="text"]').forEach(function(input) {
                            input.value = '';
                        });
                        document.getElementById('card030').querySelectorAll('select').forEach(function(select) {
                            select.value = '';
                        });
                    }
                    function mostrarFormulario030() {
                        document.getElementById('card030').style.display = 'block';
                    }
                    function ocultarFormulario030() {
                        document.getElementById('card030').style.display = 'none';
                        limpiarCampos030();
                    }
                </script>
            </div>
            <div class="31">
                <div class="row odd-row">
                    <div class="col-lg-10">
                        <p>Infarto</p>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[31][respuesta]" value="si" id="rsi" onclick="mostrarFormulario031()">
                                <label class="form-check-label" for="rsi">SI</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[31][respuesta]" value="no" id="rno" onclick="ocultarFormulario031()">
                                <label class="form-check-label" for="rno">NO</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="card031" style="display: none;">
                    <div class="row odd-row">
                        <div class="card-body">    
                            <div class="row">
                                <div class="col-lg-4">
                                    {!! Form::label('', 'Hace cuanto') !!}
                                    <div class="form-group">
                                        <div class="input-group">
                                            {!! Form::text('periodotiempolaboral31', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '3', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']) !!}
                                            <div class="input-group-append">
                                                {!! Form::select('periodo_tipo31', ['' => '', 'DIAS' => 'DIAS', 'SEMANAS' => 'SEMANAS', 'MESES' => 'MESES', 'AÑOS' => 'AÑOS'], null, ['class' => 'form-control']) !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                    function limpiarCampos031() {
                        document.getElementById('card031').querySelectorAll('input[type="text"]').forEach(function(input) {
                            input.value = '';
                        });
                        document.getElementById('card031').querySelectorAll('select').forEach(function(select) {
                            select.value = '';
                        });
                    }
                    function mostrarFormulario031() {
                        document.getElementById('card031').style.display = 'block';
                    }
                    function ocultarFormulario031() {
                        document.getElementById('card031').style.display = 'none';
                        limpiarCampos031();
                    }
                </script>
            </div>
            <div class="32">
                <div class="row">
                    <div class="col-lg-10">
                        <p>ACV</p>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[32][respuesta]" value="si" id="rsi" onclick="mostrarFormulario032()">
                                <label class="form-check-label" for="rsi">SI</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[32][respuesta]" value="no" id="rno" onclick="ocultarFormulario032()">
                                <label class="form-check-label" for="rno">NO</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="card032" style="display: none;">
                    <div class="row">
                        <div class="card-body">    
                            <div class="row">
                                <div class="col-lg-4">
                                    {!! Form::label('', 'Hace cuanto') !!}
                                    <div class="form-group">
                                        <div class="input-group">
                                            {!! Form::text('periodotiempolaboral32', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '3', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']) !!}
                                            <div class="input-group-append">
                                                {!! Form::select('periodo_tipo32', ['' => '', 'DIAS' => 'DIAS', 'SEMANAS' => 'SEMANAS', 'MESES' => 'MESES', 'AÑOS' => 'AÑOS'], null, ['class' => 'form-control']) !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                    function limpiarCampos032() {
                        document.getElementById('card032').querySelectorAll('input[type="text"]').forEach(function(input) {
                            input.value = '';
                        });
                        document.getElementById('card032').querySelectorAll('select').forEach(function(select) {
                            select.value = '';
                        });
                    }
                    function mostrarFormulario032() {
                        document.getElementById('card032').style.display = 'block';
                    }
                    function ocultarFormulario032() {
                        document.getElementById('card032').style.display = 'none';
                        limpiarCampos032();
                    }
                </script>
            </div>
            <div class="33">
                <div class="row odd-row">
                    <div class="col-lg-10">
                        <p>Alergias</p>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[33][respuesta]" value="si" id="rsi" onclick="mostrarFormulario033()">
                                <label class="form-check-label" for="rsi">SI</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[33][respuesta]" value="no" id="rno" onclick="ocultarFormulario033()">
                                <label class="form-check-label" for="rno">NO</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="card033" style="display: none;">
                    <div class="row odd-row">
                        <div class="card-body">    
                            <div class="row">
                                <div class="col-lg-4">
                                    {!! Form::label('', 'Hace cuanto') !!}
                                    <div class="form-group">
                                        <div class="input-group">
                                            {!! Form::text('periodotiempolaboral33', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '3', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']) !!}
                                            <div class="input-group-append">
                                                {!! Form::select('periodo_tipo33', ['' => '', 'DIAS' => 'DIAS', 'SEMANAS' => 'SEMANAS', 'MESES' => 'MESES', 'AÑOS' => 'AÑOS'], null, ['class' => 'form-control']) !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                    function limpiarCampos033() {
                        document.getElementById('card033').querySelectorAll('input[type="text"]').forEach(function(input) {
                            input.value = '';
                        });
                        document.getElementById('card033').querySelectorAll('select').forEach(function(select) {
                            select.value = '';
                        });
                    }
                    function mostrarFormulario033() {
                        document.getElementById('card033').style.display = 'block';
                    }
                    function ocultarFormulario033() {
                        document.getElementById('card033').style.display = 'none';
                        limpiarCampos033();
                    }
                </script>
            </div>
            <div class="34">
                <div class="row">
                    <div class="col-lg-10">
                        <p>Ulcera peptica</p>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[34][respuesta]" value="si" id="rsi" onclick="mostrarFormulario034()">
                                <label class="form-check-label" for="rsi">SI</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[34][respuesta]" value="no" id="rno" onclick="ocultarFormulario034()">
                                <label class="form-check-label" for="rno">NO</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="card034" style="display: none;">
                    <div class="row">
                        <div class="card-body">    
                            <div class="row">
                                <div class="col-lg-4">
                                    {!! Form::label('', 'Hace cuanto') !!}
                                    <div class="form-group">
                                        <div class="input-group">
                                            {!! Form::text('periodotiempolaboral34', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '3', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']) !!}
                                            <div class="input-group-append">
                                                {!! Form::select('periodo_tipo34', ['' => '', 'DIAS' => 'DIAS', 'SEMANAS' => 'SEMANAS', 'MESES' => 'MESES', 'AÑOS' => 'AÑOS'], null, ['class' => 'form-control']) !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                    function limpiarCampos034() {
                        document.getElementById('card034').querySelectorAll('input[type="text"]').forEach(function(input) {
                            input.value = '';
                        });
                        document.getElementById('card034').querySelectorAll('select').forEach(function(select) {
                            select.value = '';
                        });
                    }
                    function mostrarFormulario034() {
                        document.getElementById('card034').style.display = 'block';
                    }
                    function ocultarFormulario034() {
                        document.getElementById('card034').style.display = 'none';
                        limpiarCampos034();
                    }
                </script>
            </div>
            <div class="35">
                <div class="row odd-row">
                    <div class="col-lg-10">
                        <p>Diabetes</p>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[35][respuesta]" value="si" id="rsi" onclick="mostrarFormulario035()">
                                <label class="form-check-label" for="rsi">SI</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[35][respuesta]" value="no" id="rno" onclick="ocultarFormulario035()">
                                <label class="form-check-label" for="rno">NO</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="card035" style="display: none;">
                    <div class="row odd-row">
                        <div class="card-body">    
                            <div class="row">
                                <div class="col-lg-4">
                                    {!! Form::label('', 'Hace cuanto') !!}
                                    <div class="form-group">
                                        <div class="input-group">
                                            {!! Form::text('periodotiempolaboral35', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '3', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']) !!}
                                            <div class="input-group-append">
                                                {!! Form::select('periodo_tipo35', ['' => '', 'DIAS' => 'DIAS', 'SEMANAS' => 'SEMANAS', 'MESES' => 'MESES', 'AÑOS' => 'AÑOS'], null, ['class' => 'form-control']) !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                    function limpiarCampos035() {
                        document.getElementById('card035').querySelectorAll('input[type="text"]').forEach(function(input) {
                            input.value = '';
                        });
                        document.getElementById('card035').querySelectorAll('select').forEach(function(select) {
                            select.value = '';
                        });
                    }
                    function mostrarFormulario035() {
                        document.getElementById('card035').style.display = 'block';
                    }
                    function ocultarFormulario035() {
                        document.getElementById('card035').style.display = 'none';
                        limpiarCampos035();
                    }
                </script>
            </div>
            <div class="36">
                <div class="row">
                    <div class="col-lg-10">
                        <p>Asma</p>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[36][respuesta]" value="si" id="rsi" onclick="mostrarFormulario036()">
                                <label class="form-check-label" for="rsi">SI</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[36][respuesta]" value="no" id="rno" onclick="ocultarFormulario036()">
                                <label class="form-check-label" for="rno">NO</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="card036" style="display: none;">
                    <div class="row">
                        <div class="card-body">    
                            <div class="row">
                                <div class="col-lg-4">
                                    {!! Form::label('', 'Hace cuanto') !!}
                                    <div class="form-group">
                                        <div class="input-group">
                                            {!! Form::text('periodotiempolaboral36', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '3', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']) !!}
                                            <div class="input-group-append">
                                                {!! Form::select('periodo_tipo36', ['' => '', 'DIAS' => 'DIAS', 'SEMANAS' => 'SEMANAS', 'MESES' => 'MESES', 'AÑOS' => 'AÑOS'], null, ['class' => 'form-control']) !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                    function limpiarCampos036() {
                        document.getElementById('card036').querySelectorAll('input[type="text"]').forEach(function(input) {
                            input.value = '';
                        });
                        document.getElementById('card036').querySelectorAll('select').forEach(function(select) {
                            select.value = '';
                        });
                    }
                    function mostrarFormulario036() {
                        document.getElementById('card036').style.display = 'block';
                    }
                    function ocultarFormulario036() {
                        document.getElementById('card036').style.display = 'none';
                        limpiarCampos036();
                    }
                </script>
            </div>
            <div class="37">
                <div class="row odd-row">
                    <div class="col-lg-10">
                        <p>TBC</p>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[37][respuesta]" value="si" id="rsi" onclick="mostrarFormulario037()">
                                <label class="form-check-label" for="rsi">SI</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[37][respuesta]" value="no" id="rno" onclick="ocultarFormulario037()">
                                <label class="form-check-label" for="rno">NO</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="card037" style="display: none;">
                    <div class="row odd-row">
                        <div class="card-body">    
                            <div class="row">
                                <div class="col-lg-4">
                                    {!! Form::label('', 'Hace cuanto') !!}
                                    <div class="form-group">
                                        <div class="input-group">
                                            {!! Form::text('periodotiempolaboral37', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '3', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']) !!}
                                            <div class="input-group-append">
                                                {!! Form::select('periodo_tipo37', ['' => '', 'DIAS' => 'DIAS', 'SEMANAS' => 'SEMANAS', 'MESES' => 'MESES', 'AÑOS' => 'AÑOS'], null, ['class' => 'form-control']) !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                    function limpiarCampos037() {
                        document.getElementById('card037').querySelectorAll('input[type="text"]').forEach(function(input) {
                            input.value = '';
                        });
                        document.getElementById('card037').querySelectorAll('select').forEach(function(select) {
                            select.value = '';
                        });
                    }
                    function mostrarFormulario037() {
                        document.getElementById('card037').style.display = 'block';
                    }
                    function ocultarFormulario037() {
                        document.getElementById('card037').style.display = 'none';
                        limpiarCampos037();
                    }
                </script>
            </div>
            <div class="38">
                <div class="row">
                    <div class="col-lg-10">
                        <p>Artritis</p>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[38][respuesta]" value="si" id="rsi" onclick="mostrarFormulario038()">
                                <label class="form-check-label" for="rsi">SI</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[38][respuesta]" value="no" id="rno" onclick="ocultarFormulario038()">
                                <label class="form-check-label" for="rno">NO</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="card038" style="display: none;">
                    <div class="row">
                        <div class="card-body">    
                            <div class="row">
                                <div class="col-lg-4">
                                    {!! Form::label('', 'Hace cuanto') !!}
                                    <div class="form-group">
                                        <div class="input-group">
                                            {!! Form::text('periodotiempolaboral38', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '3', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']) !!}
                                            <div class="input-group-append">
                                                {!! Form::select('periodo_tipo38', ['' => '', 'DIAS' => 'DIAS', 'SEMANAS' => 'SEMANAS', 'MESES' => 'MESES', 'AÑOS' => 'AÑOS'], null, ['class' => 'form-control']) !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                    function limpiarCampos038() {
                        document.getElementById('card038').querySelectorAll('input[type="text"]').forEach(function(input) {
                            input.value = '';
                        });
                        document.getElementById('card038').querySelectorAll('select').forEach(function(select) {
                            select.value = '';
                        });
                    }
                    function mostrarFormulario038() {
                        document.getElementById('card038').style.display = 'block';
                    }
                    function ocultarFormulario038() {
                        document.getElementById('card038').style.display = 'none';
                        limpiarCampos038();
                    }
                </script>
            </div>
            <div class="39">
                <div class="row odd-row">
                    <div class="col-lg-10">
                        <p>Enfermedad mental</p>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[39][respuesta]" value="si" id="rsi" onclick="mostrarFormulario039()">
                                <label class="form-check-label" for="rsi">SI</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[39][respuesta]" value="no" id="rno" onclick="ocultarFormulario039()">
                                <label class="form-check-label" for="rno">NO</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="card039" style="display: none;">
                    <div class="row odd-row">
                        <div class="card-body">    
                            <div class="row">
                                <div class="col-lg-4">
                                    {!! Form::label('', 'Hace cuanto') !!}
                                    <div class="form-group">
                                        <div class="input-group">
                                            {!! Form::text('periodotiempolaboral39', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '3', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']) !!}
                                            <div class="input-group-append">
                                                {!! Form::select('periodo_tipo39', ['' => '', 'DIAS' => 'DIAS', 'SEMANAS' => 'SEMANAS', 'MESES' => 'MESES', 'AÑOS' => 'AÑOS'], null, ['class' => 'form-control']) !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                    function limpiarCampos039() {
                        document.getElementById('card039').querySelectorAll('input[type="text"]').forEach(function(input) {
                            input.value = '';
                        });
                        document.getElementById('card039').querySelectorAll('select').forEach(function(select) {
                            select.value = '';
                        });
                    }
                    function mostrarFormulario039() {
                        document.getElementById('card039').style.display = 'block';
                    }
                    function ocultarFormulario039() {
                        document.getElementById('card039').style.display = 'none';
                        limpiarCampos039();
                    }
                </script>
            </div>
            <div class="40">
                <div class="row">
                    <div class="col-lg-10">
                        <p>Cancer</p>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[40][respuesta]" value="si" id="rsi" onclick="mostrarFormulario040()">
                                <label class="form-check-label" for="rsi">SI</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[40][respuesta]" value="no" id="rno" onclick="ocultarFormulario040()">
                                <label class="form-check-label" for="rno">NO</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="card040" style="display: none;">
                    <div class="row">
                        <div class="card-body">    
                            <div class="row">
                                <div class="col-lg-4">
                                    {!! Form::label('', 'Hace cuanto') !!}
                                    <div class="form-group">
                                        <div class="input-group">
                                            {!! Form::text('periodotiempolaboral40', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '3', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']) !!}
                                            <div class="input-group-append">
                                                {!! Form::select('periodo_tipo40', ['' => '', 'DIAS' => 'DIAS', 'SEMANAS' => 'SEMANAS', 'MESES' => 'MESES', 'AÑOS' => 'AÑOS'], null, ['class' => 'form-control']) !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                    function limpiarCampos040() {
                        document.getElementById('card040').querySelectorAll('input[type="text"]').forEach(function(input) {
                            input.value = '';
                        });
                        document.getElementById('card040').querySelectorAll('select').forEach(function(select) {
                            select.value = '';
                        });
                    }
                    function mostrarFormulario040() {
                        document.getElementById('card040').style.display = 'block';
                    }
                    function ocultarFormulario040() {
                        document.getElementById('card040').style.display = 'none';
                        limpiarCampos040();
                    }
                </script>
            </div>
            <div class="41">
                <div class="row odd-row">
                    <div class="col-lg-10">
                        <p>Otros</p>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[41][respuesta]" value="si" id="rsi" onclick="mostrarFormulario041()">
                                <label class="form-check-label" for="rsi">SI</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[41][respuesta]" value="no" id="rno" onclick="ocultarFormulario041()">
                                <label class="form-check-label" for="rno">NO</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="card041" style="display: none;">
                    <div class="row odd-row">
                        <div class="card-body">    
                            <div class="row">
                                <div class="col-lg-4">
                                    {!! Form::label('', 'Hace cuanto') !!}
                                    <div class="form-group">
                                        <div class="input-group">
                                            {!! Form::text('periodotiempolaboral41', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '3', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']) !!}
                                            <div class="input-group-append">
                                                {!! Form::select('periodo_tipo41', ['' => '', 'DIAS' => 'DIAS', 'SEMANAS' => 'SEMANAS', 'MESES' => 'MESES', 'AÑOS' => 'AÑOS'], null, ['class' => 'form-control']) !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                    function limpiarCampos041() {
                        document.getElementById('card041').querySelectorAll('input[type="text"]').forEach(function(input) {
                            input.value = '';
                        });
                        document.getElementById('card041').querySelectorAll('select').forEach(function(select) {
                            select.value = '';
                        });
                    }
                    function mostrarFormulario041() {
                        document.getElementById('card041').style.display = 'block';
                    }
                    function ocultarFormulario041() {
                        document.getElementById('card041').style.display = 'none';
                        limpiarCampos041();
                    }
                </script>
            </div>

            <br>
            <h4 style="text-align: center;" style="">HISTORIA DE ENFERMEDAD ACTUAL</h4>
            <div class="row">
                <div class="col-lg-12">
                    <div class="form-group">
                        {!! Form::textarea('historiaenfermedad', null, ['class' => 'form-control', 'placeholder' => '', 'rows' => '5', 'style' => 'resize: none;']) !!}
                    </div>
                </div>
            </div>
            <h4 style="text-align: center;" style="">EXAMEN FISICO</h4>
            <div class="row">
                <div class="col-lg-12">
                    <div class="form-group">
                        {!! Form::label('Examen fisico general:') !!}
                        {!! Form::text('examenfisicogeneral', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '200']) !!}
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-6">
                    <div class="form-group">
                        {!! Form::label('Llenado capilar:') !!}
                        {!! Form::text('llenadocapilar', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '200']) !!}
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-group">
                        {!! Form::label('Lateralidad:') !!}
                        {!! Form::text('lateralidad', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '200']) !!}
                    </div>
                </div>
            </div>
            <div class="section">
                <p><strong><span style="font-size: larger;">SIGNOS VITALES</span></strong></p>
                <div class="row">
                    <div class="col-lg-2">
                        <label>Pulso</label>
                        <div class="form-group">
                            <div class="input-group">
                                <input type="text" class="form-control" value="" oninput="this.value = this.value.replace(/\D/g, '').slice(0, 3);">
                                <div class="input-group-append">
                                    <span class="input-group-text">lpm</span>
                                </div>
                            </div>
                        </div>
                    </div>                    
                    {{-- <div class="col-lg-2">
                        <label>satO2</label>
                        <div class="input-group">
                            <input type="text" class="form-control" value="" oninput="this.value = this.value.replace(/\D/g, '').slice(0, 3);">
                            <div class="input-group-append">
                                <span class="input-group-text">%</span>
                            </div>
                        </div>
                    </div> --}}
                    <div class="col-lg-2">
                        <label>satO2</label>
                        <div class="input-group">
                            <input type="text" class="form-control" value="" oninput="this.value = this.value.replace(/\D/g, ''); if(parseInt(this.value) > 100) { this.value = '100'; }">
                            <div class="input-group-append">
                                <span class="input-group-text">%</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-2">
                        <label>F. respiracion</label>
                        <div class="form-group">
                            <div class="input-group">
                                <input type="text" class="form-control" value="" oninput="this.value = this.value.replace(/\D/g, '').slice(0, 3);">
                                <div class="input-group-append">
                                    <span class="input-group-text">rpm</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <label>Temperatura</label>
                        <div class="form-group">
                            <div class="input-group">
                                <input type="text" class="form-control" value="" oninput="this.value = this.value.replace(/[^\d\.]/g, ''); this.value = this.value.match(/^\d{0,2}(\.\d{0,1})?/g)[0];">
                                <div class="input-group-append">
                                    <span class="input-group-text">°C</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-3">
                        <label>Presion arterial</label>
                        <div class="form-group">
                            <div class="input-group">
                                <input type="text" class="form-control" value="" oninput="this.value = this.value.replace(/[^\d\/]/g, ''); this.value = this.value.match(/^\d{0,3}(\/\d{0,2})?/g)[0];">
                                <div class="input-group-append">
                                    <span class="input-group-text">mmHg</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                </div>
            </div>
            <div class="section">
                <p><strong><span style="font-size: larger;">VISION</span></strong></p>
                <div class="row">
                    <div class="col-lg-6">
                        <label>Agudeza visual</label>
                        <div class="form-group">
                            <input type="text" class="form-control">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <label>Usa lentes</label>
                        <div class="form-group">
                            <select class="form-control">
                                <option value=""></option>
                                <option value="lentessi">Si</option>
                                <option value="lentesno">No</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="section">
                <p><strong><span style="font-size: larger;">DATOS ANTROPOMETRICOS</span></strong></p>
                <div class="row">
                    <div class="col-lg-4">
                        <label>Peso</label>
                        <div class="form-group">
                            <div class="input-group">
                                <input type="text" class="form-control" value="" oninput="this.value = this.value.replace(/[^\d\.]/g, ''); this.value = this.value.match(/^\d{0,3}(\.\d{0,1})?/g)[0];">
                                <div class="input-group-append">
                                    <span class="input-group-text">kg</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <label>Estatura</label>
                        <div class="form-group">
                            <div class="input-group">
                                <input type="text" class="form-control" value="" oninput="this.value = this.value.replace(/[^\d\.]/g, ''); this.value = this.value.match(/^\d{0,1}(\.\d{0,2})?/g)[0];">
                                <div class="input-group-append">
                                    <span class="input-group-text">cm</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <label>IMC</label>
                        <div class="form-group">
                            <div class="input-group">
                                <input type="text" class="form-control" value="" oninput="this.value = this.value.replace(/[^\d\.]/g, ''); this.value = this.value.match(/^\d{0,2}(\.\d{0,1})?/g)[0];">
                                {{-- <div class="input-group-append">
                                    <span class="input-group-text">kg</span>
                                </div> --}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="section">
                <p><strong><span style="font-size: larger;">EXAMEN FISICO SEGMENTADO</span></strong></p>
                <div class="row">
                    <div class="col-lg-6">
                        <label>Cabeza</label>
                        <div class="form-group">
                            <input type="text" class="form-control">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <label>Ojos</label>
                        <div class="form-group">
                            <input type="text" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <label>Nariz</label>
                        <div class="form-group">
                            <input type="text" class="form-control">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <label>Oido</label>
                        <div class="form-group">
                            <input type="text" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <label>Boca</label>
                        <div class="form-group">
                            <input type="text" class="form-control">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <label>Cuello</label>
                        <div class="form-group">
                            <input type="text" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <label>Torax</label>
                        <div class="form-group">
                            <input type="text" class="form-control">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <label>Corazon</label>
                        <div class="form-group">
                            <input type="text" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <label>Pulmones</label>
                        <div class="form-group">
                            <input type="text" class="form-control">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <label>Abdomen</label>
                        <div class="form-group">
                            <input type="text" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <label>Extremidades MMSS</label>
                        <div class="form-group">
                            <input type="text" class="form-control">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <label>Extremidades MMII</label>
                        <div class="form-group">
                            <input type="text" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <label>Neurologico</label>
                        <div class="form-group">
                            <input type="text" class="form-control">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <label>Vestibulo / Cereboloso</label>
                        <div class="form-group">
                            <input type="text" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <label>Marcha</label>
                        <div class="form-group">
                            <input type="text" class="form-control">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <label>Craneo y Columna</label>
                        <div class="form-group">
                            <input type="text" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <label>Exploracion neurovascular</label>
                        <div class="form-group">
                            <input type="text" class="form-control">
                        </div>
                    </div>
                </div>
            </div>
        </div>

<script>
    document.getElementById("mostrarQR").addEventListener("click", function() {
    var contenedorQR = document.getElementById("contenedorQR");
    if (contenedorQR.style.display === "none") {
    contenedorQR.style.display = "block";
    } else {
    contenedorQR.style.display = "none";
    }
    });


    // Función para actualizar el código QR
    function actualizarQR() {
        // Obtener los valores de los campos
        var nombreDireccion = document.getElementById('ND').value;
        var fechaConsulta = document.getElementById('FC').value;
        var tratamiento = document.getElementById('TM').value;

        // Actualizar los datos
        var newData = `Nombre y dirección de su medico particular: ${nombreDireccion}\n
        Fecha y motivo de la consulta reciente: ${fechaConsulta}\n
        Que tratamiento o medicación se transcribió: ${tratamiento}\n`;

        // Actualizar el contenido del QR
        document.getElementById('qr-code').innerHTML = '';
        new QRCode(document.getElementById('qr-code'), newData);
    }

    // Escuchar el evento de cambio en los campos relevantes
    document.getElementById('ND').addEventListener('input', actualizarQR);
    document.getElementById('FC').addEventListener('input', actualizarQR);
    document.getElementById('TM').addEventListener('input', actualizarQR);
</script>
            <style>
            .firm-container {
                width: 80%;
                margin: auto;
                display: flex;
                justify-content: space-around;
                align-items: center;
                margin-top: 40px;
            }
            .firm-column {
                flex: 1;
                text-align: center;
            }

            .firm-line {
                display: flex;
                align-items: center;
                justify-content: center;
                margin-bottom: 20px;
            }

            .title {
                display: flex;
                align-items: center;
            }
            .title img {
                margin-right: 10px;
            }

            .title span {
                font-weight: bold;
                font-size: 16px;
                margin: 0;
            }
            .firm-line input {
                border: none;
                border-bottom: 1px solid #000;
                outline: none;
                text-align: center;
                width: 90%;
                font-size: 16px;
            }
            .title-line {
                width: 90%;
                margin-top: 5px;
                border
            }
            </style>           
        </div>
    </div>
</div>
         
@stop

@section('css')
<link rel="styleheet" href="/css/admin_custom.css">
<style>
    h1, th {
        color:#94c93b; 
        font-family: "Segoe UI";
        font-weight: 900;
        }
    
    .btn-mas {
        background-color:  #ffffff;
        color: #94c93b;
        border-color: #94c93b;
        border-radius: 5px;
        padding: 10px 15px;
        }
    
    .btn-mas:hover {
        background-color: #94c93b;
        color: #ffffff;
        }
    .btn-menos {
        background-color:  #ffffff;
        color: #faa625;
        border-color: #faa625;
        border-radius: 5px;
        padding: 10px 15px;
        }
    
    .btn-menos:hover {
        background-color: #faa625;
        color: #ffffff;
        }
    .odd-row {
        background-color: #f2f3f5;
    }

    .container2 {
        width: 80%;
        margin: auto;
        display: flex;
        justify-content: space-around;
        align-items: center;
    }
    .form-line {
        display: flex;
        align-items: center;
    }
    .form-line label {
        margin-right: 10px;
    }
    .form-line input {
        border: none;
        border-bottom: 1px solid #000;
        outline: none;
        text-align: center;
        width: 150px;
    }
    </style>
@stop

@section('js')
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script type="text/javascript" src="https://jeremyfagis.github.io/dropify/dist/js/dropify.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://jeremyfagis.github.io/dropify/dist/css/dropify.min.css"> 
<script>
$('.dropify').dropify();
</script>
    @if (session('eliminar')=='ok')
    <script>
        Swal.fire(
      '¡Eliminado!',
      'El perfil se eliminó con éxito',
      'success')
    </script>
    @endif
@endsection