@extends('adminlte::page')
 
@section('content_header')
@if($rolusuario !== 'PROVEEDOR')
<a class="btn btn-sm float-right btn-regresar" href="{{ route('admin.asociados.verclienteita', $cliente) }}">REGRESAR</a>
@endif
<a class="btn custom2-button btn-sm float-right" data-toggle="modal" data-target="#ventanaModal">BATERIA DEL CLIENTE</a>

<div class="modal fade" id="ventanaModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">BATERIA DEL CLIENTE:</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="table-responsive">
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
                                    <th>Estudio/Espec.</th>
                                    <th>Proveedor</th>
                                    @if(!auth()->user()->hasRole('PROVEEDOR'))
                                        <th>Precio</th>
                                    @endif
                                    <th>Informe</th>
                                    <th>Usuario_Reg.</th>
                                    <th>Info.Ajeno</th>
                                    <th>Sel.<input type="checkbox" id="select-all"></th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
                {{-- <div class="modal-footer">
                    <div class="form-group">
                        <input type="text" name="clienteAuditoriaID" id="cliente-auditoria-id" class="form-control" placeholder="ID">
                    </div>
                    <button type="button" id="duplicar-btn" class="btn btn-traspasar">TRASPASAR A AUDITORIA</button>
                    <button type="button" id="anular-btn" class="btn btn-anular">EDITAR COMO PROV. AJENO</button>
                    <a id="ver-pdf-btn" href="#" target="_blank" class="btn btn-crear" onclick="generatePDF()">GENERAR PDF</a>
                    <button type="button" class="btn btn-cerrar" data-dismiss="modal">CERRAR</button>
                </div> --}}
                <div class="modal-footer d-flex justify-content-between">
                    <div>
                        <div class="form-group d-inline-block">
                            <input type="text" name="clienteAuditoriaID" id="cliente-auditoria-id" class="form-control" placeholder="ID" style="width: 60px;">
                        </div>
                        <button type="button" id="duplicar-btn" class="btn btn-traspasar">TRASPASAR A AUDITORIA</button>
                        <button type="button" id="anular-btn" class="btn btn-anular">EDITAR COMO PROV. AJENO</button>
                    </div>
                    <div>
                        <a id="ver-pdf-btn" href="#" target="_blank" class="btn btn-crear" onclick="generatePDF()">GENERAR PDF</a>
                        <button type="button" class="btn btn-cerrar" data-dismiss="modal">CERRAR</button>
                    </div>
                </div>
            </div>
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    const duplicarBtn = document.getElementById('duplicar-btn');
            
                    duplicarBtn.addEventListener('click', function () {
                        const selectedData = [];
                        document.querySelectorAll('.row-checkbox:checked').forEach(cb => {
                            selectedData.push(cb.value);
                        });
            
                        if (selectedData.length === 0) {
                            alert('Seleccione al menos un registro.');
                            return;
                        }
            
                        const clienteAuditoriaID = document.getElementById('cliente-auditoria-id').value.trim();
                        if (!clienteAuditoriaID) {
                            alert('Debe ingresar un Cliente Auditoria ID.');
                            return;
                        }
            
                        fetch('{{ route("ruta.duplicar") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({ registros: selectedData, clienteAuditoriaID })
                        })
                        
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                alert('Registros duplicados exitosamente.');
                                location.reload();
                            } else {
                                alert('Ocurrió un error al procesar.');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('Ocurrió un error al procesar.');
                        });
                    });
                });
            </script>


            {{-- EDITAR COMO PROVEEDOR AJENO --}}
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    const selectFechas = document.getElementById('select-fechas');
                    const accionesTable = document.getElementById('acciones-table');
                    const tbody = accionesTable.querySelector('tbody');
                    const selectAllCheckbox = document.getElementById('select-all');
                    const anularBtn = document.getElementById('anular-btn');
                    const accionesPorFecha = @json($accionesPorFecha);
                    const rolusuario = @json($rolusuario);

                    selectFechas.addEventListener('change', function () {
                        const selectedDate = this.value;
                        tbody.innerHTML = '';

                        if (selectedDate && accionesPorFecha[selectedDate]) {
                            const acciones = accionesPorFecha[selectedDate];
                            acciones.forEach(item => {
                                const esProveedorAjeno = item.proveedor === 'PROVEEDOR AJENO' || item.informe === 'NINGUNO';

                                const row = document.createElement('tr');
                                row.innerHTML = `
                                    <td>${item.id}</td>
                                    <td title="${item.accion}" class="truncar">
                                        
                                        ${item.orden ? `<a href="{{ asset('ordenesbateria') }}/${item.clienteitaid}/${item.orden}" class="btn btn-sm btn-verorden" target="_blank" title="VER ORDEN"><i class="fas fa-eye"></i></a>` : ''} ${item.accion} 
                                    </td>
                                    <td title="${item.proveedor}" class="truncar">${item.proveedor}</td>
                                    ${rolusuario === 'PROVEEDOR' ? '' : `<td>${item.precio}</td>`}
                                    <td title="${item.informe}" class="truncar">${item.informe}</td>
                                    <td title="${item.usuarioregistro}" class="truncar2">${item.usuarioregistro}</td>
                                    <td>${item.fechainforme ? item.fechainforme : 
                                    (esProveedorAjeno ? '' : `<input type="date" class="fecha-informe form-control form-control-sm" data-id="${item.id}" style="width: 100px;" disabled>`)}</td>
                                    <td><input type="checkbox" class="row-checkbox" value="${item.id}"></td>

                                `;
                                tbody.appendChild(row);
                            });

                            accionesTable.style.display = 'table';
                        } else {
                            accionesTable.style.display = 'none';
                        }
                    });
                    document.addEventListener('change', function (event) {
                        if (event.target.classList.contains('row-checkbox')) {
                            const row = event.target.closest('tr');
                            const dateInput = row.querySelector('.fecha-informe');
                            if (dateInput) {
                                dateInput.disabled = !event.target.checked;
                            }
                        }
                    });
                    selectAllCheckbox.addEventListener('change', function () {
                        const checkboxes = document.querySelectorAll('.row-checkbox:not(:disabled)');
                        checkboxes.forEach(cb => {
                            cb.checked = this.checked;
                            const row = cb.closest('tr');
                            const dateInput = row.querySelector('.fecha-informe');
                            if (dateInput) {
                                dateInput.disabled = !cb.checked;
                            }
                        });
                    });
                    anularBtn.addEventListener('click', function () {
                        const selectedData = [];
                        document.querySelectorAll('.row-checkbox:checked').forEach(cb => {
                            const row = cb.closest('tr');
                            const dateInput = row.querySelector('.fecha-informe');
                            const fechaInforme = dateInput ? dateInput.value : null;

                            /* if (!fechaInforme) {
                                alert('Debe seleccionar una fecha para cada registro marcado.');
                                return;
                            } */

                            selectedData.push({ id: cb.value, fechainforme: fechaInforme });
                        });

                        if (selectedData.length === 0) {
                            alert('Seleccione al menos un registro.');
                            return;
                        }
                        fetch('{{ route("ruta.anular") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({ registros: selectedData })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                alert('Registros anulados y nuevos registros creados.');
                                location.reload();
                            } else {
                                alert('Ocurrió un error.');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('Ocurrió un error.');
                        });
                    });
                });
            </script>

            {{-- GENERAR PDF DE PLANILLA DE BATERIA --}}
            <script> 
                function generatePDF() {
                 var fechaSeleccionada = document.getElementById('select-fechas').value;
             
                 if (!fechaSeleccionada) {
                     alert("Por favor, selecciona una fecha.");
                     return;
                 }
                 var clienteId = @json($cliente->id);
                 var url = '{{ route("admin.asociados.generarpdfcliente", ":clienteId") }}';
                 url = url.replace(':clienteId', clienteId);
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
                     return response.blob();
                 })
                 .then(blob => {
                     var link = document.createElement('a');
                     link.href = window.URL.createObjectURL(blob);
                     link.download = 'Checklist_' + '{{ $cliente->nombrecompleto }}' + '.pdf';
                     link.click();
                 })
                 .catch(error => console.error('Error:', error));
                }

                document.getElementById('ver-pdf-btn').addEventListener('click', function(e) {
                    e.preventDefault();
                
                    var fechaSeleccionada = document.getElementById('ver-pdf-btn').getAttribute('data-fecha');
                    var clienteId = @json($cliente->id);
                    var url = '{{ route('admin.asociados.generarpdfcliente', ':clienteId') }}';
                    url = url.replace(':clienteId', clienteId);
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
                        return response.blob();
                    })
                    .then(blob => {
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
<style>
    .compact-table th, .compact-table td {
        padding: 4px 8px;
        line-height: 1.5;
    }

    .compact-table {
        font-size: 16px;
    }
</style>

<h5>CREAR BATERIA DE:</h5> 
<h3>{{$cliente->nombrecompleto}}</h3>

    @isset($fechaExpiracion)
        <div id="cronometro-container" class="mt-3 p-2 bg-light border rounded">
            <strong>Tiempo restante para crear la batería:</strong>
            <span id="cronometro">Calculando...</span>
        </div>
    @endisset

    <style>
        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }
        @keyframes shake {
            0% {
                transform: translateX(0);
            }

            25% {
                transform: translateX(-5px);
            }

            50% {
                transform: translateX(5px);
            }

            75% {
                transform: translateX(-5px);
            }

            100% {
                transform: translateX(0);
            }
        }
        #cronometro-container {
            position: relative;
            z-index: 1000;
            animation: fadeIn 1s ease-in-out;
        }
        #cronometro {
            font-size: 1.2em;
            color: #dc3545;
            font-weight: bold;
        }
        .shake {
            animation: shake 0.5s;
            animation-iteration-count: 2;
        }
        @media (max-width: 576px) {
            #cronometro-container {
                text-align: center;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @if(isset($fechaExpiracion))
                var fechaExpiracion = new Date("{{ $fechaExpiracion->toIso8601String() }}").getTime();

                var x = setInterval(function() {
                    var ahora = new Date().getTime();
                    var distancia = fechaExpiracion - ahora;
                    var totalMinutos = Math.floor(distancia / (1000 * 60));
                    var segundos = Math.floor((distancia % (1000 * 60)) / 1000);
                    var minutosDisplay = totalMinutos < 10 ? "0" + totalMinutos : totalMinutos;
                    var segundosDisplay = segundos < 10 ? "0" + segundos : segundos;
                    document.getElementById("cronometro").innerHTML = minutosDisplay + "m " + segundosDisplay + "s ";

                    if (distancia < 0) {
                        clearInterval(x);
                        var cronometro = document.getElementById("cronometro");
                        cronometro.innerHTML = "¡Tiempo agotado!";

                        var cronometroContainer = document.getElementById("cronometro-container");
                        cronometroContainer.classList.add('shake');

                        setTimeout(function() {
                            location.reload();
                        }, 2000);
                    }
                }, 1000);
            @endif
        });
    </script>
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

    @if (session('success'))
        <div id="alert-success" class="alert alert-success">
            <strong>{{ session('success') }}</strong>
        </div>
        <script>
            setTimeout(function() {
                $('#alert-success').fadeOut('fast');
            }, 5000);
        </script>
    @endif

    @if ($errors->has('mensaje'))
        <div id="alert-error" class="alert alert-danger">
            <strong>{{ $errors->first('mensaje') }}</strong>
        </div>
        <script>
            setTimeout(function() {
                $('#alert-error').fadeOut('fast');
            }, 5000);
        </script>
    @endif

