@extends('adminlte::page')

@section('content_header')
<h1>PROGRAMAR CLIENTE</h1>
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
            <div class="row">
                <div class="col-lg-12">
                    {!! Form::model($clientebanco, ['route' => ['admin.clientesbancos.store3', $clientebanco], 'method' => 'POST']) !!}
                
                        {!! Form::text('usuarioid', auth()->user()->id) !!}
                        {!! Form::text('usuarioregistro', auth()->user()->name) !!}

                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    {!! Form::label('nombrecompleto', 'Nombre completo:') !!}
                                    {!! Form::text('nombrecompleto', null, ['class' => 'form-control', 'placeholder' => '', 'readonly' => true]) !!}
                                    @error('nombrecompleto')
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
                                    <strong>Acciones requeridas:</strong><br>
                                    {!! Form::select('accion', $accionesCliente, null, ['class' => 'form-control', 'placeholder' => 'Seleccionar acción']) !!}

                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <strong>Proveedores asociados:</strong><br>
                                    {!! Form::select('proveedor', $proveedores, null, ['class' => 'form-control', 'placeholder' => 'Seleccionar proveedor']) !!}
                                </div>
                            </div>
                        </div>
                        
                        {!! Form::submit('Crear programacion', ['class' => 'btn btn-crear']) !!}
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
</div>


{{-- 
    <div class="col-lg-6">
                            <div class="form-group">
                                <strong>Proveedores:</strong><br>
                                {!! Form::select('proveedornombre', $proveedores, null, ['class' => 'form-control', 'id' => 'proveedorSelect', 'placeholder' => 'Seleccionar proveedor']) !!}
                            </div>
                        </div>
                        <div class="col-lg-2">
                            <div class="form-group">
                                {!! Form::label('accionnombre', 'ACCION:') !!}
                                {!! Form::text('accionnombre', null, ['class' => 'form-control', 'readonly' => true, 'id' => 'accionInput']) !!}
                                @error('accionnombre')
                                    <small class="text-danger fas fa-exclamation-circle">
                                        {{ $message }}
                                    </small>
                                @enderror
                            </div>
                        </div>
    <div class="col-lg-2">
    <div class="form-group">
        {!! Form::label('proveedornombre', 'PROVEEEDOR:') !!}
        {!! Form::text('proveedornombre', null, ['class' => 'form-control proveedornombre', 'placeholder' => '']) !!}
        @error('proveedornombre')
            <small class="text-danger fas fa-exclamation-circle">
                {{ $message }}
            </small>
        @enderror
    </div>
</div>



<div class="col-lg-2">
    <div class="form-group">
        {!! Form::label('horariodisponible','Horarios disponibles:') !!}
        {!! Form::select('horariodisponible'.$accion, [], null, ['class' => 'form-control horariodisponible', 'placeholder' => '', 'data-accion' => $accion]) !!}
        @error('horariodisponible')
            <small class="text-danger fas fa-exclamation-circle">
                {{ $message }}
            </small>
        @enderror
    </div>
</div>

<div class="col-lg-2">
    <div class="form-group">
        {!! Form::label('horaasignada', 'Hora asignada:') !!}
        {!! Form::text('horaasignada', null, ['class' => 'form-control horaasignada', 'id' => 'horaasignada'.$accion]) !!}
        @error('horaasignada')
        <small class="text-danger fas fa-exclamation-circle">
            {{ $message }}
        </small>
    @enderror
    </div>
</div>

<div class="col-lg-2">
    <div class="form-group">
        {!! Form::label('fechaasignada', 'Fecha programada:') !!}
        {!! Form::date('fechaasignada', \Carbon\Carbon::now(), ['class' => 'form-control', 'max' => \Carbon\Carbon::now()->format('Y-m-d')]) !!}
        @error('fechaasignada')
            <small class="text-danger fas fa-exclamation-circle">
                {{ $message }}
            </small>
        @enderror
    </div>
</div>
</div> --}}

