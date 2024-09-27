@extends('adminlte::page')

@section('content_header')
<a class="btn btn-sm float-right btn-regresar" href="{{ route('admin.proveedores.index') }}">REGRESAR</a>
<a class="btn custom2-button btn-sm float-right" data-toggle="modal" data-target="#ventanaModal">BATERIA DEL PROVEEDOR</a>
<h5>CREAR BATERIA DE:</h5>
<h3>{{$proveedor->proveedor}}</h3>
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
            {!! Form::model($proveedor, ['route' => ['admin.proveedores.guardarbateriaproveedor', $proveedor], 'method' => 'POST']) !!}
            <div class="row ">
                {!! Form::hidden('usuarioid', auth()->user()->id) !!}
                {!! Form::hidden('usuarioregistro', auth()->user()->name) !!}

                <div class="row" hidden>
                    <div class="col-lg-2">
                        <div class="form-group">
                            {!! Form::label('proveedorid', 'ID:') !!}
                            {!! Form::text('proveedorid', $proveedor->id, ['class' => 'form-control', 'placeholder' => '']) !!}
                            @error('proveedorid')
                                <small class="text-danger fas fa-exclamation-circle">
                                    {{$message}}
                                </small>
                            @enderror
                        </div>
                    </div>
                </div>
                {{-- <div class="modal fade" id="ventanaModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">BATERIA DEL PROVEEDOR</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <label for="">ESTUDIOS</label>
                                <ul>
                                    @foreach($accionesProveedorEstudios as $accion)
                                    <li>
                                        {{ $accion }}
                                    </li>
                                    @endforeach
                                </ul>
                                <label for="">ESPECIALIDADES</label>
                                <ul>
                                    @foreach($accionesProveedorEspecialidad as $accion)
                                    <li>
                                        {{ $accion }}
                                    </li>
                                    @endforeach
                                </ul>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-cerrar" data-dismiss="modal">Cerrar</button>
                            </div>
                        </div>
                    </div>
                </div> --}}
                <div class="modal fade" id="ventanaModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">BATERIA DEL PROVEEDOR</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body"> 
                                @if($accionesProveedorEstudios->isNotEmpty())
                                <label for="">ESTUDIOS</label>
                                <ul>
                                    @foreach($accionesProveedorEstudios as $accion)
                                    <li>
                                        {{ $accion->accion }} - <span class="precio">PV: <strong>Bs.{{ $accion->precio }}</strong></span> - <span class="precio-compra">PC: <strong>Bs.{{ $accion->preciocompra }}</strong></span>- {{ $accion->asociado }}
                                    </li>
                                    @endforeach
                                </ul>
                                @endif
                                @if($accionesProveedorEspecialidad->isNotEmpty())
                                <label for="">ESPECIALIDADES</label>
                                <ul>
                                    @foreach($accionesProveedorEspecialidad as $accion)
                                    <li>
                                        {{ $accion->accion }} - <span class="precio">PV: <strong>Bs.{{ $accion->precio }}</strong></span> -  <span class="precio-compra">PC: <strong>Bs.{{ $accion->preciocompra }}</strong></span>- {{ $accion->asociado }}
                                    </li>
                                    @endforeach
                                </ul>
                                @endif
                            </div>
                            
                            <style>
                                .modal-body li {
                                    padding: 10px;
                                    border-bottom: 1px solid #ddd;
                                    margin-bottom: 0px;
                                }
                                .modal-body li:nth-child(odd) {
                                    background-color: #f0f0f0;
                                }

                                .modal-body li:nth-child(even) {
                                    background-color: #fff;
                                }
                                .precio {
                                    color: #333;
                                    background-color: #f7ffe9;
                                    padding: 3px 5px;
                                    border-radius: 4px;
                                    margin: 0 5px;
                                }
                                .precio-compra {
                                    color: #555;
                                    background-color: #fff4e4;
                                    padding: 3px 5px;
                                    border-radius: 4px;
                                    margin: 0 5px;
                                }
                            </style>
                            
                            <div class="modal-footer">
                                <button type="button" class="btn btn-cerrar" data-dismiss="modal">Cerrar</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="form-group" hidden>
                        {!! Form::label('proveedor', 'Nombre:') !!}
                        {!! Form::text('proveedor', null, ['class' => 'form-control', 'placeholder' => '', 'readonly' => 'readonly']) !!}
                        @error('proveedor')
                            <small class="text-danger fas fa-exclamation-circle">
                                {{$message}}
                            </small>
                        @enderror
                    </div>
                    {{-- <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                {!! Form::label('horarioinicial', 'Desde:') !!}
                                {!! Form::time('horarioinicial', null, ['class' => 'form-control', 'placeholder' => '',]) !!}
                                @error('horarioinicial')
                                    <small class="text-danger fas fa-exclamation-circle">
                                        {{$message}}
                                    </small>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                {!! Form::label('horariofinal', 'Hasta:') !!}
                                {!! Form::time('horariofinal', null, ['class' => 'form-control', 'placeholder' => '',]) !!}
                                @error('horariofinal')
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
                                {!! Form::label('tiempoatencion', 'Tiempo de atención:') !!}
                                {!! Form::time('tiempoatencion', '--:--', ['class' => 'form-control', 'placeholder' => '']) !!}
                                @error('tiempoatencion')
                                    <small class="text-danger fas fa-exclamation-circle">
                                        {{$message}}
                                    </small>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                {!! Form::label('cantidadatencion', 'Cantidad de atención:') !!}
                                {!! Form::text('cantidadatencion', null, ['class' => 'form-control', 'placeholder' => '', 'readonly' => true]) !!}
                                @error('cantidadatencion')
                                    <small class="text-danger fas fa-exclamation-circle">
                                        {{$message}}
                                    </small>
                                @enderror
                            </div>
                        </div>
                    </div> --}}
                    <div class="form-group">
                        {!! Form::label('sucursal', 'Sucursal:') !!}
                        {!! Form::select('sucursal', ['SANTA CRUZ' => 'SANTA CRUZ', 'COCHABAMBA' => 'COCHABAMBA'], null, ['class' => 'form-control', 'placeholder' => '']) !!}
                        @error('sucursal')
                            <small class="text-danger fas fa-exclamation-circle">
                                {{$message}}
                            </small>
                        @enderror
                    </div>
                    <div class="form-group">
                        {!! Form::label('asociado', 'Tipo de cliente:') !!}
                        {!! Form::select('asociado', ['CLIENTES ITA' => 'CLIENTES ITA', 'CLIENTES COMUNES' => 'CLIENTES COMUNES'], null, ['class' => 'form-control', 'id' => 'asociado', 'placeholder' => '']) !!}
                        {!! Form::hidden('asociadoid', null, ['class' => 'form-control', 'id' => 'asociadoid', 'placeholder' => '']) !!}
                        @error('asociado')
                            <small class="text-danger fas fa-exclamation-circle">
                                {{$message}}
                            </small>
                        @enderror
                    </div>
                    
                    <script>
                        document.addEventListener('DOMContentLoaded', function () {
                            var asociadoSelect = document.getElementById('asociado');
                            var asociadoIdInput = document.getElementById('asociadoid');
                    
                            // Function to update asociadoid based on the selection
                            function updateAsociadoId() {
                                var selectedValue = asociadoSelect.value;
                                if (selectedValue === 'CLIENTES ITA') {
                                    asociadoIdInput.value = '6';
                                } else if (selectedValue === 'CLIENTES COMUNES') {
                                    asociadoIdInput.value = '3';
                                } else {
                                    asociadoIdInput.value = ''; // Clear if no valid option
                                }
                            }
                    
                            // Attach event listener to select element
                            asociadoSelect.addEventListener('change', updateAsociadoId);
                    
                            // Initialize value in case the form is pre-filled
                            updateAsociadoId();
                        });
                    </script>
                    
                </div>
                
                
                <div class="col-lg-8">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                {!! Form::label('tipoarea', 'Tipo de área:', ['id' => 'area_label2']) !!}
                                {!! Form::select('tipoarea', ['Estudios' => 'ESTUDIOS', 'Especialidades' => 'ESPECIALIDADES'], null, ['class' => 'form-control', 'placeholder' => '', 'id' => 'tipoarea']) !!}
                                @error('tipoarea')
                                    <small class="text-danger fas fa-exclamation-circle">
                                        {{$message}}
                                    </small>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group" id="estudios_group" style="display: none;">
                                {!! Form::label('area', 'Estudio:', ['id' => 'area_label']) !!}
                                {!! Form::select('area', $areas, null, ['class' => 'form-control', 'id' => 'area_select', 'placeholder' => '']) !!}
                                @error('area')
                                    <small class="text-danger fas fa-exclamation-circle">
                                        {{$message}}
                                    </small>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div id="reset_button_container" style="" class=""></div>
                    @foreach($areas as $id => $nombreArea)
                        <div class="form-group acciones" id="acciones_{{ $id }}" style="display: none;">
                            <div class="card" style="max-height: 300px; overflow-y: auto;">
                                <div class="card-body">
                                    @php $count = count($accionesPorArea[$id]); @endphp
                                    @foreach($accionesPorArea[$id] as $key => $accionNombre)
                                        <div class="form-check">
                                            {!! Form::checkbox('accion[]', $accionNombre, null, ['class' => 'form-check-input', 'id' => 'accion_'.$key]) !!}
                                            {!! Form::label('accion_'.$key, $accionNombre, ['class' => 'form-check-label']) !!}
                                        </div>
                                        @if(($key + 1) == ceil($count / 1))
                                            </div><div class="card-body">
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                            @error('accion')
                                <small class="text-danger fas fa-exclamation-circle">
                                    {{$message}}
                                </small>
                            @enderror
                        </div>
                     @endforeach
                    <div class="form-group card card-body" id="especialidades_group" style="display: none;">
                        {!! Form::label('especialidades', 'Especialidades:', ['id' => 'especialidades_label']) !!}
                        <div class="row">
                            @foreach ($areas2 as $index => $area)
                                <div class="col-md-6 form-check">
                                    {!! Form::checkbox('accion[]', $area, false, ['class' => 'form-check-input', 'id' => 'accion_' . $area]) !!}
                                    {!! Form::label('accion_'. $area, $area, ['class' => 'form-check-label']) !!}
                                </div>
                               {{--  @if(($index + 1) % ceil(count($areas2) / 1) == 0)
                                    </div><div class="row">
                                @endif --}}
                            @endforeach
                        </div>
                    </div>
                </div>
            </div> 
            {!! Form::submit('CREAR BATERIA', ['class' => 'btn btn-crear']) !!}
            {!! Form::close() !!}
        </div>
    </div>
