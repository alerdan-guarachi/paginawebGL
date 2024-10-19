@extends('adminlte::page')
    
@section('content_header')

{{-- @can('admin.profiles.create') --}}
{{-- @if ($cliente) --}}
{{-- @else --}}
{{-- <a class="btn btn-crear btn-sm float-right" href="{{route('admin.clientes.create')}}">Crear cliente</a> --}}
{{-- @endif --}}
{{-- @endcan --}}
{{-- <h1>Formulario Medico</h1> --}}
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
        <h3 style="text-align: center;" style="">DECLARACIONES HECHAS AL MEDICO EXAMINADOR</h3>
        
        {{-- <div class="container"> --}}

            <div class="row">
                <div class="col-lg-12">
                    <div class="form-group">
                        {!! Form::label('Propuesto asegurado:') !!}
                        {!! Form::text('apepaterno', $clientebanco->nombrecompleto, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '45', 'readonly' => 'readonly']) !!}
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-lg-4">
                    <div class="form-group">
                        {!! Form::label('Ocupacion y/o Profesion:') !!}
                        {!! Form::text('ocupacion', $clientebanco->ocupacionprofesion, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '45', 'readonly' => 'readonly']) !!}
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="form-group">
                        {!! Form::label('Fecha de nacimiento:') !!}
                        {!! Form::text('fechanacimiento', $clientebanco->fechanacimiento, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '45', 'readonly' => 'readonly']) !!}
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="form-group">
                        {!! Form::label('Genero:') !!}
                        {!! Form::text('genero', $clientebanco->genero, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '45', 'readonly' => 'readonly']) !!}
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-6">
                    <div class="form-group">
                        {!! Form::label('Estado civil:') !!}
                        {!! Form::text('estadocivil', $clientebanco->estadocivil, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '45', 'readonly' => 'readonly']) !!}
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-group">
                        {!! Form::label('CI:') !!}
                        {!! Form::text('ci', $clientebanco->ci, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '45', 'readonly' => 'readonly']) !!}
                    </div>
                </div>
            </div>
        {{-- </div> --}}

            <p><strong><span style="font-size: larger;">POR FAVOR CONTESTE A SU MEJOR SABER Y ENTENDER</span></strong></p>
            <div class="row">
                <div class="col-lg-12">
                    <div class="form-group">
                        {!! Form::label('Nombre y direccion de su medico particular:') !!}
                        {!! Form::text('ND', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '45']) !!}
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="form-group">
                        {!! Form::label('Fecha y motivo de la consulta reciente:') !!}
                        {!! Form::text('FC', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '45']) !!}
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="form-group">
                        {!! Form::label('Que tratamiento o medicacion se transcribio:') !!}
                        {!! Form::text('TM', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '45']) !!}
                    </div>
                </div>
            </div>

@php
    $data = 
    "DECLARACIONES HECHAS AL MEDICO EXAMINADOR" . "\n\n" .
    "Nombres: " . strtoupper($clientebanco->nombrecompleto) . "\n" .
    "Ocupación: " . strtoupper($clientebanco->ocupacionprofesion) . "\n" .
    "Fecha de Nacimiento: " . strtoupper($clientebanco->fechanacimiento) . "\n" .
    "Género: " . strtoupper($clientebanco->genero) . "\n" .
    "Estado Civil: " . strtoupper($clientebanco->estadocivil) . "\n" .
    "CI: " . strtoupper($clientebanco->ci) . "\n\n";
    // Agregar campos adicionales si tienen datos
    if (!empty(request('ND'))) {
        $data .= "Nombre y dirección de su medico particular: " . strtoupper(request('ND')) . "\n";
    }
    if (!empty(request('FC'))) {
        $data .= "Fecha y motivo de la consulta reciente: " . strtoupper(request('FC')) . "\n";
    }
    if (!empty(request('TM'))) {
        $data .= "Que tratamiento o medicación se transcribió: " . strtoupper(request('TM')) . "\n";
    }
@endphp



