@extends('adminlte::page')

@section('content_header')
<a class="btn btn-sm float-right btn-regresar" href="{{route('admin.asociados.listadoclienteauditoria', $asociado)}}">REGRESAR</a>
<h1>NUEVO CLIENTE AUDITORIA MOMENTÁNEO</h1>
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
                {!! Form::model($asociado, ['route' => ['admin.asociados.guardarclienteauditoriamomentaneo', $asociado], 'method' => 'POST']) !!}
                    {!! Form::hidden('usuarioid', auth()->user()->id) !!}
                    {!! Form::hidden('usuarioregistro', auth()->user()->name) !!}
                    {!! Form::hidden('tipocliente', 'MOMENTÁNEO') !!}
                    <div class="row">
                        <div class="col-lg-2">
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
                        <div class="col-lg-2">
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
                        <div class="col-lg-1">
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
                        <div class="col-lg-2">
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
                            </script>
                        </div>
                        <div class="col-lg-2">
                            <div class="form-group">
                                {!! Form::label('estadocivil', 'Estado civil:') !!}
                                {!! Form::select('estadocivil', $estciv, null, [
                                    'class' => 'form-control',
                                    'placeholder' => '',
                                    'id' => 'estadocivil'
                                ]) !!}
                                @error('estadocivil')
                                    <small class="text-danger fas fa-exclamation-circle">
                                        {{$message}}
                                    </small>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-2 conyuge-campo">
                            <div class="form-group">
                                {!! Form::label('nombreespcon', 'Nombre Esp/Cony.:') !!}
                                {!! Form::text('nombreespcon', null, ['class' => 'form-control', 'placeholder' => '']) !!}
                                @error('nombreespcon')
                                    <small class="text-danger fas fa-exclamation-circle">
                                        {{$message}}
                                    </small>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-1 conyuge-campo">
                            <div class="form-group">
                                {!! Form::label('ciespcon', 'CI Esp/Cony:') !!}
                                {!! Form::text('ciespcon', null, ['class' => 'form-control', 'placeholder' => '']) !!}
                                @error('ciespcon')
                                    <small class="text-danger fas fa-exclamation-circle">
                                        {{$message}}
                                    </small>
                                @enderror
                            </div>
                        </div>
                        <script>
                            function toggleConyugeCampos() {
                                let estado = document.getElementById('estadocivil').value;
                                let conyugeCampos = document.querySelectorAll('.conyuge-campo');
                                let direccionCol = document.getElementById('direccion-col');

                                if (estado === 'CASAD@' || estado === 'UNION LIBRE') {
                                    conyugeCampos.forEach(el => el.classList.remove('d-none'));
                                    direccionCol.classList.remove('col-lg-7');
                                    direccionCol.classList.add('col-lg-3');
                                } else {
                                    // Ocultar campos
                                    conyugeCampos.forEach(el => {
                                        el.classList.add('d-none');
                                        // Vaciar los inputs dentro del contenedor
                                        let input = el.querySelector('input');
                                        if (input) input.value = '';
                                    });

                                    // Restaurar tamaño de dirección
                                    direccionCol.classList.remove('col-lg-3');
                                    direccionCol.classList.add('col-lg-7');
                                }
                            }

                            document.getElementById('estadocivil').addEventListener('change', toggleConyugeCampos);

                            // Ejecutar al cargar la página para estado preseleccionado
                            document.addEventListener('DOMContentLoaded', toggleConyugeCampos);
                        </script>
                    </div>
                    <div class="row">
                        <div class="col-lg-2"> 
                            <div class="form-group">
                                {!! Form::label('banco1', 'Entidad financiera 1:') !!}
                                {!! Form::select('banco1', $bancos, null, ['class' => 'form-control', 'placeholder' => 'Seleccione una opción']) !!}
                                @error('banco1')
                                    <small class="text-danger fas fa-exclamation-circle">
                                        {{$message}}
                                    </small>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-2">
                            <div class="form-group">
                                {!! Form::label('nrocredito1', 'Nro. de Crédito Banco 1:') !!}
                                {!! Form::text('nrocredito1', null, ['class' => 'form-control mb-2', 'placeholder' => 'Número de Crédito 1']) !!}
                                <div id="additional-credits">
                                    {!! Form::text('nrocredito2', null, ['class' => 'form-control mb-2', 'placeholder' => 'Número de Crédito 2', 'style' => 'display: none;']) !!}
                                    {!! Form::text('nrocredito3', null, ['class' => 'form-control mb-2', 'placeholder' => 'Número de Crédito 3', 'style' => 'display: none;']) !!}
                                    {!! Form::text('nrocredito4', null, ['class' => 'form-control mb-2', 'placeholder' => 'Número de Crédito 4', 'style' => 'display: none;']) !!}
                                    {!! Form::text('nrocredito5', null, ['class' => 'form-control mb-2', 'placeholder' => 'Número de Crédito 5', 'style' => 'display: none;']) !!}
                                    {!! Form::text('nrocredito6', null, ['class' => 'form-control mb-2', 'placeholder' => 'Número de Crédito 6', 'style' => 'display: none;']) !!}
                                </div>
                                <button type="button" id="show-more" class="btn btn-sm btn-link p-0">Mostrar más</button>
                            </div>
                        </div>
                        <script>
                            document.getElementById('show-more').addEventListener('click', function() {
                                const additionalCredits = document.getElementById('additional-credits');
                                const fields = additionalCredits.querySelectorAll('input');
                                let hasHiddenFields = false;                        
                                for (let field of fields) {
                                    if (field.style.display === 'none') {
                                        field.style.display = 'block';
                                        hasHiddenFields = true;
                                        break;
                                    }
                                }
                                if (!hasHiddenFields) {
                                    this.style.display = 'none';
                                }
                            });

                            const checkFields = () => {
                                const additionalCredits = document.getElementById('additional-credits');
                                const fields = additionalCredits.querySelectorAll('input');
                                const showMoreButton = document.getElementById('show-more');
                                const allVisible = Array.from(fields).every(field => field.style.display !== 'none');
                                if (allVisible) {
                                    showMoreButton.style.display = 'none';
                                }
                            };
                            checkFields();
                        </script>
                        <div class="col-lg-2"> 
                            <div class="form-group">
                                {!! Form::label('banco2', 'Entidad financiera 2:') !!}
                                {!! Form::select('banco2', $bancos, null, ['class' => 'form-control', 'placeholder' => 'Seleccione una opción']) !!}
                                @error('banco2')
                                    <small class="text-danger fas fa-exclamation-circle">
                                        {{$message}}
                                    </small>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-2">
                            <div class="form-group">
                                {!! Form::label('numerocuenta7', 'Nro. de Crédito Banco 2:') !!}
                                {!! Form::text('nrocredito7', null, ['class' => 'form-control mb-2', 'placeholder' => 'Número de Crédito 7']) !!}
                                <div id="additional-credits-2">
                                    {!! Form::text('nrocredito8', null, ['class' => 'form-control mb-2', 'placeholder' => 'Número de Crédito 8', 'style' => 'display: none;']) !!}
                                    {!! Form::text('nrocredito9', null, ['class' => 'form-control mb-2', 'placeholder' => 'Número de Crédito 9', 'style' => 'display: none;']) !!}
                                    {!! Form::text('nrocredito10', null, ['class' => 'form-control mb-2', 'placeholder' => 'Número de Crédito 10', 'style' => 'display: none;']) !!}
                                    {!! Form::text('nrocredito11', null, ['class' => 'form-control mb-2', 'placeholder' => 'Número de Crédito 11', 'style' => 'display: none;']) !!}
                                    {!! Form::text('nrocredito12', null, ['class' => 'form-control mb-2', 'placeholder' => 'Número de Crédito 12', 'style' => 'display: none;']) !!}
                                </div>
                                <button type="button" id="show-more-2" class="btn btn-sm btn-link p-0">Mostrar más</button>
                            </div>
                        </div>
                        <div class="col-lg-2">
                            <div class="form-group">
                                {!! Form::label('banco3', 'Entidad financiera 3:') !!}
                                {!! Form::select('banco3', $bancos, null, ['class' => 'form-control', 'placeholder' => 'Seleccione una opción']) !!}
                                @error('banco3')
                                    <small class="text-danger fas fa-exclamation-circle">
                                        {{$message}}
                                    </small>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-2">
                            <div class="form-group">
                                {!! Form::label('numerocuenta13', 'Nro. de Crédito Banco 3:') !!}
                                {!! Form::text('nrocredito13', null, ['class' => 'form-control mb-2', 'placeholder' => 'Número de Crédito 13']) !!}
                                <div id="additional-credits-3">
                                    {!! Form::text('nrocredito14', null, ['class' => 'form-control mb-2', 'placeholder' => 'Número de Crédito 14', 'style' => 'display: none;']) !!}
                                    {!! Form::text('nrocredito15', null, ['class' => 'form-control mb-2', 'placeholder' => 'Número de Crédito 15', 'style' => 'display: none;']) !!}
                                    {!! Form::text('nrocredito16', null, ['class' => 'form-control mb-2', 'placeholder' => 'Número de Crédito 16', 'style' => 'display: none;']) !!}
                                    {!! Form::text('nrocredito17', null, ['class' => 'form-control mb-2', 'placeholder' => 'Número de Crédito 17', 'style' => 'display: none;']) !!}
                                    {!! Form::text('nrocredito18', null, ['class' => 'form-control mb-2', 'placeholder' => 'Número de Crédito 18', 'style' => 'display: none;']) !!}
                                </div>
                                <button type="button" id="show-more-3" class="btn btn-sm btn-link p-0">Mostrar más</button>
                            </div>
                        </div>
                        <script>
                            function setupShowMore(buttonId, containerId) {
                                const button = document.getElementById(buttonId);
                                const container = document.getElementById(containerId);
                                const fields = container.querySelectorAll('input');
                                button.addEventListener('click', function() {
                                    let hasHiddenFields = false;
                                    for (let field of fields) {
                                        if (field.style.display === 'none') {
                                            field.style.display = 'block';
                                            hasHiddenFields = true;
                                            break;
                                        }
                                    }
                                    if (!hasHiddenFields) {
                                        button.style.display = 'none';
                                    }
                                });
                                const checkFields = () => {
                                    const allVisible = Array.from(fields).every(field => field.style.display !== 'none');
                                    if (allVisible) {
                                        button.style.display = 'none';
                                    }
                                };
                                checkFields();
                            }
                            setupShowMore('show-more-2', 'additional-credits-2');
                            setupShowMore('show-more-3', 'additional-credits-3');
                        </script>
                    </div>
                    {!! Form::submit('CREAR CLIENTE', ['class' => 'btn btn-crear']) !!}
                {!! Form::close() !!}
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
        padding: 5px 10px;
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