@extends('adminlte::page')

@section('content_header')
<a class="btn btn-sm float-right btn-azulgrande" href="{{ route('admin.asociados.verclienteita', $cliente) }}">REGRESAR</a>
@can('admin.asociados.reprogramacionclienteita')
<a class="btn btn-sm float-right btn-lilagrande" href="{{route('admin.asociados.reprogramacionclienteita', $cliente)}}">REPROGRAMAR</a>
@endcan
<a class="btn btn-sm float-right btn-naranjagrande" data-toggle="modal" data-target="#ventanaModal">PROGRAMACIONES</a>
<a class="btn btn-sm float-right btn-verdegrande" href="{{route('admin.asociados.estadoprogramacionclienteita', $cliente)}}">ESTADO DE PROG.</a>
<h5>PROGRAMAR ESTUDIOS / ESPECIALIDADES DE:</h5>
<h3>{{$cliente->nombrecompleto}}</h3>
@stop

@section('css')
<link rel="stylesheet" href="{{ asset('css/programacionesmedicas.css') }}">
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
        {!! Form::model($cliente, ['route' => ['admin.asociados.guardarprogramacionclienteita', $cliente], 'method' => 'POST']) !!}
            <div class="row">
                @if($accionesCliente)
                    {!! Form::hidden('usuarioid', auth()->user()->id) !!}
                    {!! Form::hidden('usuarioregistro', auth()->user()->name) !!}
                    {!! Form::hidden('clienteitaid', $id) !!}
                    {!! Form::hidden('clienteitanombre', $cliente->nombrecompleto) !!}
                    <div class="col-lg-8" style="margin-bottom:20px;">
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
                            <input type="hidden" id="fechabateria" name="fechabateria">
                            
                            <script>
                                document.getElementById('fecha_bateria').addEventListener('change', function() {
                                    document.getElementById('fechabateria').value = this.value;
                                });
                            </script>
                        </div>

                        {!! Form::label('', 'Estudios / Especialidades Disponibles:') !!}
                        @error('proveedornombre')
                            <small class="text-danger fas fa-exclamation-circle">
                                {{$message}}
                            </small>
                        @enderror

                        @foreach($accionesPorFecha as $fecha => $acciones)     
                            <div class="acciones-{{ $fecha }}" style="display:none;">
                                <div class="row" style="margin-top: 5px; margin-bottom: 20px; align-items: center;">
                                    <div class="col-lg-12">
                                        <input type="text" id="search-{{ $fecha }}" placeholder="Buscar estudio / especialidad..."
                                            style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.1); 
                                                    transition: box-shadow 0.3s ease; outline: none;" 
                                            onfocus="this.style.boxShadow='0 0 8px rgba(0, 0, 255, 0.3)';" 
                                            onblur="this.style.boxShadow='none';">
                                    </div>
                                </div>
                                <div class="col-lg-2" style="margin-left: -8px;">
                                    <label style="font-weight: normal; display: flex; align-items: center;">
                                        <input type="checkbox" id="select-all-{{ $fecha }}" style="margin-right: 5px;"> 
                                        <span style="font-weight: bold; font-size: 14px;">SELECCIONAR TODO</span>
                                    </label>
                                </div>
                                <div class="row action-container" style="display: flex; flex-wrap: wrap; gap: 20px;">
                                    @foreach($acciones as $accion)
                                        @php
                                            $proveedorAjeno = 'PROVEEDOR AJENO';
                                            $proveedor = isset($proveedoresDetalles[$accion]) ? $proveedoresDetalles[$accion] : null;
                                            $registrada = isset($accionesRegistradas[$fecha]) && in_array($accion, $accionesRegistradas[$fecha]);
                                            $accionSanitizada = str_replace([' ', '.'], ['_', '-'], $accion);
                                        @endphp
                                        
                                        @if(!$registrada && (!isset($proveedor) || $proveedor['proveedor'] !== $proveedorAjeno))
                                            <div class="col-lg-6 action-item" style="flex: 0 0 48%; margin-bottom: -15px;">
                                                <div class="form-group">
                                                    <div>
                                                        <label style="font-weight: normal; margin-bottom: -15px;">
                                                            <input type="checkbox" name="accionesSeleccionadas[]" value="{{ $accion }}"> {{ $accion }} - {{ $proveedor['proveedor'] }}
                                                        </label>
                                                        <input type="hidden" name="proveedor_{{ $accionSanitizada }}" value="{{ $proveedor['proveedor'] ?? '' }}">
                                                        <input type="hidden" name="areanombre_{{ $accionSanitizada }}" value="{{ $proveedor['area'] ?? '' }}">
                                                        <input type="hidden" name="precio_{{ $accionSanitizada }}" value="{{ $proveedor['precio'] ?? '' }}">
                                                        <input type="hidden" name="preciocompra_{{ $accionSanitizada }}" value="{{ $proveedor['preciocompra'] ?? '' }}">
                                                        <input type="hidden" name="servicio_{{ $accionSanitizada }}" value="{{ $proveedor['servicio'] ?? '' }}">
                                                        <input type="hidden" name="pagoservicio_{{ $accionSanitizada }}" value="{{ $proveedor['pagoservicio'] ?? '' }}">
                                                        <input type="hidden" name="bateriaid_{{ $accionSanitizada }}" value="{{ $proveedor['bateriaid'] ?? '' }}">
                                                        <input type="hidden" name="comision_{{ $accionSanitizada }}" value="{{ $proveedor['comision'] ?? '' }}">
                                                        <input type="hidden" name="medicoderivante_{{ $accionSanitizada }}" value="{{ $proveedor['medicoderivante'] ?? '' }}">
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endforeach

                        <script>
                            document.querySelectorAll('[id^="search-"]').forEach(function(searchInput) {
                                searchInput.addEventListener('keyup', function() {
                                    var input = this.value.toLowerCase();
                                    var fecha = this.id.split('-')[1];
                                    var actionItems = document.querySelectorAll('.action-item');
                                    var selectAllCheckbox = document.getElementById('select-all-' + fecha);
                                    
                                    actionItems.forEach(function(item) {
                                        var actionText = item.textContent.toLowerCase();
                                        if (actionText.includes(input)) {
                                            item.style.display = "";
                                        } else {
                                            item.style.display = "none";
                                        }
                                    });
                                    selectAllCheckbox.checked = false;
                                    updateSelectAll(fecha);
                                });
                            });
                            function updateSelectAll(fecha) {
                                var actionItems = document.querySelectorAll('.action-item');
                                var selectAllCheckbox = document.getElementById('select-all-' + fecha);
                                var visibleItems = Array.from(actionItems).filter(function(item) {
                                    return item.style.display !== "none";
                                });
                                selectAllCheckbox.checked = visibleItems.length > 0 && visibleItems.every(function(item) {
                                    return item.querySelector('input[type="checkbox"]').checked;
                                });
                            }
                            document.querySelectorAll('[id^="select-all-"]').forEach(function(selectAllCheckbox) {
                                selectAllCheckbox.addEventListener('change', function() {
                                    var fecha = this.id.split('-')[2];
                                    var actionItems = document.querySelectorAll('.action-item');
                                    actionItems.forEach(function(item) {
                                        if (item.style.display !== "none") {
                                            item.querySelector('input[type="checkbox"]').checked = selectAllCheckbox.checked;
                                        }
                                    });
                                });
                            });
                        </script>
                        <script>
                            document.addEventListener("DOMContentLoaded", function() {
                                    const fechaSelect = document.getElementById('fecha_bateria');

                                    fechaSelect.addEventListener('change', function() {
                                        const fechaSeleccionada = this.value;
                                        const todasLasAcciones = document.querySelectorAll('[class^="acciones-"]');

                                        todasLasAcciones.forEach(acc => {
                                            acc.style.display = acc.classList.contains('acciones-' + fechaSeleccionada) ? 'block' : 'none';
                                        });
                                    });
                                });
                                document.addEventListener("DOMContentLoaded", function() {
                                const mostrarAccionesBtns = document.querySelectorAll('.mostrar-acciones');
                                mostrarAccionesBtns.forEach(btn => {
                                    btn.addEventListener('click', function() {
                                        const fecha = this.getAttribute('data-fecha');
                                        const acciones = document.querySelector('.acciones-' + fecha);
                                        const accionesVisible = acciones.style.display === 'block';
                                        const todasLasAcciones = document.querySelectorAll('[class^="acciones-"]');
                                        todasLasAcciones.forEach(acc => {
                                            acc.style.display = 'none';
                                            acc.parentElement.querySelector('.mostrar-acciones').innerText = 'Mostrar acciones';
                                        });
                                        acciones.style.display = accionesVisible ? 'none' : 'block';
                                        this.innerText = accionesVisible ? 'Mostrar acciones' : 'Ocultar acciones';
                                    });
                                });
                            });
                        </script>
                        <button type="button" id="habilitarSelectores" style="display:none;" class="custom-button">Seleccionar otra acción</button>
                    </div>
                    <br>
                    <div class="col-lg-4">
                        <div class="form-group">
                            {!! Form::label('fechaasignada', 'Fecha a programar:') !!}
                            {!! Form::date('fechaasignada', null, ['class' => 'form-control']) !!}
                            @error('fechaasignada')
                                <small class="text-danger fas fa-exclamation-circle">
                                    {{ $message }}
                                </small>
                            @enderror
                        </div>
                        <div class="form-group">
                            {!! Form::label('horadesde', 'Hora Desde:') !!}
                            {!! Form::time('horadesde', null, ['class' => 'form-control', 'placeholder' => '', 'id' => 'horadesde']) !!}
                            @error('horadesde')
                                <small class="text-danger fas fa-exclamation-circle">
                                    {{$message}}
                                </small>
                            @enderror
                        </div>
                        <div class="form-group">
                            {!! Form::label('horahasta', 'Hora Hasta:') !!}
                            {!! Form::time('horahasta', null, ['class' => 'form-control', 'placeholder' => '', 'id' => 'horahasta']) !!}
                            @error('horahasta')
                                <small class="text-danger fas fa-exclamation-circle">
                                    {{$message}}
                                </small>
                            @enderror
                        </div>
                    </div>
                @else
                    <div class="alert " role="alert">
                        ESTE CLIENTE NO TIENE BATERIA
                    </div>
                @endif 
                {!! Form::submit('PROGRAMAR', ['class' => 'btn btn-verdegrande', 'style' => 'margin-top: 30px;']) !!}
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>
@stop