{{-- <div class="col-lg-2 hidden-field">
    <div class="form-group">
        {!! Form::label('horarioinicial_'.$accion, 'Desde:') !!}
        {!! Form::time('horarioinicial_'.$accion, null, ['class' => 'form-control horario-inicial', 'placeholder' => '', 'data-accion' => $accion]) !!}
        @error('horarioinicial_'.$accion)
            <small class="text-danger fas fa-exclamation-circle">
                {{ $message }}
            </small>
        @enderror
    </div>
</div>
<div class="col-lg-2 hidden-field">
    <div class="form-group">
        {!! Form::label('horariofinal_'.$accion, 'Hasta:') !!}
        {!! Form::time('horariofinal_'.$accion, null, ['class' => 'form-control horario-final', 'placeholder' => '', 'data-accion' => $accion]) !!}
        @error('horariofinal_'.$accion)
            <small class="text-danger fas fa-exclamation-circle">
                {{ $message }}
            </small>
        @enderror
    </div>
</div>
<div class="col-lg-2 hidden-field">
    <div class="form-group">
        {!! Form::label('tiempoatencion_'.$accion, 'Tiempo de Atención:') !!}
        {!! Form::time('tiempoatencion_'.$accion, null, ['class' => 'form-control tiempo-atencion', 'placeholder' => '', 'data-accion' => $accion]) !!}
        @error('tiempoatencion_'.$accion)
            <small class="text-danger fas fa-exclamation-circle">
                {{ $message }}
            </small>
        @enderror
    </div>
</div> --}}
@stop
@section('js')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script type="text/javascript" src="https://jeremyfagis.github.io/dropify/dist/js/dropify.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://jeremyfagis.github.io/dropify/dist/css/dropify.min.css"> 
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
    document.getElementById('proveedorSeleccionado').addEventListener('change', function() {
        var selectedOption = this.value;
        document.getElementById('proveedornombre').value = selectedOption;
    });
</script>
<script>
    $(document).ready(function () {
        $('.proveedor-select').change(function () {
            var accion = $(this).data('accion');
            var proveedor = $(this).val();
            var accionInput = $('input[name="accionnombre'+accion+'"]');
            accionInput.val(accion);
        });
    });
</script>

<script>
    $(document).ready(function () {
        $('.proveedor-select').change(function () {
            var accion = $(this).data('accion');
            var proveedor = $(this).val();
            var proveedorInput = $('input[name="proveedornombre'+accion+'"]');
            proveedorInput.val(proveedor);
        });
    });
</script>

<script>
    $(document).ready(function() {
        $('#area').change(function() {
            var areaId = $(this).val();
            $('.acciones').hide();
            $('#acciones_' + areaId).show();
        });
    });
</script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        $('.proveedor-select').change(function() {
            var accion = $(this).data('accion');
            var selectedText = $(this).find("option:selected").text();
            var info = selectedText.match(/\(([^)]+)\)/)[1].split(' - ');
            var horarioInicial = info[0];
            var horarioFinal = info[1];
            var tiempoAtencion = info[2];
            
            // Calcula las franjas horarias disponibles
            var disponibles = [];
            var inicio = new Date('01/01/2000 ' + horarioInicial);
            var fin = new Date('01/01/2000 ' + horarioFinal);
            var duracion = tiempoAtencion.split(':');
            var minutosAtencion = parseInt(duracion[0]) * 60 + parseInt(duracion[1]);
            while (inicio < fin) {
                var hora = inicio.getHours();
                var minutos = inicio.getMinutes();
                disponibles.push(('0' + hora).slice(-2) + ':' + ('0' + minutos).slice(-2) + ':00');
                inicio.setMinutes(inicio.getMinutes() + minutosAtencion);
            }
            
            // Limpia y genera las opciones de selección para horario disponible
            var select = $('select[name="horariodisponible'+accion+'"]');
            select.empty();
            for (var i = 0; i < disponibles.length; i++) {
                select.append('<option value="' + disponibles[i] + '">' + disponibles[i] + '</option>');
            }
        });
    });
</script>
<script>
    // Escuchar el evento change en todos los campos de selección de horarios disponibles
    document.querySelectorAll('.horariodisponible').forEach(function(select) {
        select.addEventListener('change', function() {
            // Obtener el valor seleccionado
            var selectedOption = this.value;
            // Obtener el número de acción desde el atributo data-accion
            var accion = this.getAttribute('data-accion');
            // Actualizar el valor del campo de texto correspondiente
            document.getElementById('horaasignada' + accion).value = selectedOption;
        });
    });
</script>
    
                                                
<script>

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