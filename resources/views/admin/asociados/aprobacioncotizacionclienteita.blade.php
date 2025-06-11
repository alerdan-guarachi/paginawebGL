@extends('adminlte::page')

@section('content_header')
<a class="btn btn-sm float-right btn-regresar" href="{{ route('admin.asociados.verclienteita', $cliente) }}">REGRESAR</a>
<a class="btn btn-sm float-right btn-aprobarcotizacion" href="{{ route('admin.asociados.aprobarcotizacionprogramacionclienteita', $cliente) }}">APROBAR COTIZACION</a>
<a class="btn btn-sm float-right btn-crear2" href="{{ route('admin.asociados.generarpdfcotizacionclienteita', [ 
    'cliente' => $cliente->id,
    'buscarporfecha' => $fechaSeleccionada,
    'buscarporservicio' => $servicioSeleccionado,
    'buscarporarea' => implode(',', $areasSeleccionadas),
    'total' => $total
]) }}" onclick="confirmarGeneracionPdf(event)">GENERAR PDF</a>

<script>
    function confirmarGeneracionPdf(event) {
        event.preventDefault();

        const confirmacion = confirm("Posteriormente también debe generar el consentimiento informado para la realización de evaluaciones y estudios médicos adicionales.");
        if (confirmacion) {

            window.location.href = event.target.href;
        }
    }
</script>

<h5>COTIZACIÓN DE PROGRAMACIÓN DE:</h5>
<h3>{{$cliente->nombrecompleto}}</h3>
@stop

@section('css')
<link rel="stylesheet" href="{{ asset('css/cotizacionmedicaclientes.css') }}">
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
<div class="card"> 
    <div class="card-body">
        
        <nav class="navbar navbar-expand-lg float-right">
            <div class="container-fluid">
                <div class="d-flex flex-wrap align-items-center">
                    <form action="{{ route('buscarbateriaclienteita', $cliente) }}" method="get" class="form-inline">
                        <div class="flex-grow-1">
                            <div class="form-row align-items-center">
                                <div class="col-auto">
                                    <label for="buscarporservicio">Servicio:</label>
                                </div>
                                <div class="col-auto">
                                    <select name="buscarporservicio" class="form-control mr-sm-2" id="buscarporservicio"> 
                                        <option value="" disabled selected></option>
                                        <option value="INTERNO">INTERNO</option>
                                        <option value="EXTERNO">EXTERNO</option>
                                        <option value="AJENO">AJENO</option>
                                    </select>
                                </div>
                                
                                <div class="col-auto">
                                    <label for="buscarporfecha">Fecha de Batería:</label>
                                </div>
                                <div class="col-auto">
                                    <select name="buscarporfecha" class="form-control mr-sm-2" id="select-fecha">
                                        <option value="" disabled selected></option>
                                        @foreach($fechas as $fecha)
                                            <option value="{{ $fecha }}" {{ $fechaSeleccionada == $fecha ? 'selected' : '' }}>
                                                {{ $fecha }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-auto">
                                    <label for="buscarporarea">Est/Esp::</label>
                                </div>
                                <div class="col-auto">
                                    <select name="buscarporarea[]" class="form-control mr-sm-2" id="select-area" multiple>
                                    </select>
                                </div>
                            </div>
                            
                        </div>
                        <input type="hidden" name="total" id="total" value="{{ $total }}">
                        <button id="btn-buscar" class="btn btn-buscar my-2 my-sm-0" type="submit"><i class="fas fa-search"></i></button>
                    </form>
                </div>
            </div>
        </nav>
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            const selectFecha = document.getElementById('select-fecha');
            const selectArea = document.getElementById('select-area');
            const areasPorFecha = @json($areasPorFecha);
            function actualizarAreas(fecha) {
                if (!fecha || !areasPorFecha[fecha]) {
                    selectArea.innerHTML = '<option value="" disabled selected>Área</option>';
                    return;
                }
        
                const areas = areasPorFecha[fecha];
                selectArea.innerHTML = '';
                areas.forEach(area => {
                    const option = document.createElement('option');
                    option.value = area;
                    option.textContent = area;
                    selectArea.appendChild(option);
                });
                @foreach($areasSeleccionadas as $selectedArea)
                    selectArea.querySelector(`option[value="{{ $selectedArea }}"]`).selected = true;
                @endforeach
            }
            if (selectFecha.value) {
                actualizarAreas(selectFecha.value);
            }
            selectFecha.addEventListener('change', function() {
                actualizarAreas(this.value);
            });
        });
        </script>
        
        @if($bateriasubclientes->isEmpty())
        @else
            <div class="table-responsive">
                <table class="table table-striped" id="tablaPrecios">
                    <thead>
                        <tr>
                            <th>Fecha de Bateria</th>
                            <th>Tipo de área</th>
                            <th>Proveedor</th>
                            <th>Acción</th>
                            <th>Informe</th>
                            <th>Servicio</th>
                            <th>Precio</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($bateriasubclientes as $bateriasubcliente)
                        <tr>
                            <td>{{$bateriasubcliente->fechabateria}}</td>
                            <td>{{$bateriasubcliente->tipoarea}}</td>
                            <td>{{$bateriasubcliente->proveedorasignado}}</td>
                            <td>{{$bateriasubcliente->accionnombre}}</td>
                            <td>{{$bateriasubcliente->informe}}</td>
                            <td>{{$bateriasubcliente->servicio}}</td>
                            <td class="precio" >{{$bateriasubcliente->precio}}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const rows = document.querySelectorAll('#tablaPrecios tbody tr');
                
                    rows.forEach(row => {
                        const informeCell = row.querySelector('td:nth-child(5)');
                        const precioCell = row.querySelector('td.precio');
                
                        if (informeCell && precioCell) {
                            const informeValue = informeCell.textContent.trim();
                            if (informeValue === 'SI TIENE INFORME') {
                                precioCell.textContent = '0';
                            }
                        }
                    });
                });
            </script>
        @endif
    </div>