{{-- <div class="row">
    <div class="col-lg-12">
        <div class="form-group">
            <button id="mostrarQR" class="btn btn-primary">Mostrar QR</button>
            </div>
        </div>
    </div>
    <div class="row" id="contenedorQR" style="display: none;">
        <div class="col-lg-12">
            <div class="form-group">
            <label>Código QR:</label>
           
            <div id="qr-code">{!! QrCode::size(200)->generate($data); !!}</div>

        </div>
    </div>
</div> --}}

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
            <p><strong><span style="font-size: larger;">HA SIDO TRATADO O TIENE CONOCIMIENTO DE HABER PADECIDO DE LAS SIGUIENTES ENFERMEDADES</span></strong></p>
            {!! Form::open(['route' => ['admin.asociados.formularios.guardardeclaracion', $clientebanco], 'method' => 'POST']) !!}


                <div class="1">
                    {!! Form::hidden('preguntas[1][pregunta_id]', 1) !!}
                    {!! Form::hidden('preguntas[1][pregunta_nombre]', 'Enfermedad o defecto de ojos, oídos, nariz o garganta') !!}
                    {!! Form::hidden('preguntas[1][cliente_id]', $clientebanco->id) !!}
                    <div class="row odd-row">
                        <div class="col-lg-10">
                            <p>Enfermedad o defecto de ojos, oídos, nariz o garganta</p>
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
                    <div id="card01" style="display: none;">
                        <div class="row odd-row">
                            <div class="card-body">    
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[1][diagnostico]', null, ['class' => 'form-control', 'placeholder' => 'DIAGNOSTICO']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::date('preguntas[1][fecha]', null, ['class' => 'form-control', 'placeholder' => 'FECHA']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[1][tiempo]', null, ['class' => 'form-control', 'placeholder' => 'TIEMPO']) !!}
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[1][gradorecuperacion]', null, ['class' => 'form-control', 'placeholder' => 'GRADO DE RECUPERACIÓN']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[1][medico]', null, ['class' => 'form-control', 'placeholder' => 'MÉDICO']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[1][direccionmedico]', null, ['class' => 'form-control', 'placeholder' => 'DIRECCIÓN DEL MÉDICO']) !!}
                                        </div>
                                    </div>
                                </div>
                                <abbr title="Nuevo Formulario"><button type="button" class="btn btn-mas" onclick="mostrarSiguienteCard01()"><i class="fas fa-plus"></i></button></abbr>
                                <abbr id="menosFormularioAbbr01" title="Menos Formulario" style="display: none;">
                                    <button type="button" class="btn btn-menos" onclick="ocultarSiguienteCard01()"><i class="fas fa-minus"></i></button></abbr>
                            </div>
                        </div>
                    </div>
                    <div id="siguienteCard01" style="display: none;">
                        <div class="row odd-row">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[1][diagnostico2]', null, ['class' => 'form-control', 'placeholder' => 'DIAGNOSTICO']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::date('preguntas[1][fecha2]', null, ['class' => 'form-control', 'placeholder' => 'FECHA']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[1][tiempo2]', null, ['class' => 'form-control', 'placeholder' => 'TIEMPO']) !!}
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[1][gradorecuperacion2]', null, ['class' => 'form-control', 'placeholder' => 'GRADO DE RECUPERACIÓN']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[1][medico2]', null, ['class' => 'form-control', 'placeholder' => 'MÉDICO']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[1][direccionmedico2]', null, ['class' => 'form-control', 'placeholder' => 'DIRECCIÓN DEL MÉDICO']) !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <script>
                        function limpiarCampos01() {
                            document.getElementById('card01').querySelectorAll('input[type="text"]').forEach(function(input) {
                                input.value = '';
                            });
                            document.getElementById('siguienteCard01').querySelectorAll('input[type="text"]').forEach(function(input) {
                                input.value = '';
                            });
                            }
                        function mostrarFormulario01() {
                            document.getElementById('card01').style.display = 'block';
                            }
                        function ocultarFormulario01() {
                            document.getElementById('card01').style.display = 'none';
                            document.getElementById('siguienteCard01').style.display = 'none';
                            limpiarCampos01();
                            }
                        function mostrarSiguienteCard01() {
                            document.getElementById('siguienteCard01').style.display = 'block';
                            document.getElementById('menosFormularioAbbr01').style.display = 'inline';
                            }
                        function ocultarSiguienteCard01() {
                            var siguienteCard02 = document.getElementById('siguienteCard01');
                            if (siguienteCard02.style.display === 'block') {
                                siguienteCard02.style.display = 'none';
                                limpiarCamposSiguienteCard01();
                                document.getElementById('menosFormularioAbbr01').style.display = 'none';
                                }
                            }
                        function limpiarCamposSiguienteCard01() {
                            document.getElementById('siguienteCard01').querySelectorAll('input[type="text"]').forEach(function(input) {
                                input.value = '';
                                });
                            }
                    </script>
                </div> 

                <div class="2">
                    {!! Form::hidden('preguntas[2][pregunta_id]', 2) !!}
                    {!! Form::hidden('preguntas[2][pregunta_nombre]', 'Mareos, desmayos, convulsiones, cefaleas, torpeza al hablar, parálisis o ataque cerebral; enfermedades de tipo mental  o nerviosa') !!}
                    {!! Form::hidden('preguntas[2][cliente_id]', $clientebanco->id) !!}
                    <div class="row">
                        <div class="col-lg-10">
                            <p>Mareos, desmayos, convulsiones, cefaleas, torpeza al hablar, parálisis o ataque cerebral; enfermedades de tipo mental  o nerviosa</p>
                        </div>
                        <div class="col-lg-2">
                            <div class="form-group">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="preguntas[2][respuesta]" value="si" id="rsi" onclick="mostrarFormulario02()">
                                    <label class="form-check-label" for="rsi">SI</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="preguntas[2][respuesta]" value="no" id="rno" onclick="ocultarFormulario02()">
                                    <label class="form-check-label" for="rno">NO</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="card02" style="display: none;">
                        <div class="row">
                            <div class="card-body">    
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[2][diagnostico]', null, ['class' => 'form-control', 'placeholder' => 'DIAGNOSTICO']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::date('preguntas[2][fecha]', null, ['class' => 'form-control', 'placeholder' => 'FECHA']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[2][tiempo]', null, ['class' => 'form-control', 'placeholder' => 'TIEMPO']) !!}
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[2][gradorecuperacion]', null, ['class' => 'form-control', 'placeholder' => 'GRADO DE RECUPERACIÓN']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[2][medico]', null, ['class' => 'form-control', 'placeholder' => 'MÉDICO']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[2][direccionmedico]', null, ['class' => 'form-control', 'placeholder' => 'DIRECCIÓN DEL MÉDICO']) !!}
                                        </div>
                                    </div>
                                </div>
                                <abbr title="Nuevo Formulario"><button type="button" class="btn btn-mas" onclick="mostrarSiguienteCard02()"><i class="fas fa-plus"></i></button></abbr>
                                <abbr id="menosFormularioAbbr02" title="Menos Formulario" style="display: none;">
                                    <button type="button" class="btn btn-menos" onclick="ocultarSiguienteCard02()">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </abbr>
                            </div>
                        </div>
                    </div>
                    <div id="siguienteCard02" style="display: none;">
                        <div class="row">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[2][diagnostico2]', null, ['class' => 'form-control', 'placeholder' => 'DIAGNOSTICO']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::date('preguntas[2][fecha2]', null, ['class' => 'form-control', 'placeholder' => 'FECHA']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[2][tiempo2]', null, ['class' => 'form-control', 'placeholder' => 'TIEMPO']) !!}
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[2][gradorecuperacion2]', null, ['class' => 'form-control', 'placeholder' => 'GRADO DE RECUPERACIÓN']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[2][medico2]', null, ['class' => 'form-control', 'placeholder' => 'MÉDICO']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[2][direccionmedico2]', null, ['class' => 'form-control', 'placeholder' => 'DIRECCIÓN DEL MÉDICO']) !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <script>
                        function limpiarCampos02() {
                            document.getElementById('card02').querySelectorAll('input[type="text"]').forEach(function(input) {
                                input.value = '';
                            });
                            document.getElementById('siguienteCard02').querySelectorAll('input[type="text"]').forEach(function(input) {
                                input.value = '';
                            });
                            }
                        function mostrarFormulario02() {
                            document.getElementById('card02').style.display = 'block';
                            }
                        function ocultarFormulario02() {
                            document.getElementById('card02').style.display = 'none';
                            document.getElementById('siguienteCard02').style.display = 'none';
                            limpiarCampos02();
                            }
                        function mostrarSiguienteCard02() {
                            document.getElementById('siguienteCard02').style.display = 'block';
                            document.getElementById('menosFormularioAbbr02').style.display = 'inline';
                            }
                        function ocultarSiguienteCard02() {
                            var siguienteCard02 = document.getElementById('siguienteCard02');
                            if (siguienteCard02.style.display === 'block') {
                                siguienteCard02.style.display = 'none';
                                limpiarCamposSiguienteCard02();
                                document.getElementById('menosFormularioAbbr02').style.display = 'none';
                                }
                            }
                        function limpiarCamposSiguienteCard02() {
                            document.getElementById('siguienteCard02').querySelectorAll('input[type="text"]').forEach(function(input) {
                                input.value = '';
                                });
                            }
                    </script>
                </div>

                <div class="3">
                    {!! Form::hidden('preguntas[3][pregunta_id]', 3) !!}
                    {!! Form::hidden('preguntas[3][pregunta_nombre]', 'Dificultad al respirar, ronquera o tos persistente, hemóptisis, bronquitis, pleuresía, asma, efisema, tuberculosis o enfermedad respiratoria crónica') !!}
                    {!! Form::hidden('preguntas[3][cliente_id]', $clientebanco->id) !!}
                    <div class="row odd-row">
                        <div class="col-lg-10">
                            <p>Dificultad al respirar, ronquera o tos persistente, hemóptisis, bronquitis, pleuresía, asma, efisema, tuberculosis o enfermedad respiratoria crónica</p>
                        </div>
                        <div class="col-lg-2">
                            <div class="form-group">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="preguntas[3][respuesta]" value="si" id="rsi" onclick="mostrarFormulario03()">
                                    <label class="form-check-label" for="rsi">SI</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="preguntas[3][respuesta]" value="no" id="rno" onclick="ocultarFormulario03()">
                                    <label class="form-check-label" for="rno">NO</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="card03" style="display: none;">
                        <div class="row odd-row">
                            <div class="card-body">    
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[3][diagnostico]', null, ['class' => 'form-control', 'placeholder' => 'DIAGNOSTICO']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::date('preguntas[3][fecha]', null, ['class' => 'form-control', 'placeholder' => 'FECHA']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[3][tiempo]', null, ['class' => 'form-control', 'placeholder' => 'TIEMPO']) !!}
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[3][gradorecuperacion]', null, ['class' => 'form-control', 'placeholder' => 'GRADO DE RECUPERACIÓN']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[3][medico]', null, ['class' => 'form-control', 'placeholder' => 'MÉDICO']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[3][direccionmedico]', null, ['class' => 'form-control', 'placeholder' => 'DIRECCIÓN DEL MÉDICO']) !!}
                                        </div>
                                    </div>
                                </div>
                                <abbr title="Nuevo Formulario"><button type="button" class="btn btn-mas" onclick="mostrarSiguienteCard03()"><i class="fas fa-plus"></i></button></abbr>
                                <abbr id="menosFormularioAbbr03" title="Menos Formulario" style="display: none;">
                                    <button type="button" class="btn btn-menos" onclick="ocultarSiguienteCard03()">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </abbr>
                            </div>
                        </div>
                    </div>
                    <div id="siguienteCard03" style="display: none;">
                        <div class="row odd-row">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[3][diagnostico2]', null, ['class' => 'form-control', 'placeholder' => 'DIAGNOSTICO']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::date('preguntas[3][fecha2]', null, ['class' => 'form-control', 'placeholder' => 'FECHA']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[3][tiempo2]', null, ['class' => 'form-control', 'placeholder' => 'TIEMPO']) !!}
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[3][gradorecuperacion2]', null, ['class' => 'form-control', 'placeholder' => 'GRADO DE RECUPERACIÓN']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[3][medico2]', null, ['class' => 'form-control', 'placeholder' => 'MÉDICO']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[3][direccionmedico2]', null, ['class' => 'form-control', 'placeholder' => 'DIRECCIÓN DEL MÉDICO']) !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <script>
                        function limpiarCampos03() {
                            document.getElementById('card03').querySelectorAll('input[type="text"]').forEach(function(input) {
                                input.value = '';
                            });
                            document.getElementById('siguienteCard03').querySelectorAll('input[type="text"]').forEach(function(input) {
                                input.value = '';
                            });
                            }
                        function mostrarFormulario03() {
                            document.getElementById('card03').style.display = 'block';
                            }
                        function ocultarFormulario03() {
                            document.getElementById('card03').style.display = 'none';
                            document.getElementById('siguienteCard03').style.display = 'none';
                            limpiarCampos03();
                            }
                        function mostrarSiguienteCard03() {
                            document.getElementById('siguienteCard03').style.display = 'block';
                            document.getElementById('menosFormularioAbbr03').style.display = 'inline';
                            }
                        function ocultarSiguienteCard03() {
                            var siguienteCard02 = document.getElementById('siguienteCard03');
                            if (siguienteCard02.style.display === 'block') {
                                siguienteCard02.style.display = 'none';
                                limpiarCamposSiguienteCard03();
                                document.getElementById('menosFormularioAbbr03').style.display = 'none';
                                }
                            }
                        function limpiarCamposSiguienteCard03() {
                            document.getElementById('siguienteCard03').querySelectorAll('input[type="text"]').forEach(function(input) {
                                input.value = '';
                                });
                            }
                    </script>
                </div>

                <div class="4">
                    {!! Form::hidden('preguntas[4][pregunta_id]', 4) !!}
                    {!! Form::hidden('preguntas[4][pregunta_nombre]', 'Dolor en el pecho, palpitaciones, hipertensión, fiebre reumática, soplo cardíaco, ataque cardíaco u otra enfermedad del sistema cardiovascular') !!}
                    {!! Form::hidden('preguntas[4][cliente_id]', $clientebanco->id) !!}
                    <div class="row">
                        <div class="col-lg-10">
                            <p>Dolor en el pecho, palpitaciones, hipertensión, fiebre reumática, soplo cardíaco, ataque cardíaco u otra enfermedad del sistema cardiovascular</p>
                        </div>
                        <div class="col-lg-2">
                            <div class="form-group">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="preguntas[4][respuesta]" value="si" id="rsi" onclick="mostrarFormulario04()">
                                    <label class="form-check-label" for="rsi">SI</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="preguntas[4][respuesta]" value="no" id="rno" onclick="ocultarFormulario04()">
                                    <label class="form-check-label" for="rno">NO</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="card04" style="display: none;">
                        <div class="row">
                            <div class="card-body">    
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[4][diagnostico]', null, ['class' => 'form-control', 'placeholder' => 'DIAGNOSTICO']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::date('preguntas[4][fecha]', null, ['class' => 'form-control', 'placeholder' => 'FECHA']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[4][tiempo]', null, ['class' => 'form-control', 'placeholder' => 'TIEMPO']) !!}
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[4][gradorecuperacion]', null, ['class' => 'form-control', 'placeholder' => 'GRADO DE RECUPERACIÓN']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[4][medico]', null, ['class' => 'form-control', 'placeholder' => 'MÉDICO']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[4][direccionmedico]', null, ['class' => 'form-control', 'placeholder' => 'DIRECCIÓN DEL MÉDICO']) !!}
                                        </div>
                                    </div>
                                </div>
                                <abbr title="Nuevo Formulario"><button type="button" class="btn btn-mas" onclick="mostrarSiguienteCard04()"><i class="fas fa-plus"></i></button></abbr>
                                <abbr id="menosFormularioAbbr04" title="Menos Formulario" style="display: none;">
                                    <button type="button" class="btn btn-menos" onclick="ocultarSiguienteCard04()">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </abbr>
                            </div>
                        </div>
                    </div>
                    <div id="siguienteCard04" style="display: none;">
                        <div class="row">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[4][diagnostico2]', null, ['class' => 'form-control', 'placeholder' => 'DIAGNOSTICO']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::date('preguntas[4][fecha2]', null, ['class' => 'form-control', 'placeholder' => 'FECHA']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[4][tiempo2]', null, ['class' => 'form-control', 'placeholder' => 'TIEMPO']) !!}
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[4][gradorecuperacion2]', null, ['class' => 'form-control', 'placeholder' => 'GRADO DE RECUPERACIÓN']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[4][medico2]', null, ['class' => 'form-control', 'placeholder' => 'MÉDICO']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[4][direccionmedico2]', null, ['class' => 'form-control', 'placeholder' => 'DIRECCIÓN DEL MÉDICO']) !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <script>
                        function limpiarCampos04() {
                            document.getElementById('card04').querySelectorAll('input[type="text"]').forEach(function(input) {
                                input.value = '';
                            });
                            document.getElementById('siguienteCard04').querySelectorAll('input[type="text"]').forEach(function(input) {
                                input.value = '';
                            });
                            }
                        function mostrarFormulario04() {
                            document.getElementById('card04').style.display = 'block';
                            }
                        function ocultarFormulario04() {
                            document.getElementById('card04').style.display = 'none';
                            document.getElementById('siguienteCard04').style.display = 'none';
                            limpiarCampos04();
                            }
                        function mostrarSiguienteCard04() {
                            document.getElementById('siguienteCard04').style.display = 'block';
                            document.getElementById('menosFormularioAbbr04').style.display = 'inline';
                            }
                        function ocultarSiguienteCard04() {
                            var siguienteCard02 = document.getElementById('siguienteCard04');
                            if (siguienteCard02.style.display === 'block') {
                                siguienteCard02.style.display = 'none';
                                limpiarCamposSiguienteCard04();
                                document.getElementById('menosFormularioAbbr04').style.display = 'none';
                                }
                            }
                        function limpiarCamposSiguienteCard04() {
                            document.getElementById('siguienteCard04').querySelectorAll('input[type="text"]').forEach(function(input) {
                                input.value = '';
                                });
                            }
                    </script>
                </div>

                <div class="5">
                    {!! Form::hidden('preguntas[5][pregunta_id]', 5) !!}
                    {!! Form::hidden('preguntas[5][pregunta_nombre]', 'Ictericia, hemorragia intestinal, úlcera, hernia, apendicitis, colitis, diverticulitis, hemorroides, indigestión frecuente, o cualquier otra enfermedad del estómago, intestinos, hígado o vesícula') !!}
                    {!! Form::hidden('preguntas[5][cliente_id]', $clientebanco->id) !!}
                    <div class="row odd-row">
                        <div class="col-lg-10">
                            <p>Ictericia, hemorragia intestinal, úlcera, hernia, apendicitis, colitis, diverticulitis, hemorroides, indigestión frecuente, o cualquier otra enfermedad del estómago, intestinos, hígado o vesícula</p>
                        </div>
                        <div class="col-lg-2">
                            <div class="form-group">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="preguntas[5][respuesta]" value="si" id="rsi" onclick="mostrarFormulario05()">
                                    <label class="form-check-label" for="rsi">SI</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="preguntas[5][respuesta]" value="no" id="rno" onclick="ocultarFormulario05()">
                                    <label class="form-check-label" for="rno">NO</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="card05" style="display: none;">
                        <div class="row odd-row">
                            <div class="card-body">    
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[5][diagnostico]', null, ['class' => 'form-control', 'placeholder' => 'DIAGNOSTICO']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::date('preguntas[5][fecha]', null, ['class' => 'form-control', 'placeholder' => 'FECHA']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[5][tiempo]', null, ['class' => 'form-control', 'placeholder' => 'TIEMPO']) !!}
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[5][gradorecuperacion]', null, ['class' => 'form-control', 'placeholder' => 'GRADO DE RECUPERACIÓN']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[5][medico]', null, ['class' => 'form-control', 'placeholder' => 'MÉDICO']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[5][direccionmedico]', null, ['class' => 'form-control', 'placeholder' => 'DIRECCIÓN DEL MÉDICO']) !!}
                                        </div>
                                    </div>
                                </div>
                                <abbr title="Nuevo Formulario"><button type="button" class="btn btn-mas" onclick="mostrarSiguienteCard05()"><i class="fas fa-plus"></i></button></abbr>
                                <abbr id="menosFormularioAbbr05" title="Menos Formulario" style="display: none;">
                                    <button type="button" class="btn btn-menos" onclick="ocultarSiguienteCard05()">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </abbr>
                            </div>
                        </div>
                    </div>
                    <div id="siguienteCard05" style="display: none;">
                        <div class="row odd-row">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[5][diagnostico2]', null, ['class' => 'form-control', 'placeholder' => 'DIAGNOSTICO']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::date('preguntas[5][fecha2]', null, ['class' => 'form-control', 'placeholder' => 'FECHA']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[5][tiempo2]', null, ['class' => 'form-control', 'placeholder' => 'TIEMPO']) !!}
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[5][gradorecuperacion2]', null, ['class' => 'form-control', 'placeholder' => 'GRADO DE RECUPERACIÓN']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[5][medico2]', null, ['class' => 'form-control', 'placeholder' => 'MÉDICO']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[5][direccionmedico2]', null, ['class' => 'form-control', 'placeholder' => 'DIRECCIÓN DEL MÉDICO']) !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <script>
                        function limpiarCampos05() {
                            document.getElementById('card05').querySelectorAll('input[type="text"]').forEach(function(input) {
                                input.value = '';
                            });
                            document.getElementById('siguienteCard05').querySelectorAll('input[type="text"]').forEach(function(input) {
                                input.value = '';
                            });
                            }
                        function mostrarFormulario05() {
                            document.getElementById('card05').style.display = 'block';
                            }
                        function ocultarFormulario05() {
                            document.getElementById('card05').style.display = 'none';
                            document.getElementById('siguienteCard05').style.display = 'none';
                            limpiarCampos05();
                            }
                        function mostrarSiguienteCard05() {
                            document.getElementById('siguienteCard05').style.display = 'block';
                            document.getElementById('menosFormularioAbbr05').style.display = 'inline';
                            }
                        function ocultarSiguienteCard05() {
                            var siguienteCard02 = document.getElementById('siguienteCard05');
                            if (siguienteCard02.style.display === 'block') {
                                siguienteCard02.style.display = 'none';
                                limpiarCamposSiguienteCard05();
                                document.getElementById('menosFormularioAbbr05').style.display = 'none';
                                }
                            }
                        function limpiarCamposSiguienteCard05() {
                            document.getElementById('siguienteCard05').querySelectorAll('input[type="text"]').forEach(function(input) {
                                input.value = '';
                                });
                            }
                    </script>
                </div>

                <div class="6">
                    {!! Form::hidden('preguntas[6][pregunta_id]', 6) !!}
                    {!! Form::hidden('preguntas[6][pregunta_nombre]', 'Azúcar, albúmina, sangre o pus en la orina, enfermedad venérea, piedra u otra enfermedad de los riñones, vejiga, próstata o aparato reproductor') !!}
                    {!! Form::hidden('preguntas[6][cliente_id]', $clientebanco->id) !!}
                    <div class="row">
                        <div class="col-lg-10">
                            <p>Azúcar, albúmina, sangre o pus en la orina, enfermedad venérea, piedra u otra enfermedad de los riñones, vejiga, próstata o aparato reproductor</p>
                        </div>
                        <div class="col-lg-2">
                            <div class="form-group">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="preguntas[6][respuesta]" value="si" id="rsi" onclick="mostrarFormulario06()">
                                    <label class="form-check-label" for="rsi">SI</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="preguntas[6][respuesta]" value="no" id="rno" onclick="ocultarFormulario06()">
                                    <label class="form-check-label" for="rno">NO</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="card06" style="display: none;">
                        <div class="row">
                            <div class="card-body">    
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[6][diagnostico]', null, ['class' => 'form-control', 'placeholder' => 'DIAGNOSTICO']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::date('preguntas[6][fecha]', null, ['class' => 'form-control', 'placeholder' => 'FECHA']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[6][tiempo]', null, ['class' => 'form-control', 'placeholder' => 'TIEMPO']) !!}
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[6][gradorecuperacion]', null, ['class' => 'form-control', 'placeholder' => 'GRADO DE RECUPERACIÓN']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[6][medico]', null, ['class' => 'form-control', 'placeholder' => 'MÉDICO']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[6][direccionmedico]', null, ['class' => 'form-control', 'placeholder' => 'DIRECCIÓN DEL MÉDICO']) !!}
                                        </div>
                                    </div>
                                </div>
                                <abbr title="Nuevo Formulario"><button type="button" class="btn btn-mas" onclick="mostrarSiguienteCard06()"><i class="fas fa-plus"></i></button></abbr>
                                <abbr id="menosFormularioAbbr06" title="Menos Formulario" style="display: none;">
                                    <button type="button" class="btn btn-menos" onclick="ocultarSiguienteCard06()">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </abbr>
                            </div>
                        </div>
                    </div>
                    <div id="siguienteCard06" style="display: none;">
                        <div class="row">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[6][diagnostico2]', null, ['class' => 'form-control', 'placeholder' => 'DIAGNOSTICO']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::date('preguntas[6][fecha2]', null, ['class' => 'form-control', 'placeholder' => 'FECHA']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[6][tiempo2]', null, ['class' => 'form-control', 'placeholder' => 'TIEMPO']) !!}
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[6][gradorecuperacion2]', null, ['class' => 'form-control', 'placeholder' => 'GRADO DE RECUPERACIÓN']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[6][medico2]', null, ['class' => 'form-control', 'placeholder' => 'MÉDICO']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[6][direccionmedico2]', null, ['class' => 'form-control', 'placeholder' => 'DIRECCIÓN DEL MÉDICO']) !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <script>
                        function limpiarCampos06() {
                            document.getElementById('card06').querySelectorAll('input[type="text"]').forEach(function(input) {
                                input.value = '';
                            });
                            document.getElementById('siguienteCard06').querySelectorAll('input[type="text"]').forEach(function(input) {
                                input.value = '';
                            });
                            }
                        function mostrarFormulario06() {
                            document.getElementById('card06').style.display = 'block';
                            }
                        function ocultarFormulario06() {
                            document.getElementById('card06').style.display = 'none';
                            document.getElementById('siguienteCard06').style.display = 'none';
                            limpiarCampos06();
                            }
                        function mostrarSiguienteCard06() {
                            document.getElementById('siguienteCard06').style.display = 'block';
                            document.getElementById('menosFormularioAbbr06').style.display = 'inline';
                            }
                        function ocultarSiguienteCard06() {
                            var siguienteCard02 = document.getElementById('siguienteCard06');
                            if (siguienteCard02.style.display === 'block') {
                                siguienteCard02.style.display = 'none';
                                limpiarCamposSiguienteCard06();
                                document.getElementById('menosFormularioAbbr06').style.display = 'none';
                                }
                            }
                        function limpiarCamposSiguienteCard06() {
                            document.getElementById('siguienteCard06').querySelectorAll('input[type="text"]').forEach(function(input) {
                                input.value = '';
                                });
                            }
                    </script>
                </div>

                <div class="7">
                    {!! Form::hidden('preguntas[7][pregunta_id]', 7) !!}
                    {!! Form::hidden('preguntas[7][pregunta_nombre]', 'Diabetes, enfermedad de la tiroides u otras glándulas endocrinas') !!}
                    {!! Form::hidden('preguntas[7][cliente_id]', $clientebanco->id) !!}
                    <div class="row odd-row">
                        <div class="col-lg-10">
                            <p>Diabetes, enfermedad de la tiroides u otras glándulas endocrinas</p>
                        </div>
                        <div class="col-lg-2">
                            <div class="form-group">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="preguntas[7][respuesta]" value="si" id="rsi" onclick="mostrarFormulario07()">
                                    <label class="form-check-label" for="rsi">SI</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="preguntas[7][respuesta]" value="no" id="rno" onclick="ocultarFormulario07()">
                                    <label class="form-check-label" for="rno">NO</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="card07" style="display: none;">
                        <div class="row odd-row">
                            <div class="card-body">    
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[7][diagnostico]', null, ['class' => 'form-control', 'placeholder' => 'DIAGNOSTICO']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::date('preguntas[7][fecha]', null, ['class' => 'form-control', 'placeholder' => 'FECHA']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[7][tiempo]', null, ['class' => 'form-control', 'placeholder' => 'TIEMPO']) !!}
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[7][gradorecuperacion]', null, ['class' => 'form-control', 'placeholder' => 'GRADO DE RECUPERACIÓN']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[7][medico]', null, ['class' => 'form-control', 'placeholder' => 'MÉDICO']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[7][direccionmedico]', null, ['class' => 'form-control', 'placeholder' => 'DIRECCIÓN DEL MÉDICO']) !!}
                                        </div>
                                    </div>
                                </div>
                                <abbr title="Nuevo Formulario"><button type="button" class="btn btn-mas" onclick="mostrarSiguienteCard07()"><i class="fas fa-plus"></i></button></abbr>
                                <abbr id="menosFormularioAbbr07" title="Menos Formulario" style="display: none;">
                                    <button type="button" class="btn btn-menos" onclick="ocultarSiguienteCard07()">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </abbr>
                            </div>
                        </div>
                    </div>
                    <div id="siguienteCard07" style="display: none;">
                        <div class="row odd-row">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[7][diagnostico2]', null, ['class' => 'form-control', 'placeholder' => 'DIAGNOSTICO']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::date('preguntas[7][fecha2]', null, ['class' => 'form-control', 'placeholder' => 'FECHA']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[7][tiempo2]', null, ['class' => 'form-control', 'placeholder' => 'TIEMPO']) !!}
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[7][gradorecuperacion2]', null, ['class' => 'form-control', 'placeholder' => 'GRADO DE RECUPERACIÓN']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[7][medico2]', null, ['class' => 'form-control', 'placeholder' => 'MÉDICO']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[7][direccionmedico2]', null, ['class' => 'form-control', 'placeholder' => 'DIRECCIÓN DEL MÉDICO']) !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <script>
                        function limpiarCampos07() {
                            document.getElementById('card07').querySelectorAll('input[type="text"]').forEach(function(input) {
                                input.value = '';
                            });
                            document.getElementById('siguienteCard07').querySelectorAll('input[type="text"]').forEach(function(input) {
                                input.value = '';
                            });
                            }
                        function mostrarFormulario07() {
                            document.getElementById('card07').style.display = 'block';
                            }
                        function ocultarFormulario07() {
                            document.getElementById('card07').style.display = 'none';
                            document.getElementById('siguienteCard07').style.display = 'none';
                            limpiarCampos07();
                            }
                        function mostrarSiguienteCard07() {
                            document.getElementById('siguienteCard07').style.display = 'block';
                            document.getElementById('menosFormularioAbbr07').style.display = 'inline';
                            }
                        function ocultarSiguienteCard07() {
                            var siguienteCard02 = document.getElementById('siguienteCard07');
                            if (siguienteCard02.style.display === 'block') {
                                siguienteCard02.style.display = 'none';
                                limpiarCamposSiguienteCard07();
                                document.getElementById('menosFormularioAbbr07').style.display = 'none';
                                }
                            }
                        function limpiarCamposSiguienteCard07() {
                            document.getElementById('siguienteCard07').querySelectorAll('input[type="text"]').forEach(function(input) {
                                input.value = '';
                                });
                            }
                    </script>
                </div>

                <div class="8">
                    {!! Form::hidden('preguntas[8][pregunta_id]', 8) !!}
                    {!! Form::hidden('preguntas[8][pregunta_nombre]', 'Neuritis, ciática, reumatismo, artritis, gota, o cualquier otra enfermedad o defecto muscular óseo, incluyendo la columna, espalda y articulacione') !!}
                    {!! Form::hidden('preguntas[8][cliente_id]', $clientebanco->id) !!}
                    <div class="row odd-row">
                        <div class="col-lg-10">
                            <p>Neuritis, ciática, reumatismo, artritis, gota, o cualquier otra enfermedad o defecto muscular óseo, incluyendo la columna, espalda y articulacione</p>
                        </div>
                        <div class="col-lg-2">
                            <div class="form-group">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="preguntas[8][respuesta]" value="si" id="rsi" onclick="mostrarFormulario08()">
                                    <label class="form-check-label" for="rsi">SI</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="preguntas[8][respuesta]" value="no" id="rno" onclick="ocultarFormulario08()">
                                    <label class="form-check-label" for="rno">NO</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="card08" style="display: none;">
                        <div class="row">
                            <div class="card-body">    
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[8][diagnostico]', null, ['class' => 'form-control', 'placeholder' => 'DIAGNOSTICO']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::date('preguntas[8][fecha]', null, ['class' => 'form-control', 'placeholder' => 'FECHA']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[8][tiempo]', null, ['class' => 'form-control', 'placeholder' => 'TIEMPO']) !!}
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[8][gradorecuperacion]', null, ['class' => 'form-control', 'placeholder' => 'GRADO DE RECUPERACIÓN']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[8][medico]', null, ['class' => 'form-control', 'placeholder' => 'MÉDICO']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[8][direccionmedico]', null, ['class' => 'form-control', 'placeholder' => 'DIRECCIÓN DEL MÉDICO']) !!}
                                        </div>
                                    </div>
                                </div>
                                <abbr title="Nuevo Formulario"><button type="button" class="btn btn-mas" onclick="mostrarSiguienteCard08()"><i class="fas fa-plus"></i></button></abbr>
                                <abbr id="menosFormularioAbbr08" title="Menos Formulario" style="display: none;">
                                    <button type="button" class="btn btn-menos" onclick="ocultarSiguienteCard08()">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </abbr>
                            </div>
                        </div>
                    </div>
                    <div id="siguienteCard08" style="display: none;">
                        <div class="row">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[8][diagnostico2]', null, ['class' => 'form-control', 'placeholder' => 'DIAGNOSTICO']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::date('preguntas[8][fecha2]', null, ['class' => 'form-control', 'placeholder' => 'FECHA']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[8][tiempo2]', null, ['class' => 'form-control', 'placeholder' => 'TIEMPO']) !!}
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[8][gradorecuperacion2]', null, ['class' => 'form-control', 'placeholder' => 'GRADO DE RECUPERACIÓN']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[8][medico2]', null, ['class' => 'form-control', 'placeholder' => 'MÉDICO']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[8][direccionmedico2]', null, ['class' => 'form-control', 'placeholder' => 'DIRECCIÓN DEL MÉDICO']) !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <script>
                        function limpiarCampos08() {
                            document.getElementById('card08').querySelectorAll('input[type="text"]').forEach(function(input) {
                                input.value = '';
                            });
                            document.getElementById('siguienteCard08').querySelectorAll('input[type="text"]').forEach(function(input) {
                                input.value = '';
                            });
                            }
                        function mostrarFormulario08() {
                            document.getElementById('card08').style.display = 'block';
                            }
                        function ocultarFormulario08() {
                            document.getElementById('card08').style.display = 'none';
                            document.getElementById('siguienteCard08').style.display = 'none';
                            limpiarCampos08();
                            }
                        function mostrarSiguienteCard08() {
                            document.getElementById('siguienteCard08').style.display = 'block';
                            document.getElementById('menosFormularioAbbr08').style.display = 'inline';
                            }
                        function ocultarSiguienteCard08() {
                            var siguienteCard02 = document.getElementById('siguienteCard08');
                            if (siguienteCard02.style.display === 'block') {
                                siguienteCard02.style.display = 'none';
                                limpiarCamposSiguienteCard08();
                                document.getElementById('menosFormularioAbbr08').style.display = 'none';
                                }
                            }
                        function limpiarCamposSiguienteCard08() {
                            document.getElementById('siguienteCard08').querySelectorAll('input[type="text"]').forEach(function(input) {
                                input.value = '';
                                });
                            }
                    </script>
                </div>

                <div class="9">
                    {!! Form::hidden('preguntas[9][pregunta_id]', 9) !!}
                    {!! Form::hidden('preguntas[9][pregunta_nombre]', 'Alguna deformidad, cojera o amputación') !!}
                    {!! Form::hidden('preguntas[9][cliente_id]', $clientebanco->id) !!}
                    <div class="row odd-row">
                        <div class="col-lg-10">
                            <p>Alguna deformidad, cojera o amputación</p>
                        </div>
                        <div class="col-lg-2">
                            <div class="form-group">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="preguntas[9][respuesta]" value="si" id="rsi" onclick="mostrarFormulario09()">
                                    <label class="form-check-label" for="rsi">SI</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="preguntas[9][respuesta]" value="no" id="rno" onclick="ocultarFormulario09()">
                                    <label class="form-check-label" for="rno">NO</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="card09" style="display: none;">
                        <div class="row odd-row">
                            <div class="card-body">    
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[9][diagnostico]', null, ['class' => 'form-control', 'placeholder' => 'DIAGNOSTICO']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::date('preguntas[9][fecha]', null, ['class' => 'form-control', 'placeholder' => 'FECHA']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[9][tiempo]', null, ['class' => 'form-control', 'placeholder' => 'TIEMPO']) !!}
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[9][gradorecuperacion]', null, ['class' => 'form-control', 'placeholder' => 'GRADO DE RECUPERACIÓN']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[9][medico]', null, ['class' => 'form-control', 'placeholder' => 'MÉDICO']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[9][direccionmedico]', null, ['class' => 'form-control', 'placeholder' => 'DIRECCIÓN DEL MÉDICO']) !!}
                                        </div>
                                    </div>
                                </div>
                                <abbr title="Nuevo Formulario"><button type="button" class="btn btn-mas" onclick="mostrarSiguienteCard09()"><i class="fas fa-plus"></i></button></abbr>
                                <abbr id="menosFormularioAbbr09" title="Menos Formulario" style="display: none;">
                                    <button type="button" class="btn btn-menos" onclick="ocultarSiguienteCard09()">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </abbr>
                            </div>
                        </div>
                    </div>
                    <div id="siguienteCard09" style="display: none;">
                        <div class="row odd-row">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[9][diagnostico2]', null, ['class' => 'form-control', 'placeholder' => 'DIAGNOSTICO']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::date('preguntas[9][fecha2]', null, ['class' => 'form-control', 'placeholder' => 'FECHA']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[9][tiempo2]', null, ['class' => 'form-control', 'placeholder' => 'TIEMPO']) !!}
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[9][gradorecuperacion2]', null, ['class' => 'form-control', 'placeholder' => 'GRADO DE RECUPERACIÓN']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[9][medico2]', null, ['class' => 'form-control', 'placeholder' => 'MÉDICO']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[9][direccionmedico2]', null, ['class' => 'form-control', 'placeholder' => 'DIRECCIÓN DEL MÉDICO']) !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <script>
                        function limpiarCampos09() {
                            document.getElementById('card09').querySelectorAll('input[type="text"]').forEach(function(input) {
                                input.value = '';
                            });
                            document.getElementById('siguienteCard09').querySelectorAll('input[type="text"]').forEach(function(input) {
                                input.value = '';
                            });
                            }
                        function mostrarFormulario09() {
                            document.getElementById('card09').style.display = 'block';
                            }
                        function ocultarFormulario09() {
                            document.getElementById('card09').style.display = 'none';
                            document.getElementById('siguienteCard09').style.display = 'none';
                            limpiarCampos09();
                            }
                        function mostrarSiguienteCard09() {
                            document.getElementById('siguienteCard09').style.display = 'block';
                            document.getElementById('menosFormularioAbbr09').style.display = 'inline';
                            }
                        function ocultarSiguienteCard09() {
                            var siguienteCard02 = document.getElementById('siguienteCard09');
                            if (siguienteCard02.style.display === 'block') {
                                siguienteCard02.style.display = 'none';
                                limpiarCamposSiguienteCard09();
                                document.getElementById('menosFormularioAbbr09').style.display = 'none';
                                }
                            }
                        function limpiarCamposSiguienteCard09() {
                            document.getElementById('siguienteCard09').querySelectorAll('input[type="text"]').forEach(function(input) {
                                input.value = '';
                                });
                            }
                    </script>
                </div>

                <div class="10">
                    {!! Form::hidden('preguntas[10][pregunta_id]', 10) !!}
                    {!! Form::hidden('preguntas[10][pregunta_nombre]', 'Enfermedad de la piel, ganglios linfáticos, quistes, tumores, cáncer') !!}
                    {!! Form::hidden('preguntas[10][cliente_id]', $clientebanco->id) !!}
                    <div class="row">
                        <div class="col-lg-10">
                            <p>Enfermedad de la piel, ganglios linfáticos, quistes, tumores, cáncer</p>
                        </div>
                        <div class="col-lg-2">
                            <div class="form-group">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="preguntas[10][respuesta]" value="si" id="rsi" onclick="mostrarFormulario010()">
                                    <label class="form-check-label" for="rsi">SI</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="preguntas[10][respuesta]" value="no" id="rno" onclick="ocultarFormulario010()">
                                    <label class="form-check-label" for="rno">NO</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="card010" style="display: none;">
                        <div class="row">
                            <div class="card-body">    
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[10][diagnostico]', null, ['class' => 'form-control', 'placeholder' => 'DIAGNOSTICO']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::date('preguntas[10][fecha]', null, ['class' => 'form-control', 'placeholder' => 'FECHA']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[10][tiempo]', null, ['class' => 'form-control', 'placeholder' => 'TIEMPO']) !!}
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[10][gradorecuperacion]', null, ['class' => 'form-control', 'placeholder' => 'GRADO DE RECUPERACIÓN']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[10][medico]', null, ['class' => 'form-control', 'placeholder' => 'MÉDICO']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[10][direccionmedico]', null, ['class' => 'form-control', 'placeholder' => 'DIRECCIÓN DEL MÉDICO']) !!}
                                        </div>
                                    </div>
                                </div>
                                <abbr title="Nuevo Formulario"><button type="button" class="btn btn-mas" onclick="mostrarSiguienteCard010()"><i class="fas fa-plus"></i></button></abbr>
                                <abbr id="menosFormularioAbbr010" title="Menos Formulario" style="display: none;">
                                    <button type="button" class="btn btn-menos" onclick="ocultarSiguienteCard010()">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </abbr>
                            </div>
                        </div>
                    </div>
                    <div id="siguienteCard010" style="display: none;">
                        <div class="row">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[10][diagnostico2]', null, ['class' => 'form-control', 'placeholder' => 'DIAGNOSTICO']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::date('preguntas[10][fecha2]', null, ['class' => 'form-control', 'placeholder' => 'FECHA']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[10][tiempo2]', null, ['class' => 'form-control', 'placeholder' => 'TIEMPO']) !!}
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[10][gradorecuperacion2]', null, ['class' => 'form-control', 'placeholder' => 'GRADO DE RECUPERACIÓN']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[10][medico2]', null, ['class' => 'form-control', 'placeholder' => 'MÉDICO']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[10][direccionmedico2]', null, ['class' => 'form-control', 'placeholder' => 'DIRECCIÓN DEL MÉDICO']) !!}
                                        </div>
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
                            document.getElementById('siguienteCard010').querySelectorAll('input[type="text"]').forEach(function(input) {
                                input.value = '';
                            });
                            }
                        function mostrarFormulario010() {
                            document.getElementById('card010').style.display = 'block';
                            }
                        function ocultarFormulario010() {
                            document.getElementById('card010').style.display = 'none';
                            document.getElementById('siguienteCard010').style.display = 'none';
                            limpiarCampos010();
                            }
                        function mostrarSiguienteCard010() {
                            document.getElementById('siguienteCard010').style.display = 'block';
                            document.getElementById('menosFormularioAbbr010').style.display = 'inline';
                            }
                        function ocultarSiguienteCard010() {
                            var siguienteCard02 = document.getElementById('siguienteCard010');
                            if (siguienteCard02.style.display === 'block') {
                                siguienteCard02.style.display = 'none';
                                limpiarCamposSiguienteCard010();
                                document.getElementById('menosFormularioAbbr010').style.display = 'none';
                                }
                            }
                        function limpiarCamposSiguienteCard010() {
                            document.getElementById('siguienteCard010').querySelectorAll('input[type="text"]').forEach(function(input) {
                                input.value = '';
                                });
                            }
                    </script>
                </div>

                <div class="11">
                    {!! Form::hidden('preguntas[11][pregunta_id]', 11) !!}
                    {!! Form::hidden('preguntas[11][pregunta_nombre]', 'Alergias, anemia u otra enfermedad de la sangre') !!}
                    {!! Form::hidden('preguntas[11][cliente_id]', $clientebanco->id) !!}
                    <div class="row odd-row">
                        <div class="col-lg-10">
                            <p>Alergias, anemia u otra enfermedad de la sangre</p>
                        </div>
                        <div class="col-lg-2">
                            <div class="form-group">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="preguntas[11][respuesta]" value="si" id="rsi" onclick="mostrarFormulario011()">
                                    <label class="form-check-label" for="rsi">SI</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="preguntas[11][respuesta]" value="no" id="rno" onclick="ocultarFormulario011()">
                                    <label class="form-check-label" for="rno">NO</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="card011" style="display: none;">
                        <div class="row odd-row">
                            <div class="card-body">    
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[11][diagnostico]', null, ['class' => 'form-control', 'placeholder' => 'DIAGNOSTICO']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::date('preguntas[11][fecha]', null, ['class' => 'form-control', 'placeholder' => 'FECHA']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[11][tiempo]', null, ['class' => 'form-control', 'placeholder' => 'TIEMPO']) !!}
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[11][gradorecuperacion]', null, ['class' => 'form-control', 'placeholder' => 'GRADO DE RECUPERACIÓN']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[11][medico]', null, ['class' => 'form-control', 'placeholder' => 'MÉDICO']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[11][direccionmedico]', null, ['class' => 'form-control', 'placeholder' => 'DIRECCIÓN DEL MÉDICO']) !!}
                                        </div>
                                    </div>
                                </div>
                                <abbr title="Nuevo Formulario"><button type="button" class="btn btn-mas" onclick="mostrarSiguienteCard011()"><i class="fas fa-plus"></i></button></abbr>
                                <abbr id="menosFormularioAbbr011" title="Menos Formulario" style="display: none;">
                                    <button type="button" class="btn btn-menos" onclick="ocultarSiguienteCard011()">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </abbr>
                            </div>
                        </div>
                    </div>
                    <div id="siguienteCard011" style="display: none;">
                        <div class="row odd-row">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[11][diagnostico2]', null, ['class' => 'form-control', 'placeholder' => 'DIAGNOSTICO']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::date('preguntas[11][fecha2]', null, ['class' => 'form-control', 'placeholder' => 'FECHA']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[11][tiempo2]', null, ['class' => 'form-control', 'placeholder' => 'TIEMPO']) !!}
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[11][gradorecuperacion2]', null, ['class' => 'form-control', 'placeholder' => 'GRADO DE RECUPERACIÓN']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[11][medico2]', null, ['class' => 'form-control', 'placeholder' => 'MÉDICO']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[11][direccionmedico2]', null, ['class' => 'form-control', 'placeholder' => 'DIRECCIÓN DEL MÉDICO']) !!}
                                        </div>
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
                            document.getElementById('siguienteCard011').querySelectorAll('input[type="text"]').forEach(function(input) {
                                input.value = '';
                            });
                            }
                        function mostrarFormulario011() {
                            document.getElementById('card011').style.display = 'block';
                            }
                        function ocultarFormulario011() {
                            document.getElementById('card011').style.display = 'none';
                            document.getElementById('siguienteCard011').style.display = 'none';
                            limpiarCampos011();
                            }
                        function mostrarSiguienteCard011() {
                            document.getElementById('siguienteCard011').style.display = 'block';
                            document.getElementById('menosFormularioAbbr011').style.display = 'inline';
                            }
                        function ocultarSiguienteCard011() {
                            var siguienteCard02 = document.getElementById('siguienteCard011');
                            if (siguienteCard02.style.display === 'block') {
                                siguienteCard02.style.display = 'none';
                                limpiarCamposSiguienteCard011();
                                document.getElementById('menosFormularioAbbr011').style.display = 'none';
                                }
                            }
                        function limpiarCamposSiguienteCard011() {
                            document.getElementById('siguienteCard011').querySelectorAll('input[type="text"]').forEach(function(input) {
                                input.value = '';
                                });
                            }
                    </script>
                </div>

                <div class="12">
                    {!! Form::hidden('preguntas[12][pregunta_id]', 12) !!}
                    {!! Form::hidden('preguntas[12][pregunta_nombre]', 'Uso excesivo del alcohol') !!}
                    {!! Form::hidden('preguntas[12][cliente_id]', $clientebanco->id) !!}
                    <div class="row">
                        <div class="col-lg-10">
                            <p>Uso excesivo del alcohol</p>
                        </div>
                        <div class="col-lg-2">
                            <div class="form-group">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="preguntas[12][respuesta]" value="si" id="rsi" onclick="mostrarFormulario012()">
                                    <label class="form-check-label" for="rsi">SI</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="preguntas[12][respuesta]" value="no" id="rno" onclick="ocultarFormulario012()">
                                    <label class="form-check-label" for="rno">NO</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="card012" style="display: none;">
                        <div class="row">
                            <div class="card-body">    
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::select('preguntas[12][hacecuanto]', 
                                                ['Hace un dia' => 'Hace un dia', 'Hace tres dias' => 'Hace tres dias', 'Hace una semana' => 'Hace una semana'], 
                                                null, 
                                                ['class' => 'form-control', 'placeholder' => 'HACE CUANTO']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::select('preguntas[12][cadacuanto]', 
                                                ['Cada dia' => 'Cada dia', 'Cada tres dias' => 'Cada tres dias', 'Cada semana' => 'Cada semana'], 
                                                null, 
                                                ['class' => 'form-control', 'placeholder' => 'CADA CUANTO']) !!}
                                        </div>
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
                            document.getElementById('siguienteCard012').querySelectorAll('input[type="text"]').forEach(function(input) {
                                input.value = '';
                            });
                            }
                        function mostrarFormulario012() {
                            document.getElementById('card012').style.display = 'block';
                            }
                        function ocultarFormulario012() {
                            document.getElementById('card012').style.display = 'none';
                            document.getElementById('siguienteCard012').style.display = 'none';
                            limpiarCampos012();
                            }
                    </script>
                </div>

                <div class="13">
                    {!! Form::hidden('preguntas[13][pregunta_id]', 13) !!}
                    {!! Form::hidden('preguntas[13][pregunta_nombre]', 'En la actualidad, fuma usted o durante los últimos 12 meses ha fumado cigarrillos, cigarros, pipa o ha usado tabaco en cualquier forma') !!}
                    {!! Form::hidden('preguntas[13][cliente_id]', $clientebanco->id) !!}
                    <div class="row odd-row">
                        <div class="col-lg-10">
                            <p>En la actualidad, fuma usted o durante los últimos 12 meses ha fumado cigarrillos, cigarros, pipa o ha usado tabaco en cualquier forma</p>
                        </div>
                        <div class="col-lg-2">
                            <div class="form-group">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="preguntas[13][respuesta]" value="si" id="rsi" onclick="mostrarFormulario013()">
                                    <label class="form-check-label" for="rsi">SI</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="preguntas[13][respuesta]" value="no" id="rno" onclick="ocultarFormulario013()">
                                    <label class="form-check-label" for="rno">NO</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="card013" style="display: none;">
                        <div class="row odd-row">
                            <div class="card-body">    
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::select('preguntas[13][hacecuanto]', 
                                                ['Dias' => 'Dias', 'Semanas' => 'Semanas', 'Meses' => 'Meses'], 
                                                null, 
                                                ['class' => 'form-control', 'placeholder' => 'HACE CUANTO']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::select('preguntas[13][cadacuanto]', 
                                                ['Dias' => 'Dias', 'Semanas' => 'Semanas', 'Meses' => 'Meses'], 
                                                null, 
                                                ['class' => 'form-control', 'placeholder' => 'CADA CUANTO']) !!}
                                        </div>
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
                            document.getElementById('siguienteCard013').querySelectorAll('input[type="text"]').forEach(function(input) {
                                input.value = '';
                            });
                            }
                        function mostrarFormulario013() {
                            document.getElementById('card013').style.display = 'block';
                            }
                        function ocultarFormulario013() {
                            document.getElementById('card013').style.display = 'none';
                            document.getElementById('siguienteCard013').style.display = 'none';
                            limpiarCampos013();
                            }
                    </script>
                </div>

                <div class="14">
                    {!! Form::hidden('preguntas[14][pregunta_id]', 14) !!}
                    {!! Form::hidden('preguntas[14][pregunta_nombre]', 'Ha usado alguna vez drogas estupefacientes, menos que fuera bajo consejo médico') !!}
                    {!! Form::hidden('preguntas[14][cliente_id]', $clientebanco->id) !!}
                    <div class="row">
                        <div class="col-lg-10">
                            <p>Ha usado alguna vez drogas estupefacientes, menos que fuera bajo consejo médico</p>
                        </div>
                        <div class="col-lg-2">
                            <div class="form-group">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="preguntas[14][respuesta]" value="si" id="rsi" onclick="mostrarFormulario014()">
                                    <label class="form-check-label" for="rsi">SI</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="preguntas[14][respuesta]" value="no" id="rno" onclick="ocultarFormulario014()">
                                    <label class="form-check-label" for="rno">NO</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="card014" style="display: none;">
                        <div class="row">
                            <div class="card-body">    
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::select('preguntas[14][hacecuanto]', 
                                                ['Dias' => 'Dias', 'Semanas' => 'Semanas', 'Meses' => 'Meses'], 
                                                null, 
                                                ['class' => 'form-control', 'placeholder' => 'HACE CUANTO']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::select('preguntas[14][cadacuanto]', 
                                                ['Dias' => 'Dias', 'Semanas' => 'Semanas', 'Meses' => 'Meses'], 
                                                null, 
                                                ['class' => 'form-control', 'placeholder' => 'CADA CUANTO']) !!}
                                        </div>
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
                            document.getElementById('siguienteCard014').querySelectorAll('input[type="text"]').forEach(function(input) {
                                input.value = '';
                            });
                            }
                        function mostrarFormulario014() {
                            document.getElementById('card014').style.display = 'block';
                            }
                        function ocultarFormulario014() {
                            document.getElementById('card014').style.display = 'none';
                            document.getElementById('siguienteCard014').style.display = 'none';
                            limpiarCampos014();
                            }
                    </script>
                </div>
                
                <div class="15">
                    {!! Form::hidden('preguntas[15][pregunta_id]', 15) !!}
                    {!! Form::hidden('preguntas[15][pregunta_nombre]', 'Está usted actualmente sometido a observación, tratamiento o medicación por alguna enfermedad') !!}
                    {!! Form::hidden('preguntas[15][cliente_id]', $clientebanco->id) !!}
                    <div class="row odd-row">
                        <div class="col-lg-10">
                            <p>Está usted actualmente sometido a observación, tratamiento o medicación por alguna enfermedad</p>
                        </div>
                        <div class="col-lg-2">
                            <div class="form-group">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="preguntas[15][respuesta]" value="si" id="rsi" onclick="mostrarFormulario015()">
                                    <label class="form-check-label" for="rsi">SI</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="preguntas[15][respuesta]" value="no" id="rno" onclick="ocultarFormulario015()">
                                    <label class="form-check-label" for="rno">NO</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="card015" style="display: none;">
                        <div class="row odd-row">
                            <div class="card-body">    
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[15][diagnostico]', null, ['class' => 'form-control', 'placeholder' => 'DIAGNOSTICO']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::date('preguntas[15][fecha]', null, ['class' => 'form-control', 'placeholder' => 'FECHA']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[15][tiempo]', null, ['class' => 'form-control', 'placeholder' => 'TIEMPO']) !!}
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[15][gradorecuperacion]', null, ['class' => 'form-control', 'placeholder' => 'GRADO DE RECUPERACIÓN']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[15][medico]', null, ['class' => 'form-control', 'placeholder' => 'MÉDICO']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[15][direccionmedico]', null, ['class' => 'form-control', 'placeholder' => 'DIRECCIÓN DEL MÉDICO']) !!}
                                        </div>
                                    </div>
                                </div>
                                <abbr title="Nuevo Formulario"><button type="button" class="btn btn-mas" onclick="mostrarSiguienteCard015()"><i class="fas fa-plus"></i></button></abbr>
                                <abbr id="menosFormularioAbbr015" title="Menos Formulario" style="display: none;">
                                    <button type="button" class="btn btn-menos" onclick="ocultarSiguienteCard015()">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </abbr>
                            </div>
                        </div>
                    </div>
                    <div id="siguienteCard015" style="display: none;">
                        <div class="row odd-row">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[15][diagnostico2]', null, ['class' => 'form-control', 'placeholder' => 'DIAGNOSTICO']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::date('preguntas[15][fecha2]', null, ['class' => 'form-control', 'placeholder' => 'FECHA']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[15][tiempo2]', null, ['class' => 'form-control', 'placeholder' => 'TIEMPO']) !!}
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[15][gradorecuperacion2]', null, ['class' => 'form-control', 'placeholder' => 'GRADO DE RECUPERACIÓN']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[15][medico2]', null, ['class' => 'form-control', 'placeholder' => 'MÉDICO']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[15][direccionmedico2]', null, ['class' => 'form-control', 'placeholder' => 'DIRECCIÓN DEL MÉDICO']) !!}
                                        </div>
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
                            document.getElementById('siguienteCard015').querySelectorAll('input[type="text"]').forEach(function(input) {
                                input.value = '';
                            });
                            }
                        function mostrarFormulario015() {
                            document.getElementById('card015').style.display = 'block';
                            }
                        function ocultarFormulario015() {
                            document.getElementById('card015').style.display = 'none';
                            document.getElementById('siguienteCard015').style.display = 'none';
                            limpiarCampos015();
                            }
                        function mostrarSiguienteCard015() {
                            document.getElementById('siguienteCard015').style.display = 'block';
                            document.getElementById('menosFormularioAbbr015').style.display = 'inline';
                            }
                        function ocultarSiguienteCard015() {
                            var siguienteCard02 = document.getElementById('siguienteCard015');
                            if (siguienteCard02.style.display === 'block') {
                                siguienteCard02.style.display = 'none';
                                limpiarCamposSiguienteCard015();
                                document.getElementById('menosFormularioAbbr015').style.display = 'none';
                                }
                            }
                        function limpiarCamposSiguienteCard015() {
                            document.getElementById('siguienteCard015').querySelectorAll('input[type="text"]').forEach(function(input) {
                                input.value = '';
                                });
                            }
                    </script>
                </div>

                <div class="16">
                    {!! Form::hidden('preguntas[16][pregunta_id]', 16) !!}
                    {!! Form::hidden('preguntas[16][pregunta_nombre]', 'Tiene usted la intención de buscar consejo médico, tratamiento o hacer cualquier prueba médica') !!}
                    {!! Form::hidden('preguntas[16][cliente_id]', $clientebanco->id) !!}
                    <div class="row">
                        <div class="col-lg-10">
                            <p>Tiene usted la intención de buscar consejo médico, tratamiento o hacer cualquier prueba médica</p>
                        </div>
                        <div class="col-lg-2">
                            <div class="form-group">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="preguntas[16][respuesta]" value="si" id="rsi" onclick="mostrarFormulario016()">
                                    <label class="form-check-label" for="rsi">SI</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="preguntas[16][respuesta]" value="no" id="rno" onclick="ocultarFormulario016()">
                                    <label class="form-check-label" for="rno">NO</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="card016" style="display: none;">
                        <div class="row">
                            <div class="card-body">    
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[16][diagnostico]', null, ['class' => 'form-control', 'placeholder' => 'DIAGNOSTICO']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::date('preguntas[16][fecha]', null, ['class' => 'form-control', 'placeholder' => 'FECHA']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[16][tiempo]', null, ['class' => 'form-control', 'placeholder' => 'TIEMPO']) !!}
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[16][gradorecuperacion]', null, ['class' => 'form-control', 'placeholder' => 'GRADO DE RECUPERACIÓN']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[16][medico]', null, ['class' => 'form-control', 'placeholder' => 'MÉDICO']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[16][direccionmedico]', null, ['class' => 'form-control', 'placeholder' => 'DIRECCIÓN DEL MÉDICO']) !!}
                                        </div>
                                    </div>
                                </div>
                                <abbr title="Nuevo Formulario"><button type="button" class="btn btn-mas" onclick="mostrarSiguienteCard016()"><i class="fas fa-plus"></i></button></abbr>
                                <abbr id="menosFormularioAbbr016" title="Menos Formulario" style="display: none;">
                                    <button type="button" class="btn btn-menos" onclick="ocultarSiguienteCard016()">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </abbr>
                            </div>
                        </div>
                    </div>
                    <div id="siguienteCard016" style="display: none;">
                        <div class="row">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[16][diagnostico2]', null, ['class' => 'form-control', 'placeholder' => 'DIAGNOSTICO']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::date('preguntas[16][fecha2]', null, ['class' => 'form-control', 'placeholder' => 'FECHA']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[16][tiempo2]', null, ['class' => 'form-control', 'placeholder' => 'TIEMPO']) !!}
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[16][gradorecuperacion2]', null, ['class' => 'form-control', 'placeholder' => 'GRADO DE RECUPERACIÓN']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[16][medico2]', null, ['class' => 'form-control', 'placeholder' => 'MÉDICO']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[16][direccionmedico2]', null, ['class' => 'form-control', 'placeholder' => 'DIRECCIÓN DEL MÉDICO']) !!}
                                        </div>
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
                            document.getElementById('siguienteCard016').querySelectorAll('input[type="text"]').forEach(function(input) {
                                input.value = '';
                            });
                            }
                        function mostrarFormulario016() {
                            document.getElementById('card016').style.display = 'block';
                            }
                        function ocultarFormulario016() {
                            document.getElementById('card016').style.display = 'none';
                            document.getElementById('siguienteCard016').style.display = 'none';
                            limpiarCampos016();
                            }
                        function mostrarSiguienteCard016() {
                            document.getElementById('siguienteCard016').style.display = 'block';
                            document.getElementById('menosFormularioAbbr016').style.display = 'inline';
                            }
                        function ocultarSiguienteCard016() {
                            var siguienteCard02 = document.getElementById('siguienteCard016');
                            if (siguienteCard02.style.display === 'block') {
                                siguienteCard02.style.display = 'none';
                                limpiarCamposSiguienteCard016();
                                document.getElementById('menosFormularioAbbr016').style.display = 'none';
                                }
                            }
                        function limpiarCamposSiguienteCard016() {
                            document.getElementById('siguienteCard016').querySelectorAll('input[type="text"]').forEach(function(input) {
                                input.value = '';
                                });
                            }
                    </script>
                </div>

                {!! Form::label('En los últimos 5 años:') !!}
                <div class="17">
                    {!! Form::hidden('preguntas[17][pregunta_id]', 17) !!}
                    {!! Form::hidden('preguntas[17][pregunta_nombre]', 'Ha tenido alguna enfermedad física o mental aparte de las ya mencionadas') !!}
                    {!! Form::hidden('preguntas[17][cliente_id]', $clientebanco->id) !!}
                    <div class="row odd-row">
                        <div class="col-lg-10">
                            <p>Ha tenido alguna enfermedad física o mental aparte de las ya mencionadas</p>
                        </div>
                        <div class="col-lg-2">
                            <div class="form-group">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="preguntas[17][respuesta]" value="si" id="rsi" onclick="mostrarFormulario017()">
                                    <label class="form-check-label" for="rsi">SI</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="preguntas[17][respuesta]" value="no" id="rno" onclick="ocultarFormulario017()">
                                    <label class="form-check-label" for="rno">NO</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="card017" style="display: none;">
                        <div class="row odd-row">
                            <div class="card-body">    
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[17][diagnostico]', null, ['class' => 'form-control', 'placeholder' => 'DIAGNOSTICO']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::date('preguntas[17][fecha]', null, ['class' => 'form-control', 'placeholder' => 'FECHA']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[17][tiempo]', null, ['class' => 'form-control', 'placeholder' => 'TIEMPO']) !!}
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[17][gradorecuperacion]', null, ['class' => 'form-control', 'placeholder' => 'GRADO DE RECUPERACIÓN']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[17][medico]', null, ['class' => 'form-control', 'placeholder' => 'MÉDICO']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[17][direccionmedico]', null, ['class' => 'form-control', 'placeholder' => 'DIRECCIÓN DEL MÉDICO']) !!}
                                        </div>
                                    </div>
                                </div>
                                <abbr title="Nuevo Formulario"><button type="button" class="btn btn-mas" onclick="mostrarSiguienteCard017()"><i class="fas fa-plus"></i></button></abbr>
                                <abbr id="menosFormularioAbbr017" title="Menos Formulario" style="display: none;">
                                    <button type="button" class="btn btn-menos" onclick="ocultarSiguienteCard017()">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </abbr>
                            </div>
                        </div>
                    </div>
                    <div id="siguienteCard017" style="display: none;">
                        <div class="row odd-row">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[17][diagnostico2]', null, ['class' => 'form-control', 'placeholder' => 'DIAGNOSTICO']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::date('preguntas[17][fecha2]', null, ['class' => 'form-control', 'placeholder' => 'FECHA']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[17][tiempo2]', null, ['class' => 'form-control', 'placeholder' => 'TIEMPO']) !!}
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[17][gradorecuperacion2]', null, ['class' => 'form-control', 'placeholder' => 'GRADO DE RECUPERACIÓN']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[17][medico2]', null, ['class' => 'form-control', 'placeholder' => 'MÉDICO']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[17][direccionmedico2]', null, ['class' => 'form-control', 'placeholder' => 'DIRECCIÓN DEL MÉDICO']) !!}
                                        </div>
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
                            document.getElementById('siguienteCard017').querySelectorAll('input[type="text"]').forEach(function(input) {
                                input.value = '';
                            });
                            }
                        function mostrarFormulario017() {
                            document.getElementById('card017').style.display = 'block';
                            }
                        function ocultarFormulario017() {
                            document.getElementById('card017').style.display = 'none';
                            document.getElementById('siguienteCard017').style.display = 'none';
                            limpiarCampos017();
                            }
                        function mostrarSiguienteCard017() {
                            document.getElementById('siguienteCard017').style.display = 'block';
                            document.getElementById('menosFormularioAbbr017').style.display = 'inline';
                            }
                        function ocultarSiguienteCard017() {
                            var siguienteCard02 = document.getElementById('siguienteCard017');
                            if (siguienteCard02.style.display === 'block') {
                                siguienteCard02.style.display = 'none';
                                limpiarCamposSiguienteCard017();
                                document.getElementById('menosFormularioAbbr017').style.display = 'none';
                                }
                            }
                        function limpiarCamposSiguienteCard017() {
                            document.getElementById('siguienteCard017').querySelectorAll('input[type="text"]').forEach(function(input) {
                                input.value = '';
                                });
                            }
                    </script>
                </div>

                <div class="18">
                    {!! Form::hidden('preguntas[18][pregunta_id]', 18) !!}
                    {!! Form::hidden('preguntas[18][pregunta_nombre]', 'Ha tenido alguna revisión, consulta, lesión u operación quirúrgica') !!}
                    {!! Form::hidden('preguntas[18][cliente_id]', $clientebanco->id) !!}
                    <div class="row">
                        <div class="col-lg-10">
                            <p>Ha tenido alguna revisión, consulta, lesión u operación quirúrgica</p>
                        </div>
                        <div class="col-lg-2">
                            <div class="form-group">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="preguntas[18][respuesta]" value="si" id="rsi" onclick="mostrarFormulario018()">
                                    <label class="form-check-label" for="rsi">SI</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="preguntas[18][respuesta]" value="no" id="rno" onclick="ocultarFormulario018()">
                                    <label class="form-check-label" for="rno">NO</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="card018" style="display: none;">
                        <div class="row">
                            <div class="card-body">    
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[18][diagnostico]', null, ['class' => 'form-control', 'placeholder' => 'DIAGNOSTICO']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::date('preguntas[18][fecha]', null, ['class' => 'form-control', 'placeholder' => 'FECHA']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[18][tiempo]', null, ['class' => 'form-control', 'placeholder' => 'TIEMPO']) !!}
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[18][gradorecuperacion]', null, ['class' => 'form-control', 'placeholder' => 'GRADO DE RECUPERACIÓN']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[18][medico]', null, ['class' => 'form-control', 'placeholder' => 'MÉDICO']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[18][direccionmedico]', null, ['class' => 'form-control', 'placeholder' => 'DIRECCIÓN DEL MÉDICO']) !!}
                                        </div>
                                    </div>
                                </div>
                                <abbr title="Nuevo Formulario"><button type="button" class="btn btn-mas" onclick="mostrarSiguienteCard018()"><i class="fas fa-plus"></i></button></abbr>
                                <abbr id="menosFormularioAbbr018" title="Menos Formulario" style="display: none;">
                                    <button type="button" class="btn btn-menos" onclick="ocultarSiguienteCard018()">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </abbr>
                            </div>
                        </div>
                    </div>
                    <div id="siguienteCard018" style="display: none;">
                        <div class="row">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[18][diagnostico2]', null, ['class' => 'form-control', 'placeholder' => 'DIAGNOSTICO']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::date('preguntas[18][fecha2]', null, ['class' => 'form-control', 'placeholder' => 'FECHA']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[18][tiempo2]', null, ['class' => 'form-control', 'placeholder' => 'TIEMPO']) !!}
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[18][gradorecuperacion2]', null, ['class' => 'form-control', 'placeholder' => 'GRADO DE RECUPERACIÓN']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[18][medico2]', null, ['class' => 'form-control', 'placeholder' => 'MÉDICO']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[18][direccionmedico2]', null, ['class' => 'form-control', 'placeholder' => 'DIRECCIÓN DEL MÉDICO']) !!}
                                        </div>
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
                            document.getElementById('siguienteCard018').querySelectorAll('input[type="text"]').forEach(function(input) {
                                input.value = '';
                            });
                            }
                        function mostrarFormulario018() {
                            document.getElementById('card018').style.display = 'block';
                            }
                        function ocultarFormulario018() {
                            document.getElementById('card018').style.display = 'none';
                            document.getElementById('siguienteCard018').style.display = 'none';
                            limpiarCampos018();
                            }
                        function mostrarSiguienteCard018() {
                            document.getElementById('siguienteCard018').style.display = 'block';
                            document.getElementById('menosFormularioAbbr018').style.display = 'inline';
                            }
                        function ocultarSiguienteCard018() {
                            var siguienteCard02 = document.getElementById('siguienteCard018');
                            if (siguienteCard02.style.display === 'block') {
                                siguienteCard02.style.display = 'none';
                                limpiarCamposSiguienteCard018();
                                document.getElementById('menosFormularioAbbr018').style.display = 'none';
                                }
                            }
                        function limpiarCamposSiguienteCard018() {
                            document.getElementById('siguienteCard018').querySelectorAll('input[type="text"]').forEach(function(input) {
                                input.value = '';
                                });
                            }
                    </script>
                </div>

                <div class="19">
                    {!! Form::hidden('preguntas[19][pregunta_id]', 19) !!}
                    {!! Form::hidden('preguntas[19][pregunta_nombre]', 'Ha sido paciente en hospital, clínica, sanatorio u otros estable cimientos médicos') !!}
                    {!! Form::hidden('preguntas[19][cliente_id]', $clientebanco->id) !!}
                    <div class="row odd-row">
                        <div class="col-lg-10">
                            <p>Ha sido paciente en hospital, clínica, sanatorio u otros estable cimientos médicos</p>
                        </div>
                        <div class="col-lg-2">
                            <div class="form-group">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="preguntas[19][respuesta]" value="si" id="rsi" onclick="mostrarFormulario019()">
                                    <label class="form-check-label" for="rsi">SI</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="preguntas[19][respuesta]" value="no" id="rno" onclick="ocultarFormulario019()">
                                    <label class="form-check-label" for="rno">NO</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="card019" style="display: none;">
                        <div class="row odd-row">
                            <div class="card-body">    
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[19][diagnostico]', null, ['class' => 'form-control', 'placeholder' => 'DIAGNOSTICO']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::date('preguntas[19][fecha]', null, ['class' => 'form-control', 'placeholder' => 'FECHA']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[19][tiempo]', null, ['class' => 'form-control', 'placeholder' => 'TIEMPO']) !!}
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[19][gradorecuperacion]', null, ['class' => 'form-control', 'placeholder' => 'GRADO DE RECUPERACIÓN']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[19][medico]', null, ['class' => 'form-control', 'placeholder' => 'MÉDICO']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[19][direccionmedico]', null, ['class' => 'form-control', 'placeholder' => 'DIRECCIÓN DEL MÉDICO']) !!}
                                        </div>
                                    </div>
                                </div>
                                <abbr title="Nuevo Formulario"><button type="button" class="btn btn-mas" onclick="mostrarSiguienteCard019()"><i class="fas fa-plus"></i></button></abbr>
                                <abbr id="menosFormularioAbbr019" title="Menos Formulario" style="display: none;">
                                    <button type="button" class="btn btn-menos" onclick="ocultarSiguienteCard019()">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </abbr>
                            </div>
                        </div>
                    </div>
                    <div id="siguienteCard019" style="display: none;">
                        <div class="row odd-row">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[19][diagnostico2]', null, ['class' => 'form-control', 'placeholder' => 'DIAGNOSTICO']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::date('preguntas[19][fecha2]', null, ['class' => 'form-control', 'placeholder' => 'FECHA']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[19][tiempo2]', null, ['class' => 'form-control', 'placeholder' => 'TIEMPO']) !!}
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[19][gradorecuperacion2]', null, ['class' => 'form-control', 'placeholder' => 'GRADO DE RECUPERACIÓN']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[19][medico2]', null, ['class' => 'form-control', 'placeholder' => 'MÉDICO']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[19][direccionmedico2]', null, ['class' => 'form-control', 'placeholder' => 'DIRECCIÓN DEL MÉDICO']) !!}
                                        </div>
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
                            document.getElementById('siguienteCard019').querySelectorAll('input[type="text"]').forEach(function(input) {
                                input.value = '';
                            });
                            }
                        function mostrarFormulario019() {
                            document.getElementById('card019').style.display = 'block';
                            }
                        function ocultarFormulario019() {
                            document.getElementById('card019').style.display = 'none';
                            document.getElementById('siguienteCard019').style.display = 'none';
                            limpiarCampos019();
                            }
                        function mostrarSiguienteCard019() {
                            document.getElementById('siguienteCard019').style.display = 'block';
                            document.getElementById('menosFormularioAbbr019').style.display = 'inline';
                            }
                        function ocultarSiguienteCard019() {
                            var siguienteCard02 = document.getElementById('siguienteCard019');
                            if (siguienteCard02.style.display === 'block') {
                                siguienteCard02.style.display = 'none';
                                limpiarCamposSiguienteCard019();
                                document.getElementById('menosFormularioAbbr019').style.display = 'none';
                                }
                            }
                        function limpiarCamposSiguienteCard019() {
                            document.getElementById('siguienteCard019').querySelectorAll('input[type="text"]').forEach(function(input) {
                                input.value = '';
                                });
                            }
                    </script>
                </div>

                <div class="20">
                    {!! Form::hidden('preguntas[20][pregunta_id]', 20) !!}
                    {!! Form::hidden('preguntas[20][pregunta_nombre]', 'Ha sido sometido a electrocardiograma, rayos x u otro tipo de análisis') !!}
                    {!! Form::hidden('preguntas[20][cliente_id]', $clientebanco->id) !!}
                    <div class="row">
                        <div class="col-lg-10">
                            <p>Ha sido sometido a electrocardiograma, rayos x u otro tipo de análisis</p>
                        </div>
                        <div class="col-lg-2">
                            <div class="form-group">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="preguntas[20][respuesta]" value="si" id="rsi" onclick="mostrarFormulario020()">
                                    <label class="form-check-label" for="rsi">SI</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="preguntas[20][respuesta]" value="no" id="rno" onclick="ocultarFormulario020()">
                                    <label class="form-check-label" for="rno">NO</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="card020" style="display: none;">
                        <div class="row">
                            <div class="card-body">    
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[20][diagnostico]', null, ['class' => 'form-control', 'placeholder' => 'DIAGNOSTICO']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::date('preguntas[20][fecha]', null, ['class' => 'form-control', 'placeholder' => 'FECHA']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[20][tiempo]', null, ['class' => 'form-control', 'placeholder' => 'TIEMPO']) !!}
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[20][gradorecuperacion]', null, ['class' => 'form-control', 'placeholder' => 'GRADO DE RECUPERACIÓN']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[20][medico]', null, ['class' => 'form-control', 'placeholder' => 'MÉDICO']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[20][direccionmedico]', null, ['class' => 'form-control', 'placeholder' => 'DIRECCIÓN DEL MÉDICO']) !!}
                                        </div>
                                    </div>
                                </div>
                                <abbr title="Nuevo Formulario"><button type="button" class="btn btn-mas" onclick="mostrarSiguienteCard020()"><i class="fas fa-plus"></i></button></abbr>
                                <abbr id="menosFormularioAbbr020" title="Menos Formulario" style="display: none;">
                                    <button type="button" class="btn btn-menos" onclick="ocultarSiguienteCard020()">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </abbr>
                            </div>
                        </div>
                    </div>
                    <div id="siguienteCard020" style="display: none;">
                        <div class="row">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[20][diagnostico2]', null, ['class' => 'form-control', 'placeholder' => 'DIAGNOSTICO']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::date('preguntas[20][fecha2]', null, ['class' => 'form-control', 'placeholder' => 'FECHA']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[20][tiempo2]', null, ['class' => 'form-control', 'placeholder' => 'TIEMPO']) !!}
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[20][gradorecuperacion2]', null, ['class' => 'form-control', 'placeholder' => 'GRADO DE RECUPERACIÓN']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[20][medico2]', null, ['class' => 'form-control', 'placeholder' => 'MÉDICO']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[20][direccionmedico2]', null, ['class' => 'form-control', 'placeholder' => 'DIRECCIÓN DEL MÉDICO']) !!}
                                        </div>
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
                            document.getElementById('siguienteCard020').querySelectorAll('input[type="text"]').forEach(function(input) {
                                input.value = '';
                            });
                            }
                        function mostrarFormulario020() {
                            document.getElementById('card020').style.display = 'block';
                            }
                        function ocultarFormulario020() {
                            document.getElementById('card020').style.display = 'none';
                            document.getElementById('siguienteCard020').style.display = 'none';
                            limpiarCampos020();
                            }
                        function mostrarSiguienteCard020() {
                            document.getElementById('siguienteCard020').style.display = 'block';
                            document.getElementById('menosFormularioAbbr020').style.display = 'inline';
                            }
                        function ocultarSiguienteCard020() {
                            var siguienteCard02 = document.getElementById('siguienteCard020');
                            if (siguienteCard02.style.display === 'block') {
                                siguienteCard02.style.display = 'none';
                                limpiarCamposSiguienteCard020();
                                document.getElementById('menosFormularioAbbr020').style.display = 'none';
                                }
                            }
                        function limpiarCamposSiguienteCard020() {
                            document.getElementById('siguienteCard020').querySelectorAll('input[type="text"]').forEach(function(input) {
                                input.value = '';
                                });
                            }
                    </script>
                </div>

                <div class="21">
                    {!! Form::hidden('preguntas[21][pregunta_id]', 21) !!}
                    {!! Form::hidden('preguntas[21][pregunta_nombre]', 'Se le ha aconsejado algún análisis, hospitalización u operación que no se hubiera realizado') !!}
                    {!! Form::hidden('preguntas[21][cliente_id]', $clientebanco->id) !!}
                    <div class="row odd-row">
                        <div class="col-lg-10">
                            <p>Se le ha aconsejado algún análisis, hospitalización u operación que no se hubiera realizado</p>
                        </div>
                        <div class="col-lg-2">
                            <div class="form-group">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="preguntas[21][respuesta]" value="si" id="rsi" onclick="mostrarFormulario021()">
                                    <label class="form-check-label" for="rsi">SI</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="preguntas[21][respuesta]" value="no" id="rno" onclick="ocultarFormulario021()">
                                    <label class="form-check-label" for="rno">NO</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="card021" style="display: none;">
                        <div class="row odd-row">
                            <div class="card-body">    
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[21][diagnostico]', null, ['class' => 'form-control', 'placeholder' => 'DIAGNOSTICO']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::date('preguntas[21][fecha]', null, ['class' => 'form-control', 'placeholder' => 'FECHA']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[21][tiempo]', null, ['class' => 'form-control', 'placeholder' => 'TIEMPO']) !!}
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[21][gradorecuperacion]', null, ['class' => 'form-control', 'placeholder' => 'GRADO DE RECUPERACIÓN']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[21][medico]', null, ['class' => 'form-control', 'placeholder' => 'MÉDICO']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[21][direccionmedico]', null, ['class' => 'form-control', 'placeholder' => 'DIRECCIÓN DEL MÉDICO']) !!}
                                        </div>
                                    </div>
                                </div>
                                <abbr title="Nuevo Formulario"><button type="button" class="btn btn-mas" onclick="mostrarSiguienteCard021()"><i class="fas fa-plus"></i></button></abbr>
                                <abbr id="menosFormularioAbbr021" title="Menos Formulario" style="display: none;">
                                    <button type="button" class="btn btn-menos" onclick="ocultarSiguienteCard021()">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </abbr>
                            </div>
                        </div>
                    </div>
                    <div id="siguienteCard021" style="display: none;">
                        <div class="row odd-row">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[21][diagnostico2]', null, ['class' => 'form-control', 'placeholder' => 'DIAGNOSTICO']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::date('preguntas[21][fecha2]', null, ['class' => 'form-control', 'placeholder' => 'FECHA']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[21][tiempo2]', null, ['class' => 'form-control', 'placeholder' => 'TIEMPO']) !!}
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[21][gradorecuperacion2]', null, ['class' => 'form-control', 'placeholder' => 'GRADO DE RECUPERACIÓN']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[21][medico2]', null, ['class' => 'form-control', 'placeholder' => 'MÉDICO']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[21][direccionmedico2]', null, ['class' => 'form-control', 'placeholder' => 'DIRECCIÓN DEL MÉDICO']) !!}
                                        </div>
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
                            document.getElementById('siguienteCard021').querySelectorAll('input[type="text"]').forEach(function(input) {
                                input.value = '';
                            });
                            }
                        function mostrarFormulario021() {
                            document.getElementById('card021').style.display = 'block';
                            }
                        function ocultarFormulario021() {
                            document.getElementById('card021').style.display = 'none';
                            document.getElementById('siguienteCard021').style.display = 'none';
                            limpiarCampos021();
                            }
                        function mostrarSiguienteCard021() {
                            document.getElementById('siguienteCard021').style.display = 'block';
                            document.getElementById('menosFormularioAbbr021').style.display = 'inline';
                            }
                        function ocultarSiguienteCard021() {
                            var siguienteCard02 = document.getElementById('siguienteCard021');
                            if (siguienteCard02.style.display === 'block') {
                                siguienteCard02.style.display = 'none';
                                limpiarCamposSiguienteCard021();
                                document.getElementById('menosFormularioAbbr021').style.display = 'none';
                                }
                            }
                        function limpiarCamposSiguienteCard021() {
                            document.getElementById('siguienteCard021').querySelectorAll('input[type="text"]').forEach(function(input) {
                                input.value = '';
                                });
                            }
                    </script>
                </div>

                <div class="22">
                    {!! Form::hidden('preguntas[22][pregunta_id]', 22) !!}
                    {!! Form::hidden('preguntas[22][pregunta_nombre]', 'Ha tenido aplazamiento, rechazo o reducción del servicio militar por deficiencia física o mental') !!}
                    {!! Form::hidden('preguntas[22][cliente_id]', $clientebanco->id) !!}
                    <div class="row">
                        <div class="col-lg-10">
                            <p>Ha tenido aplazamiento, rechazo o reducción del servicio militar por deficiencia física o mental</p>
                        </div>
                        <div class="col-lg-2">
                            <div class="form-group">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="preguntas[22][respuesta]" value="si" id="rsi" onclick="mostrarFormulario022()">
                                    <label class="form-check-label" for="rsi">SI</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="preguntas[22][respuesta]" value="no" id="rno" onclick="ocultarFormulario022()">
                                    <label class="form-check-label" for="rno">NO</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="card022" style="display: none;">
                        <div class="row">
                            <div class="card-body">    
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[22][diagnostico]', null, ['class' => 'form-control', 'placeholder' => 'DIAGNOSTICO']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::date('preguntas[22][fecha]', null, ['class' => 'form-control', 'placeholder' => 'FECHA']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[22][tiempo]', null, ['class' => 'form-control', 'placeholder' => 'TIEMPO']) !!}
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[22][gradorecuperacion]', null, ['class' => 'form-control', 'placeholder' => 'GRADO DE RECUPERACIÓN']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[22][medico]', null, ['class' => 'form-control', 'placeholder' => 'MÉDICO']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[22][direccionmedico]', null, ['class' => 'form-control', 'placeholder' => 'DIRECCIÓN DEL MÉDICO']) !!}
                                        </div>
                                    </div>
                                </div>
                                <abbr title="Nuevo Formulario"><button type="button" class="btn btn-mas" onclick="mostrarSiguienteCard022()"><i class="fas fa-plus"></i></button></abbr>
                                <abbr id="menosFormularioAbbr022" title="Menos Formulario" style="display: none;">
                                    <button type="button" class="btn btn-menos" onclick="ocultarSiguienteCard022()">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </abbr>
                            </div>
                        </div>
                    </div>
                    <div id="siguienteCard022" style="display: none;">
                        <div class="row">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[22][diagnostico2]', null, ['class' => 'form-control', 'placeholder' => 'DIAGNOSTICO']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::date('preguntas[22][fecha2]', null, ['class' => 'form-control', 'placeholder' => 'FECHA']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[22][tiempo2]', null, ['class' => 'form-control', 'placeholder' => 'TIEMPO']) !!}
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[22][gradorecuperacion2]', null, ['class' => 'form-control', 'placeholder' => 'GRADO DE RECUPERACIÓN']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[22][medico2]', null, ['class' => 'form-control', 'placeholder' => 'MÉDICO']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[22][direccionmedico2]', null, ['class' => 'form-control', 'placeholder' => 'DIRECCIÓN DEL MÉDICO']) !!}
                                        </div>
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
                            document.getElementById('siguienteCard022').querySelectorAll('input[type="text"]').forEach(function(input) {
                                input.value = '';
                            });
                            }
                        function mostrarFormulario022() {
                            document.getElementById('card022').style.display = 'block';
                            }
                        function ocultarFormulario022() {
                            document.getElementById('card022').style.display = 'none';
                            document.getElementById('siguienteCard022').style.display = 'none';
                            limpiarCampos022();
                            }
                        function mostrarSiguienteCard022() {
                            document.getElementById('siguienteCard022').style.display = 'block';
                            document.getElementById('menosFormularioAbbr022').style.display = 'inline';
                            }
                        function ocultarSiguienteCard022() {
                            var siguienteCard02 = document.getElementById('siguienteCard022');
                            if (siguienteCard02.style.display === 'block') {
                                siguienteCard02.style.display = 'none';
                                limpiarCamposSiguienteCard022();
                                document.getElementById('menosFormularioAbbr022').style.display = 'none';
                                }
                            }
                        function limpiarCamposSiguienteCard022() {
                            document.getElementById('siguienteCard022').querySelectorAll('input[type="text"]').forEach(function(input) {
                                input.value = '';
                                });
                            }
                    </script>
                </div>

                <div class="23">
                    {!! Form::hidden('preguntas[23][pregunta_id]', 23) !!}
                    {!! Form::hidden('preguntas[23][pregunta_nombre]', 'Realiza Deportes u hobbies riesgosos como: Bombero, Piloto Civil, Andinismo, Carreras de Veleocidad, Alas Delta, Parapente, Paracaidismo, Buceo, Motociclismo, u otro que se considere peligroso') !!}
                    {!! Form::hidden('preguntas[23][cliente_id]', $clientebanco->id) !!}
                    <div class="row odd-row">
                        <div class="col-lg-10">
                            <p>Realiza Deportes u hobbies riesgosos como: Bombero, Piloto Civil, Andinismo, Carreras de Veleocidad, Alas Delta, Parapente, Paracaidismo, Buceo, Motociclismo, u otro que se considere peligroso</p>
                        </div>
                        <div class="col-lg-2">
                            <div class="form-group">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="preguntas[23][respuesta]" value="si" id="rsi" onclick="mostrarFormulario023()">
                                    <label class="form-check-label" for="rsi">SI</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="preguntas[23][respuesta]" value="no" id="rno" onclick="ocultarFormulario023()">
                                    <label class="form-check-label" for="rno">NO</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="card023" style="display: none;">
                        <div class="row odd-row">
                            <div class="card-body">    
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::select('preguntas[23][hacecuanto]', 
                                                ['Dias' => 'Dias', 'Semanas' => 'Semanas', 'Meses' => 'Meses'], 
                                                null, 
                                                ['class' => 'form-control', 'placeholder' => 'HACE CUANTO']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::select('preguntas[23][cadacuanto]', 
                                                ['Dias' => 'Dias', 'Semanas' => 'Semanas', 'Meses' => 'Meses'], 
                                                null, 
                                                ['class' => 'form-control', 'placeholder' => 'CADA CUANTO']) !!}
                                        </div>
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
                            document.getElementById('siguienteCard023').querySelectorAll('input[type="text"]').forEach(function(input) {
                                input.value = '';
                            });
                            }
                        function mostrarFormulario023() {
                            document.getElementById('card023').style.display = 'block';
                            }
                        function ocultarFormulario023() {
                            document.getElementById('card023').style.display = 'none';
                            document.getElementById('siguienteCard023').style.display = 'none';
                            limpiarCampos023();
                            }
                    </script>
                </div>

                <div class="24">
                    {!! Form::hidden('preguntas[24][pregunta_id]', 24) !!}
                    {!! Form::hidden('preguntas[24][pregunta_nombre]', 'Ha solicitado o percibido alguna vez indemnizaciones por incapacidad de cualquier tipo') !!}
                    {!! Form::hidden('preguntas[24][cliente_id]', $clientebanco->id) !!}
                    <div class="row">
                        <div class="col-lg-10">
                            <p>Ha solicitado o percibido alguna vez indemnizaciones por incapacidad de cualquier tipo</p>
                        </div>
                        <div class="col-lg-2">
                            <div class="form-group">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="preguntas[24][respuesta]" value="si" id="rsi" onclick="mostrarFormulario024()">
                                    <label class="form-check-label" for="rsi">SI</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="preguntas[24][respuesta]" value="no" id="rno" onclick="ocultarFormulario024()">
                                    <label class="form-check-label" for="rno">NO</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="card024" style="display: none;">
                        <div class="row">
                            <div class="card-body">    
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[24][diagnostico]', null, ['class' => 'form-control', 'placeholder' => 'DIAGNOSTICO']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::date('preguntas[24][fecha]', null, ['class' => 'form-control', 'placeholder' => 'FECHA']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[24][tiempo]', null, ['class' => 'form-control', 'placeholder' => 'TIEMPO']) !!}
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[24][gradorecuperacion]', null, ['class' => 'form-control', 'placeholder' => 'GRADO DE RECUPERACIÓN']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[24][medico]', null, ['class' => 'form-control', 'placeholder' => 'MÉDICO']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[24][direccionmedico]', null, ['class' => 'form-control', 'placeholder' => 'DIRECCIÓN DEL MÉDICO']) !!}
                                        </div>
                                    </div>
                                </div>
                                <abbr title="Nuevo Formulario"><button type="button" class="btn btn-mas" onclick="mostrarSiguienteCard024()"><i class="fas fa-plus"></i></button></abbr>
                                <abbr id="menosFormularioAbbr024" title="Menos Formulario" style="display: none;">
                                    <button type="button" class="btn btn-menos" onclick="ocultarSiguienteCard024()">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </abbr>
                            </div>
                        </div>
                    </div>
                    <div id="siguienteCard024" style="display: none;">
                        <div class="row">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[24][diagnostico2]', null, ['class' => 'form-control', 'placeholder' => 'DIAGNOSTICO']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::date('preguntas[24][fecha2]', null, ['class' => 'form-control', 'placeholder' => 'FECHA']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[24][tiempo2]', null, ['class' => 'form-control', 'placeholder' => 'TIEMPO']) !!}
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[24][gradorecuperacion2]', null, ['class' => 'form-control', 'placeholder' => 'GRADO DE RECUPERACIÓN']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[24][medico2]', null, ['class' => 'form-control', 'placeholder' => 'MÉDICO']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[24][direccionmedico2]', null, ['class' => 'form-control', 'placeholder' => 'DIRECCIÓN DEL MÉDICO']) !!}
                                        </div>
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
                            document.getElementById('siguienteCard024').querySelectorAll('input[type="text"]').forEach(function(input) {
                                input.value = '';
                            });
                            }
                        function mostrarFormulario024() {
                            document.getElementById('card024').style.display = 'block';
                            }
                        function ocultarFormulario024() {
                            document.getElementById('card024').style.display = 'none';
                            document.getElementById('siguienteCard024').style.display = 'none';
                            limpiarCampos024();
                            }
                        function mostrarSiguienteCard024() {
                            document.getElementById('siguienteCard024').style.display = 'block';
                            document.getElementById('menosFormularioAbbr024').style.display = 'inline';
                            }
                        function ocultarSiguienteCard024() {
                            var siguienteCard02 = document.getElementById('siguienteCard024');
                            if (siguienteCard02.style.display === 'block') {
                                siguienteCard02.style.display = 'none';
                                limpiarCamposSiguienteCard024();
                                document.getElementById('menosFormularioAbbr024').style.display = 'none';
                                }
                            }
                        function limpiarCamposSiguienteCard024() {
                            document.getElementById('siguienteCard024').querySelectorAll('input[type="text"]').forEach(function(input) {
                                input.value = '';
                                });
                            }
                    </script>
                </div>

                <div class="25">
                    {!! Form::hidden('preguntas[25][pregunta_id]', 25) !!}
                    {!! Form::hidden('preguntas[25][pregunta_nombre]', 'Hay en su familia antecedentes de tuberculosis, diabetes, cáncer, hipertensión, enfermedad sanguínea o renal, enfermedad mental o suicidio') !!}
                    {!! Form::hidden('preguntas[25][cliente_id]', $clientebanco->id) !!}
                    <div class="row odd-row">
                        <div class="col-lg-10">
                            <p>Hay en su familia antecedentes de tuberculosis, diabetes, cáncer, hipertensión, enfermedad sanguínea o renal, enfermedad mental o suicidio</p>
                        </div>
                        <div class="col-lg-2">
                            <div class="form-group">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="preguntas[25][respuesta]" value="si" id="rsi" onclick="mostrarFormulario025()">
                                    <label class="form-check-label" for="rsi">SI</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="preguntas[25][respuesta]" value="no" id="rno" onclick="ocultarFormulario025()">
                                    <label class="form-check-label" for="rno">NO</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="card025" style="display: none;">
                        <div class="row odd-row">
                            <div class="card-body">    
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[25][diagnostico]', null, ['class' => 'form-control', 'placeholder' => 'DIAGNOSTICO']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::date('preguntas[25][fecha]', null, ['class' => 'form-control', 'placeholder' => 'FECHA']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[25][tiempo]', null, ['class' => 'form-control', 'placeholder' => 'TIEMPO']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[25][parentesco]', null, ['class' => 'form-control', 'placeholder' => 'PARENTESCO']) !!}
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[25][gradorecuperacion]', null, ['class' => 'form-control', 'placeholder' => 'GRADO DE RECUPERACIÓN']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[25][medico]', null, ['class' => 'form-control', 'placeholder' => 'MÉDICO']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[25][direccionmedico]', null, ['class' => 'form-control', 'placeholder' => 'DIRECCIÓN DEL MÉDICO']) !!}
                                        </div>
                                    </div>
                                </div>
                                <abbr title="Nuevo Formulario"><button type="button" class="btn btn-mas" onclick="mostrarSiguienteCard025()"><i class="fas fa-plus"></i></button></abbr>
                                <abbr id="menosFormularioAbbr025" title="Menos Formulario" style="display: none;">
                                    <button type="button" class="btn btn-menos" onclick="ocultarSiguienteCard025()">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </abbr>
                            </div>
                        </div>
                    </div>
                    <div id="siguienteCard025" style="display: none;">
                        <div class="row odd-row">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[25][diagnostico2]', null, ['class' => 'form-control', 'placeholder' => 'DIAGNOSTICO']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::date('preguntas[25][fecha2]', null, ['class' => 'form-control', 'placeholder' => 'FECHA']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[25][tiempo2]', null, ['class' => 'form-control', 'placeholder' => 'TIEMPO']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[25][parentesco2]', null, ['class' => 'form-control', 'placeholder' => 'PARENTESCO']) !!}
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[25][gradorecuperacion2]', null, ['class' => 'form-control', 'placeholder' => 'GRADO DE RECUPERACIÓN']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[25][medico2]', null, ['class' => 'form-control', 'placeholder' => 'MÉDICO']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[25][direccionmedico2]', null, ['class' => 'form-control', 'placeholder' => 'DIRECCIÓN DEL MÉDICO']) !!}
                                        </div>
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
                            document.getElementById('siguienteCard025').querySelectorAll('input[type="text"]').forEach(function(input) {
                                input.value = '';
                            });
                            }
                        function mostrarFormulario025() {
                            document.getElementById('card025').style.display = 'block';
                            }
                        function ocultarFormulario025() {
                            document.getElementById('card025').style.display = 'none';
                            document.getElementById('siguienteCard025').style.display = 'none';
                            limpiarCampos025();
                            }
                        function mostrarSiguienteCard025() {
                            document.getElementById('siguienteCard025').style.display = 'block';
                            document.getElementById('menosFormularioAbbr025').style.display = 'inline';
                            }
                        function ocultarSiguienteCard025() {
                            var siguienteCard02 = document.getElementById('siguienteCard025');
                            if (siguienteCard02.style.display === 'block') {
                                siguienteCard02.style.display = 'none';
                                limpiarCamposSiguienteCard025();
                                document.getElementById('menosFormularioAbbr025').style.display = 'none';
                                }
                            }
                        function limpiarCamposSiguienteCard025() {
                            document.getElementById('siguienteCard025').querySelectorAll('input[type="text"]').forEach(function(input) {
                                input.value = '';
                                });
                            }
                    </script>
                </div>

                <br>
                <p><strong><span style="font-size: larger;">ESTADO DE SALUD DE FAMILIARES</span></strong></p>
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
                                    <input type="text" class="form-control" disabled>
                                </div>
                            </div>
                            <div class="col-lg-2 edadfa hidden">
                                <label>Edad al fallecer</label>
                                <div class="form-group">
                                    <input type="text" class="form-control" disabled>
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
                                    <input type="text" class="form-control" disabled>
                                </div>
                            </div>
                            <div class="col-lg-2 edadfa hidden">
                                <label>Edad al fallecer</label>
                                <div class="form-group">
                                    <input type="text" class="form-control" disabled>
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
                        <p><strong><span style="font-size: larger;">HAERMANO/AS</span></strong></p>
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
                                    <input type="text" class="form-control" disabled>
                                </div>
                            </div>
                            <div class="col-lg-2 edadfa hidden">
                                <label>Edad al fallecer</label>
                                <div class="form-group">
                                    <input type="text" class="form-control" disabled>
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
                        <p><strong><span style="font-size: larger;">N. VIVO</span></strong></p>
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
                                    <input type="text" class="form-control" disabled>
                                </div>
                            </div>
                            <div class="col-lg-2 edadfa hidden">
                                <label>Edad al fallecer</label>
                                <div class="form-group">
                                    <input type="text" class="form-control" disabled>
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
                        <p><strong><span style="font-size: larger;">N. MUERTO</span></strong></p>
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
                                    <input type="text" class="form-control" disabled>
                                </div>
                            </div>
                            <div class="col-lg-2 edadfa hidden">
                                <label>Edad al fallecer</label>
                                <div class="form-group">
                                    <input type="text" class="form-control" disabled>
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
                </div>

                <br>
                <p><strong><span style="font-size: larger;">ESTATURA Y PESO</span></strong></p>
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label>Estatura:</label>
                            <div class="input-group">
                                <input type="text" name="estatura" class="form-control" placeholder="" maxlength="45">
                                <div class="input-group-append">
                                    <span class="input-group-text">m.</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label>Peso:</label>
                            <div class="input-group">
                                <input type="text" name="peso" class="form-control" placeholder="" maxlength="45">
                                <div class="input-group-append">
                                    <span class="input-group-text">kg.</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div><br>

                <div class="26">
                    {!! Form::hidden('preguntas[26][pregunta_id]', 26) !!}
                    {!! Form::hidden('preguntas[26][pregunta_nombre]', 'Ha tenido algún cambio de peso en los últimos 12 meses') !!}
                    {!! Form::hidden('preguntas[26][cliente_id]', $clientebanco->id) !!}
                    <div class="row">
                        <div class="col-lg-10">
                            <p>Ha tenido algún cambio de peso en los últimos 12 meses</p>
                        </div>
                        <div class="col-lg-2">
                            <div class="form-group">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="preguntas[26][respuesta]" value="si" id="rsi" onclick="mostrarFormulario026()">
                                    <label class="form-check-label" for="rsi">SI</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="preguntas[26][respuesta]" value="no" id="rno" onclick="ocultarFormulario026()">
                                    <label class="form-check-label" for="rno">NO</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="card026" style="display: none;">
                        <div class="row">
                            <div class="card-body">    
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[26][diagnostico]', null, ['class' => 'form-control', 'placeholder' => 'DIAGNOSTICO']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::date('preguntas[26][fecha]', null, ['class' => 'form-control', 'placeholder' => 'FECHA']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[26][tiempo]', null, ['class' => 'form-control', 'placeholder' => 'TIEMPO']) !!}
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[26][gradorecuperacion]', null, ['class' => 'form-control', 'placeholder' => 'GRADO DE RECUPERACIÓN']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[26][medico]', null, ['class' => 'form-control', 'placeholder' => 'MÉDICO']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[26][direccionmedico]', null, ['class' => 'form-control', 'placeholder' => 'DIRECCIÓN DEL MÉDICO']) !!}
                                        </div>
                                    </div>
                                </div>
                                <abbr title="Nuevo Formulario"><button type="button" class="btn btn-mas" onclick="mostrarSiguienteCard026()"><i class="fas fa-plus"></i></button></abbr>
                                <abbr id="menosFormularioAbbr026" title="Menos Formulario" style="display: none;">
                                    <button type="button" class="btn btn-menos" onclick="ocultarSiguienteCard026()">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </abbr>
                            </div>
                        </div>
                    </div>
                    <div id="siguienteCard026" style="display: none;">
                        <div class="row">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[26][diagnostico2]', null, ['class' => 'form-control', 'placeholder' => 'DIAGNOSTICO']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::date('preguntas[26][fecha2]', null, ['class' => 'form-control', 'placeholder' => 'FECHA']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[26][tiempo2]', null, ['class' => 'form-control', 'placeholder' => 'TIEMPO']) !!}
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[26][gradorecuperacion2]', null, ['class' => 'form-control', 'placeholder' => 'GRADO DE RECUPERACIÓN']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[26][medico2]', null, ['class' => 'form-control', 'placeholder' => 'MÉDICO']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[26][direccionmedico2]', null, ['class' => 'form-control', 'placeholder' => 'DIRECCIÓN DEL MÉDICO']) !!}
                                        </div>
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
                            document.getElementById('siguienteCard026').querySelectorAll('input[type="text"]').forEach(function(input) {
                                input.value = '';
                            });
                            }
                        function mostrarFormulario026() {
                            document.getElementById('card026').style.display = 'block';
                            }
                        function ocultarFormulario026() {
                            document.getElementById('card026').style.display = 'none';
                            document.getElementById('siguienteCard026').style.display = 'none';
                            limpiarCampos026();
                            }
                        function mostrarSiguienteCard026() {
                            document.getElementById('siguienteCard026').style.display = 'block';
                            document.getElementById('menosFormularioAbbr026').style.display = 'inline';
                            }
                        function ocultarSiguienteCard026() {
                            var siguienteCard02 = document.getElementById('siguienteCard026');
                            if (siguienteCard02.style.display === 'block') {
                                siguienteCard02.style.display = 'none';
                                limpiarCamposSiguienteCard026();
                                document.getElementById('menosFormularioAbbr026').style.display = 'none';
                                }
                            }
                        function limpiarCamposSiguienteCard026() {
                            document.getElementById('siguienteCard026').querySelectorAll('input[type="text"]').forEach(function(input) {
                                input.value = '';
                                });
                            }
                    </script>
                </div>

                @if($clientebanco->genero === 'Femenino')
                <div class="27">
                    {!! Form::hidden('preguntas[27][pregunta_id]', 27) !!}
                    {!! Form::hidden('preguntas[27][pregunta_nombre]', 'Ha tenido alguna vez transtornos de la mestruación, pechos, aparato genital o alteraciones en el embarazo') !!}
                    {!! Form::hidden('preguntas[27][cliente_id]', $clientebanco->id) !!}
                    <div class="row odd-row">
                        <div class="col-lg-10">
                            <p>Ha tenido alguna vez transtornos de la mestruación, pechos, aparato genital o alteraciones en el embarazo</p>
                        </div>
                        <div class="col-lg-2">
                            <div class="form-group">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="preguntas[27][respuesta]" value="si" id="rsi" onclick="mostrarFormulario027()">
                                    <label class="form-check-label" for="rsi">SI</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="preguntas[27][respuesta]" value="no" id="rno" onclick="ocultarFormulario027()">
                                    <label class="form-check-label" for="rno">NO</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="card027" style="display: none;">
                        <div class="row odd-row">
                            <div class="card-body">    
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[27][diagnostico]', null, ['class' => 'form-control', 'placeholder' => 'DIAGNOSTICO']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::date('preguntas[27][fecha]', null, ['class' => 'form-control', 'placeholder' => 'FECHA']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[27][tiempo]', null, ['class' => 'form-control', 'placeholder' => 'TIEMPO']) !!}
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[27][gradorecuperacion]', null, ['class' => 'form-control', 'placeholder' => 'GRADO DE RECUPERACIÓN']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[27][medico]', null, ['class' => 'form-control', 'placeholder' => 'MÉDICO']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[27][direccionmedico]', null, ['class' => 'form-control', 'placeholder' => 'DIRECCIÓN DEL MÉDICO']) !!}
                                        </div>
                                    </div>
                                </div>
                                <abbr title="Nuevo Formulario"><button type="button" class="btn btn-mas" onclick="mostrarSiguienteCard027()"><i class="fas fa-plus"></i></button></abbr>
                                <abbr id="menosFormularioAbbr027" title="Menos Formulario" style="display: none;">
                                    <button type="button" class="btn btn-menos" onclick="ocultarSiguienteCard027()">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </abbr>
                            </div>
                        </div>
                    </div>
                    <div id="siguienteCard027" style="display: none;">
                        <div class="row odd-row">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[27][diagnostico2]', null, ['class' => 'form-control', 'placeholder' => 'DIAGNOSTICO']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::date('preguntas[27][fecha2]', null, ['class' => 'form-control', 'placeholder' => 'FECHA']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[27][tiempo2]', null, ['class' => 'form-control', 'placeholder' => 'TIEMPO']) !!}
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[27][gradorecuperacion2]', null, ['class' => 'form-control', 'placeholder' => 'GRADO DE RECUPERACIÓN']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[27][medico2]', null, ['class' => 'form-control', 'placeholder' => 'MÉDICO']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[27][direccionmedico2]', null, ['class' => 'form-control', 'placeholder' => 'DIRECCIÓN DEL MÉDICO']) !!}
                                        </div>
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
                            document.getElementById('siguienteCard027').querySelectorAll('input[type="text"]').forEach(function(input) {
                                input.value = '';
                            });
                            }
                        function mostrarFormulario027() {
                            document.getElementById('card027').style.display = 'block';
                            }
                        function ocultarFormulario027() {
                            document.getElementById('card027').style.display = 'none';
                            document.getElementById('siguienteCard027').style.display = 'none';
                            limpiarCampos027();
                            }
                        function mostrarSiguienteCard027() {
                            document.getElementById('siguienteCard027').style.display = 'block';
                            document.getElementById('menosFormularioAbbr027').style.display = 'inline';
                            }
                        function ocultarSiguienteCard027() {
                            var siguienteCard02 = document.getElementById('siguienteCard027');
                            if (siguienteCard02.style.display === 'block') {
                                siguienteCard02.style.display = 'none';
                                limpiarCamposSiguienteCard027();
                                document.getElementById('menosFormularioAbbr027').style.display = 'none';
                                }
                            }
                        function limpiarCamposSiguienteCard027() {
                            document.getElementById('siguienteCard027').querySelectorAll('input[type="text"]').forEach(function(input) {
                                input.value = '';
                                });
                            }
                    </script>
                </div>

                <div class="28">
                    {!! Form::hidden('preguntas[28][pregunta_id]', 28) !!}
                    {!! Form::hidden('preguntas[28][pregunta_nombre]', 'Está embarazada en la actualidad') !!}
                    {!! Form::hidden('preguntas[28][cliente_id]', $clientebanco->id) !!}
                    <div class="row">
                        <div class="col-lg-10">
                            <p>Está embarazada en la actualidad</p>
                        </div>
                        <div class="col-lg-2">
                            <div class="form-group">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="preguntas[27][respuesta]" value="si" id="rsi" onclick="mostrarFormulario028()">
                                    <label class="form-check-label" for="rsi">SI</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="preguntas[27][respuesta]" value="no" id="rno" onclick="ocultarFormulario028()">
                                    <label class="form-check-label" for="rno">NO</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="card028" style="display: none;">
                        <div class="row odd-row">
                            <div class="card-body">    
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[28][cuantosmeses]', null, ['class' => 'form-control', 'placeholder' => 'CUANTOS MESES']) !!}
                                        </div>
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
                @endif

                <div class="29">
                    {!! Form::hidden('preguntas[29][pregunta_id]', 29) !!}
                    {!! Form::hidden('preguntas[29][pregunta_nombre]', 'Posee la enfermedad del SIDA') !!}
                    {!! Form::hidden('preguntas[29][cliente_id]', $clientebanco->id) !!}
                    <div class="row odd-row">
                        <div class="col-lg-10">
                            <p>Posee la enfermedad del SIDA</p>
                        </div>
                        <div class="col-lg-2">
                            <div class="form-group">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="preguntas[29][respuesta]" value="si" id="rsi" onclick="mostrarFormulario029()">
                                    <label class="form-check-label" for="rsi">SI</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="preguntas[29][respuesta]" value="no" id="rno" onclick="ocultarFormulario029()">
                                    <label class="form-check-label" for="rno">NO</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="card029" style="display: none;">
                        <div class="row odd-row">
                            <div class="card-body">    
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[29][detallescompletos]', null, ['class' => 'form-control', 'placeholder' => 'DETALLES COMPLETOS']) !!}
                                        </div>
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
                
                <div class="30">
                    {!! Form::hidden('preguntas[30][pregunta_id]', 30) !!}
                    {!! Form::hidden('preguntas[30][pregunta_nombre]', 'Ha recibido usted tratamiento o consejo médico en relación al SIDA o condiciones relacionadas con él, o en relación a enfermedades de transmisión sexual') !!}
                    {!! Form::hidden('preguntas[30][cliente_id]', $clientebanco->id) !!}
                    <div class="row">
                        <div class="col-lg-10">
                            <p>Ha recibido usted tratamiento o consejo médico en relación al SIDA o condiciones relacionadas con él, o en relación a enfermedades de transmisión sexual</p>
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
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[30][diagnostico]', null, ['class' => 'form-control', 'placeholder' => 'DIAGNOSTICO']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::date('preguntas[30][fecha]', null, ['class' => 'form-control', 'placeholder' => 'FECHA']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[30][tiempo]', null, ['class' => 'form-control', 'placeholder' => 'TIEMPO']) !!}
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[30][gradorecuperacion]', null, ['class' => 'form-control', 'placeholder' => 'GRADO DE RECUPERACIÓN']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[30][medico]', null, ['class' => 'form-control', 'placeholder' => 'MÉDICO']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[30][direccionmedico]', null, ['class' => 'form-control', 'placeholder' => 'DIRECCIÓN DEL MÉDICO']) !!}
                                        </div>
                                    </div>
                                </div>
                                <abbr title="Nuevo Formulario"><button type="button" class="btn btn-mas" onclick="mostrarSiguienteCard030()"><i class="fas fa-plus"></i></button></abbr>
                                <abbr id="menosFormularioAbbr030" title="Menos Formulario" style="display: none;">
                                    <button type="button" class="btn btn-menos" onclick="ocultarSiguienteCard030()">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </abbr>
                            </div>
                        </div>
                    </div>
                    <div id="siguienteCard030" style="display: none;">
                        <div class="row">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[30][diagnostico2]', null, ['class' => 'form-control', 'placeholder' => 'DIAGNOSTICO']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::date('preguntas[30][fecha2]', null, ['class' => 'form-control', 'placeholder' => 'FECHA']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[30][tiempo2]', null, ['class' => 'form-control', 'placeholder' => 'TIEMPO']) !!}
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[30][gradorecuperacion2]', null, ['class' => 'form-control', 'placeholder' => 'GRADO DE RECUPERACIÓN']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[30][medico2]', null, ['class' => 'form-control', 'placeholder' => 'MÉDICO']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[30][direccionmedico2]', null, ['class' => 'form-control', 'placeholder' => 'DIRECCIÓN DEL MÉDICO']) !!}
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
                            document.getElementById('siguienteCard030').querySelectorAll('input[type="text"]').forEach(function(input) {
                                input.value = '';
                            });
                            }
                        function mostrarFormulario030() {
                            document.getElementById('card030').style.display = 'block';
                            }
                        function ocultarFormulario030() {
                            document.getElementById('card030').style.display = 'none';
                            document.getElementById('siguienteCard030').style.display = 'none';
                            limpiarCampos030();
                            }
                        function mostrarSiguienteCard030() {
                            document.getElementById('siguienteCard030').style.display = 'block';
                            document.getElementById('menosFormularioAbbr030').style.display = 'inline';
                            }
                        function ocultarSiguienteCard030() {
                            var siguienteCard02 = document.getElementById('siguienteCard030');
                            if (siguienteCard02.style.display === 'block') {
                                siguienteCard02.style.display = 'none';
                                limpiarCamposSiguienteCard030();
                                document.getElementById('menosFormularioAbbr030').style.display = 'none';
                                }
                            }
                        function limpiarCamposSiguienteCard030() {
                            document.getElementById('siguienteCard030').querySelectorAll('input[type="text"]').forEach(function(input) {
                                input.value = '';
                                });
                            }
                    </script>
                </div>

                <div class="31">
                    {!! Form::hidden('preguntas[31][pregunta_id]', 31) !!}
                    {!! Form::hidden('preguntas[31][pregunta_nombre]', 'Le han dicho que ha tenido SIDA o el Complejo Relacionado al SIDA') !!}
                    {!! Form::hidden('preguntas[31][cliente_id]', $clientebanco->id) !!}
                    <div class="row odd-row">
                        <div class="col-lg-10">
                            <p>Le han dicho que ha tenido SIDA o el Complejo Relacionado al SIDA</p>
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
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[31][diagnostico]', null, ['class' => 'form-control', 'placeholder' => 'DIAGNOSTICO']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::date('preguntas[31][fecha]', null, ['class' => 'form-control', 'placeholder' => 'FECHA']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[31][tiempo]', null, ['class' => 'form-control', 'placeholder' => 'TIEMPO']) !!}
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[31][gradorecuperacion]', null, ['class' => 'form-control', 'placeholder' => 'GRADO DE RECUPERACIÓN']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[31][medico]', null, ['class' => 'form-control', 'placeholder' => 'MÉDICO']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[31][direccionmedico]', null, ['class' => 'form-control', 'placeholder' => 'DIRECCIÓN DEL MÉDICO']) !!}
                                        </div>
                                    </div>
                                </div>
                                <abbr title="Nuevo Formulario"><button type="button" class="btn btn-mas" onclick="mostrarSiguienteCard031()"><i class="fas fa-plus"></i></button></abbr>
                                <abbr id="menosFormularioAbbr031" title="Menos Formulario" style="display: none;">
                                    <button type="button" class="btn btn-menos" onclick="ocultarSiguienteCard031()">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </abbr>
                            </div>
                        </div>
                    </div>
                    <div id="siguienteCard031" style="display: none;">
                        <div class="row odd-row">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[31][diagnostico2]', null, ['class' => 'form-control', 'placeholder' => 'DIAGNOSTICO']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::date('preguntas[31][fecha2]', null, ['class' => 'form-control', 'placeholder' => 'FECHA']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[31][tiempo2]', null, ['class' => 'form-control', 'placeholder' => 'TIEMPO']) !!}
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[31][gradorecuperacion2]', null, ['class' => 'form-control', 'placeholder' => 'GRADO DE RECUPERACIÓN']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[31][medico2]', null, ['class' => 'form-control', 'placeholder' => 'MÉDICO']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[31][direccionmedico2]', null, ['class' => 'form-control', 'placeholder' => 'DIRECCIÓN DEL MÉDICO']) !!}
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
                            document.getElementById('siguienteCard031').querySelectorAll('input[type="text"]').forEach(function(input) {
                                input.value = '';
                            });
                            }
                        function mostrarFormulario031() {
                            document.getElementById('card031').style.display = 'block';
                            }
                        function ocultarFormulario031() {
                            document.getElementById('card031').style.display = 'none';
                            document.getElementById('siguienteCard031').style.display = 'none';
                            limpiarCampos031();
                            }
                        function mostrarSiguienteCard031() {
                            document.getElementById('siguienteCard031').style.display = 'block';
                            document.getElementById('menosFormularioAbbr031').style.display = 'inline';
                            }
                        function ocultarSiguienteCard031() {
                            var siguienteCard02 = document.getElementById('siguienteCard031');
                            if (siguienteCard02.style.display === 'block') {
                                siguienteCard02.style.display = 'none';
                                limpiarCamposSiguienteCard031();
                                document.getElementById('menosFormularioAbbr031').style.display = 'none';
                                }
                            }
                        function limpiarCamposSiguienteCard031() {
                            document.getElementById('siguienteCard031').querySelectorAll('input[type="text"]').forEach(function(input) {
                                input.value = '';
                                });
                            }
                    </script>
                </div>

                <div class="32">
                    {!! Form::hidden('preguntas[32][pregunta_id]', 32) !!}
                    {!! Form::hidden('preguntas[32][pregunta_nombre]', 'Ha tenido o le han informado que tiene prueba sanguíneas positivas para anticuerpos del virus del SIDA') !!}
                    {!! Form::hidden('preguntas[32][cliente_id]', $clientebanco->id) !!}
                    <div class="row">
                        <div class="col-lg-10">
                            <p>Ha tenido o le han informado que tiene prueba sanguíneas positivas para anticuerpos del virus del SIDA</p>
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
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[32][diagnostico]', null, ['class' => 'form-control', 'placeholder' => 'DIAGNOSTICO']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::date('preguntas[32][fecha]', null, ['class' => 'form-control', 'placeholder' => 'FECHA']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[32][tiempo]', null, ['class' => 'form-control', 'placeholder' => 'TIEMPO']) !!}
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[32][gradorecuperacion]', null, ['class' => 'form-control', 'placeholder' => 'GRADO DE RECUPERACIÓN']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[32][medico]', null, ['class' => 'form-control', 'placeholder' => 'MÉDICO']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[32][direccionmedico]', null, ['class' => 'form-control', 'placeholder' => 'DIRECCIÓN DEL MÉDICO']) !!}
                                        </div>
                                    </div>
                                </div>
                                <abbr title="Nuevo Formulario"><button type="button" class="btn btn-mas" onclick="mostrarSiguienteCard032()"><i class="fas fa-plus"></i></button></abbr>
                                <abbr id="menosFormularioAbbr032" title="Menos Formulario" style="display: none;">
                                    <button type="button" class="btn btn-menos" onclick="ocultarSiguienteCard032()">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </abbr>
                            </div>
                        </div>
                    </div>
                    <div id="siguienteCard032" style="display: none;">
                        <div class="row">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[32][diagnostico2]', null, ['class' => 'form-control', 'placeholder' => 'DIAGNOSTICO']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::date('preguntas[32][fecha2]', null, ['class' => 'form-control', 'placeholder' => 'FECHA']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[32][tiempo2]', null, ['class' => 'form-control', 'placeholder' => 'TIEMPO']) !!}
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[32][gradorecuperacion2]', null, ['class' => 'form-control', 'placeholder' => 'GRADO DE RECUPERACIÓN']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[32][medico2]', null, ['class' => 'form-control', 'placeholder' => 'MÉDICO']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[32][direccionmedico2]', null, ['class' => 'form-control', 'placeholder' => 'DIRECCIÓN DEL MÉDICO']) !!}
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
                            document.getElementById('siguienteCard032').querySelectorAll('input[type="text"]').forEach(function(input) {
                                input.value = '';
                            });
                            }
                        function mostrarFormulario032() {
                            document.getElementById('card032').style.display = 'block';
                            }
                        function ocultarFormulario032() {
                            document.getElementById('card032').style.display = 'none';
                            document.getElementById('siguienteCard032').style.display = 'none';
                            limpiarCampos032();
                            }
                        function mostrarSiguienteCard032() {
                            document.getElementById('siguienteCard032').style.display = 'block';
                            document.getElementById('menosFormularioAbbr032').style.display = 'inline';
                            }
                        function ocultarSiguienteCard032() {
                            var siguienteCard02 = document.getElementById('siguienteCard032');
                            if (siguienteCard02.style.display === 'block') {
                                siguienteCard02.style.display = 'none';
                                limpiarCamposSiguienteCard032();
                                document.getElementById('menosFormularioAbbr032').style.display = 'none';
                                }
                            }
                        function limpiarCamposSiguienteCard032() {
                            document.getElementById('siguienteCard032').querySelectorAll('input[type="text"]').forEach(function(input) {
                                input.value = '';
                                });
                            }
                    </script>
                </div>

                <div class="33">
                    {!! Form::hidden('preguntas[33][pregunta_id]', 33) !!}
                    {!! Form::hidden('preguntas[33][pregunta_nombre]', 'Tiene usted alguno de estos síntomas sin explicación fatiga pérdida de peso, diarrea, ganglios linfáticos inflamados o extrañas lesiones en la piel') !!}
                    {!! Form::hidden('preguntas[33][cliente_id]', $clientebanco->id) !!}
                    <div class="row odd-row">
                        <div class="col-lg-10">
                            <p>Tiene usted alguno de estos síntomas sin explicación fatiga pérdida de peso, diarrea, ganglios linfáticos inflamados o extrañas lesiones en la piel</p>
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
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[33][diagnostico]', null, ['class' => 'form-control', 'placeholder' => 'DIAGNOSTICO']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::date('preguntas[33][fecha]', null, ['class' => 'form-control', 'placeholder' => 'FECHA']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[33][tiempo]', null, ['class' => 'form-control', 'placeholder' => 'TIEMPO']) !!}
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[33][gradorecuperacion]', null, ['class' => 'form-control', 'placeholder' => 'GRADO DE RECUPERACIÓN']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[33][medico]', null, ['class' => 'form-control', 'placeholder' => 'MÉDICO']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[33][direccionmedico]', null, ['class' => 'form-control', 'placeholder' => 'DIRECCIÓN DEL MÉDICO']) !!}
                                        </div>
                                    </div>
                                </div>
                                <abbr title="Nuevo Formulario"><button type="button" class="btn btn-mas" onclick="mostrarSiguienteCard033()"><i class="fas fa-plus"></i></button></abbr>
                                <abbr id="menosFormularioAbbr033" title="Menos Formulario" style="display: none;">
                                    <button type="button" class="btn btn-menos" onclick="ocultarSiguienteCard033()">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </abbr>
                            </div>
                        </div>
                    </div>
                    <div id="siguienteCard033" style="display: none;">
                        <div class="row odd-row">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[33][diagnostico2]', null, ['class' => 'form-control', 'placeholder' => 'DIAGNOSTICO']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::date('preguntas[33][fecha2]', null, ['class' => 'form-control', 'placeholder' => 'FECHA']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[33][tiempo2]', null, ['class' => 'form-control', 'placeholder' => 'TIEMPO']) !!}
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[33][gradorecuperacion2]', null, ['class' => 'form-control', 'placeholder' => 'GRADO DE RECUPERACIÓN']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[33][medico2]', null, ['class' => 'form-control', 'placeholder' => 'MÉDICO']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('', '') !!}
                                            {!! Form::text('preguntas[33][direccionmedico2]', null, ['class' => 'form-control', 'placeholder' => 'DIRECCIÓN DEL MÉDICO']) !!}
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
                            document.getElementById('siguienteCard033').querySelectorAll('input[type="text"]').forEach(function(input) {
                                input.value = '';
                            });
                            }
                        function mostrarFormulario033() {
                            document.getElementById('card033').style.display = 'block';
                            }
                        function ocultarFormulario033() {
                            document.getElementById('card033').style.display = 'none';
                            document.getElementById('siguienteCard033').style.display = 'none';
                            limpiarCampos033();
                            }
                        function mostrarSiguienteCard033() {
                            document.getElementById('siguienteCard033').style.display = 'block';
                            document.getElementById('menosFormularioAbbr033').style.display = 'inline';
                            }
                        function ocultarSiguienteCard033() {
                            var siguienteCard02 = document.getElementById('siguienteCard033');
                            if (siguienteCard02.style.display === 'block') {
                                siguienteCard02.style.display = 'none';
                                limpiarCamposSiguienteCard033();
                                document.getElementById('menosFormularioAbbr033').style.display = 'none';
                                }
                            }
                        function limpiarCamposSiguienteCard033() {
                            document.getElementById('siguienteCard033').querySelectorAll('input[type="text"]').forEach(function(input) {
                                input.value = '';
                                });
                            }
                    </script>
                </div>

                <br>
                <P>Confirmo que soy la persona arriba mencionada como Propuesto Asegurado y que las declaraciones y respuestas precedentes, una vez debidamente comprobadas son completas, auténticas, correctamente transcritas y forman parte de la solicitud de Seguro sobre mi vida hecha a Nacional Vida Seguros de Personas S.A. Por la presente autorizo a todo médico, hospital, compañía de seguros u otra institución o persona cualesquiera a que, dentro de los límites legales, faciliten a Nacional Vida Seguros de Personas S.A. o su representante, información sobre mi estado de salud, historial médico y cualquier hospitalización, recomendación, diagnóstico, tratamiento, enfermedad o dolencia. Una fotocopia de esta autorización será asimismo válida</P>
                <br>
                <div class="Firmada">
                    <div class="container2">
                        <div class="form-line">
                            <label for="lugar">Firmada en</label>
                            <div style="flex: 1;">
                                <input type="text" id="lugar" name="lugar">
                            </div>
                        </div>
                        <div class="form-line">
                            <label for="dia">El</label>
                            <div style="flex: 1;">
                                <input type="text" id="dia" name="dia">
                            </div>
                        </div>
                        <div class="form-line">
                            <label for="mes">De</label>
                            <div style="flex: 1;">
                                <input type="text" id="mes" name="mes">
                            </div>
                        </div>
                        <div class="form-line">
                            <label for="anio">De 20</label>
                            <div style="flex: 1;">
                                <input type="text" id="anio" name="anio">
                            </div>
                        </div>
                    </div>
                </div>
                <br><br>

                <div class="firm-container" style="margin-top: 40px">
                    <div class="firm-column">
                        <div class="firm-line">
                            <input type="text" id="medico" name="medico" readonly>
                        </div>
                        <div class="title-line"></div>
                        <div class="firm-line">
                            <div class="title">
                                <button onclick="document.getElementById('medico_signature').click()">Seleccionar imagen</button>
                                <span>FIRMA DEL MEDICO EXAMINADOR</span>
                                <input type="file" id="medico_signature" name="medico_signature" style="display: none;" onchange="displayImage(this, 'medico_signature_preview')">
                            </div>
                        </div>
                        <div class="title-line"></div>
                        <img id="medico_signature_preview" src="" style="display: none; max-width: 200px; margin-top: 10px;">
                    </div>
                    <div class="firm-column">
                        <div class="firm-line">
                            <input type="text" id="propuesto" name="propuesto" readonly>
                        </div>
                        <div class="title-line"></div>
                        <div class="firm-line">
                            <div class="title">
                                <button onclick="document.getElementById('propuesto_signature').click()">Seleccionar imagen</button>
                                <span>FIRMA DEL PROPUESTO ASEGURADO</span>
                                <input type="file" id="propuesto_signature" name="propuesto_signature" style="display: none;" onchange="displayImage(this, 'propuesto_signature_preview')">
                            </div>
                        </div>
                        <div class="title-line"></div>
                        <img id="propuesto_signature_preview" src="" style="display: none; max-width: 200px; margin-top: 10px;">
                    </div>
                </div>


                {!! Form::submit('Crear formulario', ['class' => 'btn btn-primary']) !!}
            {!! Form::close() !!}


            {{-- <form action="{{ route('generar.qr') }}" method="POST">
                <!-- Aquí van los campos del formulario -->
                @csrf
                <button type="submit">Generar QR temporal</button>
            </form>
            
            @if(isset($rutaQR))
                <img src="{{ $rutaQR }}" alt="QR Temporal">
            @endif
            <script>
            function displayImage(input, previewId) {
                var preview = document.getElementById(previewId);
                if (input.files && input.files[0]) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        preview.src = e.target.result;
                        preview.style.display = "block";
                    }
                    reader.readAsDataURL(input.files[0]);
                } else {
                    preview.src = "";
                    preview.style.display = null;
                }
            }
            </script> --}}
            
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
                    } else if (this.value === "fallecido") {
                        parentSection.querySelector('.edadfa .form-control').disabled = false;
                        parentSection.querySelector('.causafa .form-control').disabled = false;
                    }
                });
            });
        });
</script>
         
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