<div class="card">
    <div class="card-body">
        @if ($nombreusuario === 'CARLOS ALEJANDRO GUARACHI SANDOVAL' || $nombreusuario === 'DENISSE MAUREN LOPEZ FLORES' || $nombreusuario === 'JHOSELINE EVA VELASQUEZ ESCOBAR' || $nombreusuario === 'AGUIRRE VASQUEZ MARIA RENEE' || $nombreusuario === 'YELKA MORALES VELARDE' || $permisoValido)
        {!! Form::model($cliente, ['route' => ['admin.asociados.guardarbateriaclienteita', $cliente], 'method' => 'POST', 'id' => 'form-crear-bateria', 'files' => true ]) !!}
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
                        <strong>Medico Derivador:</strong>
                        <select id="select-fechas" name="medicoderivante" class="form-control">
                            @foreach($proveedoresmedicos as $proveedor)
                                <option value="{{ $proveedor->proveedor }}" {{ $proveedor->id == 3 ? 'selected' : '' }}>
                                    {{ $proveedor->proveedor }}
                                </option>
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
                            toggleFechaInforme();
                            informeSelect.addEventListener('change', toggleFechaInforme);
                        });
                    </script>
                    @php
                        $rolusuario = strtolower(auth()->user()->getRoleNames()->first());
                    @endphp
                    
                    @if($rolusuario !== 'proveedor'/*  && $rolusuario !== 'maestro' */)
                        <div class="form-group">
                            {!! Form::label('orden', 'Orden:') !!}
                            {!! Form::file('orden', ['id' => 'orden', 'class' => 'form-control', 'required']) !!}
                            @error('orden')
                                <small class="text-danger">
                                    <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                </small>
                            @enderror
                        </div>
                    @endif
                
                    
                    <div class="form-group">
                        {!! Form::label('tipoarea', 'Tipo Area:', ['id' => 'area_label2']) !!}
                        {!! Form::select('tipoarea', ['Estudios' => 'ESTUDIOS', 'Especialidades' => 'ESPECIALIDADES'], null, ['class' => 'form-control', 'placeholder' => '', 'id' => 'tipoarea']) !!}
                        @error('tipoarea')
                            <small class="text-danger fas fa-exclamation-circle">
                                {{$message}}
                            </small>
                        @enderror
                    </div>
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
                        var query = $('#search_especialidades').val().toLowerCase();
                        $('.especialidad-item').each(function() {
                            var label = $(this).find('label').text().toLowerCase();
                            
                            if (label.includes(query)) {
                                $(this).show();
                            } else {
                                $(this).hide();
                            }
                        });
                    }
                </script>                    
                    
                </div>
            </div>
            <button type="button" class="btn btn-crear" id="btn-crear-bateria">CREAR BATERIA</button> 
            {!! Form::close() !!}
        </div>
        @else
        {!! Form::model($cliente, ['route' => ['admin.asociados.guardarbateriaclienteita', $cliente], 'method' => 'POST', 'id' => 'form-crear-bateria']) !!}
        <div class="row">
            <div class="col-lg-6">
                <div class="form-group">
                    @if (session('success'))
                        <div id="alert-success" class="alert alert-success">
                            <strong>{{ session('success') }}</strong>
                        </div>
                        <script>
                            setTimeout(function() {
                                $('#alert-success').fadeOut('fast');
                            }, 5000);
                        </script>
                    @endif
                    {!! Form::label('codigo', 'Código de Ingreso:') !!}
                    {!! Form::text('codigo', null, [
                        'class' => 'form-control',
                        'placeholder' => 'Ingrese el código',
                        'required',
                        'maxlength' => '15',
                        'size' => '15',
                        'oninput' => 'this.value = this.value.toUpperCase()',
                        'style' => 'text-transform: uppercase;',
                    ]) !!}
                    @error('codigo')
                        <small class="text-danger fas fa-exclamation-circle">
                            {{ $message }}
                        </small>
                    @enderror
                </div>
                <button type="submit" class="btn btn-crear">INGRESAR CÓDIGO</button>
            </div>
            </div>
            {!! Form::close() !!}
        </div>
        @endif
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
    .table td {
        padding: 5px 10px;
    }
    .truncar {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 150px;
    }
    .truncar2 {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 100px;
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
        .btn-verorden {
        background-color:  #ffffff;
        color: #faa625;
        border-color: #faa625;
        border-radius: 5px;
        padding: 2px 6px;
        }
    .btn-verorden:hover {
        background-color: #faa625;
        color: #ffffff;
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
    .btn-traspasar {
        background-color:  #ffffff;
        color: #faa625;
        border-color: #faa625;
        border-radius: 5px;
        padding: 5px 10px;
        }
    .btn-traspasar:hover {
        background-color: #faa625;
        color: #ffffff;
        }
        .btn-anular {
        background-color:  #ffffff;
        color: #970d8e;
        border-color: #970d8e;
        border-radius: 5px;
        padding: 5px 10px;
        }
    .btn-anular:hover {
        background-color: #970d8e;
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
        padding: 5px 10px;
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
        padding: 5px 10px;
        margin-left: 10px;
        margin-right: 10px;
    }
    .custom2-button:hover {
        background-color: #faa625;
        color: #ffffff;
    }
    .btn-cerrar {
        background-color: #ffffff;
        color: #e11616;
        border-color: #e11616;
        border-radius: 5px;
        padding: 5px 10px;

    }
    .btn-cerrar:hover {
        background-color: #e11616;
        color: #ffffff;
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