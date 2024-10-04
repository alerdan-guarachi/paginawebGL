@extends('adminlte::page')

@section('content_header')
<a class="btn btn-sm float-right btn-regresar" href="{{ route('admin.asociados.verclienteita', $cliente) }}">REGRESAR</a>
<a class="btn btn-sm float-right btn-aprobarcotizacion" href="{{ route('admin.asociados.aprobarcotizacionprogramacionclienteita', $cliente) }}">APROBAR COTIZACION</a>
<a class="btn btn-sm float-right btn-crear" href="{{ route('admin.asociados.generarpdfcotizacionclienteita', [ 
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
    

{{-- <!-- SweetAlert2 CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    function confirmarGeneracionPdf(event) {
        event.preventDefault(); // Evita la redirección inmediata
        
        Swal.fire({
            title: 'AVISO',
            text: "POSTERIORMENTE TAMBIÉN DEBE GENERAR EL CONSENTIMIENTO INFORMADO PARA LA REALIZACIÓN DE EVALUACIONES Y ESTUDIOS MÉDICOS ADICIONALES",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Entendido',
            cancelButtonText: 'Cancelar',
            customClass: {
                confirmButton: 'btn-entendido', // Clase personalizada para el botón "Entendido"
                cancelButton: 'btn-cancelar'    // Clase personalizada para el botón "Cancelar"
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // Si el usuario confirma, redirige a la ruta para generar el PDF
                window.location.href = "{{ route('admin.asociados.generarpdfcotizacionclienteita', [
                    'cliente' => $cliente->id,
                    'buscarporfecha' => $fechaSeleccionada,
                    'buscarporservicio' => $servicioSeleccionado,
                    'buscarporarea' => implode(',', $areasSeleccionadas),
                    'total' => $total
                ]) }}";
            }
        });
    }
</script> --}}
<style>
    .btn-entendido {
        background-color: #6acf81; /* Verde */
        color: white;
    }

    .btn-cancelar {
        background-color: #f36573; /* Rojo */
        color: white;
    }
</style>



{{-- {!! Form::open(['route' => 'generar.pdf.consentimientoinformado', 'method' => 'post', 'enctype' => 'multipart/form-data']) !!}
    <a class="btn btn-consentimientoinformado btn-sm float-right" href="#" onclick="event.preventDefault(); this.closest('form').submit();">CONS. INFORMADO</a>
    {!! Form::hidden('clienteitaid', $cliente->id, ['class' => 'form-control']) !!}
    {!! Form::hidden('nombres', $cliente->nombres, ['class' => 'form-control']) !!}
    {!! Form::hidden('apepaterno', $cliente->apepaterno, ['class' => 'form-control']) !!}
    {!! Form::hidden('apematerno', $cliente->apematerno, ['class' => 'form-control']) !!}
{!! Form::close() !!} --}}