</div>

@stop
@section('js')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script type="text/javascript" src="https://jeremyfagis.github.io/dropify/dist/js/dropify.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://jeremyfagis.github.io/dropify/dist/css/dropify.min.css"> 
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
    $(document).ready(function() {
        $('input[name="buscarporfecha"], input[name="buscarporarea"], input[name="buscarporservicio"]').on('keyup change', function() {
            var fechaSeleccionada = $('input[name="buscarporfecha"]').val();
            var areaSeleccionada = $('input[name="buscarporarea"]').val();
            var areaSeleccionada = $('input[name="buscarporservicio"]').val();
            var botonBuscar = $('#btn-buscar');
            
            if (fechaSeleccionada.trim() === '' && areaSeleccionada.trim() === '') {
                botonBuscar.prop('disabled', true);
            } else {
                botonBuscar.prop('disabled', false);
            }
        });
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var filas = document.querySelectorAll('#tablaPrecios tbody tr');
        var total = 0;
    
        filas.forEach(function(fila) {
            var informeCell = fila.querySelector('td:nth-child(5)');
            var precioCell = fila.querySelector('td.precio');
    
            if (informeCell && precioCell) {
                var informeValue = informeCell.textContent.trim();
                if (informeValue !== 'SI TIENE INFORME') {
                    var precio = parseFloat(precioCell.textContent.replace(',', '.'));
                    if (!isNaN(precio)) {
                        total += precio;
                    }
                }
            }
        });
    
        var totalFormateado = total.toLocaleString('es-ES', { minimumFractionDigits: 2 });
        var filaTotal = '<tr style="background-color: #fcecd4;"><td colspan="6">TOTAL</td><td>' + totalFormateado + '</td></tr>';
        document.querySelector('#tablaPrecios tbody').insertAdjacentHTML('beforeend', filaTotal);
    });
</script>
    
@endsection
