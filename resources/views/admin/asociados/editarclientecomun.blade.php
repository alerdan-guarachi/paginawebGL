@extends('adminlte::page')
<link href="assets/img/logo.png" rel="icon">
@section('content_header')
<a class="btn btn-sm float-right btn-regresar" href="{{ route('admin.asociados.verclientecomun', $clientecomun) }}">REGRESAR</a>
<h5>EDITAR DATOS DE:</h5>
<h3>{{$clientecomun->nombrecompleto}}</h3>
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
    <div class="card-body">
        <div class="row ">
            <div class="col-lg-12">
                {!! Form::model($clientecomun, ['route' => ['admin.asociados.actualizarclientecomun', $clientecomun], 'method' => 'PUT', 'files' => true]) !!}
                {!! Form::hidden('users_id', auth()->user()->id) !!}
                <div class="row">
                    <div class="col-lg-4">
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
                    <div class="col-lg-4">
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
                    <div class="col-lg-4">
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
                </div>
                <div class="row">     
                    <div class="col-lg-4">
                        <div class="form-group">
                            {!! Form::label('fechanacimiento', 'Fecha de nac.:') !!}
                            {!! Form::date('fechanacimiento', $clientecomun->fechanacimiento ?? \Carbon\Carbon::now(), ['class' => 'form-control', 'id' => 'fecha_nacimiento']) !!}
                            @error('fechanacimiento')
                                <small class="text-danger fas fa-exclamation-circle">
                                    {{ $message }}
                                </small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-lg-4">
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
                    <div class="col-lg-4">
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
                                    // Si hay código de país seleccionado, agregarlo al inicio sin espacio adicional
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
                        </script>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6">
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
                    <div class="col-lg-6">
                        <div class="form-group">
                            {!! Form::label('ciudad', 'Ciudad residencia:') !!}
                            {!! Form::select('ciudad', $departamentos, null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '45']) !!}
                            @error('ciudad')
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
                            {!! Form::label('estadocivil', 'Estado civil:') !!}
                            {!! Form::select('estadocivil', $estciv, null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '45']) !!}
                            @error('estadocivil')
                                <small class="text-danger fas fa-exclamation-circle">
                                    {{$message}}
                                </small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-lg-6">
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

            {!! Form::submit('ACTUALIZAR', ['class' => 'btn btn-crear']) !!}
            {!! Form::close() !!}
        </div>
    </div>
</div>
@stop

@section('js')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script type="text/javascript" src="https://jeremyfagis.github.io/dropify/dist/js/dropify.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://jeremyfagis.github.io/dropify/dist/css/dropify.min.css"> 
<script>
    // Función para calcular la edad
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

    // Obtener la fecha de nacimiento cuando cambie el valor del campo de fecha
    document.getElementById('fecha_nacimiento').addEventListener('change', function() {
        var fecha_nacimiento = this.value;
        var edad = calcularEdad(fecha_nacimiento);
        document.getElementById('edad').value = edad;
    });

    // Calcular la edad inicial al cargar la página
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
    label {
        margin-bottom: 0%;
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
        padding: 5px 10px;
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
</style>
@stop