<h5>COTIZACIÓN DE PROGRAMACIÓN DE:</h5>
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
                            {{-- <select name="buscarporservicio" class="form-control mr-sm-2" id="">
                                <option value="" disabled selected>Servicio</option>
                                    <option value="INTERNO">INTERNO</option>
                                    <option value="EXTERNO">EXTERNO</option>
                                    <option value="AJENO">AJENO</option>
                            </select>
                            <select name="buscarporfecha" class="form-control mr-sm-2" id="select-fecha">
                                <option value="" disabled selected>Fecha de Bateria</option>
                                @foreach($fechas as $fecha)
                                    <option value="{{ $fecha }}" {{ $fechaSeleccionada == $fecha ? 'selected' : '' }}>
                                        {{ $fecha }}
                                    </option>
                                @endforeach
                            </select>
                            <select name="buscarporarea[]" class="form-control mr-sm-2" id="select-area" multiple>
                            </select> --}}

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
                        <button id="btn-buscar" class="btn btn-buscar my-2 my-sm-0" type="submit">Buscar</button>
                    </form>
                </div>
            </div>
        </nav>
        <style>
            #select-area {
                width: 200px; /* Ajusta el ancho aquí */
                height: 100px; /* Ajusta la altura aquí */
                overflow-y: auto; /* Agrega una barra de desplazamiento si es necesario */
            }
        </style>
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            const selectFecha = document.getElementById('select-fecha');
            const selectArea = document.getElementById('select-area');
        
            // Crear un mapa de áreas por fecha desde los datos del backend
            const areasPorFecha = @json($areasPorFecha);
        
            // Función para actualizar las opciones del select de área basado en la fecha seleccionada
            function actualizarAreas(fecha) {
                if (!fecha || !areasPorFecha[fecha]) {
                    selectArea.innerHTML = '<option value="" disabled selected>Área</option>';
                    return;
                }
        
                const areas = areasPorFecha[fecha];
                selectArea.innerHTML = ''; // Limpiar opciones existentes
                
                areas.forEach(area => {
                    const option = document.createElement('option');
                    option.value = area;
                    option.textContent = area;
                    selectArea.appendChild(option);
                });
        
                // Seleccionar las áreas previamente seleccionadas si existen
                @foreach($areasSeleccionadas as $selectedArea)
                    selectArea.querySelector(`option[value="{{ $selectedArea }}"]`).selected = true;
                @endforeach
            }
        
            // Actualizar áreas cuando se carga la página si ya hay una fecha seleccionada
            if (selectFecha.value) {
                actualizarAreas(selectFecha.value);
            }
        
            // Actualizar áreas cuando cambia la fecha seleccionada
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

{{-- <script>
    var precios = document.querySelectorAll('.precio');
    var total = 0;
    precios.forEach(function(precio) {
        total += parseFloat(precio.textContent);
    });
    var totalFormateado = total.toLocaleString('es-ES', { minimumFractionDigits: 2 });
    var filaTotal = '<tr style="background-color: #fcecd4;"><td colspan="5">TOTAL</td><td>' + totalFormateado + '</td></tr>';
    document.querySelector('#tablaPrecios tbody').insertAdjacentHTML('beforeend', filaTotal);
</script> --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var filas = document.querySelectorAll('#tablaPrecios tbody tr');
        var total = 0;
    
        filas.forEach(function(fila) {
            var informeCell = fila.querySelector('td:nth-child(5)'); // La celda del informe
            var precioCell = fila.querySelector('td.precio'); // La celda del precio
    
            if (informeCell && precioCell) {
                var informeValue = informeCell.textContent.trim();
                
                // Solo incluir en el total si el informe NO es "SI TIENE INFORME"
                if (informeValue !== 'SI TIENE INFORME') {
                    var precio = parseFloat(precioCell.textContent.replace(',', '.')); // Convertir el precio a número
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

@section('css')
<link rel="styleheet" href="/css/admin_custom.css">
<style>
    .btn-consentimientoinformado {
        background-color: #ffffff;
        color: #5db2cd;
        border-color: #5db2cd;
        border-radius: 5px;
        padding: 10px 20px;
        margin-left: 10px;
    }
    .btn-consentimientoinformado:hover {
        background-color: #5db2cd;
        color: #ffffff;
    }
    .btn-aprobarcotizacion {
        background-color: #ffffff;
        color: #faa625;
        border-color: #faa625;
        border-radius: 5px;
        padding: 10px 20px;
        margin-right: 10px;
    }
    .btn-aprobarcotizacion:hover {
        background-color: #faa625;
        color: #ffffff;
    }
    .btn-buscar { 
        background-color:  #ffffff;
        color: #faa625;
        border-color: #faa625;
        border-radius: 5px;
    }
    .btn-buscar:hover {
        background-color: #faa625;
        color: #ffffff;
    } 
    .total-row {
        background-color: #94c93b;
    }
    .hidden-field {
        display: none;
    }
    th {color:#94c93b; 
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
        margin-left: 10px;
        margin-right: 10px;
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
        padding: 5px 50px;

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