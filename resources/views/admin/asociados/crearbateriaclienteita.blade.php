@extends('adminlte::page')
 
@section('content_header')
@if($rolusuario !== 'PROVEEDOR')
<a class="btn btn-sm float-right btn-regresar" href="{{ route('admin.asociados.verclienteita', $cliente) }}">REGRESAR</a>
@endif
<a class="btn custom2-button btn-sm float-right" data-toggle="modal" data-target="#ventanaModal">BATERIA DEL CLIENTE</a>
<h5>CREAR BATERIA DE:</h5> 
<h3>{{$cliente->nombrecompleto}}</h3>
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
        {!! Form::model($cliente, ['route' => ['admin.asociados.guardarbateriaclienteita', $cliente], 'method' => 'POST', 'id' => 'form-crear-bateria']) !!}
        <div class="row">
                {!! Form::hidden('usuarioid', auth()->user()->id) !!}
                {!! Form::hidden('usuarioregistro', auth()->user()->name) !!}
                {!! Form::hidden('clienteitaid', $id) !!}
                <div class="col-lg-4">
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
                        <div class="modal-dialog modal-xl" role="document">
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
                                        <option value="" disabled selected>Selecciona una fecha</option>
                                        @foreach($accionesPorFecha as $fecha => $acciones)
                                            <option value="{{ $fecha }}">{{ $fecha }}</option>
                                        @endforeach
                                    </select>
                                    <div id="acciones-container" class="mt-3">
                                        <strong>Acciones requeridas:</strong>
                                        <table id="acciones-table" class="table table-striped mt-2 compact-table" style="display: none;">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Acción</th>
                                                    <th>Informe</th>
                                                    <th>Proveedor</th>
                                                    @if(!auth()->user()->hasRole('PROVEEDOR'))
                                                        <th>Precio</th>
                                                    @endif
                                                </tr>
                                            </thead>
                                            <tbody>

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <a id="ver-pdf-btn" href="#" target="_blank" class="btn btn-crear"
                                        onclick=generatePDF()>Generar PDF</a>
                                    <button type="button" class="btn btn-cerrar" data-dismiss="modal">Cerrar</button>
                                </div>
                                <script> 
                                    function generatePDF() {
                                     // Obtener la fecha seleccionada
                                     var fechaSeleccionada = document.getElementById('select-fechas').value;
                                 
                                     if (!fechaSeleccionada) {
                                         alert("Por favor, selecciona una fecha.");
                                         return;
                                     }
                                 
                                     // Obtener el cliente ID desde Blade
                                     var clienteId = @json($cliente->id);
                                 
                                     // URL del controlador para generar el PDF
                                     var url = '{{ route("admin.asociados.generarpdfcliente", ":clienteId") }}';
                                     url = url.replace(':clienteId', clienteId);
                                 
                                     // Realizar la solicitud AJAX para generar y descargar el PDF
                                     fetch(url, {
                                         method: 'POST',
                                         headers: {
                                             'Content-Type': 'application/json',
                                             'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                         },
                                         body: JSON.stringify({
                                             fecha: fechaSeleccionada
                                         })
                                     })
                                     .then(response => {
                                         if (!response.ok) {
                                             throw new Error('Error en la respuesta del servidor.');
                                         }
                                         return response.blob();  // Obtener el PDF como un blob
                                     })
                                     .then(blob => {
                                         // Crear un enlace para descargar el archivo
                                         var link = document.createElement('a');
                                         link.href = window.URL.createObjectURL(blob);
                                         link.download = 'Checklist_' + '{{ $cliente->nombrecompleto }}' + '.pdf';
                                         link.click();
                                     })
                                     .catch(error => console.error('Error:', error));
                                    }
                                 
                                    // Asociar el evento de clic al botón "Generar PDF"
                                    document.getElementById('ver-pdf-btn').addEventListener('click', function(e) {
                                        e.preventDefault();
                                    
                                        var fechaSeleccionada = document.getElementById('ver-pdf-btn').getAttribute('data-fecha');
                                        var clienteId = @json($cliente->id); // Asegúrate de que tienes acceso a esta variable
                                        var url = '{{ route('admin.asociados.generarpdfcliente', ':clienteId') }}';
                                        url = url.replace(':clienteId', clienteId);
                                    
                                        // Realizar la solicitud AJAX para obtener el enlace del PDF
                                        fetch(url, {
                                            method: 'POST',
                                            headers: {
                                                'Content-Type': 'application/json',
                                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                            },
                                            body: JSON.stringify({
                                                fecha: fechaSeleccionada
                                            })
                                        })
                                        .then(response => {
                                            if (!response.ok) {
                                                throw new Error('Error en la respuesta del servidor.');
                                            }
                                            return response.blob();  // Obtener el PDF como un blob
                                        })
                                        .then(blob => {
                                            // Crear un enlace para descargar el archivo
                                            var link = document.createElement('a');
                                            link.href = window.URL.createObjectURL(blob);
                                            link.download = 'Checklist_' + '{{ $cliente->nombrecompleto }}' + '.pdf';
                                            link.click();
                                        })
                                        .catch(error => console.error('Error:', error));
                                    });
                                </script>
                                
                            </div>
                        </div>
                    </div>

                        <script>
                            document.addEventListener('DOMContentLoaded', function () {
                                const selectFechas = document.getElementById('select-fechas');
                                const accionesTable = document.getElementById('acciones-table');
                                const tbody = accionesTable.querySelector('tbody');
                            
                                const accionesPorFecha = @json($accionesPorFecha);
                                const rolusuario = @json($rolusuario); // Asegúrate de que el rol se pasa al script
                            
                                selectFechas.addEventListener('change', function () {
                                    const selectedDate = this.value;
                            
                                    tbody.innerHTML = '';
                            
                                    if (selectedDate && accionesPorFecha[selectedDate]) {
                                        const acciones = accionesPorFecha[selectedDate];
                            
                                        acciones.forEach(item => {
                                            const row = document.createElement('tr');
                                            row.innerHTML = `
                                                <td>${item.id}</td>
                                                <td>${item.accion}</td>
                                                <td>${item.informe}</td>
                                                <td>${item.proveedor}</td>
                                                <td>${rolusuario === 'PROVEEDOR' ? '' : item.precio}</td>
                                            `;
                                            tbody.appendChild(row);
                                        });
                                        accionesTable.style.display = 'table';
                                    } else {
                                        accionesTable.style.display = 'none';
                                    }
                                });
                            });
                        </script>

                        <style>
                            .compact-table th, .compact-table td {
                                padding: 4px 8px; /* Reduce el padding para compactar las celdas */
                                line-height: 1.2; /* Ajusta el interlineado de las celdas */
                            }

                            .compact-table {
                                font-size: 16px; /* Ajusta el tamaño de fuente si es necesario */
                            }
                        </style>

                    <div class="form-group">
                        <strong>Fecha de Batería:</strong>
                        <select id="select-fechas" name="fechabateria" class="form-control">
                            <option value="nueva_bateria">FECHA DE HOY</option>
                            @foreach($accionesPorFecha as $fecha => $acciones)
                                <option value="{{ $fecha }}">{{ $fecha }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <strong>Informe:</strong>
                        <select id="informe" name="informe" class="form-control">
                            <option value="NO TIENE INFORME">NO TIENE</option>
                            <option value="SI TIENE INFORME">SI TIENE</option>
                        </select>
                    </div>
                    <div class="form-group hidden" id="fechaInformeGroup">
                        <strong>Fecha del Informe:</strong>
                        <input type="date" id="fechainforme" name="fechainforme" class="form-control">
                    </div>
                    <style>
                        .form-group {
                            margin-bottom: 15px;
                        }
                        .hidden {
                            display: none;
                        }
                    </style>
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const informeSelect = document.getElementById('informe');
                            const fechaInformeGroup = document.getElementById('fechaInformeGroup');
                
                            function toggleFechaInforme() {
                                if (informeSelect.value === 'SI TIENE INFORME') {
                                    fechaInformeGroup.classList.remove('hidden');
                                } else {
                                    fechaInformeGroup.classList.add('hidden');
                                }
                            }
                
                            // Inicializa el estado del campo cuando la página carga
                            toggleFechaInforme();
                
                            // Añade un listener para cambios en la selección
                            informeSelect.addEventListener('change', toggleFechaInforme);
                        });
                    </script>
                    <div class="form-group">
                        {!! Form::label('tipoarea', 'Tipo Area:', ['id' => 'area_label2']) !!}
                        {!! Form::select('tipoarea', ['Estudios' => 'ESTUDIOS', 'Especialidades' => 'ESPECIALIDADES'], null, ['class' => 'form-control', 'placeholder' => '', 'id' => 'tipoarea']) !!}
                        @error('tipoarea')
                            <small class="text-danger fas fa-exclamation-circle">
                                {{$message}}
                            </small>
                        @enderror
                    </div>
                    <!-- Campo adicional "ANTECEDENTES" -->
                    <div class="form-group" id="antecedentes-field" style="display: none;">
                        {!! Form::label('antecedentes', 'Antecedentes:') !!}
                        {!! Form::text('antecedentes', null, ['class' => 'form-control', 'id' => 'antecedentes']) !!}
                        @error('antecedentes')
                            <small class="text-danger fas fa-exclamation-circle">
                                {{$message}}
                            </small>
                        @enderror
                    </div>
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const tipoareaSelect = document.getElementById('tipoarea');
                            const antecedentesField = document.getElementById('antecedentes-field');
                        
                            tipoareaSelect.addEventListener('change', function() {
                                if (tipoareaSelect.value === 'Especialidades') {
                                    antecedentesField.style.display = 'block';
                                } else {
                                    antecedentesField.style.display = 'none';
                                }
                            });
                        
                            // Opcional: si ya hay un valor seleccionado al cargar la página
                            if (tipoareaSelect.value === 'Especialidades') {
                                antecedentesField.style.display = 'block';
                            }
                        });
                        </script>
                        
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
                <div class="col-lg-8">         
                    @foreach($areas as $id => $nombreArea)  
                        <div class="form-group acciones" id="acciones_{{ $id }}" style="display: none;">
                            <input type="text" id="search_{{ $id }}" placeholder="BUSCAR ESTUDIO" class="form-control" onkeyup="buscarAccion({{ $id }})"> <!-- Input de búsqueda -->
                            
                            <div class="card" style="max-height: 500px; overflow-y: auto;">
                                <div class="card-body">
                                    @php $count = count($accionesPorArea[$id]); @endphp
                                    <div class="acciones-container" id="acciones-container-{{ $id }}">
                                        @foreach($accionesPorArea[$id] as $accion)
                                            <div class="form-check accion-item">
                                                {!! Form::checkbox('acciones[]', $accion->id, null, ['class' => 'form-check-input', 'id' => 'accion_'.$accion->id]) !!}
                                                {!! Form::label('accion_'.$accion->id, $accion->accion . ' - ID: ' . $accion->id . ' - Proveedor: ' . $accion->proveedor, ['class' => 'form-check-label']) !!}

                                                @if(!auth()->user()->hasRole('PROVEEDOR'))
                                                    <span>- Precio: {{ $accion->precio }}</span>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            @error('accionnombre')
                                <small class="text-danger fas fa-exclamation-circle">{{$message}}</small>
                            @enderror
                        </div>
                    @endforeach
                    <script>
                        function buscarAccion(areaId) {
                            var query = $('#search_' + areaId).val().toLowerCase();
                            $('#acciones_' + areaId + ' .accion-item').each(function() { 
                                var label = $(this).find('label').text().toLowerCase();
                                
                                if (label.includes(query)) { 
                                    $(this).show();
                                } else {  // Si no incluye, ocultar
                                    $(this).hide();
                                }
                            });
                        }
                    </script>
               

                    {{-- <div class="form-group card card-body" id="especialidades_group" style="display: none;"> 
                        {!! Form::label('especialidades', 'Especialidades:', ['id' => 'especialidades_label']) !!}
                        <div class="row">
                            @foreach ($areas2 as $area2)
                                <div class="col-md-12 form-check">
                                    {!! Form::checkbox('accionnombre[]', $area2->id, false, ['class' => 'form-check-input', 'id' => 'accionnombre_' . $area2->id]) !!}
                                    {!! Form::label('accionnombre_' . $area2->id, $area2->area . ' - Proveedor: ' . $area2->proveedor . (auth()->user()->hasRole('OPERATIVO') ? '' : ' - Precio: ' . $area2->precio), ['class' => 'form-check-label']) !!}
                                </div>
                            @endforeach
                        </div>
                    </div> --}}
                    <div class="form-group card card-body" id="especialidades_group" style="display: none;">  
                        {!! Form::label('especialidades', 'Especialidades:', ['id' => 'especialidades_label']) !!}
                        <input type="text" id="search_especialidades" placeholder="BUSCAR ESPECIALIDAD" class="form-control" onkeyup="buscarEspecialidad()">
                        
                        <div class="row">
                            @foreach ($areas2 as $area2)
                                <div class="col-md-12 form-check especialidad-item">
                                    {!! Form::checkbox('accionnombre[]', $area2->id, false, ['class' => 'form-check-input', 'id' => 'accionnombre_' . $area2->id]) !!}
                                    {!! Form::label('accionnombre_' . $area2->id, $area2->area . ' - Proveedor: ' . $area2->proveedor . (auth()->user()->hasRole('OPERATIVO') ? '' : ' - Precio: ' . $area2->precio), ['class' => 'form-check-label']) !!}
                                </div>
                            @endforeach
                        </div>
                    </div>
                <script>
                    
                    function buscarEspecialidad() {
                    var query = $('#search_especialidades').val().toLowerCase(); // Toma el valor del input y lo convierte a minúsculas
                    $('.especialidad-item').each(function() {  // Recorre todas las especialidades
                        var label = $(this).find('label').text().toLowerCase();
                        
                        if (label.includes(query)) {  // Si el texto de la especialidad incluye la búsqueda, mostrar
                            $(this).show();
                        } else {  // Si no incluye, ocultar
                            $(this).hide();
                        }
                    });
                }
                </script>                    
                    
                </div>
            </div>
            {{-- {!! Form::submit('CREAR BATERIA', ['class' => 'btn btn-crear']) !!} --}}
            <button type="button" class="btn btn-crear" id="btn-crear-bateria">CREAR BATERIA</button>
            {{-- <div id="loading-spinner" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); z-index: 1000; text-align: center;">
                <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100%;">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden"></span>
                    </div>
                    <span style="color: #ffffff; margin-top: 10px;">GUARDANDO...</span>
                </div>
            </div>
            <style>
                .spinner-border {
                    width: 4rem;
                    height: 4rem;
                    border: 8px solid rgba(255, 255, 255, 0.3);
                    border-left-color: #94c93b;
                    border-radius: 50%;
                    animation: spin 1s linear infinite;
                }
                @keyframes spin {
                    0% { transform: rotate(0deg); }
                    100% { transform: rotate(360deg); }
                }
                #loading-spinner {
                    display: flex;
                    align-items: center;
                    justify-content: center;
                }
            </style> --}}
            {{-- <script> 
                document.addEventListener('DOMContentLoaded', function() {
                    document.getElementById('btn-crear-bateria').addEventListener('click', function() {
                        const checkboxesAcciones = document.querySelectorAll('input[name="acciones[]"]:checked');
                        const checkboxesEspecialidades = document.querySelectorAll('input[name="accionnombre[]"]:checked');
                        if (checkboxesAcciones.length === 0 && checkboxesEspecialidades.length === 0) {
                            alert('POR FAVOR SELECCIONE AL MENOS UNA ACCIÓN.');
                        } else {
                            document.getElementById('btn-crear-bateria').style.display = 'none';
                           /*  document.getElementById('loading-spinner').style.display = 'block'; */
                            document.getElementById('form-crear-bateria').submit();
                        }
                    });
                });
            </script> --}}
            {{-- <script> 
                document.addEventListener('DOMContentLoaded', function() {
                    document.getElementById('btn-crear-bateria').addEventListener('click', function() {
                        document.getElementById('btn-crear-bateria').style.display = 'none';
                        document.getElementById('loading-spinner').style.display = 'block';
                        document.getElementById('form-crear-bateria').submit();
                    });
                });
            </script> --}}
                
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
{{-- <script>
    function generatePDF() {
        // Obtener la fecha seleccionada
        var fechaSeleccionada = document.getElementById('select-fechas').value;

        if (!fechaSeleccionada) {
            alert("Por favor, selecciona una fecha.");
            return;
        }

        // Obtener el cliente ID
        var clienteId = {{ $cliente->id }}; // Asegúrate de que tienes acceso a esta variable

        // URL del controlador para generar el PDF
        var url = '/admin/asociados/generarpdfcliente/' + clienteId;

        // Redirigir a la URL para descargar el PDF directamente
        window.location.href = url + '?fecha=' + encodeURIComponent(fechaSeleccionada);
    }

    // Asociar el evento de clic al botón "Generar PDF"
    document.getElementById('ver-pdf-btn').addEventListener('click', function(e) {
        e.preventDefault();
        generatePDF();
    });
</script> --}}

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