@extends('adminlte::page')
 
@section('content_header')
<a class="btn btn-sm float-right btn-regresar" href="{{ route('admin.asociados.verclientecomun', $clientecomun) }}">REGRESAR</a>
<a class="btn custom2-button btn-sm float-right" data-toggle="modal" data-target="#ventanaModal">BATERIA DEL CLIENTE</a>
<h5>CREAR BATERIA DE:</h5> 
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
<div class="card">
    <div class="card-body">
        {!! Form::model($clientecomun, ['route' => ['admin.asociados.guardarbateriaclientecomun', $clientecomun], 'method' => 'POST']) !!}
        <div class="row">
                {!! Form::hidden('usuarioid', auth()->user()->id) !!}
                {!! Form::hidden('usuarioregistro', auth()->user()->name) !!}
                {!! Form::hidden('clientecomunid', $id) !!}
                <div class="col-lg-6">
                    <div class="form-group" hidden>
                        {!! Form::label('nombrecompleto', 'Nombre completo:') !!}
                        {!! Form::text('nombrecompleto', null, ['class' => 'form-control', 'placeholder' => '', 'readonly' => 'readonly']) !!}
                        @error('nombrecompleto')
                            <small class="text-danger fas fa-exclamation-circle">
                                {{$message}}
                            </small>
                        @enderror
                    </div> 
                    <div class="modal fade" id="ventanaModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">BATERIA DEL CLIENTE:</h5>
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
                                        <strong>Acciones requeridas:</strong>
                                        @foreach($accionesPorFecha as $fecha => $acciones)
                                            <div id="acciones-{{ $fecha }}" class="acciones" style="display: none;">
                                                @foreach($acciones as $accion)
                                                    <div style="color: black;">&#10003; {{ $accion }}</div>
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
                    <div class="form-group">
                        {!! Form::label('tipoarea', 'Tipo Area:', ['id' => 'area_label2']) !!}
                        {!! Form::select('tipoarea', ['Estudios' => 'ESTUDIOS', 'Especialidades' => 'ESPECIALIDADES'], null, ['class' => 'form-control', 'placeholder' => '', 'id' => 'tipoarea']) !!}
                        @error('tipoarea')
                            <small class="text-danger fas fa-exclamation-circle">
                                {{$message}}
                            </small>
                        @enderror
                    </div>
                    <div class="form-group" id="estudios_group" style="display: none;">
                        {!! Form::label('areanombre', 'Estudio:', ['id' => 'area_label']) !!}
                        {!! Form::select('areanombre', $areas, null, ['class' => 'form-control', 'id' => 'area_select', 'placeholder' => '']) !!}
                        @error('areanombre')
                            <small class="text-danger fas fa-exclamation-circle">
                                {{$message}}
                            </small>
                        @enderror
                    </div>
                    <div id="reset_button_container" style="margin-bottom: 20px" class=""></div>
                </div>
                <div class="col-lg-6">
                    @foreach($areas as $id => $nombreArea)
                        <div class="form-group acciones" id="acciones_{{ $id }}" style="display: none;">
                            <div class="card" style="max-height: 300px; overflow-y: auto;">
                                <div class="card-body">
                                    @php $count = count($accionesPorArea[$id]); @endphp
                                    @foreach($accionesPorArea[$id] as $key => $accionNombre)
                                        <div class="form-check">
                                            {!! Form::checkbox('accionnombre[]', $accionNombre, null, ['class' => 'form-check-input', 'id' => 'accionnombre_'.$key]) !!}
                                            {!! Form::label('accionnombre_'.$key, $accionNombre, ['class' => 'form-check-label']) !!}
                                        </div>
                                        @if(($key + 1) == ceil($count / 1))
                                            </div><div class="card-body">
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                            @error('accionnombre')
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
                                    {!! Form::checkbox('accionnombre[]', $area, false, ['class' => 'form-check-input', 'id' => 'accionnombre_' . $area]) !!}
                                    {!! Form::label('accionnombre_'. $area, $area, ['class' => 'form-check-label']) !!}
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            {!! Form::submit('CREAR BATERIA', ['class' => 'btn btn-crear']) !!}
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
                button.innerHTML = 'Elegir otro estudio';
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
        padding: 5px 20px;
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