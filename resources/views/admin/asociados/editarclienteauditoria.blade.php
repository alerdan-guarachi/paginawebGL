@extends('adminlte::page')

@section('content_header')
<a class="btn btn-sm float-right btn-regresar" href="{{ route('admin.asociados.verclienteauditoria', $clienteauditoria) }}">REGRESAR</a>
<h5>EDITAR DATOS DE:</h5>
<h3>{{$clienteauditoria->nombrecompleto}}</h3>
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
            <div class="row ">
                <div class="col-lg-12">
                    {!! Form::model($clienteauditoria, ['route' => ['admin.asociados.actualizarclienteauditoria', $clienteauditoria], 'method' => 'PUT']) !!}
                    {!! Form::hidden('usersid', auth()->user()->id) !!}
                    {!! Form::hidden('usuarioregistro', auth()->user()->name) !!}
                    <div class="row">
                        <div class="col-lg-3">
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
                                {!! Form::text('ci', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '45']) !!}
                                @error('ci')
                                    <small class="text-danger fas fa-exclamation-circle">
                                        {{$message}}
                                    </small>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row"> 
                        <div class="col-lg-2">
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
                        <div class="col-lg-3">
                            <div class="form-group">
                                {!! Form::label('lugarnacimiento', 'Lugar nacimiento:') !!}
                                {!! Form::text('lugarnacimiento', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '45']) !!}
                                @error('lugarnacimiento')
                                    <small class="text-danger fas fa-exclamation-circle">
                                        {{$message}}
                                    </small>
                                @enderror
                            </div>
                        </div>
                        {{-- <div class="col-lg-3" id="custom_place_group" style="display: {{ $isCustomPlace ? 'block' : 'none' }};"> 
                            <div class="form-group">
                                {!! Form::label('custom_lugarnacimiento', 'Otro lugar de nac.:') !!}
                                {!! Form::text('custom_lugarnacimiento', $isCustomPlace ? $lugarnacimiento : '', ['class' => 'form-control', 'maxlength' => '45']) !!}
                                @error('custom_lugarnacimiento')
                                    <small class="text-danger fas fa-exclamation-circle">
                                        {{$message}}
                                    </small>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-lg-3" id="select_place_group" style="display: {{ $isCustomPlace ? 'none' : 'block' }};">
                            <div class="form-group">
                                {!! Form::label('lugarnacimiento', 'Lugar nacimiento:') !!}
                                <input style="margin-left: 80px;" type="checkbox" id="enable_custom_place" {{ $isCustomPlace ? 'checked' : '' }}> Otro
                                {!! Form::select('lugarnacimiento', $departamentos, !$isCustomPlace ? $lugarnacimiento : null, ['class' => 'form-control', 'placeholder' => 'Seleccione un lugar', 'maxlength' => '45']) !!}
                                @error('lugarnacimiento')
                                    <small class="text-danger fas fa-exclamation-circle">
                                        {{$message}}
                                    </small>
                                @enderror
                            </div>
                        </div>
                        
                        <script>
                            document.getElementById('enable_custom_place').addEventListener('change', function() {
                                if (this.checked) {
                                    // Mostrar campo de texto y ocultar el select
                                    document.getElementById('custom_place_group').style.display = 'block';
                                    document.getElementById('select_place_group').style.display = 'none';
                                } else {
                                    // Mostrar el select y ocultar el campo de texto
                                    document.getElementById('custom_place_group').style.display = 'none';
                                    document.getElementById('select_place_group').style.display = 'block';
                                }
                            });
                        </script> --}}
                        
                        <div class="col-lg-2">
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
                        <div class="col-lg-3">
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
                                    var valorCelular = campoCelular.value.trim(); // Eliminar espacios en blanco al inicio y final
                        
                                    // Validar si el valor comienza con un código de país
                                    var codigoPais = document.getElementById('pais').value;
                                    if (codigoPais && !valorCelular.startsWith(codigoPais)) {
                                        campoCelular.value = codigoPais + valorCelular; // Agregar el código de país al inicio
                                    } else {
                                        campoCelular.value = valorCelular; // Mantener el número tal como está
                                    }
                                });
                            </script>
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
                        <div class="col-lg-2">
                            <div class="form-group">
                                {!! Form::label('banco1', 'Entidad financiera 1:') !!}
                                {!! Form::select('banco1', $bancos, $bancoActual, ['class' => 'form-control', 'placeholder' => '']) !!}
                                @error('banco1')
                                    <small class="text-danger fas fa-exclamation-circle">
                                        {{$message}}
                                    </small>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-2">
                            <div class="form-group">
                                {!! Form::label('numerocuenta1', 'Nro. de Cuenta 1:') !!}
                                {!! Form::text('numerocuenta1', null, ['class' => 'form-control', 'placeholder' => '', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']) !!}
                                @error('numerocuenta1')
                                    <small class="text-danger fas fa-exclamation-circle">
                                        {{$message}}
                                    </small>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-2">
                            <div class="form-group">
                                {!! Form::label('banco2', 'Entidad financiera 2:') !!}
                                {!! Form::select('banco2', $bancos, $bancoActual, ['class' => 'form-control', 'placeholder' => '']) !!}
                                @error('banco2')
                                    <small class="text-danger fas fa-exclamation-circle">
                                        {{$message}}
                                    </small>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-2">
                            <div class="form-group">
                                {!! Form::label('numerocuenta2', 'Nro. de Cuenta 2:') !!}
                                {!! Form::text('numerocuenta2', null, ['class' => 'form-control', 'placeholder' => '', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']) !!}
                                @error('numerocuenta2')
                                    <small class="text-danger fas fa-exclamation-circle">
                                        {{$message}}
                                    </small>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-2">
                            <div class="form-group">
                                {!! Form::label('banco3', 'Entidad financiera 3:') !!}
                                {!! Form::select('banco3', $bancos, $bancoActual, ['class' => 'form-control', 'placeholder' => '']) !!}
                                @error('banco3')
                                    <small class="text-danger fas fa-exclamation-circle">
                                        {{$message}}
                                    </small>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-2">
                            <div class="form-group">
                                {!! Form::label('numerocuenta3', 'Nro. de Cuenta 3:') !!}
                                {!! Form::text('numerocuenta3', null, ['class' => 'form-control', 'placeholder' => '', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']) !!}
                                @error('numerocuenta3')
                                    <small class="text-danger fas fa-exclamation-circle">
                                        {{$message}}
                                    </small>
                                @enderror
                            </div>
                        </div>
                    </div>
                {!! Form::submit('ACTUALIZAR CLIENTE', ['class' => 'btn btn-crear']) !!}
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
        padding: 10px 20px;
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
        padding: 10px 20px;
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
        padding: 10px 10px;
    }
    .btn-regresar:hover {
        background-color: #2926e2;
        color: #ffffff;
    }
</style>
@stop