@extends('adminlte::page')

@section('content_header')

<a class="btn btn-sm float-right btn-regresar" href="{{route('admin.asociados.listadoclienteita', $asociado)}}">REGRESAR</a>
<button type="button" class="btn btn-sm float-right btn-crearempresa" style="margin-right: 10px;" data-toggle="modal" data-target="#crearEmpresaModal">
CREAR EMPRESA
</button>
<h1>NUEVO CLIENTE ITA</h1>
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
        {!! Form::model($asociado, ['route' => ['admin.asociados.guardarclienteita', $asociado], 'method' => 'POST', 'files' => true]) !!}
        {!! Form::hidden('users_id', auth()->user()->id) !!}
        {!! Form::hidden('usuarioregistro', auth()->user()->name) !!}
        <div class="row">
            <div class="col-lg-5">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group">
                            {!! Form::label('sucursal', 'Sucursal:') !!}
                            {!! Form::select('sucursal', $suc, null, ['class' => 'form-control', 'placeholder' => '']) !!}
                            @error('sucursal')
                                <small class="text-danger fas fa-exclamation-circle">
                                    {{$message}}
                                </small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group">
                            {!! Form::label('derivacion', 'Derivación:') !!}
                            {!! Form::select('derivacion', [
                                '' => '',
                                'PROGRAMACIÓN' => 'PROGRAMACIÓN',
                                'CARTERA DE CLIENTES' => 'CARTERA DE CLIENTES',
                            ], null, ['class' => 'form-control']) !!}
                            @error('derivacion')
                                <small class="text-danger fas fa-exclamation-circle">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-3">
                        <div class="form-group">
                            {!! Form::label('apepaterno', 'Ap. Paterno:') !!}
                            {!! Form::text('apepaterno', null, ['class' => 'form-control', 'id' => 'apepaterno', 'placeholder' => '', 'maxlength' => '45']) !!}
                            @error('apepaterno')
                                <small class="text-danger fas fa-exclamation-circle">
                                    {{$message}}
                                </small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="form-group">
                            {!! Form::label('apematerno', 'Ap. Materno:') !!}
                            {!! Form::text('apematerno', null, ['class' => 'form-control', 'id' => 'apematerno', 'placeholder' => '', 'maxlength' => '45']) !!}
                            @error('apematerno')
                                <small class="text-danger fas fa-exclamation-circle">
                                    {{$message}}
                                </small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group">
                            {!! Form::label('nombres', 'Nombres:') !!}
                            {!! Form::text('nombres', null, ['class' => 'form-control', 'id' => 'nombres', 'placeholder' => '', 'maxlength' => '45']) !!}
                            @error('nombres')
                                <small class="text-danger fas fa-exclamation-circle">
                                    {{$message}}
                                </small>
                            @enderror
                        </div>
                    </div>
                </div>
                {!! Form::hidden('nombrecompleto', null, ['id' => 'nombrecompleto']) !!}
                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        const apepaterno = document.getElementById('apepaterno');
                        const apematerno = document.getElementById('apematerno');
                        const nombres = document.getElementById('nombres');
                        const nombrecompleto = document.getElementById('nombrecompleto');
                        function updateNombreCompleto() {
                            nombrecompleto.value = `${nombres.value} ${apepaterno.value} ${apematerno.value}`.trim();
                        }
                        apepaterno.addEventListener('input', updateNombreCompleto);
                        apematerno.addEventListener('input', updateNombreCompleto);
                        nombres.addEventListener('input', updateNombreCompleto);
                    });
                </script>
                <div class="row">
                    <div class="col-lg-4">
                        <div class="form-group">
                            {!! Form::label('tipoidentificacion', 'Tipo de Iden.:') !!}
                            {!! Form::select('tipoidentificacion', $tipoide, null, ['class' => 'form-control', 'placeholder' => '']) !!}
                            @error('tipoidentificacion')
                                <small class="text-danger fas fa-exclamation-circle">
                                    {{$message}}
                                </small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="form-group">
                            {!! Form::label('ci', 'CI / Iden.:') !!}
                            {!! Form::text('ci', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '45', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']) !!}
                            @error('ci')
                                <small class="text-danger fas fa-exclamation-circle">
                                    {{$message}}
                                </small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            {!! Form::label('cicomplemento', 'Compl.:') !!}
                            {!! Form::text('cicomplemento', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '45']) !!}
                            @error('cicomplemento')
                                <small class="text-danger fas fa-exclamation-circle">
                                    {{$message}}
                                </small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="form-group">
                            {!! Form::label('ciexp', 'C/ exp.:') !!}
                            {!! Form::select('ciexp', $ciudadexp, null, ['class' => 'form-control', 'placeholder' => '']) !!}
                            @error('ciexp')
                                <small class="text-danger fas fa-exclamation-circle">
                                    {{$message}}
                                </small>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-5"> 
                        <div class="form-group">
                            {!! Form::label('fechavencci', 'Fecha Venc/CI.:') !!}
                            <div class="input-group">
                                {!! Form::date('fechavencci', null, ['class' => 'form-control', 'id' => 'fechavencci']) !!}
                                <div class="input-group-append">
                                    <div class="input-group-text">
                                        <input type="checkbox" id="indefinidaCheckbox">
                                        <label for="indefinidaCheckbox" class="ml-2 mb-0">Indef.</label>
                                    </div>
                                </div>
                            </div>
                            @error('fechavencci')
                                <small class="text-danger fas fa-exclamation-circle">
                                    {{ $message }}
                                </small>
                            @enderror
                        </div>
                    </div>
                    <script>
                        document.addEventListener('DOMContentLoaded', function () {
                            const fechaInput = document.getElementById('fechavencci');
                            const checkbox = document.getElementById('indefinidaCheckbox');
                            checkbox.addEventListener('change', function () {
                                if (this.checked) {
                                    fechaInput.value = '';
                                    fechaInput.disabled = true;
                                } else {
                                    fechaInput.disabled = false;
                                }
                            });
                        });
                    </script>
                    <div class="col-lg-5">
                        <div class="form-group">
                            {!! Form::label('fechanacimiento', 'Fecha de nac.:') !!}
                            {!! Form::date('fechanacimiento', null, ['class' => 'form-control', 'id' => 'fecha_nacimiento']) !!}
                            @error('fechanacimiento')
                                <small class="text-danger fas fa-exclamation-circle">
                                    {{ $message }}
                                </small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-lg-2">
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
                </div>
                <div class="row">
                    <div class="col-lg-3">
                        <div class="form-group">
                            {!! Form::label('paisnacimiento', 'País de nac.:') !!}
                            {!! Form::select('paisnacimiento', [
                                '' => '',
                                'ARGENTINA' => 'ARGENTINA',
                                'BOLIVIA' => 'BOLIVIA',
                                'COLOMBIA' => 'COLOMBIA',
                                'EE.UU.' => 'EE.UU.',
                                'ESPAÑA' => 'ESPAÑA',
                                'MEXICO' => 'MEXICO',
                                'PARAGUAY' => 'PARAGUAY',
                                'PERÚ' => 'PERÚ',
                                'VENEZUELA' => 'VENEZUELA'
                            ], $cliente->paisnacimiento, ['class' => 'form-control', 'id' => 'paisnacimiento']) !!}
                            @error('paisnacimiento')
                                <small class="text-danger fas fa-exclamation-circle">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-lg-3" id="select-lugar-nacimiento">
                        <div class="form-group">
                            {!! Form::label('lugarnacimiento_select', 'Ciudad de nac.:') !!}
                            {!! Form::select('lugarnacimiento_select', $departamentos, 
                                in_array($cliente->lugarnacimiento, $departamentos->toArray()) ? $cliente->lugarnacimiento : null, 
                                ['class' => 'form-control', 'placeholder' => '']) !!}
                            @error('lugarnacimiento_select')
                                <small class="text-danger fas fa-exclamation-circle">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-lg-3" id="input-lugar-nacimiento" style="display: none;">
                        <div class="form-group">
                            {!! Form::label('lugarnacimiento_text', 'Ciudad de nac.:') !!}
                            {!! Form::text('lugarnacimiento_text', 
                                !in_array($cliente->lugarnacimiento, $departamentos->toArray()) ? $cliente->lugarnacimiento : null, 
                                ['class' => 'form-control', 'placeholder' => '']) !!}
                            @error('lugarnacimiento_text')
                                <small class="text-danger fas fa-exclamation-circle">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <script>
                        document.addEventListener('DOMContentLoaded', function () {
                            const paisSelect = document.getElementById('paisnacimiento');
                            const selectLugarNacimiento = document.getElementById('select-lugar-nacimiento');
                            const inputLugarNacimiento = document.getElementById('input-lugar-nacimiento');
                            const selectLugarNacimientoField = document.querySelector('select[name="lugarnacimiento_select"]');
                            const inputLugarNacimientoField = document.querySelector('input[name="lugarnacimiento_text"]');
                            function actualizarCampos() {
                                if (paisSelect.value === 'BOLIVIA') {
                                    selectLugarNacimiento.style.display = 'block';
                                    inputLugarNacimiento.style.display = 'none';
                                    inputLugarNacimientoField.value = '';
                                } else {
                                    selectLugarNacimiento.style.display = 'none';
                                    inputLugarNacimiento.style.display = 'block';
                                    selectLugarNacimientoField.value = '';
                                }
                            }
                            actualizarCampos();
                            paisSelect.addEventListener('change', actualizarCampos);
                        });
                    </script>         
                    <div class="col-lg-3">
                        <div class="form-group">
                            {!! Form::label('genero', 'Género:') !!}
                            {!! Form::select('genero', $genero, null, ['class' => 'form-control', 'placeholder' => '', 'id' => 'genero']) !!}
                            @error('genero')
                                <small class="text-danger fas fa-exclamation-circle">
                                    {{$message}}
                                </small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="form-group">
                            {!! Form::label('estadocivil', 'Estado civil:') !!}
                            {!! Form::select('estadocivil', $estciv, null, ['class' => 'form-control', 'placeholder' => '']) !!}
                            @error('estadocivil')
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
                            {!! Form::label('email', 'Email:') !!}
                            {!! Form::text('email', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '45']) !!}
                            @error('email')
                                <small class="text-danger fas fa-exclamation-circle">
                                    {{$message}}
                                </small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="form-group">
                            {!! Form::label('ocupacion', 'Prof. / Ocup.:') !!}
                            {!! Form::text('ocupacion', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '45']) !!}
                            @error('ocupacion')
                                <small class="text-danger fas fa-exclamation-circle">
                                    {{$message}}
                                </small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="form-group">
                            {!! Form::label('gradoinstruccion', 'Grado de Inst.:') !!}
                            {!! Form::select('gradoinstruccion', $gradins, null, ['class' => 'form-control', 'placeholder' => '']) !!}
                            @error('gradoinstruccion')
                                <small class="text-danger fas fa-exclamation-circle">
                                    {{$message}}
                                </small>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-5">
                        <div class="form-group">
                            {!! Form::label('', 'Celular:') !!}
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <select id="pais" class="form-control">
                                        <option value="">Pais</option>
                                        <option value="54">ARG</option>
                                        <option value="591">BOL</option>
                                        <option value="55">BRA</option>
                                        <option value="56">CHI</option>
                                        <option value="57">COL</option>
                                        <option value="593">ECU</option>
                                        <option value="1">E.U</option>
                                        <option value="34">ESP</option>
                                        <option value="52">MEX</option>
                                        <option value="595">PAR</option>
                                        <option value="51">PER</option>
                                        <option value="598">URU</option>
                                        <option value="58">VEN</option> 
                                    </select>
                                </div>
                                {!! Form::text('celular', null, ['id' => 'celular', 'class' => 'form-control', 'placeholder' => '', 'maxlength' => '25', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']) !!}
                            </div>
                            @error('celular')
                                <small class="text-danger fas fa-exclamation-circle">
                                    {{$message}}
                                </small>
                            @enderror
                        </div>
                        <script>
                            document.getElementById('pais').addEventListener('change', function() {
                                var codigoPais = this.value;
                                var campoCelular = document.getElementById('celular');
                                if (codigoPais) {
                                    campoCelular.value = codigoPais;
                                    campoCelular.focus();
                                } else {
                                    campoCelular.value = '';
                                }
                            });
                            document.getElementById('celular').addEventListener('input', function() {
                                var campoCelular = document.getElementById('celular');
                                var valorCelular = campoCelular.value.trim();
                                var codigoPais = document.getElementById('pais').value;
                                if (codigoPais && !valorCelular.startsWith(codigoPais)) {
                                    campoCelular.value = codigoPais + valorCelular;
                                } else {
                                    campoCelular.value = valorCelular;
                                }
                            });
                            document.addEventListener('DOMContentLoaded', function() {
                                const paisSelect = document.getElementById('pais');
                                const celularInput = document.getElementById('celular');

                                const longitudesCelulares = {
                                    '54': 12,  // Argentina
                                    '591': 11, // Bolivia
                                    '55': 13,  // Brasil
                                    '56': 11,  // Chile
                                    '57': 12,  // Colombia
                                    '593': 12, // Ecuador
                                    '1': 11,   // Estados Unidos
                                    '34': 11,  // España
                                    '52': 12,  // México
                                    '595': 12, // Paraguay
                                    '51': 11,  // Perú
                                    '598': 12, // Uruguay
                                    '58': 12   // Venezuela
                                };

                                function validarTelefono() {
                                    const codigoPais = paisSelect.value;
                                    const longitudEsperada = longitudesCelulares[codigoPais] || null;
                                    if (longitudEsperada !== null) {
                                        if (celularInput.value.length !== longitudEsperada) {
                                            celularInput.setCustomValidity(`El número de celular debe tener ${longitudEsperada} dígitos.`);
                                        } else {
                                            celularInput.setCustomValidity('');
                                        }
                                    } else {
                                        celularInput.setCustomValidity('');
                                    }
                                }
                                paisSelect.addEventListener('change', validarTelefono);
                                celularInput.addEventListener('input', validarTelefono);
                            });
                        </script>
                    </div>
                    <div class="col-lg-3">
                        <div class="form-group">
                            {!! Form::label('telefono', 'Teléfono:') !!}
                            {!! Form::text('telefono', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '45', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']) !!}
                            @error('telefono')
                                <small class="text-danger fas fa-exclamation-circle">
                                    {{$message}}
                                </small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="form-group">
                            {!! Form::label('domicilio', 'Domicilio:') !!}
                            {!! Form::text('domicilio', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '45']) !!}
                            @error('domicilio')
                                <small class="text-danger fas fa-exclamation-circle">
                                    {{$message}}
                                </small>
                            @enderror
                        </div>
                    </div> 
                </div>
            </div>
            <div class="col-lg-4">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group">
                            {!! Form::label('nuacua', 'NUA / CUA:') !!}
                            {!! Form::text('nuacua', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '45']) !!}
                            @error('nuacua')
                                <small class="text-danger fas fa-exclamation-circle">
                                    {{$message}}
                                </small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group">
                            {!! Form::label('estadolaboral', 'Estado laboral:') !!}
                            {!! Form::select('estadolaboral', $estlab, null, ['class' => 'form-control', 'placeholder' => '']) !!}
                            @error('estadolaboral')
                                <small class="text-danger fas fa-exclamation-circle">
                                    {{$message}}
                                </small>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-8 d-flex flex-column">
                        <div class="form-group mb-0">
                            {!! Form::label('empresa', 'Empresa:') !!}
                        </div>
                        <div class="form-group mb-0 d-flex">
                            <div class="input-group w-100">
                                {!! Form::select('empresa', $empresas, null, ['class' => 'form-control', 'placeholder' => '']) !!}
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-outline-primary" id="actualizarEmpresasBtn" title="ACTUALIZAR EMPRESAS">
                                        <i class="fas fa-sync-alt"></i>
                                    </button>
                                </div>
                            </div>
                            @error('empresa')
                                <small class="text-danger fas fa-exclamation-circle">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="form-group">
                            {!! Form::label('paisresidencia', 'Pais de Res.:') !!}
                            {!! Form::select('paisresidencia', $pais, null, ['class' => 'form-control', 'placeholder' => '']) !!}
                            @error('paisresidencia')
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
                            {!! Form::label('departamentoresidencia', 'Dep. de Residencia:') !!}
                            {!! Form::select('departamentoresidencia', $departamentos, null, ['class' => 'form-control', 'placeholder' => '']) !!}
                            @error('departamentoresidencia')
                                <small class="text-danger fas fa-exclamation-circle">
                                    {{$message}}
                                </small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group">
                            {!! Form::label('ciudadresidencia', 'Prov. de Residencia:') !!}
                            {!! Form::select('ciudadresidencia', $ciudades, null, ['class' => 'form-control', 'placeholder' => '']) !!}
                            @error('ciudadresidencia')
                                <small class="text-danger fas fa-exclamation-circle">
                                    {{$message}}
                                </small>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    {!! Form::label('aseguradora', 'Aseguradora:') !!}
                    {!! Form::select('aseguradora', $aseguradoras, null, ['class' => 'form-control', 'placeholder' => '']) !!}
                    @error('aseguradora')
                        <small class="text-danger fas fa-exclamation-circle">
                            {{$message}}
                        </small>
                    @enderror
                </div>
                {{-- NUEVO 111225 --}}
                @php
                    $referenciadores = collect()
                        ->concat(App\Models\Cliente::select('id', 'nombrecompleto')->get())
                        /* ->concat(App\Models\ClienteAuditoria::select('id', 'nombrecompleto')->get())
                        ->concat(App\Models\ClienteComun::select('id', 'nombrecompleto')->get()) */
                        ->sortBy('nombrecompleto');
                @endphp
                <div class="row">
                    <div class="col-lg-9">
                        <div class="form-group">
                            {!! Form::label('referenciador', 'Referenciador:') !!}
                            <input list="referenciadoresList" name="referenciador" id="referenciador" class="form-control" placeholder="">
                            <datalist id="referenciadoresList">
                                @foreach($referenciadores as $r)
                                    <option value="{{ $r->nombrecompleto }}" data-id="{{ $r->id }}"></option>
                                @endforeach
                            </datalist>
                            @error('referenciador')
                                <small class="text-danger fas fa-exclamation-circle">
                                    {{$message}}
                                </small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="form-group">
                            {!! Form::label('referenciadorid', 'Ref. ID:') !!}
                            {!! Form::text('referenciadorid', null, ['id' => 'referenciadorid', 'class' => 'form-control', 'placeholder' => '', 'readonly']) !!}
                        </div>
                    </div>
                </div>
                <script>
                    const input = document.getElementById('referenciador');
                    const inputId = document.getElementById('referenciadorid');
                    const options = document.querySelectorAll('#referenciadoresList option');

                    input.addEventListener('input', function() {
                        const val = this.value;
                        let found = false;

                        options.forEach(option => {
                            if(option.value === val) {
                                inputId.value = option.dataset.id;
                                found = true;
                            }
                        });
                        if(!found) inputId.value = '';
                    });
                </script>
                <div class="form-group">
                    {!! Form::label('afp', 'Gestora:') !!}
                    {!! Form::text('afp', $afp, ['class' => 'form-control', 'placeholder' => '', 'readonly' => 'readonly']) !!}
                    @error('afp')
                        <small class="text-danger fas fa-exclamation-circle">
                            {{$message}}
                        </small>
                    @enderror
                </div>
                <div class="row">
                    <div class="col-lg-4">
                        <div class="form-group">
                            {!! Form::label('numhijosmenores', 'Hijos < 25 años:') !!}
                            {!! Form::text('numhijosmenores', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '45']) !!}
                            @error('numhijosmenores')
                                <small class="text-danger fas fa-exclamation-circle">
                                    {{$message}}
                                </small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-lg-4" id="campo-hijos" style="display: none;">
                        <div class="form-group">
                            {!! Form::label('numhijostotal', 'Total hijos:') !!}
                            {!! Form::text('numhijostotal', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '45']) !!}
                            @error('numhijostotal')
                                <small class="text-danger fas fa-exclamation-circle">
                                    {{$message}}
                                </small>
                            @enderror
                        </div>
                    </div>
                    <script>
                        document.addEventListener("DOMContentLoaded", function () {
                            const generoSelect = document.getElementById("genero");
                            const campoHijos = document.getElementById("campo-hijos");
                            function toggleCampoHijos() {
                                if (generoSelect.value === "FEMENINO") {
                                    campoHijos.style.display = "block";
                                } else {
                                    campoHijos.style.display = "none";
                                }
                            }
                            generoSelect.addEventListener("change", toggleCampoHijos);
                            toggleCampoHijos();
                        });
                    </script>
                    <div class="col-lg-4">
                        <div class="form-group">
                            {!! Form::label('alertas', 'Alertas:') !!}
                            {!! Form::text('alertas', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '45']) !!}
                            @error('alertas')
                                <small class="text-danger fas fa-exclamation-circle">
                                    {{$message}}
                                </small>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
            <div id="cameraModal" class="modal" style="display: none;">
                <div class="modal-content">
                    <div class="modal-body">
                        <video id="camara" autoplay></video>
                        <div class="buttons-container">
                            <button type="button" id="capturar-btn" class="capturar-btn-style">CAPTURAR FOTO</button>
                            <button type="button" class="btn-cancelar" id="cerrar-modal">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>                    
            <div class="col-lg-3">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="form-group">
                            <label for="file">Foto de perfil:</label>
                            <input type="file" class="form-control-file" id="picture" name="picture">
                            <button type="button" id="abrir-modal" class="btningresar2">TOMAR FOTO</button>
                            <canvas id="canvas" style="display: none;"></canvas>
                            <img id="vista-previa" src="#" alt="Vista previa de la imagen" style="display: none;">
                            @error('picture')
                            <small class="text-danger fas fa-exclamation-circle">
                                {{$message}}
                            </small>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
            <script>
                document.getElementById('abrir-modal').addEventListener('click', function(event) {
                    event.preventDefault();
                    document.getElementById('cameraModal').style.display = 'block';
                    iniciarCamara();
                });
                document.getElementById('cerrar-modal').addEventListener('click', function() {
                    detenerCamara();
                    document.getElementById('cameraModal').style.display = 'none';
                });
                var cameraStream = null;
                function iniciarCamara() {
                    navigator.mediaDevices.getUserMedia({ video: true })
                    .then(function(stream) {
                        cameraStream = stream;
                        var video = document.getElementById('camara');
                        video.srcObject = stream;
                        video.play();
                    })
                    .catch(function(error) {
                        console.log('Error al acceder a la cámara: ', error);
                    });
                }
                function detenerCamara() {
                    if (cameraStream) {
                        cameraStream.getTracks().forEach(track => track.stop());
                    }
                }
                document.getElementById('capturar-btn').addEventListener('click', function() {
                    var canvas = document.createElement('canvas');
                    var video = document.getElementById('camara');
                    canvas.width = video.videoWidth;
                    canvas.height = video.videoHeight;
                    var ctx = canvas.getContext('2d');
                    ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
                    canvas.toBlob(function(blob) {
                        var pictureInput = document.getElementById('picture');
                        pictureInput.value = null;
                        var newFile = new File([blob], 'photo.png', { type: 'image/png' });
                        var dataTransfer = new DataTransfer();
                        dataTransfer.items.add(newFile);
                        pictureInput.files = dataTransfer.files;
                        var event = new Event('change');
                        pictureInput.dispatchEvent(event);
                        var vistaPrevia = document.getElementById('vista-previa');
                        vistaPrevia.src = URL.createObjectURL(blob);
                        vistaPrevia.style.display = 'block';
                        detenerCamara();
                        document.getElementById('cameraModal').style.display = 'none';
                    });
                });
            </script>
            <style>
                .buttons-container {
                    display: flex;
                    justify-content: space-between;
                }
                .modal {
                    position: fixed;
                    top: 50%;
                    left: 50%;
                    transform: translate(-50%, -50%);
                    border-radius: 20px;
                    padding: 20px;
                    z-index: 1000;
                    max-width: 700px;
                    width: 100%;
                    margin-top: 50px;
                }
                .modal-header {
                    text-align: center;
                }
                .modal-header h2 {
                    margin: 0;
                    color: #333;
                }
                .modal-body {
                    text-align: center;
                }
                #camara {
                    width: 100%;
                    height: auto;
                    border-radius: 15px;
                }
                .capturar-btn-style {
                    display: block;
                    width: 30%;
                    padding: 10px;
                    margin-top: 10px;
                    background-color: #faa625;
                    color: #fff;
                    border: none;
                    border-radius: 8px;
                    cursor: pointer;
                    margin: auto;
                }
                .modal-footer {
                    text-align: center;
                    margin-top: -20px;
                }
                .btn-cancelar {
                    background-color: #dc3545;
                    color: #fff;
                    border: none;
                    border-radius: 8px;
                    padding: 10px 10px;
                    cursor: pointer;
                    margin-top: 0px;
                }
                .text-danger {
                    color: #dc3545;
                }
            </style>
        </div>
        {!! Form::submit('CREAR CLIENTE', ['class' => 'btn btn-crear']) !!}
        {!! Form::close() !!}
    </div>
