@extends('adminlte::page')

@section('content_header')
<a class="btn btn-sm float-right btn-regresar" href="{{ route('admin.asociados.verclientecomun', $clientecomun) }}">REGRESAR</a>
@can('admin.asociados.reprogramacionclientecomun')
<a class="btn btn-sm float-right btn-crear" href="{{route('admin.asociados.reprogramacionclientecomun', $clientecomun)}}">REPROGRAMAR</a>
@endcan
<a class="btn btn-sm float-right btn-bateria" data-toggle="modal" data-target="#ventanaModal">PROGRAMACIONES DEL CLIENTE</a>
<a class="btn btn-sm float-right btn-crear" href="{{route('admin.asociados.estadoprogramacionclientecomun', $clientecomun)}}">PROGRAMACIONES</a>
<h5>PROGRAMAR ACCIONES DE:</h5>
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
        }, 5000); 
    </script>
@endif
@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif
@if (session('eliminar') == 'ok')
<script>
    Swal.fire(
        '¡Proceso exitoso!',
        'Ya puede reprogramar al cliente',
        'success'
    )
</script>
@endif
<div class="card">
    <div class="card-body">
        {!! Form::model($clientecomun, ['route' => ['admin.asociados.guardarprogramacionclientecomun', $clientecomun], 'method' => 'POST']) !!}
            <div class="row">
                @if($accionesCliente)
                    {!! Form::hidden('usuarioid', auth()->user()->id) !!}
                    {!! Form::hidden('usuarioregistro', auth()->user()->name) !!}
                    {!! Form::hidden('clientecomunid', $id) !!}
                    <div class="col-lg-8">
                        {!! Form::label('', 'ACCIONES PARA PROGRAMAR:') !!}
                        <div class="form-group" hidden>
                            {!! Form::label('nombrecompleto', 'NOMBRE DEL CLIENTE:') !!}
                            {!! Form::text('nombrecompleto', null, ['class' => 'form-control', 'placeholder' => '', 'readonly' => true]) !!}
                            @error('nombrecompleto')
                                <small class="text-danger fas fa-exclamation-circle">
                                    {{$message}}
                                </small>
                            @enderror
                        </div>
                        <div class="form-group">
                            {!! Form::label('', 'Fecha de Bateria:') !!}
                            <select class="form-control" id="fecha_bateria">
                                <option value="" disabled selected></option>
                                @foreach($accionesPorFecha as $fecha => $acciones)
                                    <option value="{{ $fecha }}">{{ $fecha }}</option>
                                @endforeach
                            </select>
                            @error('fechabateria')
                                <small class="text-danger fas fa-exclamation-circle">
                                    {{$message}}
                                </small>
                            @enderror
                        </div>
                        
                        <input type="hidden" id="fechabateria" name="fechabateria">
                        
                        <script>
                            document.getElementById('fecha_bateria').addEventListener('change', function() {
                                var selectedDate = this.value;
                                document.getElementById('fechabateria').value = selectedDate;
                            });
                        </script>

                        <div class="modal fade" id="ventanaModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">PROGRAMACIONES DEL CLIENTE:</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div> 
                                    <div class="modal-body">
                                        <strong>Fecha de Bateria:</strong>
                                        <select id="select-fechas" class="form-control">
                                            <option value="" disabled selected></option>
                                            @foreach($accionesPorFecha as $fecha => $acciones)
                                                <option value="{{ $fecha }}">{{ $fecha }}</option>
                                            @endforeach
                                        </select>
                                        <div id="acciones-container" class="mt-3">
                                            <strong>Acciones programadas:</strong>
                                            @foreach($accionesPorFecha as $fecha => $acciones)
                                                <div id="acciones-{{ $fecha }}" class="acciones" style="display: none;">
                                                    @foreach($acciones as $accion)
                                                        @if(in_array($accion, $accionesRegistradas))
                                                            <div style="color: green;">&#10003; {{ $accion }}</div>
                                                        @else
                                                            <div style="color: red;">&#10007; {{ $accion }}</div>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-cerrar" data-dismiss="modal">Cerrar</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <script>
                            document.addEventListener('DOMContentLoaded', function () {
                                const selectFechas = document.getElementById('select-fechas');
                                const accionesContainer = document.getElementById('acciones-container');
                        
                                selectFechas.addEventListener('change', function(event) {
                                    const selectedFecha = this.value;
                                    const allActionDivs = accionesContainer.querySelectorAll('.acciones');
                                    allActionDivs.forEach(function(actionDiv) {
                                        if (actionDiv.id === 'acciones-' + selectedFecha) {
                                            actionDiv.style.display = "block";
                                        } else {
                                            actionDiv.style.display = "none";
                                        }
                                    });
                                });
                            });
                        </script>

                        {!! Form::label('', 'ACCIONES REQUERIDAS Y PROVEEDORES DISPONIBLES:') !!}
                        @php
                            $proveedoresAsociadosChunks = $proveedoresAsociados->toArray();
                            $proveedoresAsociadosChunks = array_chunk($proveedoresAsociadosChunks, 3, true);
                        @endphp
                        @foreach($accionesPorFecha as $fecha => $acciones)
                            <div class="row">
                                <div class="acciones-{{ $fecha }}" style="display:none;">
                                    <div style="display:flex; flex-wrap: wrap;">
                                        @foreach($acciones as $accion)
                                            @php
                                                if(isset($proveedoresAsociados[$accion])) {
                                                    $proveedores = $proveedoresAsociados[$accion];
                                                } else {
                                                    $proveedores = [];
                                                }
                                                $registrada = in_array($accion, $accionesRegistradas);
                                                $fechaBateriaAccion = isset($fechasBateriaPorAccion[$accion]) ? $fechasBateriaPorAccion[$accion] : null;
                                                $accionShort = strlen($accion) > 18 ? substr($accion, 0, 18) . '...' : $accion;
                                                if(empty($proveedores)){
                                                    $options = [];
                                                } else {
                                                    $options = [];
                                                    foreach($proveedores as $proveedor) {
                                                        $horario = $proveedor['horarioinicial'] . ' - ' . $proveedor['horariofinal'];
                                                        $optionLabel = $proveedor['proveedor'] . ' (' . $horario . ') (' . $proveedor['tiempoatencion'] . ') (' . $proveedor['accion'] . ') (' . $proveedor['area'] . ') (' . $proveedor['precio'] . ')';
                                                        $options[$proveedor['id']] = $optionLabel;
                                                    }
                                                }
                                            @endphp
                                            @if(!$registrada)
                                                <div class="col-lg-4" style="margin-bottom: 20px;">
                                                    <div class="form-group">
                                                        <strong title="{{ $accion }}">{{ $accionShort }}</strong><br>
                                                        <div>
                                                            {!! Form::select('proveedor_' . $accion, $options, null, ['class' => 'form-control', 'placeholder' => '']) !!}
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endforeach
                        <button type="button" id="habilitarSelectores" style="display:none;" class="custom-button">Seleccionar otra acción</button>
                    </div>
                    
                    <div class="col-lg-4" hidden>
                        <div class="form-group">
                            {!! Form::label('proveedornombre', 'Proveedor seleccionado:') !!}
                            {!! Form::text('proveedornombre', null, ['id' => 'proveedornombre', 'class' => 'form-control', 'placeholder' => '']) !!}
                            @error('proveedornombre')
                                <small class="text-danger fas fa-exclamation-circle">
                                    {{$message}}
                                </small>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-lg-4" hidden>
                        <div class="form-group">
                            {!! Form::label('horarioinicial', 'Horario inicial:') !!}
                            {!! Form::text('horarioinicial', null, ['id' => 'horarioinicial', 'class' => 'form-control', 'placeholder' => '']) !!}
                            @error('horarioinicial')
                                <small class="text-danger fas fa-exclamation-circle">
                                    {{$message}}
                                </small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-lg-4" hidden>
                        <div class="form-group">
                            {!! Form::label('horariofinal', 'Horario final:') !!}
                            {!! Form::text('horariofinal', null, ['id' => 'horariofinal', 'class' => 'form-control', 'placeholder' => '']) !!}
                            @error('horariofinal')
                                <small class="text-danger fas fa-exclamation-circle">
                                    {{$message}}
                                </small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-lg-4" hidden>
                        <div class="form-group">
                            {!! Form::label('tiempoatencion', 'Tiempo atención:') !!}
                            {!! Form::text('tiempoatencion', null, ['id' => 'tiempoatencion', 'class' => 'form-control', 'placeholder' => '']) !!}
                            @error('tiempoatencion')
                                <small class="text-danger fas fa-exclamation-circle">
                                    {{$message}}
                                </small>
                            @enderror
                        </div>
                        <div class="form-group" hidden>
                            {!! Form::label('accionnombre', 'Accion selecionada:') !!}
                            {!! Form::text('accionnombre', null, ['class' => 'form-control', 'placeholder' => '', 'id' => 'accionnombre']) !!}
                            @error('accionnombre')
                                <small class="text-danger fas fa-exclamation-circle">
                                    {{$message}}
                                </small>
                            @enderror
                        </div>
                        <div class="form-group" hidden>
                            {!! Form::label('areanombre', 'Area selecionada:') !!}
                            {!! Form::text('areanombre', null, ['class' => 'form-control', 'placeholder' => '', 'id' => 'areanombre']) !!}
                            @error('areanombre')
                                <small class="text-danger fas fa-exclamation-circle">
                                    {{$message}}
                                </small>
                            @enderror
                        </div>
                    </div>
                    <br>
                    <div class="col-lg-4">
                    {!! Form::label('', 'PROGRAMAR ACCION:') !!}
                            <div class="form-group">
                                {!! Form::label('precio', 'Precio:') !!}
                                {!! Form::text('precio', null, ['class' => 'form-control', 'placeholder' => '', 'id' => 'precio', 'readonly' => true]) !!}
                                @error('precio')
                                    <small class="text-danger fas fa-exclamation-circle">
                                        {{$message}}
                                    </small>
                                @enderror
                            </div>
                            <div class="form-group">
                                {!! Form::label('fechaasignada', 'Fecha a programar:') !!}
                                {!! Form::date('fechaasignada', null, ['class' => 'form-control', 'min' => \Carbon\Carbon::now()->format('Y-m-d')]) !!}
                                @error('fechaasignada')
                                    <small class="text-danger fas fa-exclamation-circle">
                                        {{ $message }}
                                    </small>
                                @enderror
                            </div>
                            <div class="form-group">
                                {!! Form::label('horadesde', 'Desde:') !!}
                                {!! Form::time('horadesde', null, ['class' => 'form-control', 'placeholder' => '', 'id' => 'horadesde']) !!}
                                @error('horadesde')
                                    <small class="text-danger fas fa-exclamation-circle">
                                        {{$message}}
                                    </small>
                                @enderror
                            </div>
                            <div class="form-group">
                                {!! Form::label('horahasta', 'Hasta:') !!}
                                {!! Form::time('horahasta', null, ['class' => 'form-control', 'placeholder' => '', 'id' => 'horahasta']) !!}
                                @error('horahasta')
                                    <small class="text-danger fas fa-exclamation-circle">
                                        {{$message}}
                                    </small>
                                @enderror
                            </div>
                            <div class="form-group" hidden>
                                <label for="horariodisponible">Horarios disponibles:</label>
                                <select name="horariodisponible{{ isset($accion) ? $accion : '' }}" class="form-control horariodisponible no-bloquear" placeholder="" id="horariosdisponibles" {{ isset($accion) ? 'data-accion=' . $accion : '' }}>
                                </select>
                                @error('horaasignada')
                                    <small class="text-danger fas fa-exclamation-circle">
                                        {{ $message }}
                                    </small>
                                @enderror
                            </div>
                            <div class="form-group" hidden>
                                {!! Form::label('horaasignada', 'Hora asignada:') !!}
                                {!! Form::text('horaasignada', null, ['class' => 'form-control', 'placeholder' => '', 'id' => 'horaasignada']) !!}
                                @error('horaasignada')
                                    <small class="text-danger fas fa-exclamation-circle">
                                        {{$message}}
                                    </small>
                                @enderror
                            </div>
                        </div>
                    </div>
                @else
                    <div class="alert " role="alert">
                        ESTE CLIENTE NO TIENE BATERIA
                    </div>
                @endif  
                {!! Form::submit('PROGRAMAR CLIENTE', ['class' => 'btn btn-crear']) !!}   
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>
@stop