<div class="modal fade" id="ventanaModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">PROGRAMACIONES DEL CLIENTE:</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <strong>Fecha de Batería:</strong>
                <select id="select-fechas" class="form-control">
                    <option value="">Seleccione una fecha</option>
                    @foreach ($fechasBateria as $fecha)
                        <option value="{{ $fecha }}">{{ $fecha }}</option>
                    @endforeach
                </select>
                <form id="update-form" method="post"
                    action="{{ route('admin.asociados.actualizarproveedorfecha', ['cliente' => $cliente->id]) }}">
                    @csrf
                    <div id="acciones-container" class="mt-3 table-responsive">
                        <strong>Acciones programadas:</strong>
                        <table class="table mt-3 table-striped table-bordered" id="acciones-table">
                            <thead class="table-secondary">
                                <tr>
                                    <th>ID</th>
                                    <th>Estudio / Especialidad</th>
                                    <th>Proveedor</th>
                                    <th>Fecha Asignada</th>
                                    <th>Hora Asignada</th>
                                    @if (!$esProveedor)
                                        <th>Precio</th>
                                    @endif
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="acciones-lista"></tbody>
                        </table>
                    </div>
                </form>
            </div>

            <script>
                document.getElementById('select-fechas').addEventListener('change', function() {
                    const fechaSeleccionada = this.value;
                    const accionesBateria = @json($accionesPorFechaBateria);
                    const accionesDetalles = @json($accionesDetallesPorFecha);
                    const esProveedor = @json($esProveedor);
                    const accionesLista = document.getElementById('acciones-lista');
                    const accionesTable = document.getElementById('acciones-table');
                    accionesLista.innerHTML = '';
                    let hasProveedorAjeno = false;
            
                    if (fechaSeleccionada && accionesBateria[fechaSeleccionada]) {
                        accionesBateria[fechaSeleccionada].forEach(function(accionNombre) {
                            const detalles = accionesDetalles[fechaSeleccionada]?.[accionNombre];
                            if (detalles && detalles.proveedornombre === 'PROVEEDOR AJENO') {
                                hasProveedorAjeno = true;
                            }
                        });
                        let tableHeader = `
                            <tr>
                                <th>ID</th>
                                <th>Estudio / Especialidad</th>
                                <th>Proveedor</th>
                                <th>Fecha Asignada</th>
                                <th>Hora Asignada</th>
                                ${!esProveedor ? `<th>Precio</th>` : ''}
                                ${hasProveedorAjeno ? `<th>Acciones</th>` : ''}
                            </tr>
                        `;
                        accionesTable.querySelector('thead').innerHTML = tableHeader;
                        accionesBateria[fechaSeleccionada]
                        .filter(accion => accion !== 'INFORME FINAL')
                        .forEach(function(accionNombre) {
                            const detalles = accionesDetalles[fechaSeleccionada]?.[accionNombre];
                            const row = document.createElement('tr');
                            if (detalles) {
                                const isProveedorAjeno = detalles.proveedornombre === 'PROVEEDOR AJENO';
                                let accionesColumn = '';
                                if (hasProveedorAjeno) {
                                    accionesColumn = `
                                        <td>
                                            ${isProveedorAjeno ? `
                                                <button type="button" class="btn btn-sm btn-edit">
                                                    <i class="fas fa-edit icono-editar"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-save" style="display: none;">
                                                    <i class="fas fa-save icono-guardar"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-cancel" style="display: none;">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            ` : ''}
                                        </td>
                                    `;
                                }
                                let precioColumn = '';
                                if (!esProveedor) {
                                    precioColumn = `<td>${detalles.precio || 'No registrado'}</td>`;
                                }
                                row.innerHTML = `
                                    <td>${detalles.id || '0'}</td>
                                    <td>${accionNombre}</td>
                                    <td>
                                        <span class="valor-proveedor">${detalles.proveedornombre || 'No registrado'}</span>
                                        <input type="text" name="proveedor" class="input-proveedor form-control" value="${detalles.proveedornombre || ''}" style="display: none;" />
                                    </td>
                                    <td>
                                        <span class="valor-fecha">${detalles.fechaasignada || 'No registrado'}</span>
                                        <input type="date" name="fechaasignada" class="input-fecha form-control" value="${detalles.fechaasignada || ''}" style="display: none;" />
                                    </td>
                                    <td>${detalles.horadesde || 'No registrado'} - ${detalles.horahasta || 'No registrado'}</td>
                                    ${precioColumn}
                                    ${accionesColumn}
                                `;
                                row.style.color = 'green';
                            } else {
                                let accionesColumn = hasProveedorAjeno ? `<td></td>` : '';
                                let precioColumn = !esProveedor ? `<td>No registrado</td>` : '';
                                row.innerHTML = `
                                    <td>0</td>
                                    <td>${accionNombre}</td>
                                    <td>No registrado</td>
                                    <td>No registrado</td>
                                    <td>No registrado</td>
                                    ${precioColumn}
                                    ${accionesColumn}
                                `;
                                row.style.color = 'red';
                            }
                            accionesLista.appendChild(row);
                        });
                    } else {
                        accionesTable.querySelector('thead').innerHTML = '';
                        accionesLista.innerHTML = '';
                    }
                });
            </script>
            <div class="modal-footer">
                <button type="button" class="btn btn-rojogrande" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

@section('js')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script type="text/javascript" src="https://jeremyfagis.github.io/dropify/dist/js/dropify.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://jeremyfagis.github.io/dropify/dist/css/dropify.min.css"> 
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
    document.getElementById('acciones-lista').addEventListener('click', function(event) {

    const target = event.target;
    const row = target.closest('tr');
    if (target.closest('.btn-edit')) {
        row.querySelector('.valor-proveedor').style.display = 'none';
        row.querySelector('.input-proveedor').style.display = 'block';
        row.querySelector('.valor-fecha').style.display = 'none';
        row.querySelector('.input-fecha').style.display = 'block';
        row.querySelector('.btn-edit').style.display = 'none';
        row.querySelector('.btn-save').style.display = 'inline-block';
    } else if (target.closest('.btn-save')) {
        const id = row.cells[0].textContent.trim();
        const proveedor = row.querySelector('.input-proveedor').value;
        const fechaAsignada = row.querySelector('.input-fecha').value;

    fetch('{{ route('admin.asociados.actualizarproveedorfecha', ['cliente' => $cliente->id]) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                id: id,
                proveedor: proveedor,
                fechaasignada: fechaAsignada
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                row.querySelector('.valor-proveedor').textContent = proveedor;
                row.querySelector('.valor-fecha').textContent = fechaAsignada;
                row.querySelector('.valor-proveedor').style.display = 'inline';
                row.querySelector('.input-proveedor').style.display = 'none';
                row.querySelector('.valor-fecha').style.display = 'inline';
                row.querySelector('.input-fecha').style.display = 'none';
                row.querySelector('.btn-edit').style.display = 'inline-block';
                row.querySelector('.btn-save').style.display = 'none';
                alert('Los cambios se han guardado correctamente.');

                if (proveedor === 'PROVEEDOR AJENO') {
                    row.querySelector('.btn-edit').style.display = 'inline-block';
                } else {
                    row.querySelector('.btn-edit').remove();
                }

            } else {
                alert('Error al guardar los cambios.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al guardar los cambios.');
        });
    }
    });
</script>

<script>
    document.querySelector('.horariodisponible').addEventListener('change', function() {
        var horarioSeleccionado = this.value;
        document.getElementById('horaasignada').value = horarioSeleccionado;
    });
</script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    $(document).ready(function() {
        $('select[name^="proveedor_"]').change(function() {
            var selectedOption = $(this).val();
            $('.row').find('select').not('.no-bloquear').not(this).prop('disabled', selectedOption !== '');
            
            if ($('select:disabled').not('.no-bloquear').length > 0) {
                $('#habilitarSelectores').show();
            } else {
                
            }
        });

        $('#habilitarSelectores').click(function() {
            var $selectores = $('.row').find('select').not('.no-bloquear');
            var fechaSeleccionada = $('#fecha_bateria').val();

            $selectores.prop('disabled', false);
            $selectores.val('');

            $('#horariosdisponibles').val('');
            $('#proveedornombre').val('');
            $('#horaasignada').val('');
            $('#horarioinicial').val('');
            $('#horariofinal').val('');
            $('#tiempoatencion').val('');
            $('#accionnombre').val('');
            $('#areanombre').val('');
            $('#precio').val('');
            $('#preciocompra').val('');
            $('#fecha_bateria').val(fechaSeleccionada);
            $(this).hide();
        });
    });
</script>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script type="text/javascript">
    $(document).ready(function(){
    function actualizarCamposYHorariosDisponibles(proveedorData) {
        var nombreProveedor = proveedorData[0];
        var horarioInicial = proveedorData[1].split(' - ')[0];
        var horarioFinal = proveedorData[1].split(' - ')[1].slice(0, -1);
        var tiempoAtencion = proveedorData[2].split(')')[0];
        var accionnombre = proveedorData[3].split(')')[0];
        var areanombre = proveedorData[4].split(')')[0];
        var precio = proveedorData[5].split(')')[0];
        var preciocompra = proveedorData[6].split(')')[0];

        $('#proveedornombre').val(nombreProveedor);
        $('#horarioinicial').val(horarioInicial);
        $('#horariofinal').val(horarioFinal);
        $('#tiempoatencion').val(tiempoAtencion);
        $('#accionnombre').val(accionnombre);
        $('#areanombre').val(areanombre);
        $('#precio').val(precio);
        $('#preciocompra').val(preciocompra);
        actualizarHorariosDisponibles();
    }

    $('select[name^="proveedor_"]').change(function(){
        var proveedorData = $(this).find('option:selected').text().split(' (');
        actualizarCamposYHorariosDisponibles(proveedorData);
    });
    var proveedorData = $('select[name^="proveedor_"]').find('option:selected').text().split(' (');
    actualizarCamposYHorariosDisponibles(proveedorData);
    });
    function dividirHorario(horaInicial, horaFinal, tiempoAtencion) {
        var horarios = [];
        var hora = horaInicial;
        while (hora <= horaFinal) {
            var horaFinAtencion = sumarTiempo(hora, tiempoAtencion);
            if (horaFinAtencion <= horaFinal) {
                horarios.push(formatoHora(hora));
            }
            hora = horaFinAtencion;
        }
        return horarios;
    }
    function formatoHora(hora) {
        return hora.split(':').slice(0, 2).join(':');
    }
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
        if (horas > parseInt(document.getElementById('horariofinal').value.split(':')[0])) {
            horas = parseInt(document.getElementById('horariofinal').value.split(':')[0]);
            minutos = parseInt(document.getElementById('horariofinal').value.split(':')[1]);
        }
        return (horas < 10 ? '0' : '') + horas + ':' + (minutos < 10 ? '0' : '') + minutos;
    }
    function actualizarHorariosDisponibles() {
        var horarioInicial = document.getElementById('horarioinicial').value;
        var horarioFinal = document.getElementById('horariofinal').value;
        var tiempoAtencion = document.getElementById('tiempoatencion').value;
        var accionnombre = document.getElementById('accionnombre').value;
        var areanombre = document.getElementById('areanombre').value;
        var precio = document.getElementById('precio').value;
        var preciocompra = document.getElementById('preciocompra').value;
        var selectHorariosDisponibles = document.querySelector('.horariodisponible');

        var horarios = dividirHorario(horarioInicial, horarioFinal, tiempoAtencion);
        selectHorariosDisponibles.innerHTML = '';
        var option = document.createElement('option');
        option.text = '';
        selectHorariosDisponibles.add(option);

        horarios.forEach(function(horario) {
            var option = document.createElement('option');
            option.text = horario;
            selectHorariosDisponibles.add(option);
        });
    }

    window.addEventListener('load', function() {
        actualizarHorariosDisponibles();
    });
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
    document.getElementById('preciocompra').addEventListener('input', function() {
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