</div>
<div class="modal fade" id="crearEmpresaModal" tabindex="-1" aria-labelledby="crearEmpresaModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="crearEmpresaModalLabel" style="font-weight: 900;">REGISTRAR NUEVA EMPRESA</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>                                        
            <div class="modal-body">
                <form id="crearEmpresaForm">
                    @csrf
                    <div class="form-group" style="text-align: left">
                        {!! Form::label('nombreempresa', 'Nombre de la empresa:') !!}
                        {!! Form::text('nombreempresa', null, ['class' => 'form-control', 'placeholder' => '', 'required']) !!}
                    </div>
                    <div class="form-group" style="text-align: left">
                        {!! Form::label('contacto', 'Contacto:') !!}
                        {!! Form::text('contacto', null, ['class' => 'form-control', 'placeholder' => '', 'required']) !!}
                    </div>
                    <div class="form-group" style="text-align: left">
                        {!! Form::label('celular', 'Celular:') !!}
                        {!! Form::text('celular', null, ['class' => 'form-control', 'placeholder' => '', 'required']) !!}
                    </div>
                    <div class="form-group" style="text-align: left">
                        {!! Form::label('telefono', 'Teléfono:') !!}
                        {!! Form::text('telefono', null, ['class' => 'form-control', 'placeholder' => '', 'required']) !!}
                    </div>
                    <div class="form-group" style="text-align: left">
                        {!! Form::label('direccion', 'Dirección:') !!}
                        {!! Form::text('direccion', null, ['class' => 'form-control', 'placeholder' => '', 'required']) !!}
                    </div>
                    <button type="submit" class="btn btn-sm btn-crear">GUARDAR</button>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    document.getElementById('crearEmpresaForm').addEventListener('submit', function (e) { 
        e.preventDefault();
        let formData = new FormData(this);
        fetch('{{ route('admin.asociados.crearempresa') }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert("La empresa ha sido registrada exitosamente.");  
                let empresaSelect = document.getElementById('empresa-select');
                let option = new Option(data.empresa.nombreempresa, data.empresa.id);
                empresaSelect.add(option);
                $('#crearEmpresaModal').modal('hide');
                empresaSelect.value = data.empresa.id;
                document.getElementById('crearEmpresaForm').reset();
            } else {
                alert('Hubo un error al registrar la empresa.');
            }
        })
        .catch(error => console.error('Error:', error));
    });

    document.getElementById('actualizarEmpresasBtn').addEventListener('click', function () {
        let empresaSelect = document.getElementById('empresa');
        fetch('{{ route('admin.asociados.obtenerEmpresas') }}')
            .then(response => response.json())
            .then(data => {
                empresaSelect.innerHTML = '';
                let placeholderOption = new Option('Seleccione una empresa', '');
                empresaSelect.add(placeholderOption);
                data.empresas.forEach(empresa => {
                    let option = new Option(empresa.nombreempresa, empresa.id);
                    empresaSelect.add(option);
                });
                if (data.empresas.length > 0) {
                    empresaSelect.value = data.empresas[0].id;
                }
            })
        .catch(error => console.error('Error al actualizar las empresas:', error));
    });