@stop
@section('js')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script type="text/javascript" src="https://jeremyfagis.github.io/dropify/dist/js/dropify.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://jeremyfagis.github.io/dropify/dist/css/dropify.min.css"> 
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    document.getElementById('tipoarea').addEventListener('change', function() {
        var select = document.getElementById('tipoarea');
        var selectedOption = select.options[select.selectedIndex].value;
        if (selectedOption === 'Especialidades') {
            var resetButton = document.getElementById('reset_button');
            if (resetButton) {
                resetButton.style.display = 'none';
            }
        } else {
            var resetButton = document.getElementById('reset_button');
            if (resetButton) {
                resetButton.style.display = 'block'; 
            }
        }
    });
</script>

<script>
//CALCULAR LOS HORARIOS
$(document).ready(function() {
        $('#horarioinicial').change(function() {
            $('#cantidadatencion').val('');
        });
        $('#horariofinal').change(function() {
            var horarioinicial = $('#horarioinicial').val();
            var horariofinal = $('#horafinal').val();
            var inicio = new Date("01/01/2024 " + horarioinicial);
            var fin = new Date("01/01/2024 " + horariofinal);
            if (fin <= inicio) {
                $('#horafinal').val('');
                $('#cantidadatencion').val('');
            }
        });

//CALCULAR LA CANTIDAD DE ATENCION SEGUN LOS HORARIOS
$('#tiempoatencion').change(function() {
            var horarioinicial = $('#horarioinicial').val();
            var horariofinal = $('#horariofinal').val();
            var tiempoAtencion = $('#tiempoatencion').val();
            var inicio = new Date("01/01/2024 " + horarioinicial);
            var fin = new Date("01/01/2024 " + horariofinal);
            var diff = (fin - inicio) / 1000 / 60;
            if (tiempoAtencion !== '00:00' && diff > 0) {
                var cantidadAtencion = diff / (parseInt(tiempoAtencion.split(':')[0]) * 60 + parseInt(tiempoAtencion.split(':')[1]));
                $('#cantidadatencion').val(Math.floor(cantidadAtencion));
            } else {
                $('#cantidadatencion').val('');
            }
        });
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

<script>
    $(document).ready(function() {
        $('#area').change(function() {
            var areaId = $(this).val();
            $('.acciones').hide();
            $('#acciones_' + areaId).show();
        });
    });

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

//CANCELAR FUNCION DE LA TECLA ENTER
    document.addEventListener('DOMContentLoaded', function() {
        document.addEventListener('keypress', function(event) {
            if (event.key === 'Enter') {
                event.preventDefault();
            }
        });
    });

    document.getElementById('area_select').addEventListener('change', function() {
        var select = document.getElementById('area_select');
        var selectedOption = select.options[select.selectedIndex];
        if (selectedOption.value !== '') {
            select.style.display = 'none';
            document.getElementById('area_label').style.display = 'none';
            var areaName = selectedOption.text;
            var accionesDiv = document.getElementById('acciones_' + selectedOption.value);
            accionesDiv.style.display = 'block';
            if (!document.getElementById('acciones_label_' + selectedOption.value)) {
                var accionesLabel = document.createElement('label');
                accionesLabel.innerHTML = 'Acciones para: ' + areaName;
                accionesLabel.id = 'acciones_label_' + selectedOption.value;
                accionesDiv.prepend(accionesLabel);
            }
            var resetButton = document.getElementById('reset_button');
            if (!resetButton) {
                var button = document.createElement('button');
                button.type = 'button';
                button.innerHTML = 'ELEGIR OTRO ESTUDIO';
                button.classList.add('custom-button');

                button.id = 'reset_button';
                button.onclick = resetSelectAndCheckboxes;
                document.getElementById('reset_button_container').appendChild(button);
            }
        }
    });

    function resetSelectAndCheckboxes() {
        var select = document.getElementById('area_select');
        select.style.display = 'block';
        select.value = '';
        document.getElementById('area_label').style.display = 'block';
        var checkboxes = document.querySelectorAll('[id^="accionnombre_"]');
        checkboxes.forEach(function(checkbox) {
            checkbox.checked = false;
        });

        var accionesDivs = document.querySelectorAll('[id^="acciones_"]');
        accionesDivs.forEach(function(div) {
            div.style.display = 'none';
            var label = document.getElementById('acciones_label_' + div.id.split('_')[1]);
            if (label) {
                label.remove();
            }
        });

        var resetButton = document.getElementById('reset_button');
        if (resetButton) {
            resetButton.remove();
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        const tipoAreaSelect = document.getElementById('tipoarea');
        const estudiosGroup = document.getElementById('estudios_group');
        const especialidadesGroup = document.getElementById('especialidades_group');
        const accionesContainers = document.querySelectorAll('.acciones');
    
        tipoAreaSelect.addEventListener('change', function () {
            const selectedValue = this.value;
            if (selectedValue === 'Estudios') {
                estudiosGroup.style.display = 'block';
                especialidadesGroup.style.display = 'none';
                clearCheckboxes('especialidades_group');
                hideAcciones();
            } else if (selectedValue === 'Especialidades') {
                estudiosGroup.style.display = 'none';
                especialidadesGroup.style.display = 'block';
                clearCheckboxes('estudios_group');
                hideAcciones();
            } else {
                estudiosGroup.style.display = 'none';
                especialidadesGroup.style.display = 'none';
                hideAcciones();
            }
        });
    
        function clearCheckboxes(groupId) {
            const group = document.getElementById(groupId);
            if (group) {
                const checkboxes = group.querySelectorAll('input[type="checkbox"]');
                checkboxes.forEach(checkbox => {
                    checkbox.checked = false;
                });
            }
        }
    
        function hideAcciones() {
            accionesContainers.forEach(container => {
                container.style.display = 'none';
                const checkboxes = container.querySelectorAll('input[type="checkbox"]');
                checkboxes.forEach(checkbox => {
                    checkbox.checked = false;
                });
            });
        }
    
        tipoAreaSelect.dispatchEvent(new Event('change'));
    });


    document.getElementById('area_select').addEventListener('change', function() {
        var select = document.getElementById('area_select');
        var selectedOption = select.options[select.selectedIndex];
        if (selectedOption.value !== '') {
            select.style.display = 'none';
            document.getElementById('area_label').style.display = 'none';
            var areaName = selectedOption.text;
            var accionesDiv = document.getElementById('acciones_' + selectedOption.value);
            accionesDiv.style.display = 'block';
            if (!document.getElementById('acciones_label_' + selectedOption.value)) {
                var accionesLabel = document.createElement('label');
                accionesLabel.innerHTML = 'Acciones para: ' + areaName;
                accionesLabel.id = 'acciones_label_' + selectedOption.value;
                accionesDiv.prepend(accionesLabel);
            }
            var resetButton = document.getElementById('reset_button');
            if (!resetButton) {
                var button = document.createElement('button');
                button.type = 'button';
                button.innerHTML = 'Elegir otro estudio';
                button.classList.add('custom-button');
                button.id = 'reset_button';
                button.onclick = resetSelectAndCheckboxes;
                document.getElementById('reset_button_container').appendChild(button);
            }
        } else {
            var resetButton = document.getElementById('reset_button');
            if (resetButton) {
                resetButton.parentNode.removeChild(resetButton);
            }
        }
    });

    document.getElementById('tipoarea').addEventListener('change', function() {
        var select = document.getElementById('tipoarea');
        var selectedOption = select.options[select.selectedIndex].value;
        if (selectedOption === 'Especialidades') {
            var resetButton = document.getElementById('reset_button');
            if (resetButton) {
                resetButton.style.display = 'none';
            }
        } else {
            var resetButton = document.getElementById('reset_button');
            if (resetButton) {
                resetButton.style.display = 'block'; 
            }
        }
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
    .custom-button {
        background-color: #ffffff;
        color: #faa625;
        border-color: #faa625;
        border-radius: 5px;
        padding: 5px 25px;
        margin-top: 0px;
        margin-bottom: 10px;
    }
    .custom-button:hover {
        background-color: #faa625;
        color: #ffffff;
    }
    .custom2-button {
        background-color: #ffffff;
        color: #faa625;
        border-color: #faa625;
        border-radius: 5px;
        padding: 10px 20px;
        margin-left: 10px;
        margin-right: 10px;
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
