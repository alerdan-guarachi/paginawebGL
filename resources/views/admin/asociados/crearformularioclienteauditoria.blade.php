@extends('adminlte::page')
    
@section('content_header')
<a class="btn btn-sm float-right btn-regresar" href="{{ route('admin.asociados.verclienteauditoria', $clienteauditoria) }}">REGRESAR</a>
<h1>FICHA MEDICA DE "{{$clienteauditoria->nombrecompleto}}"</h1>
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
            <form action="{{ route('admin.asociados.generarpdfclienteauditoria', $clienteauditoria) }}" method="post">
                @csrf
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
                        {!! Form::text('empresa', $clienteauditoria->empresa, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '45', 'readonly' => 'readonly']) !!}
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="form-group">
                        {!! Form::label('Regional:') !!}
                        {!! Form::text('regional', $clienteauditoria->lugarnacimiento, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '45', 'readonly' => 'readonly']) !!}
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
                <div class="col-lg-8">
                    <div class="form-group">
                        {!! Form::label('Nombre completo:') !!}
                        {!! Form::text('nombrecompleto', $clienteauditoria->nombrecompleto, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '45', 'readonly' => 'readonly']) !!}
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="form-group">
                        {!! Form::label('Código:') !!}
                        {!! Form::text('nombrecompleto', $clienteauditoria->id, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '45', 'readonly' => 'readonly']) !!}
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-lg-3">
                    <div class="form-group">
                        {!! Form::label('Género:') !!}
                        {!! Form::text('genero', $clienteauditoria->genero, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '45', 'readonly' => 'readonly']) !!}
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="form-group">
                        {!! Form::label('Fecha de nacimiento:') !!}
                        {!! Form::text('fechanacimiento', $clienteauditoria->fechanacimiento, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '45', 'readonly' => 'readonly']) !!}
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="form-group">
                        {!! Form::label('Edad:') !!}
                        {!! Form::text('edad', $clienteauditoria->edad, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '45', 'readonly' => 'readonly']) !!}
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="form-group">
                        {!! Form::label('Lugar de nacimiento:') !!}
                        {!! Form::text('lugarnacimiento', $clienteauditoria->lugarnacimiento, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '45', 'readonly' => 'readonly']) !!}
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-4">
                    <div class="form-group">
                        {!! Form::label('Residencia:') !!}
                        {!! Form::text('residencia', $clienteauditoria->lugarnacimiento, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '45', 'readonly' => 'readonly']) !!}
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="form-group">
                        {!! Form::label('Grado de instrucción:') !!}
                        {!! Form::text('gradoinstruccion', $clienteauditoria->gradoinstruccion, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '45', 'readonly' => 'readonly']) !!}
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="form-group">
                        {!! Form::label('Estado civil:') !!}
                        {!! Form::text('estadocivil', $clienteauditoria->estadocivil, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '45', 'readonly' => 'readonly']) !!}
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-3">
                    <div class="form-group">
                        {!! Form::label('CI:') !!}
                        {!! Form::text('ci', $clienteauditoria->ci, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '45', 'readonly' => 'readonly']) !!}
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="form-group">
                        {!! Form::label('Telefono del paciente:') !!}
                        {!! Form::text('telefono', $clienteauditoria->telefono, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '45', 'readonly' => 'readonly']) !!}
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-group">
                        {!! Form::label('Direccion domiciliaria:') !!}
                        {!! Form::text('domicilio', $clienteauditoria->domicilio, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '45', 'readonly' => 'readonly']) !!}
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-6">
                    <div class="form-group">
                        {!! Form::label('Motivo de consulta:') !!}
                        {!! Form::text('motivoconsulta', $clienteauditoria->motivoconsulta, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '255']) !!}
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-group">
                        {!! Form::label('Ente gestor de salud:') !!}
                        {!! Form::text('entgestorsalud', $clienteauditoria->entgestorsalud, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '45']) !!}
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-6">
                    <div class="form-group">
                        {!! Form::label('Profesion / Ocupacion:') !!}
                        {!! Form::text('ocupacion', $clienteauditoria->ocupacion, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '45', 'readonly' => 'readonly']) !!}
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-group">
                        {!! Form::label('Actividad laboral:') !!}
                        {!! Form::text('actividadlaboral', $clienteauditoria->estadolaboral, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '45', 'readonly' => 'readonly']) !!}
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
            


            <h4 style="text-align: center;" style="">IDENTIFICACIÓN DE PELIGROS</h4>

            <div class="Peligros fisicos odd-row">
                <div class="row">
                    <div class="col-lg-10">
                        <p>Peligros físicos</p>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[4][respuesta]" value="SI" id="rsi" onclick="mostrarForm4()">
                                <label class="form-check-label" for="rsi">SI</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[4][respuesta]" value="NO" id="rno" onclick="ocultarForm4()">
                                <label class="form-check-label" for="rno">NO</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="form4" style="display: none;">
                    <label for="detpe4">Describa los peligros físicos:</label>
                    <input type="text" id="detpe4" name="detpe4" class="form-control">
                </div>
                <script>
                    function mostrarForm4() {
                        document.getElementById('form4').style.display = 'block';
                    }
                    function ocultarForm4() {
                        var formulario = document.getElementById('form4');
                        formulario.style.display = 'none';
                        document.getElementById('detpe4').value = '';
                    }
                </script>
            </div>

            <div class="Peligros quimicos">
                <div class="row">
                    <div class="col-lg-10">
                        <p>Peligros quimicos</p>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[5][respuesta]" value="SI" id="rsi" onclick="mostrarForm5()">
                                <label class="form-check-label" for="rsi">SI</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[5][respuesta]" value="NO" id="rno" onclick="ocultarForm5()">
                                <label class="form-check-label" for="rno">NO</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="form5" style="display: none;">
                    <label for="detpe5">Describa los peligros quimicos:</label>
                    <input type="text" id="detpe5" name="detpe5" class="form-control">
                </div>
                <script>
                    function mostrarForm5() {
                        document.getElementById('form5').style.display = 'block';
                    }
                    function ocultarForm5() {
                        var formulario = document.getElementById('form5');
                        formulario.style.display = 'none';
                        document.getElementById('detpe5').value = '';
                    }
                </script>
            </div>

            <div class="Peligros ergonomicos odd-row">
                <div class="row">
                    <div class="col-lg-10">
                        <p>Peligros ergonomicos</p>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[6][respuesta]" value="SI" id="rsi" onclick="mostrarForm6()">
                                <label class="form-check-label" for="rsi">SI</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[6][respuesta]" value="NO" id="rno" onclick="ocultarForm6()">
                                <label class="form-check-label" for="rno">NO</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="form6" style="display: none;">
                    <label for="detpe6">Describa los peligros ergonomicos:</label>
                    <input type="text" id="detpe6" name="detpe6" class="form-control">
                </div>
                <script>
                    function mostrarForm6() {
                        document.getElementById('form6').style.display = 'block';
                    }
                    function ocultarForm6() {
                        var formulario = document.getElementById('form6');
                        formulario.style.display = 'none';
                        document.getElementById('detpe6').value = '';
                    }
                </script>
            </div>

            <div class="EPP'S">
                <div class="row">
                    <div class="col-lg-10">
                        <p>EPP'S</p>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[7][respuesta]" value="SI" id="rsi" onclick="mostrarForm7()">
                                <label class="form-check-label" for="rsi">SI</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[7][respuesta]" value="NO" id="rno" onclick="ocultarForm7()">
                                <label class="form-check-label" for="rno">NO</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="form7" style="display: none;">
                    <label for="detpe7">Describa los EPP'S:</label>
                    <input type="text" id="detpe7" name="detpe7" class="form-control">
                </div>
                <script>
                    function mostrarForm7() {
                        document.getElementById('form7').style.display = 'block';
                    }
                    function ocultarForm7() {
                        var formulario = document.getElementById('form7');
                        formulario.style.display = 'none';
                        document.getElementById('detpe7').value = '';
                    }
                </script>
            </div>

            <div class="Peligros biologicos odd-row">
                <div class="row">
                    <div class="col-lg-10">
                        <p>Peligros biologicos</p>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[8][respuesta]" value="SI" id="rsi" onclick="mostrarForm8()">
                                <label class="form-check-label" for="rsi">SI</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[8][respuesta]" value="NO" id="rno" onclick="ocultarForm8()">
                                <label class="form-check-label" for="rno">NO</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="form8" style="display: none;">
                    <label for="detpe8">Describa los peligros biologicos:</label>
                    <input type="text" id="detpe8" name="detpe8" class="form-control">
                </div>
                <script>
                    function mostrarForm8() {
                        document.getElementById('form8').style.display = 'block';
                    }
                    function ocultarForm8() {
                        var formulario = document.getElementById('form8');
                        formulario.style.display = 'none';
                        document.getElementById('detpe8').value = '';
                    }
                </script>
            </div>

            <div class="Peligros mecanicos">
                <div class="row">
                    <div class="col-lg-10">
                        <p>Peligros mecanicos</p>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[9][respuesta]" value="SI" id="rsi" onclick="mostrarForm9()">
                                <label class="form-check-label" for="rsi">SI</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[9][respuesta]" value="NO" id="rno" onclick="ocultarForm9()">
                                <label class="form-check-label" for="rno">NO</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="form9" style="display: none;">
                    <label for="detpe9">Describa los peligros mecanicos:</label>
                    <input type="text" id="detpe9" name="detpe9" class="form-control">
                </div>
                <script>
                    function mostrarForm9() {
                        document.getElementById('form9').style.display = 'block';
                    }
                    function ocultarForm9() {
                        var formulario = document.getElementById('form9');
                        formulario.style.display = 'none';
                        document.getElementById('detpe9').value = '';
                    }
                </script>
            </div>

            <div class="Peligros ambientales odd-row">
                <div class="row">
                    <div class="col-lg-10">
                        <p>Peligros ambientales</p>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[10][respuesta]" value="SI" id="rsi" onclick="mostrarForm10()">
                                <label class="form-check-label" for="rsi">SI</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[10][respuesta]" value="NO" id="rno" onclick="ocultarForm10()">
                                <label class="form-check-label" for="rno">NO</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="form10" style="display: none;">
                    <label for="detpe10">Describa los peligros ambientales:</label>
                    <input type="text" id="detpe10" name="detpe10" class="form-control">
                </div>
                <script>
                    function mostrarForm10() {
                        document.getElementById('form10').style.display = 'block';
                    }
                    function ocultarForm10() {
                        var formulario = document.getElementById('form10');
                        formulario.style.display = 'none';
                        document.getElementById('detpe10').value = '';
                    }
                </script>
            </div>

            <div class="Peligros psicosociales">
                <div class="row">
                    <div class="col-lg-10">
                        <p>Peligros psicosociales</p>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[11][respuesta]" value="SI" id="rsi" onclick="mostrarForm11()">
                                <label class="form-check-label" for="rsi">SI</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[11][respuesta]" value="NO" id="rno" onclick="ocultarForm11()">
                                <label class="form-check-label" for="rno">NO</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="form11" style="display: none;">
                    <label for="detpe11">Describa los peligros psicosociales:</label>
                    <input type="text" id="detpe11" name="detpe11" class="form-control">
                </div>
                <script>
                    function mostrarForm11() {
                        document.getElementById('form11').style.display = 'block';
                    }
                    function ocultarForm11() {
                        var formulario = document.getElementById('form11');
                        formulario.style.display = 'none';
                        document.getElementById('detpe11').value = '';
                    }
                </script>
            </div>

            <div class="OTROS odd-row">
                <div class="col-lg-12">
                    <div class="form-group">
                        {!! Form::label('Otros:') !!}
                        {!! Form::text('otros', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '45']) !!}
                    </div>
                </div>
            </div>
            
            <h4 style="text-align: center;" style="">ANTECEDENTES PATOLOGICOS</h4>
            <h5 style="text-align: rigth" style="">OFTALMOLOGIA</h5>

            <div class="001">
                <div class="row">
                    <div class="col-lg-10">
                        <p>Cefalea</p>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[001][respuesta]" value="si" id="rsi" onclick="mostrarFormulario001()">
                                <label class="form-check-label" for="rsi">SI</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[001][respuesta]" value="no" id="rno" onclick="ocultarFormulario001()">
                                <label class="form-check-label" for="rno">NO</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="card001" style="display: none;">   
                    <div class="col-lg-4">
                        {!! Form::label('', 'Hace cuanto') !!}
                        <div class="form-group">
                            <div class="input-group">
                                {!! Form::text('hacecuanto001', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '3', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']) !!}
                                <div class="input-group-append">
                                    {!! Form::select('periodotipo001', ['' => '', 'DIAS' => 'DIAS', 'SEMANAS' => 'SEMANAS', 'MESES' => 'MESES', 'AÑOS' => 'AÑOS'], null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <script>
                    function limpiarCampos001() {
                        document.getElementById('card001').querySelectorAll('input[type="text"]').forEach(function(input) {
                            input.value = '';
                        });
                        document.getElementById('card001').querySelectorAll('select').forEach(function(select) {
                            select.value = '';
                        });
                    }
                    function mostrarFormulario001() {
                        document.getElementById('card001').style.display = 'block';
                    }
                    function ocultarFormulario001() {
                        document.getElementById('card001').style.display = 'none';
                        limpiarCampos001();
                    }
                </script>
            </div>
            
            <div class="002">
                <div class="row odd-row">
                    <div class="col-lg-10">
                        <p>Defecto visual</p>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[002][respuesta]" value="si" id="rsi" onclick="mostrarFormulario002()">
                                <label class="form-check-label" for="rsi">SI</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[002][respuesta]" value="no" id="rno" onclick="ocultarFormulario002()">
                                <label class="form-check-label" for="rno">NO</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="card002" style="display: none;">
                    <div class="col-lg-4">
                        {!! Form::label('', 'Hace cuanto') !!}
                        <div class="form-group">
                            <div class="input-group">
                                {!! Form::text('hacecuanto002', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '3', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']) !!}
                                <div class="input-group-append">
                                    {!! Form::select('periodotipo002', ['' => '', 'DIAS' => 'DIAS', 'SEMANAS' => 'SEMANAS', 'MESES' => 'MESES', 'AÑOS' => 'AÑOS'], null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                    function limpiarCampos002() {
                        document.getElementById('card002').querySelectorAll('input[type="text"]').forEach(function(input) {
                            input.value = '';
                        });
                        document.getElementById('card002').querySelectorAll('select').forEach(function(select) {
                            select.value = '';
                        });
                    }
                    function mostrarFormulario002() {
                        document.getElementById('card002').style.display = 'block';
                    }
                    function ocultarFormulario002() {
                        document.getElementById('card002').style.display = 'none';
                        limpiarCampos002();
                    }
                </script>
            </div>

            <div class="003">
                <div class="row">
                    <div class="col-lg-10">
                        <p>Irritacion ocular</p>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[003][respuesta]" value="si" id="rsi" onclick="mostrarFormulario003()">
                                <label class="form-check-label" for="rsi">SI</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[003][respuesta]" value="no" id="rno" onclick="ocultarFormulario003()">
                                <label class="form-check-label" for="rno">NO</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="card003" style="display: none;">
                    <div class="col-lg-4">
                        {!! Form::label('', 'Hace cuanto') !!}
                        <div class="form-group">
                            <div class="input-group">
                                {!! Form::text('hacecuanto003', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '3', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']) !!}
                                <div class="input-group-append">
                                    {!! Form::select('periodotipo003', ['' => '', 'DIAS' => 'DIAS', 'SEMANAS' => 'SEMANAS', 'MESES' => 'MESES', 'AÑOS' => 'AÑOS'], null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                        </div>     
                    </div>
                </div>

                <script>
                    function limpiarCampos003() {
                        document.getElementById('card003').querySelectorAll('input[type="text"]').forEach(function(input) {
                            input.value = '';
                        });
                        document.getElementById('card003').querySelectorAll('select').forEach(function(select) {
                            select.value = '';
                        });
                    }
                    function mostrarFormulario003() {
                        document.getElementById('card003').style.display = 'block';
                    }
                    function ocultarFormulario003() {
                        document.getElementById('card003').style.display = 'none';
                        limpiarCampos003();
                    }
                </script>
            </div>

            <div class="004">
                <div class="row odd-row">
                    <div class="col-lg-10">
                        <p>Sequedad ocular</p>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[004][respuesta]" value="si" id="rsi" onclick="mostrarFormulario004()">
                                <label class="form-check-label" for="rsi">SI</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[004][respuesta]" value="no" id="rno" onclick="ocultarFormulario004()">
                                <label class="form-check-label" for="rno">NO</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="card004" style="display: none;">
                    <div class="col-lg-4">
                        {!! Form::label('', 'Hace cuanto') !!}
                        <div class="form-group">
                            <div class="input-group">
                                {!! Form::text('hacecuanto004', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '3', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']) !!}
                                <div class="input-group-append">
                                    {!! Form::select('periodotipo004', ['' => '', 'DIAS' => 'DIAS', 'SEMANAS' => 'SEMANAS', 'MESES' => 'MESES', 'AÑOS' => 'AÑOS'], null, ['class' => 'form-control']) !!}
                                </div>
                            </div>     
                        </div>
                    </div>
                </div>

                <script>
                    function limpiarCampos004() {
                        document.getElementById('card004').querySelectorAll('input[type="text"]').forEach(function(input) {
                            input.value = '';
                        });
                        document.getElementById('card004').querySelectorAll('select').forEach(function(select) {
                            select.value = '';
                        });
                    }
                    function mostrarFormulario004() {
                        document.getElementById('card004').style.display = 'block';
                    }
                    function ocultarFormulario004() {
                        document.getElementById('card004').style.display = 'none';
                        limpiarCampos004();
                    }
                </script>
            </div>

            <div class="005">
                <div class="row">
                    <div class="col-lg-10">
                        <p>Lagrimeo</p>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[005][respuesta]" value="si" id="rsi" onclick="mostrarFormulario005()">
                                <label class="form-check-label" for="rsi">SI</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[005][respuesta]" value="no" id="rno" onclick="ocultarFormulario005()">
                                <label class="form-check-label" for="rno">NO</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="card005" style="display: none;">
                    <div class="col-lg-4">
                        {!! Form::label('', 'Hace cuanto') !!}
                        <div class="form-group">
                            <div class="input-group">
                                {!! Form::text('hacecuanto005', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '3', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']) !!}
                                <div class="input-group-append">
                                    {!! Form::select('periodotipo005', ['' => '', 'DIAS' => 'DIAS', 'SEMANAS' => 'SEMANAS', 'MESES' => 'MESES', 'AÑOS' => 'AÑOS'], null, ['class' => 'form-control']) !!}
                                </div>
                            </div>     
                        </div>
                    </div>
                </div>

                <script>
                    function limpiarCampos005() {
                        document.getElementById('card005').querySelectorAll('input[type="text"]').forEach(function(input) {
                            input.value = '';
                        });
                        document.getElementById('card005').querySelectorAll('select').forEach(function(select) {
                            select.value = '';
                        });
                    }
                    function mostrarFormulario005() {
                        document.getElementById('card005').style.display = 'block';
                    }
                    function ocultarFormulario005() {
                        document.getElementById('card005').style.display = 'none';
                        limpiarCampos005();
                    }
                </script>
            </div>

            <div class="006">
                <div class="row odd-row">
                    <div class="col-lg-10">
                        <p>Vision borrosa</p>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[006][respuesta]" value="si" id="rsi" onclick="mostrarFormulario006()">
                                <label class="form-check-label" for="rsi">SI</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[006][respuesta]" value="no" id="rno" onclick="ocultarFormulario006()">
                                <label class="form-check-label" for="rno">NO</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="card006" style="display: none;">
                    <div class="col-lg-4">
                        {!! Form::label('', 'Hace cuanto') !!}
                        <div class="form-group">
                            <div class="input-group">
                                {!! Form::text('hacecuanto006', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '3', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']) !!}
                                <div class="input-group-append">
                                    {!! Form::select('periodotipo006', ['' => '', 'DIAS' => 'DIAS', 'SEMANAS' => 'SEMANAS', 'MESES' => 'MESES', 'AÑOS' => 'AÑOS'], null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                        </div>
                    </div>    
                </div>

                <script>
                    function limpiarCampos006() {
                        document.getElementById('card006').querySelectorAll('input[type="text"]').forEach(function(input) {
                            input.value = '';
                        });
                        document.getElementById('card006').querySelectorAll('select').forEach(function(select) {
                            select.value = '';
                        });
                    }
                    function mostrarFormulario006() {
                        document.getElementById('card006').style.display = 'block';
                    }
                    function ocultarFormulario006() {
                        document.getElementById('card006').style.display = 'none';
                        limpiarCampos006();
                    }
                </script>
            </div>
            
            <br><h5 style="text-align: rigth" style="">OTORRINOLARINGOLOGIA</h5>

            <div class="007">
                <div class="row">
                    <div class="col-lg-10">
                        <p>Hipoacuasia</p>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[007][respuesta]" value="si" id="rsi" onclick="mostrarFormulario007()">
                                <label class="form-check-label" for="rsi">SI</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[007][respuesta]" value="no" id="rno" onclick="ocultarFormulario007()">
                                <label class="form-check-label" for="rno">NO</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="card007" style="display: none;">
                    <div class="col-lg-4">
                        {!! Form::label('', 'Hace cuanto') !!}
                        <div class="form-group">
                            <div class="input-group">
                                {!! Form::text('hacecuanto007', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '3', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']) !!}
                                <div class="input-group-append">
                                    {!! Form::select('periodotipo007', ['' => '', 'DIAS' => 'DIAS', 'SEMANAS' => 'SEMANAS', 'MESES' => 'MESES', 'AÑOS' => 'AÑOS'], null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                        </div>
                    </div>   
                </div>

                <script>
                    function limpiarCampos007() {
                        document.getElementById('card007').querySelectorAll('input[type="text"]').forEach(function(input) {
                            input.value = '';
                        });
                        document.getElementById('card007').querySelectorAll('select').forEach(function(select) {
                            select.value = '';
                        });
                    }
                    function mostrarFormulario007() {
                        document.getElementById('card007').style.display = 'block';
                    }
                    function ocultarFormulario007() {
                        document.getElementById('card007').style.display = 'none';
                        limpiarCampos007();
                    }
                </script>
            </div>
            
            <div class="008">
                <div class="row odd-row">
                    <div class="col-lg-10">
                        <p>Otitis media</p>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[008][respuesta]" value="si" id="rsi" onclick="mostrarFormulario008()">
                                <label class="form-check-label" for="rsi">SI</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[008][respuesta]" value="no" id="rno" onclick="ocultarFormulario008()">
                                <label class="form-check-label" for="rno">NO</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="card008" style="display: none;">
                    <div class="col-lg-4">
                        {!! Form::label('', 'Hace cuanto') !!}
                        <div class="form-group">
                            <div class="input-group">
                                {!! Form::text('hacecuanto008', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '3', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']) !!}
                                <div class="input-group-append">
                                    {!! Form::select('periodotipo008', ['' => '', 'DIAS' => 'DIAS', 'SEMANAS' => 'SEMANAS', 'MESES' => 'MESES', 'AÑOS' => 'AÑOS'], null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                        </div>
                    </div>     
                </div>

                <script>
                    function limpiarCampos008() {
                        document.getElementById('card008').querySelectorAll('input[type="text"]').forEach(function(input) {
                            input.value = '';
                        });
                        document.getElementById('card008').querySelectorAll('select').forEach(function(select) {
                            select.value = '';
                        });
                    }
                    function mostrarFormulario008() {
                        document.getElementById('card008').style.display = 'block';
                    }
                    function ocultarFormulario008() {
                        document.getElementById('card008').style.display = 'none';
                        limpiarCampos008();
                    }
                </script>
            </div>

            <div class="009">
                <div class="row">
                    <div class="col-lg-10">
                        <p>Sinusitis</p>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[009][respuesta]" value="si" id="rsi" onclick="mostrarFormulario009()">
                                <label class="form-check-label" for="rsi">SI</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[009][respuesta]" value="no" id="rno" onclick="ocultarFormulario009()">
                                <label class="form-check-label" for="rno">NO</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="card009" style="display: none;">
                    <div class="col-lg-4">
                        {!! Form::label('', 'Hace cuanto') !!}
                        <div class="form-group">
                            <div class="input-group">
                                {!! Form::text('hacecuanto009', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '3', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']) !!}
                                <div class="input-group-append">
                                    {!! Form::select('periodotipo009', ['' => '', 'DIAS' => 'DIAS', 'SEMANAS' => 'SEMANAS', 'MESES' => 'MESES', 'AÑOS' => 'AÑOS'], null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                        </div>     
                    </div>
                </div>

                <script>
                    function limpiarCampos009() {
                        document.getElementById('card009').querySelectorAll('input[type="text"]').forEach(function(input) {
                            input.value = '';
                        });
                        document.getElementById('card009').querySelectorAll('select').forEach(function(select) {
                            select.value = '';
                        });
                    }
                    function mostrarFormulario009() {
                        document.getElementById('card009').style.display = 'block';
                    }
                    function ocultarFormulario009() {
                        document.getElementById('card009').style.display = 'none';
                        limpiarCampos009();
                    }
                </script>
            </div>

            <div class="010">
                <div class="row odd-row">
                    <div class="col-lg-10">
                        <p>Tinitus</p>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[010][respuesta]" value="si" id="rsi" onclick="mostrarFormulario010()">
                                <label class="form-check-label" for="rsi">SI</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[010][respuesta]" value="no" id="rno" onclick="ocultarFormulario010()">
                                <label class="form-check-label" for="rno">NO</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="card010" style="display: none;">
                    <div class="col-lg-4">
                        {!! Form::label('', 'Hace cuanto') !!}
                        <div class="form-group">
                            <div class="input-group">
                                {!! Form::text('hacecuanto010', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '3', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']) !!}
                                <div class="input-group-append">
                                    {!! Form::select('periodotipo010', ['' => '', 'DIAS' => 'DIAS', 'SEMANAS' => 'SEMANAS', 'MESES' => 'MESES', 'AÑOS' => 'AÑOS'], null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                    function limpiarCampos010() {
                        document.getElementById('card010').querySelectorAll('input[type="text"]').forEach(function(input) {
                            input.value = '';
                        });
                        document.getElementById('card010').querySelectorAll('select').forEach(function(select) {
                            select.value = '';
                        });
                    }
                    function mostrarFormulario010() {
                        document.getElementById('card010').style.display = 'block';
                    }
                    function ocultarFormulario010() {
                        document.getElementById('card010').style.display = 'none';
                        limpiarCampos010();
                    }
                </script>
            </div>

            <br><h5 style="text-align: rigth" style="">NEUROLOGIA</h5>

            <div class="011">
                <div class="row">
                    <div class="col-lg-10">
                        <p>Convulsiones</p>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[011][respuesta]" value="si" id="rsi" onclick="mostrarFormulario011()">
                                <label class="form-check-label" for="rsi">SI</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[011][respuesta]" value="no" id="rno" onclick="ocultarFormulario011()">
                                <label class="form-check-label" for="rno">NO</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="card011" style="display: none;">
                    <div class="col-lg-4">
                        {!! Form::label('', 'Hace cuanto') !!}
                        <div class="form-group">
                            <div class="input-group">
                                {!! Form::text('hacecuanto011', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '3', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']) !!}
                                <div class="input-group-append">
                                    {!! Form::select('periodotipo011', ['' => '', 'DIAS' => 'DIAS', 'SEMANAS' => 'SEMANAS', 'MESES' => 'MESES', 'AÑOS' => 'AÑOS'], null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                        </div>
                    </div>    
                </div>

                <script>
                    function limpiarCampos011() {
                        document.getElementById('card011').querySelectorAll('input[type="text"]').forEach(function(input) {
                            input.value = '';
                        });
                        document.getElementById('card011').querySelectorAll('select').forEach(function(select) {
                            select.value = '';
                        });
                    }
                    function mostrarFormulario011() {
                        document.getElementById('card011').style.display = 'block';
                    }
                    function ocultarFormulario011() {
                        document.getElementById('card011').style.display = 'none';
                        limpiarCampos011();
                    }
                </script>
            </div>

            <div class="012">
                <div class="row odd-row">
                    <div class="col-lg-10">
                        <p>Epilepsia</p>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[012][respuesta]" value="si" id="rsi" onclick="mostrarFormulario012()">
                                <label class="form-check-label" for="rsi">SI</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[012][respuesta]" value="no" id="rno" onclick="ocultarFormulario012()">
                                <label class="form-check-label" for="rno">NO</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="card012" style="display: none;">
                    <div class="col-lg-4">
                        {!! Form::label('', 'Hace cuanto') !!}
                        <div class="form-group">
                            <div class="input-group">
                                {!! Form::text('hacecuanto012', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '3', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']) !!}
                                <div class="input-group-append">
                                    {!! Form::select('periodotipo012', ['' => '', 'DIAS' => 'DIAS', 'SEMANAS' => 'SEMANAS', 'MESES' => 'MESES', 'AÑOS' => 'AÑOS'], null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                        </div>
                    </div> 
                </div>

                <script>
                    function limpiarCampos012() {
                        document.getElementById('card012').querySelectorAll('input[type="text"]').forEach(function(input) {
                            input.value = '';
                        });
                        document.getElementById('card012').querySelectorAll('select').forEach(function(select) {
                            select.value = '';
                        });
                    }
                    function mostrarFormulario012() {
                        document.getElementById('card012').style.display = 'block';
                    }
                    function ocultarFormulario012() {
                        document.getElementById('card012').style.display = 'none';
                        limpiarCampos012();
                    }
                </script>
            </div>

            <div class="013">
                <div class="row">
                    <div class="col-lg-10">
                        <p>Lumbalgia</p>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[013][respuesta]" value="si" id="rsi" onclick="mostrarFormulario013()">
                                <label class="form-check-label" for="rsi">SI</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[013][respuesta]" value="no" id="rno" onclick="ocultarFormulario013()">
                                <label class="form-check-label" for="rno">NO</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="card013" style="display: none;">
                    <div class="col-lg-4">
                        {!! Form::label('', 'Hace cuanto') !!}
                        <div class="form-group">
                            <div class="input-group">
                                {!! Form::text('hacecuanto013', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '3', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']) !!}
                                <div class="input-group-append">
                                    {!! Form::select('periodotipo013', ['' => '', 'DIAS' => 'DIAS', 'SEMANAS' => 'SEMANAS', 'MESES' => 'MESES', 'AÑOS' => 'AÑOS'], null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                        </div>
                    </div> 
                </div>

                <script>
                    function limpiarCampos013() {
                        document.getElementById('card013').querySelectorAll('input[type="text"]').forEach(function(input) {
                            input.value = '';
                        });
                        document.getElementById('card013').querySelectorAll('select').forEach(function(select) {
                            select.value = '';
                        });
                    }
                    function mostrarFormulario013() {
                        document.getElementById('card013').style.display = 'block';
                    }
                    function ocultarFormulario013() {
                        document.getElementById('card013').style.display = 'none';
                        limpiarCampos013();
                    }
                </script>
            </div>

            <div class="014">
                <div class="row odd-row">
                    <div class="col-lg-10">
                        <p>Neuropatia</p>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[014][respuesta]" value="si" id="rsi" onclick="mostrarFormulario014()">
                                <label class="form-check-label" for="rsi">SI</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[014][respuesta]" value="no" id="rno" onclick="ocultarFormulario014()">
                                <label class="form-check-label" for="rno">NO</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="card014" style="display: none;">
                    <div class="col-lg-4">
                        {!! Form::label('', 'Hace cuanto') !!}
                        <div class="form-group">
                            <div class="input-group">
                                {!! Form::text('hacecuanto014', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '3', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']) !!}
                                <div class="input-group-append">
                                    {!! Form::select('periodotipo014', ['' => '', 'DIAS' => 'DIAS', 'SEMANAS' => 'SEMANAS', 'MESES' => 'MESES', 'AÑOS' => 'AÑOS'], null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                        </div>
                    </div> 
                </div>

                <script>
                    function limpiarCampos014() {
                        document.getElementById('card014').querySelectorAll('input[type="text"]').forEach(function(input) {
                            input.value = '';
                        });
                        document.getElementById('card014').querySelectorAll('select').forEach(function(select) {
                            select.value = '';
                        });
                    }
                    function mostrarFormulario014() {
                        document.getElementById('card014').style.display = 'block';
                    }
                    function ocultarFormulario014() {
                        document.getElementById('card014').style.display = 'none';
                        limpiarCampos014();
                    }
                </script>
            </div>

            <div class="015">
                <div class="row">
                    <div class="col-lg-10">
                        <p>ACV</p>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[015][respuesta]" value="si" id="rsi" onclick="mostrarFormulario015()">
                                <label class="form-check-label" for="rsi">SI</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[015][respuesta]" value="no" id="rno" onclick="ocultarFormulario015()">
                                <label class="form-check-label" for="rno">NO</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="card015" style="display: none;">
                    <div class="col-lg-4">
                        {!! Form::label('', 'Hace cuanto') !!}
                        <div class="form-group">
                            <div class="input-group">
                                {!! Form::text('hacecuanto015', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '3', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']) !!}
                                <div class="input-group-append">
                                    {!! Form::select('periodotipo015', ['' => '', 'DIAS' => 'DIAS', 'SEMANAS' => 'SEMANAS', 'MESES' => 'MESES', 'AÑOS' => 'AÑOS'], null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                        </div>
                    </div> 
                </div>

                <script>
                    function limpiarCampos015() {
                        document.getElementById('card015').querySelectorAll('input[type="text"]').forEach(function(input) {
                            input.value = '';
                        });
                        document.getElementById('card015').querySelectorAll('select').forEach(function(select) {
                            select.value = '';
                        });
                    }
                    function mostrarFormulario015() {
                        document.getElementById('card015').style.display = 'block';
                    }
                    function ocultarFormulario015() {
                        document.getElementById('card015').style.display = 'none';
                        limpiarCampos015();
                    }
                </script>
            </div>

            <div class="016">
                <div class="row odd-row">
                    <div class="col-lg-10">
                        <p>Cefaleas</p>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[016][respuesta]" value="si" id="rsi" onclick="mostrarFormulario016()">
                                <label class="form-check-label" for="rsi">SI</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[016][respuesta]" value="no" id="rno" onclick="ocultarFormulario016()">
                                <label class="form-check-label" for="rno">NO</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="card016" style="display: none;">
                    <div class="col-lg-4">
                        {!! Form::label('', 'Hace cuanto') !!}
                        <div class="form-group">
                            <div class="input-group">
                                {!! Form::text('hacecuanto016', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '3', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']) !!}
                                <div class="input-group-append">
                                    {!! Form::select('periodotipo016', ['' => '', 'DIAS' => 'DIAS', 'SEMANAS' => 'SEMANAS', 'MESES' => 'MESES', 'AÑOS' => 'AÑOS'], null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                        </div>
                    </div> 
                </div>

                <script>
                    function limpiarCampos016() {
                        document.getElementById('card016').querySelectorAll('input[type="text"]').forEach(function(input) {
                            input.value = '';
                        });
                        document.getElementById('card016').querySelectorAll('select').forEach(function(select) {
                            select.value = '';
                        });
                    }
                    function mostrarFormulario016() {
                        document.getElementById('card016').style.display = 'block';
                    }
                    function ocultarFormulario016() {
                        document.getElementById('card016').style.display = 'none';
                        limpiarCampos016();
                    }
                </script>
            </div>

            <div class="017">
                <div class="row">
                    <div class="col-lg-10">
                        <p>Disformia muscular</p>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[017][respuesta]" value="si" id="rsi" onclick="mostrarFormulario017()">
                                <label class="form-check-label" for="rsi">SI</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[017][respuesta]" value="no" id="rno" onclick="ocultarFormulario017()">
                                <label class="form-check-label" for="rno">NO</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="card017" style="display: none;">
                    <div class="col-lg-4">
                        {!! Form::label('', 'Hace cuanto') !!}
                        <div class="form-group">
                            <div class="input-group">
                                {!! Form::text('hacecuanto017', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '3', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']) !!}
                                <div class="input-group-append">
                                    {!! Form::select('periodotipo017', ['' => '', 'DIAS' => 'DIAS', 'SEMANAS' => 'SEMANAS', 'MESES' => 'MESES', 'AÑOS' => 'AÑOS'], null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                        </div>
                    </div> 
                </div>

                <script>
                    function limpiarCampos017() {
                        document.getElementById('card017').querySelectorAll('input[type="text"]').forEach(function(input) {
                            input.value = '';
                        });
                        document.getElementById('card017').querySelectorAll('select').forEach(function(select) {
                            select.value = '';
                        });
                    }
                    function mostrarFormulario017() {
                        document.getElementById('card017').style.display = 'block';
                    }
                    function ocultarFormulario017() {
                        document.getElementById('card017').style.display = 'none';
                        limpiarCampos017();
                    }
                </script>
            </div>

            <div class="018">
                <div class="row odd-row">
                    <div class="col-lg-10">
                        <p>Lesion en medula espinal</p>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[018][respuesta]" value="si" id="rsi" onclick="mostrarFormulario018()">
                                <label class="form-check-label" for="rsi">SI</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[018][respuesta]" value="no" id="rno" onclick="ocultarFormulario018()">
                                <label class="form-check-label" for="rno">NO</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="card018" style="display: none;">
                    <div class="col-lg-4">
                        {!! Form::label('', 'Hace cuanto') !!}
                        <div class="form-group">
                            <div class="input-group">
                                {!! Form::text('hacecuanto018', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '3', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']) !!}
                                <div class="input-group-append">
                                    {!! Form::select('periodotipo018', ['' => '', 'DIAS' => 'DIAS', 'SEMANAS' => 'SEMANAS', 'MESES' => 'MESES', 'AÑOS' => 'AÑOS'], null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                        </div>
                    </div> 
                </div>

                <script>
                    function limpiarCampos018() {
                        document.getElementById('card018').querySelectorAll('input[type="text"]').forEach(function(input) {
                            input.value = '';
                        });
                        document.getElementById('card018').querySelectorAll('select').forEach(function(select) {
                            select.value = '';
                        });
                    }
                    function mostrarFormulario018() {
                        document.getElementById('card018').style.display = 'block';
                    }
                    function ocultarFormulario018() {
                        document.getElementById('card018').style.display = 'none';
                        limpiarCampos018();
                    }
                </script>
            </div>

            <br><h5 style="text-align: rigth" style="">CARDIOLOGIA</h5>

            <div class="019">
                <div class="row">
                    <div class="col-lg-10">
                        <p>HTA</p>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[019][respuesta]" value="si" id="rsi" onclick="mostrarFormulario019()">
                                <label class="form-check-label" for="rsi">SI</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[019][respuesta]" value="no" id="rno" onclick="ocultarFormulario019()">
                                <label class="form-check-label" for="rno">NO</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="card019" style="display: none;">
                    <div class="col-lg-4">
                        {!! Form::label('', 'Hace cuanto') !!}
                        <div class="form-group">
                            <div class="input-group">
                                {!! Form::text('hacecuanto019', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '3', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']) !!}
                                <div class="input-group-append">
                                    {!! Form::select('periodotipo019', ['' => '', 'DIAS' => 'DIAS', 'SEMANAS' => 'SEMANAS', 'MESES' => 'MESES', 'AÑOS' => 'AÑOS'], null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                        </div>
                    </div>   
                </div>
                <script>
                    function limpiarCampos019() {
                        document.getElementById('card019').querySelectorAll('input[type="text"]').forEach(function(input) {
                            input.value = '';
                        });
                        document.getElementById('card019').querySelectorAll('select').forEach(function(select) {
                            select.value = '';
                        });
                    }
                    function mostrarFormulario019() {
                        document.getElementById('card019').style.display = 'block';
                    }
                    function ocultarFormulario019() {
                        document.getElementById('card019').style.display = 'none';
                        limpiarCampos019();
                    }
                </script>
            </div>
            
            <div class="020">
                <div class="row odd-row">
                    <div class="col-lg-10">
                        <p>Arritmia</p>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[020][respuesta]" value="si" id="rsi" onclick="mostrarFormulario020()">
                                <label class="form-check-label" for="rsi">SI</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[020][respuesta]" value="no" id="rno" onclick="ocultarFormulario020()">
                                <label class="form-check-label" for="rno">NO</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="card020" style="display: none;">
                    <div class="col-lg-4">
                        {!! Form::label('', 'Hace cuanto') !!}
                        <div class="form-group">
                            <div class="input-group">
                                {!! Form::text('hacecuanto020', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '3', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']) !!}
                                <div class="input-group-append">
                                    {!! Form::select('periodotipo020', ['' => '', 'DIAS' => 'DIAS', 'SEMANAS' => 'SEMANAS', 'MESES' => 'MESES', 'AÑOS' => 'AÑOS'], null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                        </div>
                    </div>   
                </div>
                <script>
                    function limpiarCampos020() {
                        document.getElementById('card020').querySelectorAll('input[type="text"]').forEach(function(input) {
                            input.value = '';
                        });
                        document.getElementById('card020').querySelectorAll('select').forEach(function(select) {
                            select.value = '';
                        });
                    }
                    function mostrarFormulario020() {
                        document.getElementById('card020').style.display = 'block';
                    }
                    function ocultarFormulario020() {
                        document.getElementById('card020').style.display = 'none';
                        limpiarCampos020();
                    }
                </script>
            </div>

            <div class="021">
                <div class="row">
                    <div class="col-lg-10">
                        <p>Chagas</p>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[021][respuesta]" value="si" id="rsi" onclick="mostrarFormulario021()">
                                <label class="form-check-label" for="rsi">SI</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[021][respuesta]" value="no" id="rno" onclick="ocultarFormulario021()">
                                <label class="form-check-label" for="rno">NO</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="card021" style="display: none;">
                    <div class="col-lg-4">
                        {!! Form::label('', 'Hace cuanto') !!}
                        <div class="form-group">
                            <div class="input-group">
                                {!! Form::text('hacecuanto021', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '3', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']) !!}
                                <div class="input-group-append">
                                    {!! Form::select('periodotipo021', ['' => '', 'DIAS' => 'DIAS', 'SEMANAS' => 'SEMANAS', 'MESES' => 'MESES', 'AÑOS' => 'AÑOS'], null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                        </div>
                    </div>   
                </div>
                <script>
                    function limpiarCampos021() {
                        document.getElementById('card021').querySelectorAll('input[type="text"]').forEach(function(input) {
                            input.value = '';
                        });
                        document.getElementById('card021').querySelectorAll('select').forEach(function(select) {
                            select.value = '';
                        });
                    }
                    function mostrarFormulario021() {
                        document.getElementById('card021').style.display = 'block';
                    }
                    function ocultarFormulario021() {
                        document.getElementById('card021').style.display = 'none';
                        limpiarCampos021();
                    }
                </script>
            </div>

            <div class="022">
                <div class="row odd-row">
                    <div class="col-lg-10">
                        <p>Taquicardia</p>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[022][respuesta]" value="si" id="rsi" onclick="mostrarFormulario022()">
                                <label class="form-check-label" for="rsi">SI</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[022][respuesta]" value="no" id="rno" onclick="ocultarFormulario022()">
                                <label class="form-check-label" for="rno">NO</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="card022" style="display: none;">
                    <div class="col-lg-4">
                        {!! Form::label('', 'Hace cuanto') !!}
                        <div class="form-group">
                            <div class="input-group">
                                {!! Form::text('hacecuanto022', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '3', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']) !!}
                                <div class="input-group-append">
                                    {!! Form::select('periodotipo022', ['' => '', 'DIAS' => 'DIAS', 'SEMANAS' => 'SEMANAS', 'MESES' => 'MESES', 'AÑOS' => 'AÑOS'], null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                        </div>
                    </div>   
                </div>
                <script>
                    function limpiarCampos022() {
                        document.getElementById('card022').querySelectorAll('input[type="text"]').forEach(function(input) {
                            input.value = '';
                        });
                        document.getElementById('card022').querySelectorAll('select').forEach(function(select) {
                            select.value = '';
                        });
                    }
                    function mostrarFormulario022() {
                        document.getElementById('card022').style.display = 'block';
                    }
                    function ocultarFormulario022() {
                        document.getElementById('card022').style.display = 'none';
                        limpiarCampos022();
                    }
                </script>
            </div>

            <div class="023">
                <div class="row">
                    <div class="col-lg-10">
                        <p>Bradicardia</p>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[023][respuesta]" value="si" id="rsi" onclick="mostrarFormulario023()">
                                <label class="form-check-label" for="rsi">SI</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[023][respuesta]" value="no" id="rno" onclick="ocultarFormulario023()">
                                <label class="form-check-label" for="rno">NO</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="card023" style="display: none;">
                    <div class="col-lg-4">
                        {!! Form::label('', 'Hace cuanto') !!}
                        <div class="form-group">
                            <div class="input-group">
                                {!! Form::text('hacecuanto023', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '3', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']) !!}
                                <div class="input-group-append">
                                    {!! Form::select('periodotipo023', ['' => '', 'DIAS' => 'DIAS', 'SEMANAS' => 'SEMANAS', 'MESES' => 'MESES', 'AÑOS' => 'AÑOS'], null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                        </div>
                    </div>   
                </div>
                <script>
                    function limpiarCampos023() {
                        document.getElementById('card023').querySelectorAll('input[type="text"]').forEach(function(input) {
                            input.value = '';
                        });
                        document.getElementById('card023').querySelectorAll('select').forEach(function(select) {
                            select.value = '';
                        });
                    }
                    function mostrarFormulario023() {
                        document.getElementById('card023').style.display = 'block';
                    }
                    function ocultarFormulario023() {
                        document.getElementById('card023').style.display = 'none';
                        limpiarCampos023();
                    }
                </script>
            </div>

            <div class="024">
                <div class="row odd-row">
                    <div class="col-lg-10">
                        <p>Bloqueo de rama</p>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[024][respuesta]" value="si" id="rsi" onclick="mostrarFormulario024()">
                                <label class="form-check-label" for="rsi">SI</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[024][respuesta]" value="no" id="rno" onclick="ocultarFormulario024()">
                                <label class="form-check-label" for="rno">NO</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="card024" style="display: none;">
                    <div class="col-lg-4">
                        {!! Form::label('', 'Hace cuanto') !!}
                        <div class="form-group">
                            <div class="input-group">
                                {!! Form::text('hacecuanto024', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '3', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']) !!}
                                <div class="input-group-append">
                                    {!! Form::select('periodotipo024', ['' => '', 'DIAS' => 'DIAS', 'SEMANAS' => 'SEMANAS', 'MESES' => 'MESES', 'AÑOS' => 'AÑOS'], null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                        </div>
                    </div>   
                </div>
                <script>
                    function limpiarCampos024() {
                        document.getElementById('card024').querySelectorAll('input[type="text"]').forEach(function(input) {
                            input.value = '';
                        });
                        document.getElementById('card024').querySelectorAll('select').forEach(function(select) {
                            select.value = '';
                        });
                    }
                    function mostrarFormulario024() {
                        document.getElementById('card024').style.display = 'block';
                    }
                    function ocultarFormulario024() {
                        document.getElementById('card024').style.display = 'none';
                        limpiarCampos024();
                    }
                </script>
            </div>

            <div class="025">
                <div class="row">
                    <div class="col-lg-10">
                        <p>Stent coronario</p>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[025][respuesta]" value="si" id="rsi" onclick="mostrarFormulario025()">
                                <label class="form-check-label" for="rsi">SI</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[025][respuesta]" value="no" id="rno" onclick="ocultarFormulario025()">
                                <label class="form-check-label" for="rno">NO</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="card025" style="display: none;">
                    <div class="col-lg-4">
                        {!! Form::label('', 'Hace cuanto') !!}
                        <div class="form-group">
                            <div class="input-group">
                                {!! Form::text('hacecuanto025', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '3', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']) !!}
                                <div class="input-group-append">
                                    {!! Form::select('periodotipo025', ['' => '', 'DIAS' => 'DIAS', 'SEMANAS' => 'SEMANAS', 'MESES' => 'MESES', 'AÑOS' => 'AÑOS'], null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                        </div>
                    </div>   
                </div>
                <script>
                    function limpiarCampos025() {
                        document.getElementById('card025').querySelectorAll('input[type="text"]').forEach(function(input) {
                            input.value = '';
                        });
                        document.getElementById('card025').querySelectorAll('select').forEach(function(select) {
                            select.value = '';
                        });
                    }
                    function mostrarFormulario025() {
                        document.getElementById('card025').style.display = 'block';
                    }
                    function ocultarFormulario025() {
                        document.getElementById('card025').style.display = 'none';
                        limpiarCampos025();
                    }
                </script>
            </div>

            <div class="026">
                <div class="row odd-row">
                    <div class="col-lg-10">
                        <p>Marcapaso</p>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[026][respuesta]" value="si" id="rsi" onclick="mostrarFormulario026()">
                                <label class="form-check-label" for="rsi">SI</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[026][respuesta]" value="no" id="rno" onclick="ocultarFormulario026()">
                                <label class="form-check-label" for="rno">NO</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="card026" style="display: none;">
                    <div class="col-lg-4">
                        {!! Form::label('', 'Hace cuanto') !!}
                        <div class="form-group">
                            <div class="input-group">
                                {!! Form::text('hacecuanto026', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '3', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']) !!}
                                <div class="input-group-append">
                                    {!! Form::select('periodotipo026', ['' => '', 'DIAS' => 'DIAS', 'SEMANAS' => 'SEMANAS', 'MESES' => 'MESES', 'AÑOS' => 'AÑOS'], null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                        </div>
                    </div>   
                </div>
                <script>
                    function limpiarCampos026() {
                        document.getElementById('card026').querySelectorAll('input[type="text"]').forEach(function(input) {
                            input.value = '';
                        });
                        document.getElementById('card026').querySelectorAll('select').forEach(function(select) {
                            select.value = '';
                        });
                    }
                    function mostrarFormulario026() {
                        document.getElementById('card026').style.display = 'block';
                    }
                    function ocultarFormulario026() {
                        document.getElementById('card026').style.display = 'none';
                        limpiarCampos026();
                    }
                </script>
            </div>

            <br><h5 style="text-align: rigth" style="">ENDOCRINOLOGIA</h5>

            <div class="027">
                <div class="row">
                    <div class="col-lg-10">
                        <p>DMT2</p>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[027][respuesta]" value="si" id="rsi" onclick="mostrarFormulario027()">
                                <label class="form-check-label" for="rsi">SI</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[027][respuesta]" value="no" id="rno" onclick="ocultarFormulario027()">
                                <label class="form-check-label" for="rno">NO</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="card027" style="display: none;">
                    <div class="col-lg-4">
                        {!! Form::label('', 'Hace cuanto') !!}
                        <div class="form-group">
                            <div class="input-group">
                                {!! Form::text('hacecuanto027', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '3', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']) !!}
                                <div class="input-group-append">
                                    {!! Form::select('periodotipo027', ['' => '', 'DIAS' => 'DIAS', 'SEMANAS' => 'SEMANAS', 'MESES' => 'MESES', 'AÑOS' => 'AÑOS'], null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                        </div>
                    </div>   
                </div>
                <script>
                    function limpiarCampos027() {
                        document.getElementById('card027').querySelectorAll('input[type="text"]').forEach(function(input) {
                            input.value = '';
                        });
                        document.getElementById('card027').querySelectorAll('select').forEach(function(select) {
                            select.value = '';
                        });
                    }
                    function mostrarFormulario027() {
                        document.getElementById('card027').style.display = 'block';
                    }
                    function ocultarFormulario027() {
                        document.getElementById('card027').style.display = 'none';
                        limpiarCampos027();
                    }
                </script>
            </div>

            <div class="028">
                <div class="row odd-row">
                    <div class="col-lg-10">
                        <p>Lupus eritematoso</p>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[028][respuesta]" value="si" id="rsi" onclick="mostrarFormulario028()">
                                <label class="form-check-label" for="rsi">SI</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[028][respuesta]" value="no" id="rno" onclick="ocultarFormulario028()">
                                <label class="form-check-label" for="rno">NO</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="card028" style="display: none;">
                    <div class="col-lg-4">
                        {!! Form::label('', 'Hace cuanto') !!}
                        <div class="form-group">
                            <div class="input-group">
                                {!! Form::text('hacecuanto028', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '3', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']) !!}
                                <div class="input-group-append">
                                    {!! Form::select('periodotipo028', ['' => '', 'DIAS' => 'DIAS', 'SEMANAS' => 'SEMANAS', 'MESES' => 'MESES', 'AÑOS' => 'AÑOS'], null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                        </div>
                    </div>   
                </div>
                <script>
                    function limpiarCampos028() {
                        document.getElementById('card028').querySelectorAll('input[type="text"]').forEach(function(input) {
                            input.value = '';
                        });
                        document.getElementById('card028').querySelectorAll('select').forEach(function(select) {
                            select.value = '';
                        });
                    }
                    function mostrarFormulario028() {
                        document.getElementById('card028').style.display = 'block';
                    }
                    function ocultarFormulario028() {
                        document.getElementById('card028').style.display = 'none';
                        limpiarCampos028();
                    }
                </script>
            </div>

            <div class="029">
                <div class="row">
                    <div class="col-lg-10">
                        <p>Colesterol elevado</p>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[029][respuesta]" value="si" id="rsi" onclick="mostrarFormulario029()">
                                <label class="form-check-label" for="rsi">SI</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[029][respuesta]" value="no" id="rno" onclick="ocultarFormulario029()">
                                <label class="form-check-label" for="rno">NO</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="card029" style="display: none;">
                    <div class="col-lg-4">
                        {!! Form::label('', 'Hace cuanto') !!}
                        <div class="form-group">
                            <div class="input-group">
                                {!! Form::text('hacecuanto029', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '3', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']) !!}
                                <div class="input-group-append">
                                    {!! Form::select('periodotipo029', ['' => '', 'DIAS' => 'DIAS', 'SEMANAS' => 'SEMANAS', 'MESES' => 'MESES', 'AÑOS' => 'AÑOS'], null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                        </div>
                    </div>   
                </div>
                <script>
                    function limpiarCampos029() {
                        document.getElementById('card029').querySelectorAll('input[type="text"]').forEach(function(input) {
                            input.value = '';
                        });
                        document.getElementById('card029').querySelectorAll('select').forEach(function(select) {
                            select.value = '';
                        });
                    }
                    function mostrarFormulario029() {
                        document.getElementById('card029').style.display = 'block';
                    }
                    function ocultarFormulario029() {
                        document.getElementById('card029').style.display = 'none';
                        limpiarCampos029();
                    }
                </script>
            </div>

            <div class="030">
                <div class="row odd-row">
                    <div class="col-lg-10">
                        <p>Hipotiroidismo</p>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[030][respuesta]" value="si" id="rsi" onclick="mostrarFormulario030()">
                                <label class="form-check-label" for="rsi">SI</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[030][respuesta]" value="no" id="rno" onclick="ocultarFormulario030()">
                                <label class="form-check-label" for="rno">NO</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="card030" style="display: none;">
                    <div class="col-lg-4">
                        {!! Form::label('', 'Hace cuanto') !!}
                        <div class="form-group">
                            <div class="input-group">
                                {!! Form::text('hacecuanto030', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '3', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']) !!}
                                <div class="input-group-append">
                                    {!! Form::select('periodotipo030', ['' => '', 'DIAS' => 'DIAS', 'SEMANAS' => 'SEMANAS', 'MESES' => 'MESES', 'AÑOS' => 'AÑOS'], null, ['class' => 'form-control']) !!}
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

            <div class="031">
                <div class="row">
                    <div class="col-lg-10">
                        <p>Hipertiroidismo</p>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[031][respuesta]" value="si" id="rsi" onclick="mostrarFormulario031()">
                                <label class="form-check-label" for="rsi">SI</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[031][respuesta]" value="no" id="rno" onclick="ocultarFormulario031()">
                                <label class="form-check-label" for="rno">NO</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="card031" style="display: none;">
                    <div class="col-lg-4">
                        {!! Form::label('', 'Hace cuanto') !!}
                        <div class="form-group">
                            <div class="input-group">
                                {!! Form::text('hacecuanto031', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '3', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']) !!}
                                <div class="input-group-append">
                                    {!! Form::select('periodotipo031', ['' => '', 'DIAS' => 'DIAS', 'SEMANAS' => 'SEMANAS', 'MESES' => 'MESES', 'AÑOS' => 'AÑOS'], null, ['class' => 'form-control']) !!}
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

            <br><h5 style="text-align: rigth" style="">TRAUMATOLOGIA</h5>

            <div class="032">
                <div class="row odd-row">
                    <div class="col-lg-10">
                        <p>Artritis</p>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[032][respuesta]" value="si" id="rsi" onclick="mostrarFormulario032()">
                                <label class="form-check-label" for="rsi">SI</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[032][respuesta]" value="no" id="rno" onclick="ocultarFormulario032()">
                                <label class="form-check-label" for="rno">NO</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="card032" style="display: none;">
                    <div class="col-lg-4">
                        {!! Form::label('', 'Hace cuanto') !!}
                        <div class="form-group">
                            <div class="input-group">
                                {!! Form::text('hacecuanto032', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '3', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']) !!}
                                <div class="input-group-append">
                                    {!! Form::select('periodotipo032', ['' => '', 'DIAS' => 'DIAS', 'SEMANAS' => 'SEMANAS', 'MESES' => 'MESES', 'AÑOS' => 'AÑOS'], null, ['class' => 'form-control']) !!}
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

            <div class="033">
                <div class="row">
                    <div class="col-lg-10">
                        <p>Dolores articulares</p>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[033][respuesta]" value="si" id="rsi" onclick="mostrarFormulario033()">
                                <label class="form-check-label" for="rsi">SI</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[033][respuesta]" value="no" id="rno" onclick="ocultarFormulario033()">
                                <label class="form-check-label" for="rno">NO</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="card033" style="display: none;">
                    <div class="col-lg-4">
                        {!! Form::label('', 'Hace cuanto') !!}
                        <div class="form-group">
                            <div class="input-group">
                                {!! Form::text('hacecuanto033', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '3', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']) !!}
                                <div class="input-group-append">
                                    {!! Form::select('periodotipo033', ['' => '', 'DIAS' => 'DIAS', 'SEMANAS' => 'SEMANAS', 'MESES' => 'MESES', 'AÑOS' => 'AÑOS'], null, ['class' => 'form-control']) !!}
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

            <div class="034">
                <div class="row odd-row">
                    <div class="col-lg-10">
                        <p>Lumbalgia</p>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[034][respuesta]" value="si" id="rsi" onclick="mostrarFormulario034()">
                                <label class="form-check-label" for="rsi">SI</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[034][respuesta]" value="no" id="rno" onclick="ocultarFormulario034()">
                                <label class="form-check-label" for="rno">NO</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="card034" style="display: none;">
                    <div class="col-lg-4">
                        {!! Form::label('', 'Hace cuanto') !!}
                        <div class="form-group">
                            <div class="input-group">
                                {!! Form::text('hacecuanto034', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '3', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']) !!}
                                <div class="input-group-append">
                                    {!! Form::select('periodotipo034', ['' => '', 'DIAS' => 'DIAS', 'SEMANAS' => 'SEMANAS', 'MESES' => 'MESES', 'AÑOS' => 'AÑOS'], null, ['class' => 'form-control']) !!}
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

            <div class="035">
                <div class="row">
                    <div class="col-lg-10">
                        <p>Cervicalgia</p>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[035][respuesta]" value="si" id="rsi" onclick="mostrarFormulario035()">
                                <label class="form-check-label" for="rsi">SI</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[035][respuesta]" value="no" id="rno" onclick="ocultarFormulario035()">
                                <label class="form-check-label" for="rno">NO</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="card035" style="display: none;">
                    <div class="col-lg-4">
                        {!! Form::label('', 'Hace cuanto') !!}
                        <div class="form-group">
                            <div class="input-group">
                                {!! Form::text('hacecuanto035', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '3', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']) !!}
                                <div class="input-group-append">
                                    {!! Form::select('periodotipo035', ['' => '', 'DIAS' => 'DIAS', 'SEMANAS' => 'SEMANAS', 'MESES' => 'MESES', 'AÑOS' => 'AÑOS'], null, ['class' => 'form-control']) !!}
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

            <div class="036">
                <div class="row odd-row">
                    <div class="col-lg-10">
                        <p>Dorsalgia</p>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[036][respuesta]" value="si" id="rsi" onclick="mostrarFormulario036()">
                                <label class="form-check-label" for="rsi">SI</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[036][respuesta]" value="no" id="rno" onclick="ocultarFormulario036()">
                                <label class="form-check-label" for="rno">NO</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="card036" style="display: none;">
                    <div class="col-lg-4">
                        {!! Form::label('', 'Hace cuanto') !!}
                        <div class="form-group">
                            <div class="input-group">
                                {!! Form::text('hacecuanto036', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '3', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']) !!}
                                <div class="input-group-append">
                                    {!! Form::select('periodotipo036', ['' => '', 'DIAS' => 'DIAS', 'SEMANAS' => 'SEMANAS', 'MESES' => 'MESES', 'AÑOS' => 'AÑOS'], null, ['class' => 'form-control']) !!}
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

            <div class="037">
                <div class="row">
                    <div class="col-lg-10">
                        <p>Silicosis</p>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[037][respuesta]" value="si" id="rsi" onclick="mostrarFormulario037()">
                                <label class="form-check-label" for="rsi">SI</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[037][respuesta]" value="no" id="rno" onclick="ocultarFormulario037()">
                                <label class="form-check-label" for="rno">NO</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="card037" style="display: none;">
                    <div class="col-lg-4">
                        {!! Form::label('', 'Hace cuanto') !!}
                        <div class="form-group">
                            <div class="input-group">
                                {!! Form::text('hacecuanto037', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '3', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']) !!}
                                <div class="input-group-append">
                                    {!! Form::select('periodotipo037', ['' => '', 'DIAS' => 'DIAS', 'SEMANAS' => 'SEMANAS', 'MESES' => 'MESES', 'AÑOS' => 'AÑOS'], null, ['class' => 'form-control']) !!}
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

            <br><h5 style="text-align: rigth" style="">NEUMOLOGIA</h5>

            <div class="038">
                <div class="row odd-row">
                    <div class="col-lg-10">
                        <p>Bronquitis</p>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[038][respuesta]" value="si" id="rsi" onclick="mostrarFormulario038()">
                                <label class="form-check-label" for="rsi">SI</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[038][respuesta]" value="no" id="rno" onclick="ocultarFormulario038()">
                                <label class="form-check-label" for="rno">NO</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="card038" style="display: none;">
                    <div class="col-lg-4">
                        {!! Form::label('', 'Hace cuanto') !!}
                        <div class="form-group">
                            <div class="input-group">
                                {!! Form::text('hacecuanto038', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '3', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']) !!}
                                <div class="input-group-append">
                                    {!! Form::select('periodotipo038', ['' => '', 'DIAS' => 'DIAS', 'SEMANAS' => 'SEMANAS', 'MESES' => 'MESES', 'AÑOS' => 'AÑOS'], null, ['class' => 'form-control']) !!}
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

            <div class="039">
                <div class="row">
                    <div class="col-lg-10">
                        <p>Asma</p>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[039][respuesta]" value="si" id="rsi" onclick="mostrarFormulario039()">
                                <label class="form-check-label" for="rsi">SI</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[039][respuesta]" value="no" id="rno" onclick="ocultarFormulario039()">
                                <label class="form-check-label" for="rno">NO</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="card039" style="display: none;">
                    <div class="col-lg-4">
                        {!! Form::label('', 'Hace cuanto') !!}
                        <div class="form-group">
                            <div class="input-group">
                                {!! Form::text('hacecuanto039', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '3', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']) !!}
                                <div class="input-group-append">
                                    {!! Form::select('periodotipo039', ['' => '', 'DIAS' => 'DIAS', 'SEMANAS' => 'SEMANAS', 'MESES' => 'MESES', 'AÑOS' => 'AÑOS'], null, ['class' => 'form-control']) !!}
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
            
            <div class="040">
                <div class="row odd-row">
                    <div class="col-lg-10">
                        <p>Tuberculosis</p>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[040][respuesta]" value="si" id="rsi" onclick="mostrarFormulario040()">
                                <label class="form-check-label" for="rsi">SI</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[040][respuesta]" value="no" id="rno" onclick="ocultarFormulario040()">
                                <label class="form-check-label" for="rno">NO</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="card040" style="display: none;">
                    <div class="col-lg-4">
                        {!! Form::label('', 'Hace cuanto') !!}
                        <div class="form-group">
                            <div class="input-group">
                                {!! Form::text('hacecuanto040', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '3', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']) !!}
                                <div class="input-group-append">
                                    {!! Form::select('periodotipo040', ['' => '', 'DIAS' => 'DIAS', 'SEMANAS' => 'SEMANAS', 'MESES' => 'MESES', 'AÑOS' => 'AÑOS'], null, ['class' => 'form-control']) !!}
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

            <div class="041">
                <div class="row">
                    <div class="col-lg-10">
                        <p>EPOC</p>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[041][respuesta]" value="si" id="rsi" onclick="mostrarFormulario041()">
                                <label class="form-check-label" for="rsi">SI</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[041][respuesta]" value="no" id="rno" onclick="ocultarFormulario041()">
                                <label class="form-check-label" for="rno">NO</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="card041" style="display: none;">
                    <div class="col-lg-4">
                        {!! Form::label('', 'Hace cuanto') !!}
                        <div class="form-group">
                            <div class="input-group">
                                {!! Form::text('hacecuanto041', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '3', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']) !!}
                                <div class="input-group-append">
                                    {!! Form::select('periodotipo041', ['' => '', 'DIAS' => 'DIAS', 'SEMANAS' => 'SEMANAS', 'MESES' => 'MESES', 'AÑOS' => 'AÑOS'], null, ['class' => 'form-control']) !!}
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

            <div class="042">
                <div class="row odd-row">
                    <div class="col-lg-10">
                        <p>Enfisema pulmonar</p>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[042][respuesta]" value="si" id="rsi" onclick="mostrarFormulario042()">
                                <label class="form-check-label" for="rsi">SI</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[042][respuesta]" value="no" id="rno" onclick="ocultarFormulario042()">
                                <label class="form-check-label" for="rno">NO</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="card042" style="display: none;">
                    <div class="col-lg-4">
                        {!! Form::label('', 'Hace cuanto') !!}
                        <div class="form-group">
                            <div class="input-group">
                                {!! Form::text('hacecuanto042', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '3', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']) !!}
                                <div class="input-group-append">
                                    {!! Form::select('periodotipo042', ['' => '', 'DIAS' => 'DIAS', 'SEMANAS' => 'SEMANAS', 'MESES' => 'MESES', 'AÑOS' => 'AÑOS'], null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                        </div>
                    </div>   
                </div>
                <script>
                    function limpiarCampos042() {
                        document.getElementById('card042').querySelectorAll('input[type="text"]').forEach(function(input) {
                            input.value = '';
                        });
                        document.getElementById('card042').querySelectorAll('select').forEach(function(select) {
                            select.value = '';
                        });
                    }
                    function mostrarFormulario042() {
                        document.getElementById('card042').style.display = 'block';
                    }
                    function ocultarFormulario042() {
                        document.getElementById('card042').style.display = 'none';
                        limpiarCampos042();
                    }
                </script>
            </div>

            <br><h5 style="text-align: rigth" style="">GASTROENTEROLOGIA</h5>

            <div class="043">
                <div class="row">
                    <div class="col-lg-10">
                        <p>Gastritis</p>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[043][respuesta]" value="si" id="rsi" onclick="mostrarFormulario043()">
                                <label class="form-check-label" for="rsi">SI</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[043][respuesta]" value="no" id="rno" onclick="ocultarFormulario043()">
                                <label class="form-check-label" for="rno">NO</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="card043" style="display: none;">
                    <div class="col-lg-4">
                        {!! Form::label('', 'Hace cuanto') !!}
                        <div class="form-group">
                            <div class="input-group">
                                {!! Form::text('hacecuanto043', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '3', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']) !!}
                                <div class="input-group-append">
                                    {!! Form::select('periodotipo043', ['' => '', 'DIAS' => 'DIAS', 'SEMANAS' => 'SEMANAS', 'MESES' => 'MESES', 'AÑOS' => 'AÑOS'], null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                        </div>
                    </div>   
                </div>
                <script>
                    function limpiarCampos043() {
                        document.getElementById('card043').querySelectorAll('input[type="text"]').forEach(function(input) {
                            input.value = '';
                        });
                        document.getElementById('card043').querySelectorAll('select').forEach(function(select) {
                            select.value = '';
                        });
                    }
                    function mostrarFormulario043() {
                        document.getElementById('card043').style.display = 'block';
                    }
                    function ocultarFormulario043() {
                        document.getElementById('card043').style.display = 'none';
                        limpiarCampos043();
                    }
                </script>
            </div>

            <div class="044">
                <div class="row odd-row">
                    <div class="col-lg-10">
                        <p>Enf. acido peptica</p>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[044][respuesta]" value="si" id="rsi" onclick="mostrarFormulario044()">
                                <label class="form-check-label" for="rsi">SI</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[044][respuesta]" value="no" id="rno" onclick="ocultarFormulario044()">
                                <label class="form-check-label" for="rno">NO</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="card044" style="display: none;">
                    <div class="col-lg-4">
                        {!! Form::label('', 'Hace cuanto') !!}
                        <div class="form-group">
                            <div class="input-group">
                                {!! Form::text('hacecuanto044', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '3', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']) !!}
                                <div class="input-group-append">
                                    {!! Form::select('periodotipo044', ['' => '', 'DIAS' => 'DIAS', 'SEMANAS' => 'SEMANAS', 'MESES' => 'MESES', 'AÑOS' => 'AÑOS'], null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                        </div>
                    </div>   
                </div>
                <script>
                    function limpiarCampos044() {
                        document.getElementById('card044').querySelectorAll('input[type="text"]').forEach(function(input) {
                            input.value = '';
                        });
                        document.getElementById('card044').querySelectorAll('select').forEach(function(select) {
                            select.value = '';
                        });
                    }
                    function mostrarFormulario044() {
                        document.getElementById('card044').style.display = 'block';
                    }
                    function ocultarFormulario044() {
                        document.getElementById('card044').style.display = 'none';
                        limpiarCampos044();
                    }
                </script>
            </div>

            <div class="045">
                <div class="row">
                    <div class="col-lg-10">
                        <p>Colon irritable</p>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[045][respuesta]" value="si" id="rsi" onclick="mostrarFormulario045()">
                                <label class="form-check-label" for="rsi">SI</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[045][respuesta]" value="no" id="rno" onclick="ocultarFormulario045()">
                                <label class="form-check-label" for="rno">NO</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="card045" style="display: none;">
                    <div class="col-lg-4">
                        {!! Form::label('', 'Hace cuanto') !!}
                        <div class="form-group">
                            <div class="input-group">
                                {!! Form::text('hacecuanto045', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '3', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']) !!}
                                <div class="input-group-append">
                                    {!! Form::select('periodotipo045', ['' => '', 'DIAS' => 'DIAS', 'SEMANAS' => 'SEMANAS', 'MESES' => 'MESES', 'AÑOS' => 'AÑOS'], null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                        </div>
                    </div>   
                </div>
                <script>
                    function limpiarCampos045() {
                        document.getElementById('card045').querySelectorAll('input[type="text"]').forEach(function(input) {
                            input.value = '';
                        });
                        document.getElementById('card045').querySelectorAll('select').forEach(function(select) {
                            select.value = '';
                        });
                    }
                    function mostrarFormulario045() {
                        document.getElementById('card045').style.display = 'block';
                    }
                    function ocultarFormulario045() {
                        document.getElementById('card045').style.display = 'none';
                        limpiarCampos045();
                    }
                </script>
            </div>

            <div class="046">
                <div class="row odd-row">
                    <div class="col-lg-10">
                        <p>Cololetiasis</p>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[046][respuesta]" value="si" id="rsi" onclick="mostrarFormulario046()">
                                <label class="form-check-label" for="rsi">SI</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[046][respuesta]" value="no" id="rno" onclick="ocultarFormulario046()">
                                <label class="form-check-label" for="rno">NO</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="card046" style="display: none;">
                    <div class="col-lg-4">
                        {!! Form::label('', 'Hace cuanto') !!}
                        <div class="form-group">
                            <div class="input-group">
                                {!! Form::text('hacecuanto046', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '3', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']) !!}
                                <div class="input-group-append">
                                    {!! Form::select('periodotipo046', ['' => '', 'DIAS' => 'DIAS', 'SEMANAS' => 'SEMANAS', 'MESES' => 'MESES', 'AÑOS' => 'AÑOS'], null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                        </div>
                    </div>   
                </div>
                <script>
                    function limpiarCampos046() {
                        document.getElementById('card046').querySelectorAll('input[type="text"]').forEach(function(input) {
                            input.value = '';
                        });
                        document.getElementById('card046').querySelectorAll('select').forEach(function(select) {
                            select.value = '';
                        });
                    }
                    function mostrarFormulario046() {
                        document.getElementById('card046').style.display = 'block';
                    }
                    function ocultarFormulario046() {
                        document.getElementById('card046').style.display = 'none';
                        limpiarCampos046();
                    }
                </script>
            </div>

            <div class="047">
                <div class="row">
                    <div class="col-lg-10">
                        <p>Distension</p>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[047][respuesta]" value="si" id="rsi" onclick="mostrarFormulario047()">
                                <label class="form-check-label" for="rsi">SI</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[047][respuesta]" value="no" id="rno" onclick="ocultarFormulario047()">
                                <label class="form-check-label" for="rno">NO</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="card047" style="display: none;">
                    <div class="col-lg-4">
                        {!! Form::label('', 'Hace cuanto') !!}
                        <div class="form-group">
                            <div class="input-group">
                                {!! Form::text('hacecuanto047', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '3', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']) !!}
                                <div class="input-group-append">
                                    {!! Form::select('periodotipo047', ['' => '', 'DIAS' => 'DIAS', 'SEMANAS' => 'SEMANAS', 'MESES' => 'MESES', 'AÑOS' => 'AÑOS'], null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                        </div>
                    </div>   
                </div>
                <script>
                    function limpiarCampos047() {
                        document.getElementById('card047').querySelectorAll('input[type="text"]').forEach(function(input) {
                            input.value = '';
                        });
                        document.getElementById('card047').querySelectorAll('select').forEach(function(select) {
                            select.value = '';
                        });
                    }
                    function mostrarFormulario047() {
                        document.getElementById('card047').style.display = 'block';
                    }
                    function ocultarFormulario047() {
                        document.getElementById('card047').style.display = 'none';
                        limpiarCampos047();
                    }
                </script>
            </div>

            <div class="048">
                <div class="row odd-row">
                    <div class="col-lg-10">
                        <p>Calculos biliares</p>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[048][respuesta]" value="si" id="rsi" onclick="mostrarFormulario048()">
                                <label class="form-check-label" for="rsi">SI</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[048][respuesta]" value="no" id="rno" onclick="ocultarFormulario048()">
                                <label class="form-check-label" for="rno">NO</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="card048" style="display: none;">
                    <div class="col-lg-4">
                        {!! Form::label('', 'Hace cuanto') !!}
                        <div class="form-group">
                            <div class="input-group">
                                {!! Form::text('hacecuanto048', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '3', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']) !!}
                                <div class="input-group-append">
                                    {!! Form::select('periodotipo048', ['' => '', 'DIAS' => 'DIAS', 'SEMANAS' => 'SEMANAS', 'MESES' => 'MESES', 'AÑOS' => 'AÑOS'], null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                        </div>
                    </div>   
                </div>
                <script>
                    function limpiarCampos048() {
                        document.getElementById('card048').querySelectorAll('input[type="text"]').forEach(function(input) {
                            input.value = '';
                        });
                        document.getElementById('card048').querySelectorAll('select').forEach(function(select) {
                            select.value = '';
                        });
                    }
                    function mostrarFormulario048() {
                        document.getElementById('card048').style.display = 'block';
                    }
                    function ocultarFormulario048() {
                        document.getElementById('card048').style.display = 'none';
                        limpiarCampos048();
                    }
                </script>
            </div>

            <div class="049">
                <div class="row">
                    <div class="col-lg-10">
                        <p>Ulcera intestinal</p>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[049][respuesta]" value="si" id="rsi" onclick="mostrarFormulario049()">
                                <label class="form-check-label" for="rsi">SI</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[049][respuesta]" value="no" id="rno" onclick="ocultarFormulario049()">
                                <label class="form-check-label" for="rno">NO</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="card049" style="display: none;">
                    <div class="col-lg-4">
                        {!! Form::label('', 'Hace cuanto') !!}
                        <div class="form-group">
                            <div class="input-group">
                                {!! Form::text('hacecuanto049', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '3', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']) !!}
                                <div class="input-group-append">
                                    {!! Form::select('periodotipo049', ['' => '', 'DIAS' => 'DIAS', 'SEMANAS' => 'SEMANAS', 'MESES' => 'MESES', 'AÑOS' => 'AÑOS'], null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                        </div>
                    </div>   
                </div>
                <script>
                    function limpiarCampos049() {
                        document.getElementById('card049').querySelectorAll('input[type="text"]').forEach(function(input) {
                            input.value = '';
                        });
                        document.getElementById('card049').querySelectorAll('select').forEach(function(select) {
                            select.value = '';
                        });
                    }
                    function mostrarFormulario049() {
                        document.getElementById('card049').style.display = 'block';
                    }
                    function ocultarFormulario049() {
                        document.getElementById('card049').style.display = 'none';
                        limpiarCampos049();
                    }
                </script>
            </div>

            <div class="050">
                <div class="row odd-row">
                    <div class="col-lg-10">
                        <p>Hepatitis</p>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[050][respuesta]" value="si" id="rsi" onclick="mostrarFormulario050()">
                                <label class="form-check-label" for="rsi">SI</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[050][respuesta]" value="no" id="rno" onclick="ocultarFormulario050()">
                                <label class="form-check-label" for="rno">NO</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="card050" style="display: none;">
                    <div class="col-lg-4">
                        {!! Form::label('', 'Hace cuanto') !!}
                        <div class="form-group">
                            <div class="input-group">
                                {!! Form::text('hacecuanto050', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '3', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']) !!}
                                <div class="input-group-append">
                                    {!! Form::select('periodotipo050', ['' => '', 'DIAS' => 'DIAS', 'SEMANAS' => 'SEMANAS', 'MESES' => 'MESES', 'AÑOS' => 'AÑOS'], null, ['class' => 'form-control']) !!}
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

            <br><h5 style="text-align: rigth" style="">UROLOGIA / NEFROLOGIA</h5>

            <div class="051">
                <div class="row">
                    <div class="col-lg-10">
                        <p>Urolitiasis</p>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[051][respuesta]" value="si" id="rsi" onclick="mostrarFormulario051()">
                                <label class="form-check-label" for="rsi">SI</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[051][respuesta]" value="no" id="rno" onclick="ocultarFormulario051()">
                                <label class="form-check-label" for="rno">NO</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="card051" style="display: none;">
                    <div class="col-lg-4">
                        {!! Form::label('', 'Hace cuanto') !!}
                        <div class="form-group">
                            <div class="input-group">
                                {!! Form::text('hacecuanto051', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '3', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']) !!}
                                <div class="input-group-append">
                                    {!! Form::select('periodotipo051', ['' => '', 'DIAS' => 'DIAS', 'SEMANAS' => 'SEMANAS', 'MESES' => 'MESES', 'AÑOS' => 'AÑOS'], null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                        </div>
                    </div>   
                </div>
                <script>
                    function limpiarCampos051() {
                        document.getElementById('card051').querySelectorAll('input[type="text"]').forEach(function(input) {
                            input.value = '';
                        });
                        document.getElementById('card051').querySelectorAll('select').forEach(function(select) {
                            select.value = '';
                        });
                    }
                    function mostrarFormulario051() {
                        document.getElementById('card051').style.display = 'block';
                    }
                    function ocultarFormulario051() {
                        document.getElementById('card051').style.display = 'none';
                        limpiarCampos051();
                    }
                </script>
            </div>

            <div class="052">
                <div class="row odd-row">
                    <div class="col-lg-10">
                        <p>Infeccion urinaria</p>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[052][respuesta]" value="si" id="rsi" onclick="mostrarFormulario052()">
                                <label class="form-check-label" for="rsi">SI</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[052][respuesta]" value="no" id="rno" onclick="ocultarFormulario052()">
                                <label class="form-check-label" for="rno">NO</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="card052" style="display: none;">
                    <div class="col-lg-4">
                        {!! Form::label('', 'Hace cuanto') !!}
                        <div class="form-group">
                            <div class="input-group">
                                {!! Form::text('hacecuanto052', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '3', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']) !!}
                                <div class="input-group-append">
                                    {!! Form::select('periodotipo052', ['' => '', 'DIAS' => 'DIAS', 'SEMANAS' => 'SEMANAS', 'MESES' => 'MESES', 'AÑOS' => 'AÑOS'], null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                        </div>
                    </div>   
                </div>
                <script>
                    function limpiarCampos052() {
                        document.getElementById('card052').querySelectorAll('input[type="text"]').forEach(function(input) {
                            input.value = '';
                        });
                        document.getElementById('card052').querySelectorAll('select').forEach(function(select) {
                            select.value = '';
                        });
                    }
                    function mostrarFormulario052() {
                        document.getElementById('card052').style.display = 'block';
                    }
                    function ocultarFormulario052() {
                        document.getElementById('card052').style.display = 'none';
                        limpiarCampos052();
                    }
                </script>
            </div>

            <div class="053">
                <div class="row">
                    <div class="col-lg-10">
                        <p>Prostatitis</p>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[053][respuesta]" value="si" id="rsi" onclick="mostrarFormulario053()">
                                <label class="form-check-label" for="rsi">SI</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[053][respuesta]" value="no" id="rno" onclick="ocultarFormulario053()">
                                <label class="form-check-label" for="rno">NO</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="card053" style="display: none;">
                    <div class="col-lg-4">
                        {!! Form::label('', 'Hace cuanto') !!}
                        <div class="form-group">
                            <div class="input-group">
                                {!! Form::text('hacecuanto053', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '3', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']) !!}
                                <div class="input-group-append">
                                    {!! Form::select('periodotipo053', ['' => '', 'DIAS' => 'DIAS', 'SEMANAS' => 'SEMANAS', 'MESES' => 'MESES', 'AÑOS' => 'AÑOS'], null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                        </div>
                    </div>   
                </div>
                <script>
                    function limpiarCampos053() {
                        document.getElementById('card053').querySelectorAll('input[type="text"]').forEach(function(input) {
                            input.value = '';
                        });
                        document.getElementById('card053').querySelectorAll('select').forEach(function(select) {
                            select.value = '';
                        });
                    }
                    function mostrarFormulario053() {
                        document.getElementById('card053').style.display = 'block';
                    }
                    function ocultarFormulario053() {
                        document.getElementById('card053').style.display = 'none';
                        limpiarCampos053();
                    }
                </script>
            </div>

            <div class="054">
                <div class="row odd-row">
                    <div class="col-lg-10">
                        <p>Varicocele</p>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[054][respuesta]" value="si" id="rsi" onclick="mostrarFormulario054()">
                                <label class="form-check-label" for="rsi">SI</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[054][respuesta]" value="no" id="rno" onclick="ocultarFormulario054()">
                                <label class="form-check-label" for="rno">NO</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="card054" style="display: none;">
                    <div class="col-lg-4">
                        {!! Form::label('', 'Hace cuanto') !!}
                        <div class="form-group">
                            <div class="input-group">
                                {!! Form::text('hacecuanto054', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '3', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']) !!}
                                <div class="input-group-append">
                                    {!! Form::select('periodotipo054', ['' => '', 'DIAS' => 'DIAS', 'SEMANAS' => 'SEMANAS', 'MESES' => 'MESES', 'AÑOS' => 'AÑOS'], null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                        </div>
                    </div>   
                </div>
                <script>
                    function limpiarCampos054() {
                        document.getElementById('card054').querySelectorAll('input[type="text"]').forEach(function(input) {
                            input.value = '';
                        });
                        document.getElementById('card054').querySelectorAll('select').forEach(function(select) {
                            select.value = '';
                        });
                    }
                    function mostrarFormulario054() {
                        document.getElementById('card054').style.display = 'block';
                    }
                    function ocultarFormulario054() {
                        document.getElementById('card054').style.display = 'none';
                        limpiarCampos054();
                    }
                </script>
            </div>

            <br><h5 style="text-align: rigth" style="">DERMATOLOGIA</h5>

            <div class="055">
                <div class="row">
                    <div class="col-lg-10">
                        <p>Dermatitis</p>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[055][respuesta]" value="si" id="rsi" onclick="mostrarFormulario055()">
                                <label class="form-check-label" for="rsi">SI</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[055][respuesta]" value="no" id="rno" onclick="ocultarFormulario055()">
                                <label class="form-check-label" for="rno">NO</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="card055" style="display: none;">
                    <div class="col-lg-4">
                        {!! Form::label('', 'Hace cuanto') !!}
                        <div class="form-group">
                            <div class="input-group">
                                {!! Form::text('hacecuanto055', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '3', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']) !!}
                                <div class="input-group-append">
                                    {!! Form::select('periodotipo055', ['' => '', 'DIAS' => 'DIAS', 'SEMANAS' => 'SEMANAS', 'MESES' => 'MESES', 'AÑOS' => 'AÑOS'], null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                        </div>
                    </div>   
                </div>
                <script>
                    function limpiarCampos055() {
                        document.getElementById('card055').querySelectorAll('input[type="text"]').forEach(function(input) {
                            input.value = '';
                        });
                        document.getElementById('card055').querySelectorAll('select').forEach(function(select) {
                            select.value = '';
                        });
                    }
                    function mostrarFormulario055() {
                        document.getElementById('card055').style.display = 'block';
                    }
                    function ocultarFormulario055() {
                        document.getElementById('card055').style.display = 'none';
                        limpiarCampos055();
                    }
                </script>
            </div>

            <div class="056">
                <div class="row odd-row">
                    <div class="col-lg-10">
                        <p>Lupus eritematoso</p>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[056][respuesta]" value="si" id="rsi" onclick="mostrarFormulario056()">
                                <label class="form-check-label" for="rsi">SI</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[056][respuesta]" value="no" id="rno" onclick="ocultarFormulario056()">
                                <label class="form-check-label" for="rno">NO</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="card056" style="display: none;">
                    <div class="col-lg-4">
                        {!! Form::label('', 'Hace cuanto') !!}
                        <div class="form-group">
                            <div class="input-group">
                                {!! Form::text('hacecuanto056', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '3', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']) !!}
                                <div class="input-group-append">
                                    {!! Form::select('periodotipo056', ['' => '', 'DIAS' => 'DIAS', 'SEMANAS' => 'SEMANAS', 'MESES' => 'MESES', 'AÑOS' => 'AÑOS'], null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                        </div>
                    </div>   
                </div>
                <script>
                    function limpiarCampos056() {
                        document.getElementById('card056').querySelectorAll('input[type="text"]').forEach(function(input) {
                            input.value = '';
                        });
                        document.getElementById('card056').querySelectorAll('select').forEach(function(select) {
                            select.value = '';
                        });
                    }
                    function mostrarFormulario056() {
                        document.getElementById('card056').style.display = 'block';
                    }
                    function ocultarFormulario056() {
                        document.getElementById('card056').style.display = 'none';
                        limpiarCampos056();
                    }
                </script>
            </div>

            <div class="057">
                <div class="row">
                    <div class="col-lg-10">
                        <p>Vitiligo</p>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[057][respuesta]" value="si" id="rsi" onclick="mostrarFormulario057()">
                                <label class="form-check-label" for="rsi">SI</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[057][respuesta]" value="no" id="rno" onclick="ocultarFormulario057()">
                                <label class="form-check-label" for="rno">NO</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="card057" style="display: none;">
                    <div class="col-lg-4">
                        {!! Form::label('', 'Hace cuanto') !!}
                        <div class="form-group">
                            <div class="input-group">
                                {!! Form::text('hacecuanto057', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '3', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']) !!}
                                <div class="input-group-append">
                                    {!! Form::select('periodotipo057', ['' => '', 'DIAS' => 'DIAS', 'SEMANAS' => 'SEMANAS', 'MESES' => 'MESES', 'AÑOS' => 'AÑOS'], null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                        </div>
                    </div>   
                </div>
                <script>
                    function limpiarCampos057() {
                        document.getElementById('card057').querySelectorAll('input[type="text"]').forEach(function(input) {
                            input.value = '';
                        });
                        document.getElementById('card057').querySelectorAll('select').forEach(function(select) {
                            select.value = '';
                        });
                    }
                    function mostrarFormulario057() {
                        document.getElementById('card057').style.display = 'block';
                    }
                    function ocultarFormulario057() {
                        document.getElementById('card057').style.display = 'none';
                        limpiarCampos057();
                    }
                </script>
            </div>

            <div class="058">
                <div class="row odd-row">
                    <div class="col-lg-10">
                        <p>Eccema</p>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[058][respuesta]" value="si" id="rsi" onclick="mostrarFormulario058()">
                                <label class="form-check-label" for="rsi">SI</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[058][respuesta]" value="no" id="rno" onclick="ocultarFormulario058()">
                                <label class="form-check-label" for="rno">NO</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="card058" style="display: none;">
                    <div class="col-lg-4">
                        {!! Form::label('', 'Hace cuanto') !!}
                        <div class="form-group">
                            <div class="input-group">
                                {!! Form::text('hacecuanto058', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '3', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']) !!}
                                <div class="input-group-append">
                                    {!! Form::select('periodotipo058', ['' => '', 'DIAS' => 'DIAS', 'SEMANAS' => 'SEMANAS', 'MESES' => 'MESES', 'AÑOS' => 'AÑOS'], null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                        </div>
                    </div>   
                </div>
                <script>
                    function limpiarCampos058() {
                        document.getElementById('card058').querySelectorAll('input[type="text"]').forEach(function(input) {
                            input.value = '';
                        });
                        document.getElementById('card058').querySelectorAll('select').forEach(function(select) {
                            select.value = '';
                        });
                    }
                    function mostrarFormulario058() {
                        document.getElementById('card058').style.display = 'block';
                    }
                    function ocultarFormulario058() {
                        document.getElementById('card058').style.display = 'none';
                        limpiarCampos058();
                    }
                </script>
            </div>

            <div class="059">
                <div class="row">
                    <div class="col-lg-10">
                        <p>Impetigo</p>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[059][respuesta]" value="si" id="rsi" onclick="mostrarFormulario059()">
                                <label class="form-check-label" for="rsi">SI</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[059][respuesta]" value="no" id="rno" onclick="ocultarFormulario059()">
                                <label class="form-check-label" for="rno">NO</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="card059" style="display: none;">
                    <div class="col-lg-4">
                        {!! Form::label('', 'Hace cuanto') !!}
                        <div class="form-group">
                            <div class="input-group">
                                {!! Form::text('hacecuanto059', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '3', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']) !!}
                                <div class="input-group-append">
                                    {!! Form::select('periodotipo059', ['' => '', 'DIAS' => 'DIAS', 'SEMANAS' => 'SEMANAS', 'MESES' => 'MESES', 'AÑOS' => 'AÑOS'], null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                        </div>
                    </div>   
                </div>
                <script>
                    function limpiarCampos059() {
                        document.getElementById('card059').querySelectorAll('input[type="text"]').forEach(function(input) {
                            input.value = '';
                        });
                        document.getElementById('card059').querySelectorAll('select').forEach(function(select) {
                            select.value = '';
                        });
                    }
                    function mostrarFormulario059() {
                        document.getElementById('card059').style.display = 'block';
                    }
                    function ocultarFormulario059() {
                        document.getElementById('card059').style.display = 'none';
                        limpiarCampos059();
                    }
                </script>
            </div>

            <div class="060">
                <div class="row odd-row">
                    <div class="col-lg-10">
                        <p>Psoriasis</p>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[060][respuesta]" value="si" id="rsi" onclick="mostrarFormulario060()">
                                <label class="form-check-label" for="rsi">SI</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[060][respuesta]" value="no" id="rno" onclick="ocultarFormulario060()">
                                <label class="form-check-label" for="rno">NO</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="card060" style="display: none;">
                    <div class="col-lg-4">
                        {!! Form::label('', 'Hace cuanto') !!}
                        <div class="form-group">
                            <div class="input-group">
                                {!! Form::text('hacecuanto060', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '3', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']) !!}
                                <div class="input-group-append">
                                    {!! Form::select('periodotipo060', ['' => '', 'DIAS' => 'DIAS', 'SEMANAS' => 'SEMANAS', 'MESES' => 'MESES', 'AÑOS' => 'AÑOS'], null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                        </div>
                    </div>   
                </div>
                <script>
                    function limpiarCampos060() {
                        document.getElementById('card060').querySelectorAll('input[type="text"]').forEach(function(input) {
                            input.value = '';
                        });
                        document.getElementById('card060').querySelectorAll('select').forEach(function(select) {
                            select.value = '';
                        });
                    }
                    function mostrarFormulario060() {
                        document.getElementById('card060').style.display = 'block';
                    }
                    function ocultarFormulario060() {
                        document.getElementById('card060').style.display = 'none';
                        limpiarCampos060();
                    }
                </script>
            </div>

            <br><h5 style="text-align: rigth" style="">CIRUGIA VASCULAR</h5>

            <div class="061">
                <div class="row">
                    <div class="col-lg-10">
                        <p>Varices en piernas</p>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[061][respuesta]" value="si" id="rsi" onclick="mostrarFormulario061()">
                                <label class="form-check-label" for="rsi">SI</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[061][respuesta]" value="no" id="rno" onclick="ocultarFormulario061()">
                                <label class="form-check-label" for="rno">NO</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="card061" style="display: none;">
                    <div class="col-lg-4">
                        {!! Form::label('', 'Hace cuanto') !!}
                        <div class="form-group">
                            <div class="input-group">
                                {!! Form::text('hacecuanto061', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '3', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']) !!}
                                <div class="input-group-append">
                                    {!! Form::select('periodotipo061', ['' => '', 'DIAS' => 'DIAS', 'SEMANAS' => 'SEMANAS', 'MESES' => 'MESES', 'AÑOS' => 'AÑOS'], null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                        </div>
                    </div>   
                </div>
                <script>
                    function limpiarCampos061() {
                        document.getElementById('card061').querySelectorAll('input[type="text"]').forEach(function(input) {
                            input.value = '';
                        });
                        document.getElementById('card061').querySelectorAll('select').forEach(function(select) {
                            select.value = '';
                        });
                    }
                    function mostrarFormulario061() {
                        document.getElementById('card061').style.display = 'block';
                    }
                    function ocultarFormulario061() {
                        document.getElementById('card061').style.display = 'none';
                        limpiarCampos061();
                    }
                </script>
            </div>

            <div class="062">
                <div class="row odd-row">
                    <div class="col-lg-10">
                        <p>Celulitis en MMII</p>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[062][respuesta]" value="si" id="rsi" onclick="mostrarFormulario062()">
                                <label class="form-check-label" for="rsi">SI</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[062][respuesta]" value="no" id="rno" onclick="ocultarFormulario062()">
                                <label class="form-check-label" for="rno">NO</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="card062" style="display: none;">
                    <div class="col-lg-4">
                        {!! Form::label('', 'Hace cuanto') !!}
                        <div class="form-group">
                            <div class="input-group">
                                {!! Form::text('hacecuanto062', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '3', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']) !!}
                                <div class="input-group-append">
                                    {!! Form::select('periodotipo062', ['' => '', 'DIAS' => 'DIAS', 'SEMANAS' => 'SEMANAS', 'MESES' => 'MESES', 'AÑOS' => 'AÑOS'], null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                        </div>
                    </div>   
                </div>
                <script>
                    function limpiarCampos062() {
                        document.getElementById('card062').querySelectorAll('input[type="text"]').forEach(function(input) {
                            input.value = '';
                        });
                        document.getElementById('card062').querySelectorAll('select').forEach(function(select) {
                            select.value = '';
                        });
                    }
                    function mostrarFormulario062() {
                        document.getElementById('card062').style.display = 'block';
                    }
                    function ocultarFormulario062() {
                        document.getElementById('card062').style.display = 'none';
                        limpiarCampos062();
                    }
                </script>
            </div>

            <div class="063">
                <div class="row">
                    <div class="col-lg-10">
                        <p>Trombosis</p>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[063][respuesta]" value="si" id="rsi" onclick="mostrarFormulario063()">
                                <label class="form-check-label" for="rsi">SI</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[063][respuesta]" value="no" id="rno" onclick="ocultarFormulario063()">
                                <label class="form-check-label" for="rno">NO</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="card063" style="display: none;">
                    <div class="col-lg-4">
                        {!! Form::label('', 'Hace cuanto') !!}
                        <div class="form-group">
                            <div class="input-group">
                                {!! Form::text('hacecuanto063', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '3', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']) !!}
                                <div class="input-group-append">
                                    {!! Form::select('periodotipo063', ['' => '', 'DIAS' => 'DIAS', 'SEMANAS' => 'SEMANAS', 'MESES' => 'MESES', 'AÑOS' => 'AÑOS'], null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                        </div>
                    </div>   
                </div>
                <script>
                    function limpiarCampos063() {
                        document.getElementById('card063').querySelectorAll('input[type="text"]').forEach(function(input) {
                            input.value = '';
                        });
                        document.getElementById('card063').querySelectorAll('select').forEach(function(select) {
                            select.value = '';
                        });
                    }
                    function mostrarFormulario063() {
                        document.getElementById('card063').style.display = 'block';
                    }
                    function ocultarFormulario063() {
                        document.getElementById('card063').style.display = 'none';
                        limpiarCampos063();
                    }
                </script>
            </div>

            <br><h5 style="text-align: rigth" style="">REUMATOLOGIA</h5>

            <div class="064">
                <div class="row odd-row">
                    <div class="col-lg-10">
                        <p>Artritis reumatoidea</p>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[064][respuesta]" value="si" id="rsi" onclick="mostrarFormulario064()">
                                <label class="form-check-label" for="rsi">SI</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[064][respuesta]" value="no" id="rno" onclick="ocultarFormulario064()">
                                <label class="form-check-label" for="rno">NO</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="card064" style="display: none;">
                    <div class="col-lg-4">
                        {!! Form::label('', 'Hace cuanto') !!}
                        <div class="form-group">
                            <div class="input-group">
                                {!! Form::text('hacecuanto064', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '3', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']) !!}
                                <div class="input-group-append">
                                    {!! Form::select('periodotipo064', ['' => '', 'DIAS' => 'DIAS', 'SEMANAS' => 'SEMANAS', 'MESES' => 'MESES', 'AÑOS' => 'AÑOS'], null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                        </div>
                    </div>   
                </div>
                <script>
                    function limpiarCampos064() {
                        document.getElementById('card064').querySelectorAll('input[type="text"]').forEach(function(input) {
                            input.value = '';
                        });
                        document.getElementById('card064').querySelectorAll('select').forEach(function(select) {
                            select.value = '';
                        });
                    }
                    function mostrarFormulario064() {
                        document.getElementById('card064').style.display = 'block';
                    }
                    function ocultarFormulario064() {
                        document.getElementById('card064').style.display = 'none';
                        limpiarCampos064();
                    }
                </script>
            </div>
            
            <div class="065">
                <div class="row">
                    <div class="col-lg-10">
                        <p>Artrosis</p>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[065][respuesta]" value="si" id="rsi" onclick="mostrarFormulario065()">
                                <label class="form-check-label" for="rsi">SI</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[065][respuesta]" value="no" id="rno" onclick="ocultarFormulario065()">
                                <label class="form-check-label" for="rno">NO</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="card065" style="display: none;">
                    <div class="col-lg-4">
                        {!! Form::label('', 'Hace cuanto') !!}
                        <div class="form-group">
                            <div class="input-group">
                                {!! Form::text('hacecuanto065', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '3', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']) !!}
                                <div class="input-group-append">
                                    {!! Form::select('periodotipo065', ['' => '', 'DIAS' => 'DIAS', 'SEMANAS' => 'SEMANAS', 'MESES' => 'MESES', 'AÑOS' => 'AÑOS'], null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                        </div>
                    </div>   
                </div>
                <script>
                    function limpiarCampos065() {
                        document.getElementById('card065').querySelectorAll('input[type="text"]').forEach(function(input) {
                            input.value = '';
                        });
                        document.getElementById('card065').querySelectorAll('select').forEach(function(select) {
                            select.value = '';
                        });
                    }
                    function mostrarFormulario065() {
                        document.getElementById('card065').style.display = 'block';
                    }
                    function ocultarFormulario065() {
                        document.getElementById('card065').style.display = 'none';
                        limpiarCampos065();
                    }
                </script>
            </div>

            <div class="066">
                <div class="row odd-row">
                    <div class="col-lg-10">
                        <p>Psoriasis</p>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[066][respuesta]" value="si" id="rsi" onclick="mostrarFormulario066()">
                                <label class="form-check-label" for="rsi">SI</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[066][respuesta]" value="no" id="rno" onclick="ocultarFormulario066()">
                                <label class="form-check-label" for="rno">NO</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="card066" style="display: none;">
                    <div class="col-lg-4">
                        {!! Form::label('', 'Hace cuanto') !!}
                        <div class="form-group">
                            <div class="input-group">
                                {!! Form::text('hacecuanto066', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '3', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']) !!}
                                <div class="input-group-append">
                                    {!! Form::select('periodotipo066', ['' => '', 'DIAS' => 'DIAS', 'SEMANAS' => 'SEMANAS', 'MESES' => 'MESES', 'AÑOS' => 'AÑOS'], null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                        </div>
                    </div>   
                </div>
                <script>
                    function limpiarCampos066() {
                        document.getElementById('card066').querySelectorAll('input[type="text"]').forEach(function(input) {
                            input.value = '';
                        });
                        document.getElementById('card066').querySelectorAll('select').forEach(function(select) {
                            select.value = '';
                        });
                    }
                    function mostrarFormulario066() {
                        document.getElementById('card066').style.display = 'block';
                    }
                    function ocultarFormulario066() {
                        document.getElementById('card066').style.display = 'none';
                        limpiarCampos066();
                    }
                </script>
            </div>

            <div class="067">
                <div class="row">
                    <div class="col-lg-10">
                        <p>Lupus eritematoso</p>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[067][respuesta]" value="si" id="rsi" onclick="mostrarFormulario067()">
                                <label class="form-check-label" for="rsi">SI</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[067][respuesta]" value="no" id="rno" onclick="ocultarFormulario067()">
                                <label class="form-check-label" for="rno">NO</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="card067" style="display: none;">
                    <div class="col-lg-4">
                        {!! Form::label('', 'Hace cuanto') !!}
                        <div class="form-group">
                            <div class="input-group">
                                {!! Form::text('hacecuanto067', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '3', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']) !!}
                                <div class="input-group-append">
                                    {!! Form::select('periodotipo067', ['' => '', 'DIAS' => 'DIAS', 'SEMANAS' => 'SEMANAS', 'MESES' => 'MESES', 'AÑOS' => 'AÑOS'], null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                        </div>
                    </div>   
                </div>
                <script>
                    function limpiarCampos067() {
                        document.getElementById('card067').querySelectorAll('input[type="text"]').forEach(function(input) {
                            input.value = '';
                        });
                        document.getElementById('card067').querySelectorAll('select').forEach(function(select) {
                            select.value = '';
                        });
                    }
                    function mostrarFormulario067() {
                        document.getElementById('card067').style.display = 'block';
                    }
                    function ocultarFormulario067() {
                        document.getElementById('card067').style.display = 'none';
                        limpiarCampos067();
                    }
                </script>
            </div>

            <div class="068">
                <div class="row odd-row">
                    <div class="col-lg-10">
                        <p>Gota</p>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[068][respuesta]" value="si" id="rsi" onclick="mostrarFormulario068()">
                                <label class="form-check-label" for="rsi">SI</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[068][respuesta]" value="no" id="rno" onclick="ocultarFormulario068()">
                                <label class="form-check-label" for="rno">NO</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="card068" style="display: none;">
                    <div class="col-lg-4">
                        {!! Form::label('', 'Hace cuanto') !!}
                        <div class="form-group">
                            <div class="input-group">
                                {!! Form::text('hacecuanto068', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '3', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']) !!}
                                <div class="input-group-append">
                                    {!! Form::select('periodotipo068', ['' => '', 'DIAS' => 'DIAS', 'SEMANAS' => 'SEMANAS', 'MESES' => 'MESES', 'AÑOS' => 'AÑOS'], null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                        </div>
                    </div>   
                </div>
                <script>
                    function limpiarCampos068() {
                        document.getElementById('card068').querySelectorAll('input[type="text"]').forEach(function(input) {
                            input.value = '';
                        });
                        document.getElementById('card068').querySelectorAll('select').forEach(function(select) {
                            select.value = '';
                        });
                    }
                    function mostrarFormulario068() {
                        document.getElementById('card068').style.display = 'block';
                    }
                    function ocultarFormulario068() {
                        document.getElementById('card068').style.display = 'none';
                        limpiarCampos068();
                    }
                </script>
            </div>

            <div class="069">
                <div class="row">
                    <div class="col-lg-10">
                        <p>Espondilitis anquilosante</p>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[069][respuesta]" value="si" id="rsi" onclick="mostrarFormulario069()">
                                <label class="form-check-label" for="rsi">SI</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[069][respuesta]" value="no" id="rno" onclick="ocultarFormulario069()">
                                <label class="form-check-label" for="rno">NO</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="card069" style="display: none;">
                    <div class="col-lg-4">
                        {!! Form::label('', 'Hace cuanto') !!}
                        <div class="form-group">
                            <div class="input-group">
                                {!! Form::text('hacecuanto069', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '3', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']) !!}
                                <div class="input-group-append">
                                    {!! Form::select('periodotipo069', ['' => '', 'DIAS' => 'DIAS', 'SEMANAS' => 'SEMANAS', 'MESES' => 'MESES', 'AÑOS' => 'AÑOS'], null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                        </div>
                    </div>   
                </div>
                <script>
                    function limpiarCampos069() {
                        document.getElementById('card069').querySelectorAll('input[type="text"]').forEach(function(input) {
                            input.value = '';
                        });
                        document.getElementById('card069').querySelectorAll('select').forEach(function(select) {
                            select.value = '';
                        });
                    }
                    function mostrarFormulario069() {
                        document.getElementById('card069').style.display = 'block';
                    }
                    function ocultarFormulario069() {
                        document.getElementById('card069').style.display = 'none';
                        limpiarCampos069();
                    }
                </script>
            </div>

            <div class="070">
                <div class="row odd-row">
                    <div class="col-lg-10">
                        <p>Fibromialgia</p>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[070][respuesta]" value="si" id="rsi" onclick="mostrarFormulario070()">
                                <label class="form-check-label" for="rsi">SI</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[070][respuesta]" value="no" id="rno" onclick="ocultarFormulario070()">
                                <label class="form-check-label" for="rno">NO</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="card070" style="display: none;">
                    <div class="col-lg-4">
                        {!! Form::label('', 'Hace cuanto') !!}
                        <div class="form-group">
                            <div class="input-group">
                                {!! Form::text('hacecuanto070', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '3', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']) !!}
                                <div class="input-group-append">
                                    {!! Form::select('periodotipo070', ['' => '', 'DIAS' => 'DIAS', 'SEMANAS' => 'SEMANAS', 'MESES' => 'MESES', 'AÑOS' => 'AÑOS'], null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                        </div>
                    </div>   
                </div>
                <script>
                    function limpiarCampos070() {
                        document.getElementById('card070').querySelectorAll('input[type="text"]').forEach(function(input) {
                            input.value = '';
                        });
                        document.getElementById('card070').querySelectorAll('select').forEach(function(select) {
                            select.value = '';
                        });
                    }
                    function mostrarFormulario070() {
                        document.getElementById('card070').style.display = 'block';
                    }
                    function ocultarFormulario070() {
                        document.getElementById('card070').style.display = 'none';
                        limpiarCampos070();
                    }
                </script>
            </div>

            <div class="071">
                <div class="row">
                    <div class="col-lg-10">
                        <p>Reumatismo</p>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[071][respuesta]" value="si" id="rsi" onclick="mostrarFormulario071()">
                                <label class="form-check-label" for="rsi">SI</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[071][respuesta]" value="no" id="rno" onclick="ocultarFormulario071()">
                                <label class="form-check-label" for="rno">NO</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="card071" style="display: none;">
                    <div class="col-lg-4">
                        {!! Form::label('', 'Hace cuanto') !!}
                        <div class="form-group">
                            <div class="input-group">
                                {!! Form::text('hacecuanto071', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '3', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']) !!}
                                <div class="input-group-append">
                                    {!! Form::select('periodotipo071', ['' => '', 'DIAS' => 'DIAS', 'SEMANAS' => 'SEMANAS', 'MESES' => 'MESES', 'AÑOS' => 'AÑOS'], null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                        </div>
                    </div>   
                </div>
                <script>
                    function limpiarCampos071() {
                        document.getElementById('card071').querySelectorAll('input[type="text"]').forEach(function(input) {
                            input.value = '';
                        });
                        document.getElementById('card071').querySelectorAll('select').forEach(function(select) {
                            select.value = '';
                        });
                    }
                    function mostrarFormulario071() {
                        document.getElementById('card071').style.display = 'block';
                    }
                    function ocultarFormulario071() {
                        document.getElementById('card071').style.display = 'none';
                        limpiarCampos071();
                    }
                </script>
            </div>

            <br><h5 style="text-align: rigth" style="">ONCOLOGIA</h5>

            <div class="072">
                <div class="row odd-row">
                    <div class="col-lg-10">
                        <p>Cancer</p>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[072][respuesta]" value="si" id="rsi" onclick="mostrarFormulario072()">
                                <label class="form-check-label" for="rsi">SI</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[072][respuesta]" value="no" id="rno" onclick="ocultarFormulario072()">
                                <label class="form-check-label" for="rno">NO</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="card072" style="display: none;">
                    <div class="col-lg-4">
                        {!! Form::label('', 'Hace cuanto') !!}
                        <div class="form-group">
                            <div class="input-group">
                                {!! Form::text('hacecuanto072', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '3', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']) !!}
                                <div class="input-group-append">
                                    {!! Form::select('periodotipo072', ['' => '', 'DIAS' => 'DIAS', 'SEMANAS' => 'SEMANAS', 'MESES' => 'MESES', 'AÑOS' => 'AÑOS'], null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                        </div>
                    </div>   
                </div>
                <script>
                    function limpiarCampos072() {
                        document.getElementById('card072').querySelectorAll('input[type="text"]').forEach(function(input) {
                            input.value = '';
                        });
                        document.getElementById('card072').querySelectorAll('select').forEach(function(select) {
                            select.value = '';
                        });
                    }
                    function mostrarFormulario072() {
                        document.getElementById('card072').style.display = 'block';
                    }
                    function ocultarFormulario072() {
                        document.getElementById('card072').style.display = 'none';
                        limpiarCampos072();
                    }
                </script>
            </div>

            <br><h5 style="text-align: rigth" style="">CIRUGIA GENERAL</h5>

            <div class="073">
                <div class="row">
                    <div class="col-lg-10">
                        <p>Hernia inguinal</p>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[073][respuesta]" value="si" id="rsi" onclick="mostrarFormulario073()">
                                <label class="form-check-label" for="rsi">SI</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[073][respuesta]" value="no" id="rno" onclick="ocultarFormulario073()">
                                <label class="form-check-label" for="rno">NO</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="card073" style="display: none;">
                    <div class="col-lg-4">
                        {!! Form::label('', 'Hace cuanto') !!}
                        <div class="form-group">
                            <div class="input-group">
                                {!! Form::text('hacecuanto073', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '3', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']) !!}
                                <div class="input-group-append">
                                    {!! Form::select('periodotipo073', ['' => '', 'DIAS' => 'DIAS', 'SEMANAS' => 'SEMANAS', 'MESES' => 'MESES', 'AÑOS' => 'AÑOS'], null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                        </div>
                    </div>   
                </div>
                <script>
                    function limpiarCampos073() {
                        document.getElementById('card073').querySelectorAll('input[type="text"]').forEach(function(input) {
                            input.value = '';
                        });
                        document.getElementById('card073').querySelectorAll('select').forEach(function(select) {
                            select.value = '';
                        });
                    }
                    function mostrarFormulario073() {
                        document.getElementById('card073').style.display = 'block';
                    }
                    function ocultarFormulario073() {
                        document.getElementById('card073').style.display = 'none';
                        limpiarCampos073();
                    }
                </script>
            </div>

            <div class="074">
                <div class="row odd-row">
                    <div class="col-lg-10">
                        <p>Hernia umbilical</p>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[074][respuesta]" value="si" id="rsi" onclick="mostrarFormulario074()">
                                <label class="form-check-label" for="rsi">SI</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[074][respuesta]" value="no" id="rno" onclick="ocultarFormulario074()">
                                <label class="form-check-label" for="rno">NO</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="card074" style="display: none;">
                    <div class="col-lg-4">
                        {!! Form::label('', 'Hace cuanto') !!}
                        <div class="form-group">
                            <div class="input-group">
                                {!! Form::text('hacecuanto074', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '3', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']) !!}
                                <div class="input-group-append">
                                    {!! Form::select('periodotipo074', ['' => '', 'DIAS' => 'DIAS', 'SEMANAS' => 'SEMANAS', 'MESES' => 'MESES', 'AÑOS' => 'AÑOS'], null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                        </div>
                    </div>   
                </div>
                <script>
                    function limpiarCampos074() {
                        document.getElementById('card074').querySelectorAll('input[type="text"]').forEach(function(input) {
                            input.value = '';
                        });
                        document.getElementById('card074').querySelectorAll('select').forEach(function(select) {
                            select.value = '';
                        });
                    }
                    function mostrarFormulario074() {
                        document.getElementById('card074').style.display = 'block';
                    }
                    function ocultarFormulario074() {
                        document.getElementById('card074').style.display = 'none';
                        limpiarCampos074();
                    }
                </script>
            </div>

            <br><h5 style="text-align: rigth" style="">GINECOLOGIA</h5>

            <div class="075">
                <div class="row">
                    <div class="col-lg-10">
                        <p>Endometriosis</p>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[075][respuesta]" value="si" id="rsi" onclick="mostrarFormulario075()">
                                <label class="form-check-label" for="rsi">SI</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[075][respuesta]" value="no" id="rno" onclick="ocultarFormulario075()">
                                <label class="form-check-label" for="rno">NO</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="card075" style="display: none;">
                    <div class="col-lg-4">
                        {!! Form::label('', 'Hace cuanto') !!}
                        <div class="form-group">
                            <div class="input-group">
                                {!! Form::text('hacecuanto075', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '3', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']) !!}
                                <div class="input-group-append">
                                    {!! Form::select('periodotipo075', ['' => '', 'DIAS' => 'DIAS', 'SEMANAS' => 'SEMANAS', 'MESES' => 'MESES', 'AÑOS' => 'AÑOS'], null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                        </div>
                    </div>   
                </div>
                <script>
                    function limpiarCampos075() {
                        document.getElementById('card075').querySelectorAll('input[type="text"]').forEach(function(input) {
                            input.value = '';
                        });
                        document.getElementById('card075').querySelectorAll('select').forEach(function(select) {
                            select.value = '';
                        });
                    }
                    function mostrarFormulario075() {
                        document.getElementById('card075').style.display = 'block';
                    }
                    function ocultarFormulario075() {
                        document.getElementById('card075').style.display = 'none';
                        limpiarCampos075();
                    }
                </script>
            </div>

            <div class="076">
                <div class="row odd-row">
                    <div class="col-lg-10">
                        <p>Miomas uterinos</p>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[076][respuesta]" value="si" id="rsi" onclick="mostrarFormulario076()">
                                <label class="form-check-label" for="rsi">SI</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[076][respuesta]" value="no" id="rno" onclick="ocultarFormulario076()">
                                <label class="form-check-label" for="rno">NO</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="card076" style="display: none;">
                    <div class="col-lg-4">
                        {!! Form::label('', 'Hace cuanto') !!}
                        <div class="form-group">
                            <div class="input-group">
                                {!! Form::text('hacecuanto076', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '3', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']) !!}
                                <div class="input-group-append">
                                    {!! Form::select('periodotipo076', ['' => '', 'DIAS' => 'DIAS', 'SEMANAS' => 'SEMANAS', 'MESES' => 'MESES', 'AÑOS' => 'AÑOS'], null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                        </div>
                    </div>   
                </div>
                <script>
                    function limpiarCampos076() {
                        document.getElementById('card076').querySelectorAll('input[type="text"]').forEach(function(input) {
                            input.value = '';
                        });
                        document.getElementById('card076').querySelectorAll('select').forEach(function(select) {
                            select.value = '';
                        });
                    }
                    function mostrarFormulario076() {
                        document.getElementById('card076').style.display = 'block';
                    }
                    function ocultarFormulario076() {
                        document.getElementById('card076').style.display = 'none';
                        limpiarCampos076();
                    }
                </script>
            </div>

            <div class="077">
                <div class="row">
                    <div class="col-lg-10">
                        <p>Polipos uterinos</p>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[077][respuesta]" value="si" id="rsi" onclick="mostrarFormulario077()">
                                <label class="form-check-label" for="rsi">SI</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[077][respuesta]" value="no" id="rno" onclick="ocultarFormulario077()">
                                <label class="form-check-label" for="rno">NO</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="card077" style="display: none;">
                    <div class="col-lg-4">
                        {!! Form::label('', 'Hace cuanto') !!}
                        <div class="form-group">
                            <div class="input-group">
                                {!! Form::text('hacecuanto077', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '3', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']) !!}
                                <div class="input-group-append">
                                    {!! Form::select('periodotipo077', ['' => '', 'DIAS' => 'DIAS', 'SEMANAS' => 'SEMANAS', 'MESES' => 'MESES', 'AÑOS' => 'AÑOS'], null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                        </div>
                    </div>   
                </div>
                <script>
                    function limpiarCampos077() {
                        document.getElementById('card077').querySelectorAll('input[type="text"]').forEach(function(input) {
                            input.value = '';
                        });
                        document.getElementById('card077').querySelectorAll('select').forEach(function(select) {
                            select.value = '';
                        });
                    }
                    function mostrarFormulario077() {
                        document.getElementById('card077').style.display = 'block';
                    }
                    function ocultarFormulario077() {
                        document.getElementById('card077').style.display = 'none';
                        limpiarCampos077();
                    }
                </script>
            </div>

            <div class="078">
                <div class="row odd-row">
                    <div class="col-lg-10">
                        <p>Quistes de ovario</p>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[078][respuesta]" value="si" id="rsi" onclick="mostrarFormulario078()">
                                <label class="form-check-label" for="rsi">SI</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[078][respuesta]" value="no" id="rno" onclick="ocultarFormulario078()">
                                <label class="form-check-label" for="rno">NO</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="card078" style="display: none;">
                    <div class="col-lg-4">
                        {!! Form::label('', 'Hace cuanto') !!}
                        <div class="form-group">
                            <div class="input-group">
                                {!! Form::text('hacecuanto078', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '3', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']) !!}
                                <div class="input-group-append">
                                    {!! Form::select('periodotipo078', ['' => '', 'DIAS' => 'DIAS', 'SEMANAS' => 'SEMANAS', 'MESES' => 'MESES', 'AÑOS' => 'AÑOS'], null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                        </div>
                    </div>   
                </div>
                <script>
                    function limpiarCampos078() {
                        document.getElementById('card078').querySelectorAll('input[type="text"]').forEach(function(input) {
                            input.value = '';
                        });
                        document.getElementById('card078').querySelectorAll('select').forEach(function(select) {
                            select.value = '';
                        });
                    }
                    function mostrarFormulario078() {
                        document.getElementById('card078').style.display = 'block';
                    }
                    function ocultarFormulario078() {
                        document.getElementById('card078').style.display = 'none';
                        limpiarCampos078();
                    }
                </script>
            </div>

            <div class="079">
                <div class="row">
                    <div class="col-lg-10">
                        <p>Prolapso genital</p>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[079][respuesta]" value="si" id="rsi" onclick="mostrarFormulario079()">
                                <label class="form-check-label" for="rsi">SI</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="preguntas[079][respuesta]" value="no" id="rno" onclick="ocultarFormulario079()">
                                <label class="form-check-label" for="rno">NO</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="card079" style="display: none;">
                    <div class="col-lg-4">
                        {!! Form::label('', 'Hace cuanto') !!}
                        <div class="form-group">
                            <div class="input-group">
                                {!! Form::text('hacecuanto079', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '3', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']) !!}
                                <div class="input-group-append">
                                    {!! Form::select('periodotipo079', ['' => '', 'DIAS' => 'DIAS', 'SEMANAS' => 'SEMANAS', 'MESES' => 'MESES', 'AÑOS' => 'AÑOS'], null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                        </div>
                    </div>   
                </div>
                <script>
                    function limpiarCampos079() {
                        document.getElementById('card079').querySelectorAll('input[type="text"]').forEach(function(input) {
                            input.value = '';
                        });
                        document.getElementById('card079').querySelectorAll('select').forEach(function(select) {
                            select.value = '';
                        });
                    }
                    function mostrarFormulario079() {
                        document.getElementById('card079').style.display = 'block';
                    }
                    function ocultarFormulario079() {
                        document.getElementById('card079').style.display = 'none';
                        limpiarCampos079();
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
                            <select class="form-control estadocigarrillos" name="estadocigarrillos">
                                <option value="No fuma">No fuma</option>
                                <option value="Ex fumador">Ex fumador</option>
                                <option value="Fumador">Fumador</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-2 anossuspensionfumador hidden">
                        <label>Tiempo de suspension</label>
                        <div class="form-group">
                            <div class="input-group">
                                {!! Form::text('suspcigarillos', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '2', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57', 'disabled']) !!}
                                <div class="input-group-append">
                                    {!! Form::select('tiemposuspcigarillos', ['' => '', 'DIAS' => 'DIAS', 'SEMANAS' => 'SEMANAS', 'MESES' => 'MESES', 'AÑOS' => 'AÑOS'], null, ['class' => 'form-control', 'disabled']) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 frecuenciafumador hidden">
                        <label>Frecuencia</label>
                        <div class="form-group">
                            <div class="input-group">
                                {!! Form::text('freccigarillos', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '2', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57', 'disabled']) !!}
                                <div class="input-group-append">
                                    {!! Form::select('tiempofreccigarillos', ['' => '', 'AL DIA' => 'AL DIA', 'A LA SEMANA' => 'A LA SEMANA', 'AL MES' => 'AL MES', 'AL AÑO' => 'AL AÑO'], null, ['class' => 'form-control', 'disabled']) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-2 anosfumador hidden">
                        <label>Tiempo de consumo</label>
                        <div class="form-group">
                            <div class="input-group">
                                {!! Form::text('consumocigarillos', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '2', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57', 'disabled']) !!}
                                <div class="input-group-append">
                                    {!! Form::select('tiempoconscigarillos', ['' => '', 'DIAS' => 'DIAS', 'SEMANAS' => 'SEMANAS', 'MESES' => 'MESES', 'AÑOS' => 'AÑOS'], null, ['class' => 'form-control', 'disabled']) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-2 numerocigarrillos hidden">
                        <label>Nro. de cigarrillos / Día</label>
                        <div class="form-group">
                            <input type="text" name="numerocigarrillos" class="form-control numerocigarrillos-input" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 3);" disabled>
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
                                    if (this.value === "No Fuma") {
                                        parentSection.querySelector('.anossuspensionfumador .form-control').value = "";
                                        parentSection.querySelector('.anossuspensionfumador select').value = "";
                                        parentSection.querySelector('.frecuenciafumador .form-control').value = "";
                                        parentSection.querySelector('.frecuenciafumador select').value = "";
                                        parentSection.querySelector('.numerocigarrillos .form-control').value = "";
                                        parentSection.querySelector('.anosfumador .form-control').value = "";
                                        parentSection.querySelector('.anosfumador select').value = "";
                                    } else if (this.value === "Ex fumador") {
                                        parentSection.querySelector('.anossuspensionfumador .form-control').disabled = false;
                                        parentSection.querySelector('.anossuspensionfumador select').disabled = false;
                                        parentSection.querySelector('.frecuenciafumador .form-control').value = "";
                                        parentSection.querySelector('.frecuenciafumador select').value = "";
                                        parentSection.querySelector('.numerocigarrillos .form-control').value = "";
                                        parentSection.querySelector('.anosfumador .form-control').value = "";
                                        parentSection.querySelector('.anosfumador select').value = "";
                                    } else if (this.value === "Fumador") {
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
                            <select class="form-control estadoalcoholismo" name="estadoalcoholismo">
                                <option value="No bebe">No bebe</option>
                                <option value="Ex bebedor">Ex bebedor</option>
                                <option value="Bebedor">Bebedor</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-2 anossuspensionalcohol hidden">
                        <label>Tiempo de suspension</label>
                        <div class="form-group">
                            <div class="form-group">
                                <div class="input-group">
                                    {!! Form::text('suspensionalcohol', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '2', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57', 'disabled']) !!}
                                    <div class="input-group-append">
                                        {!! Form::select('tiemposuspalcohol', ['' => '', 'DIAS' => 'DIAS', 'SEMANAS' => 'SEMANAS', 'MESES' => 'MESES', 'AÑOS' => 'AÑOS'], null, ['class' => 'form-control', 'disabled']) !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 frecuenciaalcohol hidden">
                        <label>Frecuencia</label>
                        <div class="form-group">
                            <div class="input-group">
                                {!! Form::text('frecuenciaalcohol', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '2', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57', 'disabled']) !!}
                                <div class="input-group-append">
                                    {!! Form::select('tiempofrecalcohol', ['' => '', 'AL DIA' => 'AL DIA', 'A LA SEMANA' => 'A LA SEMANA', 'AL MES' => 'AL MES', 'AL AÑO' => 'AL AÑO'], null, ['class' => 'form-control', 'disabled']) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-2 anosalcohol hidden">
                        <label>Tiempo de consumo</label>
                        <div class="form-group">
                            <div class="input-group">
                                {!! Form::text('consumoalcohol', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '2', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57', 'disabled']) !!}
                                <div class="input-group-append">
                                    {!! Form::select('tiempoconsalcohol', ['' => '', 'DIAS' => 'DIAS', 'SEMANAS' => 'SEMANAS', 'MESES' => 'MESES', 'AÑOS' => 'AÑOS'], null, ['class' => 'form-control', 'disabled']) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-2 tipobebida hidden">
                        <label>Tipo de bebida</label>
                        <div class="form-group">
                            <input type="text" name="tipobebida" class="form-control" disabled>
                        </div>
                    </div>
                </div>
                <script>
                    document.addEventListener("DOMContentLoaded", function() {
                            var selectElements = document.querySelectorAll('.estadoalcoholismo');
                            selectElements.forEach(function(selectElement) {
                                selectElement.addEventListener("change", function() {
                                    var parentSection = this.closest('.section');
                                    var relatedElements = parentSection.querySelectorAll('.form-control');
                
                                    relatedElements.forEach(function(element) {
                                        if (element !== selectElement) {
                                            element.disabled = true;
                                        }
                                    });
                                    if (this.value === "No bebe") {
                                        parentSection.querySelector('.anossuspensionfumador .form-control').value = "";
                                        parentSection.querySelector('.anossuspensionfumador select').value = "";
                                        parentSection.querySelector('.frecuenciafumador .form-control').value = "";
                                        parentSection.querySelector('.frecuenciafumador select').value = "";
                                        parentSection.querySelector('.numerocigarrillos .form-control').value = "";
                                        parentSection.querySelector('.anosfumador .form-control').value = "";
                                        parentSection.querySelector('.anosfumador select').value = "";
                                    } else if (this.value === "Ex bebedor") {
                                        parentSection.querySelector('.anossuspensionalcohol .form-control').disabled = false;
                                        parentSection.querySelector('.anossuspensionalcohol select').disabled = false;
                                        parentSection.querySelector('.frecuenciaalcohol .form-control').value = "";
                                        parentSection.querySelector('.frecuenciaalcohol select').value = "";
                                        parentSection.querySelector('.tipobebida .form-control').value = "";
                                        parentSection.querySelector('.anosalcohol .form-control').value = "";
                                        parentSection.querySelector('.anosalcohol select').value = "";
                                    }
                                    else if (this.value === "Bebedor") {
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
                            <select class="form-control estadococa" name="estadococa">
                                <option value="No mastica">No</option>
                                <option value="Si mastica">Si</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-4 anoscoca hidden">
                        <label>Tiempo de consumo</label>
                        <div class="form-group">
                            <div class="input-group">
                                {!! Form::text('consumococa', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '2', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57', 'disabled']) !!}
                                <div class="input-group-append">
                                    {!! Form::select('tiempoconscoca', ['' => '', 'DIAS' => 'DIAS', 'SEMANAS' => 'SEMANAS', 'MESES' => 'MESES', 'AÑOS' => 'AÑOS'], null, ['class' => 'form-control', 'disabled']) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-5 frecuenciacoca hidden">
                        <label>Frecuencia</label>
                        <div class="form-group">
                            <div class="input-group">
                                {!! Form::text('frecuenciacoca', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '2', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57', 'disabled']) !!}
                                <div class="input-group-append">
                                    {!! Form::select('tiempofreccoca', ['' => '', 'AL DIA' => 'AL DIA', 'A LA SEMANA' => 'A LA SEMANA', 'AL MES' => 'AL MES', 'AL AÑO' => 'AL AÑO'], null, ['class' => 'form-control', 'disabled']) !!}
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
                
                                    if (this.value === "Si mastica") {
                                        parentSection.querySelector('.anoscoca .form-control').disabled = false;
                                        parentSection.querySelector('.anoscoca select').disabled = false;
                                        parentSection.querySelector('.frecuenciacoca .form-control').disabled = false;
                                        parentSection.querySelector('.frecuenciacoca select').disabled = false;
                                    } else if (this.value === "No mastica") {
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
                            <select class="form-control estadomedicamento" name="estadomedicamento">
                                <option value="No consume">No</option>
                                <option value="Si consume">Si</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-9 cualesmedicamentos hidden">
                        <label>Cuales</label>
                        <div class="form-group">
                            <input type="text" name="cualesmedicamentos" class="form-control" disabled>
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
                
                                    if (this.value === "Si consume") {
                                        parentSection.querySelector('.cualesmedicamentos .form-control').disabled = false;
                                    } else if (this.value === "No consume") {
                                        parentSection.querySelector('.cualesmedicamentos .form-control').value = "";
                                    }
                                });
                            });
                        });
                </script>
            </div>

            <h4 style="text-align: center;" style="">DATOS ADICIONALES</h4>
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
            <div class="row">
                <div class="col-lg-6">
                    <div class="form-group">
                        {!! Form::label('Combe:') !!}
                        {!! Form::text('combe', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '200']) !!}
                    </div>
                </div>
            </div>
            
            <h4 style="text-align: center;" style="">ANTECEDENTES QUIRURGICOS</h4>
            <div id="card100">
                <div class="row">
                    <div class="col-lg-9">
                        <div class="form-group">
                            {!! Form::label('', 'Antecedente quirurgico 1:') !!}
                            {!! Form::text('preguntas[100][antecedente]', null, ['class' => 'form-control', 'placeholder' => '']) !!}
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="form-group">
                            {!! Form::label('', 'Periodo de tiempo:') !!}
                            {!! Form::text('preguntas[100][periodotiempo]', null, ['class' => 'form-control', 'placeholder' => '']) !!}
                        </div>
                    </div>
                </div>
                <abbr title="Nuevo Formulario"><button type="button" class="btn btn-mas" onclick="mostrarcard200()"><i class="fas fa-plus"></i></button></abbr>
                <abbr id="menosformulario200" title="Menos Formulario" style="display: none;">
                    <button type="button" class="btn btn-menos" onclick="ocultarcard200()"><i class="fas fa-minus"></i></button></abbr>
            </div>
            <div id="card200" style="display: none;">
                <div class="row">
                    <div class="col-lg-9">
                        <div class="form-group">
                            {!! Form::label('', 'Antecedente quirurgico 2:') !!}
                            {!! Form::text('preguntas[200][antecedente]', null, ['class' => 'form-control', 'placeholder' => '']) !!}
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="form-group">
                            {!! Form::label('', 'Periodo de tiempo:') !!}
                            {!! Form::text('preguntas[200][periodotiempo]', null, ['class' => 'form-control', 'placeholder' => '']) !!}
                        </div>
                    </div>
                </div>
                <abbr title="Nuevo Formulario"><button type="button" class="btn btn-mas" onclick="mostrarcard300()"><i class="fas fa-plus"></i></button></abbr>
                <abbr id="menosformulario300" title="Menos Formulario" style="display: none;">
                    <button type="button" class="btn btn-menos" onclick="ocultarcard300()"><i class="fas fa-minus"></i></button></abbr>
            </div>
            <div id="card300" style="display: none;">
                <div class="row">
                    <div class="col-lg-9">
                        <div class="form-group">
                            {!! Form::label('', 'Antecedente quirurgico 3:') !!}
                            {!! Form::text('preguntas[300][antecedente]', null, ['class' => 'form-control', 'placeholder' => '']) !!}
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="form-group">
                            {!! Form::label('', 'Periodo de tiempo:') !!}
                            {!! Form::text('preguntas[300][periodotiempo]', null, ['class' => 'form-control', 'placeholder' => '']) !!}
                        </div>
                    </div>
                </div>
            </div>
            <script>
                function limpiarCampos100() {
                    document.getElementById('card100').querySelectorAll('input[type="text"]').forEach(function(input) {
                        input.value = '';
                    });
                    document.getElementById('card200').querySelectorAll('input[type="text"]').forEach(function(input) {
                        input.value = '';
                    });
                    document.getElementById('card300').querySelectorAll('input[type="text"]').forEach(function(input) {
                        input.value = '';
                    });
                    }
                function mostrarcard200() {
                    document.getElementById('card200').style.display = 'block';
                    document.getElementById('menosformulario200').style.display = 'inline';
                    }
                function ocultarcard200() {
                    var card200 = document.getElementById('card200');
                    if (card200.style.display === 'block') {
                        card200.style.display = 'none';
                        limpiarcard200();
                        document.getElementById('menosformulario200').style.display = 'none';
                        }
                    var card300 = document.getElementById('card300');
                    if (card300.style.display === 'block') {
                        card300.style.display = 'none';
                        limpiarcard300();
                        document.getElementById('menosformulario300').style.display = 'none';
                        }
                    }
                function limpiarcard200() {
                    document.getElementById('card200').querySelectorAll('input[type="text"]').forEach(function(input) {
                        input.value = '';
                        });
                    }
                function mostrarcard300() {
                    document.getElementById('card300').style.display = 'block';
                    document.getElementById('menosformulario300').style.display = 'inline';
                    }
                function ocultarcard300() {
                    var card300 = document.getElementById('card300');
                    if (card300.style.display === 'block') {
                        card300.style.display = 'none';
                        limpiarcard300();
                        document.getElementById('menosformulario300').style.display = 'none';
                        }
                    }
                function limpiarcard300() {
                    document.getElementById('card300').querySelectorAll('input[type="text"]').forEach(function(input) {
                        input.value = '';
                        });
                    }
            </script>

            <h4 style="text-align: center;" style="">ANTECEDENTES TRAUMATICOS</h4>
            <div id="card1000">
                <div class="row">
                    <div class="col-lg-9">
                        <div class="form-group">
                            {!! Form::label('', 'Antecedente traumatico 1:') !!}
                            {!! Form::text('preguntas[1000][antecedente]', null, ['class' => 'form-control', 'placeholder' => '']) !!}
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="form-group">
                            {!! Form::label('', 'Fecha:') !!}
                            {!! Form::date('preguntas[1000][periodotiempo]', null, ['class' => 'form-control', 'placeholder' => '']) !!}
                        </div>
                    </div>
                </div>
                <abbr title="Nuevo Formulario"><button type="button" class="btn btn-mas" onclick="mostrarcard2000()"><i class="fas fa-plus"></i></button></abbr>
                <abbr id="menosformulario2000" title="Menos Formulario" style="display: none;">
                    <button type="button" class="btn btn-menos" onclick="ocultarcard2000()"><i class="fas fa-minus"></i></button></abbr>
            </div>
            <div id="card2000" style="display: none;">
                <div class="row">
                    <div class="col-lg-9">
                        <div class="form-group">
                            {!! Form::label('', 'Antecedente traumatico 2:') !!}
                            {!! Form::text('preguntas[2000][antecedente]', null, ['class' => 'form-control', 'placeholder' => '']) !!}
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="form-group">
                            {!! Form::label('', 'Fecha:') !!}
                            {!! Form::date('preguntas[2000][periodotiempo]', null, ['class' => 'form-control', 'placeholder' => '']) !!}
                        </div>
                    </div>
                </div>
                <abbr title="Nuevo Formulario"><button type="button" class="btn btn-mas" onclick="mostrarcard3000()"><i class="fas fa-plus"></i></button></abbr>
                <abbr id="menosformulario3000" title="Menos Formulario" style="display: none;">
                    <button type="button" class="btn btn-menos" onclick="ocultarcard3000()"><i class="fas fa-minus"></i></button></abbr>
            </div>
            <div id="card3000" style="display: none;">
                <div class="row">
                    <div class="col-lg-9">
                        <div class="form-group">
                            {!! Form::label('', 'Antecedente traumatico 3:') !!}
                            {!! Form::text('preguntas[3000][antecedente]', null, ['class' => 'form-control', 'placeholder' => '']) !!}
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="form-group">
                            {!! Form::label('', 'Fecha:') !!}
                            {!! Form::date('preguntas[3000][periodotiempo]', null, ['class' => 'form-control', 'placeholder' => '']) !!}
                        </div>
                    </div>
                </div>
            </div>
            <script>
                function limpiarCampos1000() {
                    document.getElementById('card1000').querySelectorAll('input[type="text"]').forEach(function(input) {
                        input.value = '';
                    });
                    document.getElementById('card2000').querySelectorAll('input[type="text"]').forEach(function(input) {
                        input.value = '';
                    });
                    document.getElementById('card3000').querySelectorAll('input[type="text"]').forEach(function(input) {
                        input.value = '';
                    });
                    }
                function mostrarcard2000() {
                    document.getElementById('card2000').style.display = 'block';
                    document.getElementById('menosformulario2000').style.display = 'inline';
                    }
                function ocultarcard2000() {
                    var card2000 = document.getElementById('card2000');
                    if (card2000.style.display === 'block') {
                        card2000.style.display = 'none';
                        limpiarcard2000();
                        document.getElementById('menosformulario2000').style.display = 'none';
                        }
                    var card3000 = document.getElementById('card3000');
                    if (card3000.style.display === 'block') {
                        card3000.style.display = 'none';
                        limpiarcard3000();
                        document.getElementById('menosformulario3000').style.display = 'none';
                        }
                    }
                function limpiarcard2000() {
                    document.getElementById('card2000').querySelectorAll('input[type="text"]').forEach(function(input) {
                        input.value = '';
                        });
                    }
                function mostrarcard3000() {
                    document.getElementById('card3000').style.display = 'block';
                    document.getElementById('menosformulario3000').style.display = 'inline';
                    }
                function ocultarcard3000() {
                    var card3000 = document.getElementById('card3000');
                    if (card3000.style.display === 'block') {
                        card3000.style.display = 'none';
                        limpiarcard3000();
                        document.getElementById('menosformulario3000').style.display = 'none';
                        }
                    }
                function limpiarcard3000() {
                    document.getElementById('card3000').querySelectorAll('input[type="text"]').forEach(function(input) {
                        input.value = '';
                        });
                    }
            </script>

            <h4 style="text-align: center;" style="">ANTECEDENTES FAMILIARES</h4>
            <div class="Familiares">
                <div class="section">
                    <p><strong><span style="font-size: larger;">PADRE</span></strong></p>
                    <div class="row">
                        <div class="col-lg-2">
                            <label>Estado de salud</label>
                            <div class="form-group">
                                <select class="form-control estadosalud" name="estadosaludpadre">
                                    <option value=""></option>
                                    <option value="vivo">Vivo</option>
                                    <option value="fallecido">Fallecido</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-2 edadvi hidden">
                            <label>Edad vivo</label>
                            <div class="form-group">
                                <input type="text" name="edadvivopadre" class="form-control edadvi-input" oninput="this.value = this.value.replace(/\D/g, '').slice(0, 3);" disabled>
                            </div>
                        </div>                        
                        <div class="col-lg-2 edadfa hidden">
                            <label>Edad al fallecer</label>
                            <div class="form-group">
                                <input type="text" name="edadfallecidopadre" class="form-control edadfa-input" oninput="this.value = this.value.replace(/\D/g, '').slice(0, 3);" disabled>
                            </div>
                        </div>
                        <div class="col-lg-3 causafa hidden">
                            <label>Causa de fallecimiento</label>
                            <div class="form-group">
                                <input type="text" name="causafallecidopadre" class="form-control" disabled>
                            </div>
                        </div>
                        <div class="col-lg-3 obser hidden">
                            <label>Observaciones / Enfermedades</label>
                            <div class="form-group">
                                <input type="text" name="enfermedadespadre" class="form-control" disabled>
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
                                <select class="form-control estadosalud" name="estadosaludmadre">
                                    <option value=""></option>
                                    <option value="vivo">Vivo</option>
                                    <option value="fallecido">Fallecido</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-2 edadvi hidden">
                            <label>Edad vivo</label>
                            <div class="form-group">
                                <input type="text" name="edadvivomadre" class="form-control edadvi-input" oninput="this.value = this.value.replace(/\D/g, '').slice(0, 3);" disabled>
                            </div>
                        </div>
                        <div class="col-lg-2 edadfa hidden">
                            <label>Edad al fallecer</label>
                            <div class="form-group">
                                <input type="text" name="edadfallecemadre" class="form-control edadfa-input" oninput="this.value = this.value.replace(/\D/g, '').slice(0, 3);" disabled>
                            </div>
                        </div>
                        <div class="col-lg-3 causafa hidden">
                            <label>Causa de fallecimiento</label>
                            <div class="form-group">
                                <input type="text" name="causafallecemadre" class="form-control" disabled>
                            </div>
                        </div>
                        <div class="col-lg-3 obser hidden">
                            <label>Observaciones / Enfermedades</label>
                            <div class="form-group">
                                <input type="text" name="enfermedadesmadre" class="form-control" disabled>
                            </div>
                        </div>
                    </div>
                </div>

                <br>
                {{-- <div class="section">
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
                </div> --}}
                <div class="section">
                    <p><strong><span style="font-size: larger;">HERMANOS/AS</span></strong></p>
                    <div class="row">
                        <div class="col-lg-2">
                            <label>Cantidad Vivo/Fallecido</label>
                            <div class="form-group">
                                <input type="text" name="cantidadhermanos" class="form-control">
                            </div>
                        </div>
                        <div class="col-lg-2 edadvi">
                            <label>Edad vivo</label>
                            <div class="form-group">
                                <input type="text" name="hermanovivo" class="form-control edadvi-input">
                            </div>
                        </div>
                        <div class="col-lg-2 edadfa">
                            <label>Edad al fallecer</label>
                            <div class="form-group">
                                <input type="text" name="hermanofallece" class="form-control edadfa-input">
                            </div>
                        </div>
                        <div class="col-lg-3 causafa">
                            <label>Causa de fallecimiento</label>
                            <div class="form-group">
                                <input type="text" name="caudafallecehermano" class="form-control">
                            </div>
                        </div>
                        <div class="col-lg-3 obser">
                            <label>Observaciones / Enfermedades</label>
                            <div class="form-group">
                                <input type="text" name="enfermedadeshermano" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>
                <br>
                <div class="section">
                    <p><strong><span style="font-size: larger;">ESPOSO/A</span></strong></p>
                    <div class="row">
                        <div class="col-lg-2">
                            <label>Estado de salud</label>
                            <div class="form-group">
                                <select class="form-control estadosalud" name="estadosaludesposo">
                                    <option value=""></option>
                                    <option value="vivo">Vivo</option>
                                    <option value="fallecido">Fallecido</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-2 edadvi hidden">
                            <label>Edad vivo</label>
                            <div class="form-group">
                                <input type="text" name="edadvivoesposo" class="form-control edadvi-input" oninput="this.value = this.value.replace(/\D/g, '').slice(0, 3);" disabled>
                            </div>
                        </div>
                        <div class="col-lg-2 edadfa hidden">
                            <label>Edad al fallecer</label>
                            <div class="form-group">
                                <input type="text" name="edadfalleceesposo" class="form-control edadfa-input" oninput="this.value = this.value.replace(/\D/g, '').slice(0, 3);" disabled>
                            </div>
                        </div>
                        <div class="col-lg-3 causafa hidden">
                            <label>Causa de fallecimiento</label>
                            <div class="form-group">
                                <input type="text" name="causafalleceesposo" class="form-control" disabled>
                            </div>
                        </div>
                        <div class="col-lg-3 obser hidden">
                            <label>Observaciones / Enfermedades</label>
                            <div class="form-group">
                                <input type="text" name="enfermedadesesposo" class="form-control" disabled>
                            </div>
                        </div>
                    </div>
                </div>

                <br>
                <div class="section">
                    <p><strong><span style="font-size: larger;">HIJOS/AS
                    </span></strong></p>
                    <div class="row">
                        <div class="col-lg-2">
                            <label>Cantidad Vivo/Fallecido</label>
                            <div class="form-group">
                                <input type="text" name="cantidadhijos" class="form-control">
                            </div>
                        </div>
                        <div class="col-lg-2 edadvi">
                            <label>Edad vivo</label>
                            <div class="form-group">
                                <input type="text" name="hijosvivo" class="form-control edadvi-input">
                            </div>
                        </div>
                        <div class="col-lg-2 edadfa">
                            <label>Edad al fallecer</label>
                            <div class="form-group">
                                <input type="text" name="hijosfallece" class="form-control edadfa-input">
                            </div>
                        </div>
                        <div class="col-lg-3 causafa">
                            <label>Causa de fallecimiento</label>
                            <div class="form-group">
                                <input type="text" name="causafallecehijos" class="form-control">
                            </div>
                        </div>
                        <div class="col-lg-3 obser">
                            <label>Observaciones / Enfermedades</label>
                            <div class="form-group">
                                <input type="text" name="enfermedadeshijos" class="form-control">
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
                                            {!! Form::text('hacecuanto30', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '3', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']) !!}
                                            <div class="input-group-append">
                                                {!! Form::select('periodotipo30', ['' => '', 'DIAS' => 'DIAS', 'SEMANAS' => 'SEMANAS', 'MESES' => 'MESES', 'AÑOS' => 'AÑOS'], null, ['class' => 'form-control']) !!}
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
                                            {!! Form::text('hacecuanto31', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '3', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']) !!}
                                            <div class="input-group-append">
                                                {!! Form::select('periodotipo31', ['' => '', 'DIAS' => 'DIAS', 'SEMANAS' => 'SEMANAS', 'MESES' => 'MESES', 'AÑOS' => 'AÑOS'], null, ['class' => 'form-control']) !!}
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
                                            {!! Form::text('hacecuanto32', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '3', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']) !!}
                                            <div class="input-group-append">
                                                {!! Form::select('periodotipo32', ['' => '', 'DIAS' => 'DIAS', 'SEMANAS' => 'SEMANAS', 'MESES' => 'MESES', 'AÑOS' => 'AÑOS'], null, ['class' => 'form-control']) !!}
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
                                            {!! Form::text('hacecuanto33', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '3', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']) !!}
                                            <div class="input-group-append">
                                                {!! Form::select('periodotipo33', ['' => '', 'DIAS' => 'DIAS', 'SEMANAS' => 'SEMANAS', 'MESES' => 'MESES', 'AÑOS' => 'AÑOS'], null, ['class' => 'form-control']) !!}
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
                                            {!! Form::text('hacecuanto34', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '3', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']) !!}
                                            <div class="input-group-append">
                                                {!! Form::select('periodotipo34', ['' => '', 'DIAS' => 'DIAS', 'SEMANAS' => 'SEMANAS', 'MESES' => 'MESES', 'AÑOS' => 'AÑOS'], null, ['class' => 'form-control']) !!}
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
                                            {!! Form::text('hacecuanto35', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '3', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']) !!}
                                            <div class="input-group-append">
                                                {!! Form::select('periodotipo35', ['' => '', 'DIAS' => 'DIAS', 'SEMANAS' => 'SEMANAS', 'MESES' => 'MESES', 'AÑOS' => 'AÑOS'], null, ['class' => 'form-control']) !!}
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
                                            {!! Form::text('hacecuanto36', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '3', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']) !!}
                                            <div class="input-group-append">
                                                {!! Form::select('periodotipo36', ['' => '', 'DIAS' => 'DIAS', 'SEMANAS' => 'SEMANAS', 'MESES' => 'MESES', 'AÑOS' => 'AÑOS'], null, ['class' => 'form-control']) !!}
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
                                            {!! Form::text('hacecuanto37', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '3', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']) !!}
                                            <div class="input-group-append">
                                                {!! Form::select('periodotipo37', ['' => '', 'DIAS' => 'DIAS', 'SEMANAS' => 'SEMANAS', 'MESES' => 'MESES', 'AÑOS' => 'AÑOS'], null, ['class' => 'form-control']) !!}
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
                                            {!! Form::text('hacecuanto38', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '3', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']) !!}
                                            <div class="input-group-append">
                                                {!! Form::select('periodotipo38', ['' => '', 'DIAS' => 'DIAS', 'SEMANAS' => 'SEMANAS', 'MESES' => 'MESES', 'AÑOS' => 'AÑOS'], null, ['class' => 'form-control']) !!}
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
                                            {!! Form::text('hacecuanto39', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '3', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']) !!}
                                            <div class="input-group-append">
                                                {!! Form::select('periodotipo39', ['' => '', 'DIAS' => 'DIAS', 'SEMANAS' => 'SEMANAS', 'MESES' => 'MESES', 'AÑOS' => 'AÑOS'], null, ['class' => 'form-control']) !!}
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
                                            {!! Form::text('hacecuanto40', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '3', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']) !!}
                                            <div class="input-group-append">
                                                {!! Form::select('periodotipo40', ['' => '', 'DIAS' => 'DIAS', 'SEMANAS' => 'SEMANAS', 'MESES' => 'MESES', 'AÑOS' => 'AÑOS'], null, ['class' => 'form-control']) !!}
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
                                            {!! Form::text('hacecuanto41', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '3', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']) !!}
                                            <div class="input-group-append">
                                                {!! Form::select('periodotipo41', ['' => '', 'DIAS' => 'DIAS', 'SEMANAS' => 'SEMANAS', 'MESES' => 'MESES', 'AÑOS' => 'AÑOS'], null, ['class' => 'form-control']) !!}
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
            
            <br><h4 style="text-align: center;" style="">ANTECEDENTES LABORALES</h4>
            <div class="row">
                <div class="col-lg-6">
                    <div class="form-group">
                        {!! Form::label('Fecha inicio:') !!}
                        {!! Form::date('fechainicioatclab', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '45']) !!}
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-group">
                        {!! Form::label('Fecha final:') !!}
                        {!! Form::date('fechafinalatclab', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '45']) !!}
                    </div>
                </div>
            </div>
            <div id="card01">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group">
                            {!! Form::label('', 'Caracteristicas importantes y exigencias:') !!}
                            {!! Form::text('preguntas[1][carac]', null, ['class' => 'form-control', 'placeholder' => '']) !!}
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="form-group">
                            {!! Form::label('', 'Denuncia de accidentes:') !!}
                            {!! Form::date('preguntas[1][denun]', null, ['class' => 'form-control', 'placeholder' => 'FECHA']) !!}
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="form-group">
                            {!! Form::label('', 'Atencion a la capacidad laboral:') !!}
                            {!! Form::date('preguntas[1][aten]', null, ['class' => 'form-control', 'placeholder' => 'FECHA']) !!}
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
                            {!! Form::label('', 'Caracteristicas importantes y exigencias:') !!}
                            {!! Form::text('preguntas[2][carac]', null, ['class' => 'form-control', 'placeholder' => '']) !!}
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="form-group">
                            {!! Form::label('', 'Denuncia de accidentes:') !!}
                            {!! Form::date('preguntas[2][denun]', null, ['class' => 'form-control', 'placeholder' => 'FECHA']) !!}
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="form-group">
                            {!! Form::label('', 'Atencion a la capacidad laboral:') !!}
                            {!! Form::date('preguntas[2][aten]', null, ['class' => 'form-control', 'placeholder' => 'FECHA']) !!}
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
                            {!! Form::label('', 'Caracteristicas importantes y exigencias:') !!}
                            {!! Form::text('preguntas[3][carac]', null, ['class' => 'form-control', 'placeholder' => '']) !!}
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="form-group">
                            {!! Form::label('', 'Denuncia de accidentes:') !!}
                            {!! Form::date('preguntas[3][denun]', null, ['class' => 'form-control', 'placeholder' => 'FECHA']) !!}
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="form-group">
                            {!! Form::label('', 'Atencion a la capacidad laboral:') !!}
                            {!! Form::date('preguntas[3][aten]', null, ['class' => 'form-control', 'placeholder' => 'FECHA']) !!}
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


            <br>
            <h4 style="text-align: center;" style="">HISTORIA DE LA ENFERMEDAD ACTUAL</h4>
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
                            <div class="input-group" >
                                <input type="text" name="pulso" class="form-control" value="" oninput="this.value = this.value.replace(/\D/g, '').slice(0, 3);">
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
                            <input type="text" name="satO2" class="form-control" value="" oninput="this.value = this.value.replace(/\D/g, ''); if(parseInt(this.value) > 100) { this.value = '100'; }">
                            <div class="input-group-append">
                                <span class="input-group-text">%</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-2">
                        <label>F. respiracion</label>
                        <div class="form-group">
                            <div class="input-group">
                                <input type="text" name="frespiracion" class="form-control" value="" oninput="this.value = this.value.replace(/\D/g, '').slice(0, 3);">
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
                                <input type="text" name="temperatura" class="form-control" value="" oninput="this.value = this.value.replace(/[^\d\.]/g, ''); this.value = this.value.match(/^\d{0,2}(\.\d{0,1})?/g)[0];">
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
                                <input type="text" name="presionarterial" class="form-control" value="" oninput="this.value = this.value.replace(/[^\d\/]/g, ''); this.value = this.value.match(/^\d{0,3}(\/\d{0,2})?/g)[0];">
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
                            <input type="text" name="agudezavisual" class="form-control">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <label>Usa lentes</label>
                        <div class="form-group">
                            <select class="form-control" name="usalentes">
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
                                <input type="text" name="peso" class="form-control" value="" oninput="this.value = this.value.replace(/[^\d\.]/g, ''); this.value = this.value.match(/^\d{0,3}(\.\d{0,1})?/g)[0];">
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
                                <input type="text" name="estatura" class="form-control" value="" oninput="this.value = this.value.replace(/[^\d\.]/g, ''); this.value = this.value.match(/^\d{0,1}(\.\d{0,2})?/g)[0];">
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
                                <input type="text" name="imc" class="form-control" value="" oninput="this.value = this.value.replace(/[^\d\.]/g, ''); this.value = this.value.match(/^\d{0,2}(\.\d{0,1})?/g)[0];">
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
                            <input type="text" name="exficabeza" class="form-control">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <label>Ojos</label>
                        <div class="form-group">
                            <input type="text" name="exfiojos" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <label>Nariz</label>
                        <div class="form-group">
                            <input type="text" name="exfinariz" class="form-control">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <label>Oido</label>
                        <div class="form-group">
                            <input type="text" name="exfioidos" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <label>Boca</label>
                        <div class="form-group">
                            <input type="text" name="exfiboca" class="form-control">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <label>Cuello</label>
                        <div class="form-group">
                            <input type="text" name="exficuello" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <label>Torax</label>
                        <div class="form-group">
                            <input type="text" name="exfitorax" class="form-control">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <label>Corazon</label>
                        <div class="form-group">
                            <input type="text" name="exficorazon" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <label>Pulmones</label>
                        <div class="form-group">
                            <input type="text" name="exfipulmones" class="form-control">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <label>Abdomen</label>
                        <div class="form-group">
                            <input type="text" name="exfiabdomen" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <label>Extremidades MMSS</label>
                        <div class="form-group">
                            <input type="text" name="exfiextremidadesmmss" class="form-control">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <label>Extremidades MMII</label>
                        <div class="form-group">
                            <input type="text" name="exfiextremidadesmmii" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <label>Neurologico</label>
                        <div class="form-group">
                            <input type="text" name="exfineurologico" class="form-control">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <label>Vestibulo / Cereboloso</label>
                        <div class="form-group">
                            <input type="text" name="exfivestibulocereboloso" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <label>Marcha</label>
                        <div class="form-group">
                            <input type="text" name="exfimarcha" class="form-control">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <label>Craneo y Columna</label>
                        <div class="form-group">
                            <input type="text" name="exficraneoycolumna" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <label>Exploracion neurovascular</label>
                        <div class="form-group">
                            <input type="text" name="exfiexploracionneuro" class="form-control">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Guardar Formulario</button>
        </form>

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
    .btn-regresar {
        background-color: #ffffff;
        color: #2926e2;
        border-color: #2926e2;
        border-radius: 5px;
        padding: 10px 10px;
    }
    .btn-regresar:hover {
        background-color: #2926e2;
        color: #ffffff;
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