</script>          
<style>
    .modal {
    z-index: 1050 !important;
    }
    .modal-backdrop {
    z-index: 1040 !important;
    }
</style>      

@stop
@section('js')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script type="text/javascript" src="https://jeremyfagis.github.io/dropify/dist/js/dropify.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://jeremyfagis.github.io/dropify/dist/css/dropify.min.css"> 
<script>
    function calcularEdad(fecha_nacimiento) {
        var fecha_actual = new Date();
        var fecha_nacimiento = new Date(fecha_nacimiento);
        var edad = fecha_actual.getFullYear() - fecha_nacimiento.getFullYear();
        var mes = fecha_actual.getMonth() - fecha_nacimiento.getMonth();
        
        if (mes < 0 || (mes === 0 && fecha_actual.getDate() < fecha_nacimiento.getDate())) {
            edad--;
        }
        return edad;
    }
    document.getElementById('fecha_nacimiento').addEventListener('change', function() {
        var fecha_nacimiento = this.value;
        var edad = calcularEdad(fecha_nacimiento);
        document.getElementById('edad').value = edad;
    });
    var fecha_nacimiento = document.getElementById('fecha_nacimiento').value;
    var edad = calcularEdad(fecha_nacimiento);
    document.getElementById('edad').value = edad;
</script>
<script>
$('.dropify').dropify();
</script>
<script>
    function mostrarVistaPrevia(input) {
        if (input.files && input.files[0]) {
            var lector = new FileReader();
            lector.onload = function(e) {
                $('#vista-previa').attr('src', e.target.result);
                $('#vista-previa').show();
            }
            lector.readAsDataURL(input.files[0]);
        }
    }
    $(document).ready(function() {
        $("#picture").change(function() {
            mostrarVistaPrevia(this);
        });
    });