@section('js')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script type="text/javascript" src="https://jeremyfagis.github.io/dropify/dist/js/dropify.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://jeremyfagis.github.io/dropify/dist/css/dropify.min.css"> 
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
    // Capturar el evento de cambio en el select de horarios disponibles
    document.querySelector('.horariodisponible').addEventListener('change', function() {
        // Obtener el valor seleccionado en el select de horarios disponibles
        var horarioSeleccionado = this.value;
        // Actualizar el valor del campo de hora asignada con el valor seleccionado
        document.getElementById('horaasignada').value = horarioSeleccionado;
    });
</script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const fechaSelect = document.getElementById('fecha_bateria');
        fechaSelect.addEventListener('change', function() {
            const fechaSeleccionada = fechaSelect.value;
            const todasLasAcciones = document.querySelectorAll('[class^="acciones-"]');
            todasLasAcciones.forEach(acc => {
                if (acc.classList.contains('acciones-' + fechaSeleccionada)) {
                    acc.style.display = 'block';
                } else {
                    acc.style.display = 'none';
                }
            });
        });
    });

    document.addEventListener("DOMContentLoaded", function() {
        const mostrarAccionesBtns = document.querySelectorAll('.mostrar-acciones');
        mostrarAccionesBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const fecha = this.getAttribute('data-fecha');
                const acciones = document.querySelector('.acciones-' + fecha);
                const todasLasAcciones = document.querySelectorAll('[class^="acciones-"]');
                todasLasAcciones.forEach(acc => {
                    if (acc !== acciones) {
                        acc.style.display = 'none';
                        const btn = acc.parentElement.querySelector('.mostrar-acciones');
                        btn.innerText = 'Mostrar acciones';
                    }
                });
                if (acciones.style.display === 'none') {
                    acciones.style.display = 'block';
                    this.innerText = 'Ocultar acciones';
                } else {
                    acciones.style.display = 'none';
                    this.innerText = 'Mostrar acciones';
                }
            });
        });
    });

    $(document).ready(function() {
    $('select[name^="proveedor_"]').change(function() {
        var selectedOption = $(this).val();
        // Seleccionar todos los selectores dentro de todas las filas excepto aquellos con la clase "no-bloquear"
        $('.row').find('select').not('.no-bloquear').not(this).prop('disabled', selectedOption !== '');
        
        // Mostrar u ocultar el botón según el estado de los selectores
        if ($('select:disabled').not('.no-bloquear').length > 0) {
            $('#habilitarSelectores').show();
        } else {
            
        }
    });

    // Controlar el evento click del botón para habilitar los selectores
    $('#habilitarSelectores').click(function() {
        var $selectores = $('.row').find('select').not('.no-bloquear');
        var fechaSeleccionada = $('#fecha_bateria').val(); // Guardar la fecha seleccionada

        $selectores.prop('disabled', false);
        $selectores.val(''); // Limpiar los campos seleccionados

        // Limpiar horarios disponibles
        $('#horariosdisponibles').val('');

        // Limpiar otros campos
        $('#proveedornombre').val('');
        $('#horaasignada').val('');
        $('#horarioinicial').val('');
        $('#horariofinal').val('');
        $('#tiempoatencion').val('');
        $('#accionnombre').val('');
        $('#areanombre').val('');
        $('#precio').val('');

        $('#fecha_bateria').val(fechaSeleccionada); // Restaurar la fecha seleccionada
        $(this).hide();
    });
});
</script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script type="text/javascript">
    $(document).ready(function(){
    // Función para actualizar los campos y el select de horarios disponibles
    function actualizarCamposYHorariosDisponibles(proveedorData) {
        var nombreProveedor = proveedorData[0];
        var horarioInicial = proveedorData[1].split(' - ')[0];
        // Eliminar el paréntesis y el último carácter (el paréntesis)
        var horarioFinal = proveedorData[1].split(' - ')[1].slice(0, -1);
        var tiempoAtencion = proveedorData[2].split(')')[0];
        var accionnombre = proveedorData[3].split(')')[0];
        var areanombre = proveedorData[4].split(')')[0];
        var precio = proveedorData[5].split(')')[0];

        $('#proveedornombre').val(nombreProveedor);
        $('#horarioinicial').val(horarioInicial);
        $('#horariofinal').val(horarioFinal);
        $('#tiempoatencion').val(tiempoAtencion);
        $('#accionnombre').val(accionnombre);
        $('#areanombre').val(areanombre);
        $('#precio').val(precio);
        actualizarHorariosDisponibles();
    }

    // Evento change para el select de proveedores
    $('select[name^="proveedor_"]').change(function(){
        var proveedorData = $(this).find('option:selected').text().split(' (');
        actualizarCamposYHorariosDisponibles(proveedorData);
    });

    // Actualizar campos y horarios disponibles al cargar la página
    var proveedorData = $('select[name^="proveedor_"]').find('option:selected').text().split(' (');
    actualizarCamposYHorariosDisponibles(proveedorData);
    });

    // Función para dividir el horario en partes de tiempo de atención
    function dividirHorario(horaInicial, horaFinal, tiempoAtencion) {
        var horarios = [];
        var hora = horaInicial;
        while (hora <= horaFinal) {
            var horaFinAtencion = sumarTiempo(hora, tiempoAtencion);
            // Asegurarse de que no se supere la hora final
            if (horaFinAtencion <= horaFinal) {
                horarios.push(formatoHora(hora));
            }
            hora = horaFinAtencion;
        }
        return horarios;
    }
    // Función para formatear la hora en formato HH:mm
    function formatoHora(hora) {
        return hora.split(':').slice(0, 2).join(':');
    }
    // Función para sumar tiempo a una hora
    function sumarTiempo(hora, tiempo) {
        var partesHora = hora.split(':');
        var horas = parseInt(partesHora[0]);
        var minutos = parseInt(partesHora[1]);
        var partesTiempo = tiempo.split(':');
        var horasTiempo = parseInt(partesTiempo[0]);
        var minutosTiempo = parseInt(partesTiempo[1]);

        horas += horasTiempo;
        minutos += minutosTiempo;
        if (minutos >= 60) {
            horas += 1;
            minutos -= 60;
        }

        // Asegurarse de que la hora no supere la hora final especificada
        if (horas > parseInt(document.getElementById('horariofinal').value.split(':')[0])) {
            horas = parseInt(document.getElementById('horariofinal').value.split(':')[0]);
            minutos = parseInt(document.getElementById('horariofinal').value.split(':')[1]);
        }
        return (horas < 10 ? '0' : '') + horas + ':' + (minutos < 10 ? '0' : '') + minutos;
    }
    // Función para actualizar las opciones del select
    function actualizarHorariosDisponibles() {
        var horarioInicial = document.getElementById('horarioinicial').value;
        var horarioFinal = document.getElementById('horariofinal').value;
        var tiempoAtencion = document.getElementById('tiempoatencion').value;
        var accionnombre = document.getElementById('accionnombre').value;
        var areanombre = document.getElementById('areanombre').value;
        var precio = document.getElementById('precio').value;
        var selectHorariosDisponibles = document.querySelector('.horariodisponible');

        var horarios = dividirHorario(horarioInicial, horarioFinal, tiempoAtencion);
        selectHorariosDisponibles.innerHTML = '';

        // Agregar opción en blanco
        var option = document.createElement('option');
        option.text = '';
        selectHorariosDisponibles.add(option);

        horarios.forEach(function(horario) {
            var option = document.createElement('option');
            option.text = horario;
            selectHorariosDisponibles.add(option);
        });
    }

    // Actualizar horarios disponibles al cargar la página
    window.addEventListener('load', function() {
        actualizarHorariosDisponibles();
    });

    // Escuchar cambios en los campos de horario inicial, final y tiempo de atención
    document.getElementById('horarioinicial').addEventListener('input', function() {
        actualizarHorariosDisponibles();
    });

    document.getElementById('horariofinal').addEventListener('input', function() {
        actualizarHorariosDisponibles();
    });

    document.getElementById('tiempoatencion').addEventListener('input', function() {
        actualizarHorariosDisponibles();
    });
    document.getElementById('accionnombre').addEventListener('input', function() {
        actualizarHorariosDisponibles();
    });
    document.getElementById('areanombre').addEventListener('input', function() {
        actualizarHorariosDisponibles();
    });
    document.getElementById('precio').addEventListener('input', function() {
        actualizarHorariosDisponibles();
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
    .hidden-field {
        display: none;
    }
    h1 {
        color:#94c93b; 
        font-family: "Segoe UI";
        font-weight: 900;
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
    .custom-button {
        background-color:  #ffffff;
        color: #faa625;
        border-color: #faa625;
        border-radius: 5px;
        padding: 5px 15px;
        }
    .custom-button:hover {
        background-color: #faa625;
        color: #ffffff;
        }
    .btn-crear {
        background-color:  #ffffff;
        color: #94c93b;
        border-color: #94c93b;
        border-radius: 5px;
        padding: 10px 20px;
        margin-left: 10px;
        margin-right: 10px;
        }
    .btn-crear:hover {
        background-color: #94c93b;
        color: #ffffff;
        }
    .btn-bateria {
        background-color:  #ffffff;
        color: #faa625;
        border-color: #faa625;
        border-radius: 5px;
        padding: 10px 20px;
        }
    .btn-bateria:hover {
        background-color: #faa625;
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
    .custom2-button {
        background-color: #ffffff;
        color: #faa625;
        border-color: #faa625;
        border-radius: 5px;
        padding: 5px 20px;
        margin-top: 33px;
    }
    .custom2-button:hover {
        background-color: #faa625;
        color: #ffffff;
    }
    .btn-cerrar {
        background-color: #ffffff;
        color: #94c93b;
        border-color: #94c93b;
        border-radius: 5px;
        padding: 5px 10px;

    }
    .btn-cerrar:hover {
        background-color: #94c93b;
        color: #ffffff;
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