</script>

@endsection
@section('css')
<link rel="styleheet" href="/css/admin_custom.css">
<style>
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
    #vista-previa {
        display: block;
        max-width: 100%;
        max-height: 100%;
        margin-top: 10px;
        border: 1px solid #ccc;
        padding: 5px;
        box-shadow: 0px 0px 5px 0px rgba(0, 0, 0, 0.2);
        }
        h2 {font-size:  80%;
        text-align : right;
    }
    .capturar-btn-style {
        background-color: #faa625;
        color: #fff;
        border: none;
        padding: 10px 20px;
        border-radius: 7px;
        text-decoration: none;
        cursor: pointer;
    }
    .capturar-btn-style:hover {
        background-color: #94c93b;
    }
    .btningresar {
        background-color: #faa625;
        color: #fff;
        border: none;
        padding: 10px 20px;
        border-radius: 7px;
        text-decoration: none;
        cursor: pointer;
    }
    .btningresar:hover {
        background-color: #94c93b;
    }
    .btningresar2 {
        background-color: #94c93b;
        color: #fff;
        border: none;
        padding: 5px 10px;
        border-radius: 7px;
        text-decoration: none;
        cursor: pointer;
        position: relative;
        margin-top: 10px;
    }
    .btningresar2:hover {
        background-color: #faa625;
    }
    .txt1 {
        font-family: "Segoe UI";
        font-size: 15px;
        line-height: 1.6;
        padding-right: 15px;
        color: #faa625;
    }
    .txt2 {
        font-family: "Segoe UI";
        font-size: 30px;
        line-height: 1.6;
        text-align: center;
        font-weight: 700;
        color: #94c93b;
    }
    .txt3 {
        font-family: "Segoe UI";
        font-size: 14px;
        line-height: 1.6;
        text-align: center;
        font-weight: 400;
    }
    .txt4 {
        font-family: "Segoe UI";
        font-size: 15px;
        line-height: 1.6;
        font-weight: 600;
        color: #94c93b;
    }
    .txt1:hover{
        color: #94c93b;
    }
    .btn-regresar {
        background-color: #ffffff;
        color: #2926e2;
        border-color: #2926e2;
        border-radius: 5px;
        padding: 5px 10px;
    }
    .btn-regresar:hover {
        background-color: #2926e2;
        color: #ffffff;
    }
    .btn-crearempresa {
        background-color: #ffffff;
        color: #faa625;
        border-color: #faa625;
        border-radius: 5px;
        padding: 5px 10px;
    }
    .btn-crearempresa:hover {
        background-color: #faa625;
        color: #ffffff;
    }
</style>
@stop