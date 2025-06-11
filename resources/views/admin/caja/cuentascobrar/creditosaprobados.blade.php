@extends('adminlte::page')
    
@section('content_header')
<h1>CRÉDITOS APROBADOS</h1>
@stop 

@section('css')
<link rel="stylesheet" href="{{ asset('css/opcionesmultiples.css') }}">
<style>
    h1, th {
        color:#000000; 
        font-family: "Segoe UI";
        font-weight: 700;
    }
    .table td {
        padding: 5px 10px;;
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
</style>
@stop
@section('content')
@if (session('info'))
    <div id="alert-info" class="alert alert-success">
        <strong>{{ session('info') }}</strong>
    </div>
    <script>
        setTimeout(function() {
            $('#alert-info').fadeOut('fast');
        }, 3000);
    </script>
@endif

<div class="card">
    <div class="card-header">
        <ul class="nav nav-tabs card-header-tabs" id="myTabs">
            <li class="nav-item">
                <a class="nav-link active" id="tab-1" data-toggle="tab" href="#tab-content-1" role="tab" aria-controls="tab-content-1" aria-selected="true">
                    CRÉDITOS APROBADOS
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="tab-2" data-toggle="tab" href="#tab-content-2" role="tab" aria-controls="tab-content-2" aria-selected="true">
                    CRÉDITOS DE HOY
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="tab-3" data-toggle="tab" href="#tab-content-3" role="tab" aria-controls="tab-content-3" aria-selected="true">
                    CRÉDITOS PROCESADOS
                </a>
            </li>
        </ul>
    </div>
    <div class="card-body">
        <div class="tab-content" id="myTabContent">
            
            {{-- CREDITOS PROXIMOS --}}
            <div class="tab-pane fade show active" id="tab-content-1" role="tabpanel" aria-labelledby="tab-1"> 
                <div class="table-responsive">
                    <form action="{{ route('creditos.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>ID_Bat.</th>
                                    <th>ID_Cli.</th>
                                    <th>Cliente</th>
                                    <th>Proveedor</th>
                                    <th>Fecha_Crédito</th>
                                    <th>Monto_Cuota</th>
                                    <th>Usu_Autoriza.</th>
                                    <th>Doc._Respaldos</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    use Carbon\Carbon;
                                    $fechaActual = Carbon::now()->toDateString();
                                @endphp
                                @foreach ($creditos as $credito)
                                    {{-- @if ($credito->fechacredito > $fechaActual) --}}
                                        @if (!is_null($credito->cartacredito) || $credito->cartacredito === '')
                                            @if ($credito->estado === 'PENDIENTE' || is_null($credito->estado))
                                                <tr data-bateriaid="{{ $credito->bateriaid }}">
                                                    <td>{{ $credito->id }}</td>
                                                    <td>{{ $credito->bateriaid ?? 0}}</td>
                                                    <td>{{ $credito->clienteid }}</td>
                                                    <td>{{ $credito->clientenombre }}</td>
                                                    <td>{{ $credito->proveedor }}</td>
                                                    <td>
                                                        <input type="date" name="fechacredito[{{ $credito->id }}]" 
                                                               value="{{ $credito->fechacredito }}" 
                                                               class="form-control">
                                                    </td>
                                                    <td>{{ $credito->montocuota }}</td>
                                                    <td>{{ $credito->usuarioautorizador }}</td>
                                                    <td>
                                                        @if ($credito->docrespaldo)
                                                            <a href="{{ asset('creditos/' . $credito->clienteid . '/' . $credito->docrespaldo) }}" class="btn btn-sm btn-outline-success" target="_blank" title="VER RESPALDO">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                        @else
                                                            <span class="badge badge-danger">VACIO</span>
                                                        @endif
                                
                                                        @if ($credito->letracambio)
                                                            <a href="{{ asset('creditos/' . $credito->clienteid . '/' . $credito->letracambio) }}" class="btn btn-sm btn-outline-success" target="_blank" title="VER LETRA DE CAMBIO">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                        @else
                                                            <span class="badge badge-danger">VACIO</span>
                                                        @endif
                                                        @if ($credito->cartacredito)
                                                            <a href="{{ asset('creditos/' . $credito->clienteid . '/' . $credito->cartacredito) }}" class="btn btn-sm btn-outline-success" target="_blank" title="VER CARTA DE CRÉDITO">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                        @else
                                                            <span class="badge badge-danger">VACIO</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <p><span class="badge bg-warning">PENDIENTE</span></p>
                                                    </td>
                                                </tr>
                                            @endif
                                        @endif
                                    {{-- @endif --}}
                                @endforeach
                            </tbody>
                        </table>
                        <button type="submit" class="btn btn-sm btn-outline-secondary mt-2">ACTUALIZAR FECHA</button>
                    </form>
                </div>
            </div>
            
            {{-- CREDITOS EN MORA --}}
            <div class="tab-pane fade" id="tab-content-2" role="tabpanel" aria-labelledby="tab-2">  
                <div class="table-responsive">
                    <form action="{{ route('creditos.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>ID_Bat.</th>
                                <th>ID_Cli.</th>
                                <th>Cliente</th>
                                {{-- <th>Detalle</th> --}}
                                <th>Proveedor</th>
                                <th>Fecha_Crédito</th>
                                <th>Monto_Cuota</th>
                                <th>Usu_Autoriza.</th>
                                <th>Doc._Respaldos</th>
                                {{-- <th>Estado</th> --}}
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($creditos as $credito)
                                @if ($credito->fechacredito == $fechaActual)
                                    @if (!is_null($credito->cartacredito) || $credito->cartacredito === '')
                                        @if ($credito->estado === 'PENDIENTE' || is_null($credito->estado))
                                        <tr data-bateriaid="{{ $credito->bateriaid }}">
                                            <td>{{ $credito->id }}</td>
                                            <td>{{ $credito->bateriaid ?? 0}}</td>
                                            <td>{{ $credito->clienteid }}</td>
                                            <td>{{ $credito->clientenombre }}</td>
                                            {{-- <td>{{ $credito->detalle }}</td> --}}
                                            <td>{{ $credito->proveedor }}</td>
                                            <td>
                                                <input type="date" name="fechacredito[{{ $credito->id }}]" 
                                                       value="{{ $credito->fechacredito }}" 
                                                       class="form-control">
                                            </td>
                                            <td>{{ $credito->montocuota }}</td>
                                            <td>{{ $credito->usuarioautorizador }}</td>
                                            <td>
                                                @if ($credito->docrespaldo)
                                                    <a href="{{ asset('creditos/' . $credito->clienteid . '/' . $credito->docrespaldo) }}" class="btn btn-sm btn-outline-success" target="_blank" title="VER RESPALDO">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                @else
                                                    <span class="badge badge-danger">VACIO</span>
                                                @endif
                        
                                                @if ($credito->letracambio)
                                                    <a href="{{ asset('creditos/' . $credito->clienteid . '/' . $credito->letracambio) }}" class="btn btn-sm btn-outline-success" target="_blank" title="VER LETRA DE CAMBIO">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                @else
                                                    <span class="badge badge-danger">VACIO</span>
                                                @endif
                                                @if ($credito->cartacredito)
                                                    <a href="{{ asset('creditos/' . $credito->clienteid . '/' . $credito->cartacredito) }}" class="btn btn-sm btn-outline-success" target="_blank" title="VER CARTA DE CRÉDITO">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                @else
                                                    <span class="badge badge-danger">VACIO</span>
                                                @endif
                                            </td>
                                            {{-- <td>
                                                @php
                                                    $diasRetraso = \Carbon\Carbon::parse($credito->fechacredito)->diffInDays($fechaActual);
                                                @endphp
                                                <p><span class="badge bg-danger">MORA: {{ $diasRetraso }} días</span></p>
                                            </td> --}}
                                        </tr>
                                        @endif
                                    @endif
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                    <button type="submit" class="btn btn-sm btn-outline-secondary mt-2">ACTUALIZAR FECHA</button>
                    </form>
                </div>
            </div>

            {{-- CRÉDITOS PROCESADOS --}}
            <div class="tab-pane fade" id="tab-content-3" role="tabpanel" aria-labelledby="tab-3"> 
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>ID_Bat.</th>
                                <th>ID_Cli.</th>
                                <th>Cliente</th>
                                {{-- <th>Detalle</th> --}}
                                <th>Proveedor</th>
                                <th>Fecha_Crédito</th>
                                <th>Monto_Cuota</th>
                                <th>Usu_Autoriza.</th>
                                <th>Fecha_Cobro</th>
                                <th>Doc._Respaldos</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($creditos as $credito)
                            @if (!is_null($credito->cartacredito) || $credito->cartacredito === '')
                                @if ($credito->estado === 'PROCESADO')
                                <tr data-bateriaid="{{ $credito->bateriaid }}">
                                    <td>{{ $credito->id }}</td>
                                    <td>{{ $credito->bateriaid ?? 0}}</td>
                                    <td>{{ $credito->clienteid }}</td>
                                    <td>{{ $credito->clientenombre }}</td>
                                    {{-- <td>{{ $credito->detalle }}</td> --}}
                                    <td>{{ $credito->proveedor }}</td>
                                    <td>{{ $credito->fechacredito }}</td>
                                    <td>{{ $credito->montocuota }}</td>
                                    <td>{{ $credito->usuarioautorizador }}</td>
                                    <td>{{ \Carbon\Carbon::parse($credito->updated_at)->toDateString() }}</td>
                                    <td>
                                        @if ($credito->docrespaldo)
                                            <a href="{{ asset('creditos/' . $credito->clienteid . '/' . $credito->docrespaldo) }}" class="btn btn-sm btn-outline-success" target="_blank" title="VER RESPALDO">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        @else
                                            <span class="badge badge-danger">VACIO</span>
                                        @endif
                
                                        @if ($credito->letracambio)
                                            <a href="{{ asset('creditos/' . $credito->clienteid . '/' . $credito->letracambio) }}" class="btn btn-sm btn-outline-success" target="_blank" title="VER LETRA DE CAMBIO">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        @else
                                            <span class="badge badge-danger">VACIO</span>
                                        @endif
                                        @if ($credito->cartacredito)
                                            <a href="{{ asset('creditos/' . $credito->clienteid . '/' . $credito->cartacredito) }}" class="btn btn-sm btn-outline-success" target="_blank" title="VER CARTA DE CRÉDITO">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        @else
                                            <span class="badge badge-danger">VACIO</span>
                                        @endif
                                    </td>
                                    <td>
                                        <p><span class="badge bg-success">{{ $credito->estado }}</span></p>
                                    </td>
                                </tr>
                                @endif
                            @endif
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>


@stop

@section('js')
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script type="text/javascript" src="https://jeremyfagis.github.io/dropify/dist/js/dropify.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://jeremyfagis.github.io/dropify/dist/css/dropify.min.css"> 
<script>
$('.dropify').dropify();
</script>
    @if (session('eliminar')=='ok')
    <script>
        Swal.fire(
      '¡Eliminado!',
      'El perfil se eliminó con éxito',
      'success')
    </script>
    @endif

<script>
    $('.formulario-eliminar').submit(function(e){
        e.preventDefault();

        Swal.fire({
        title: '¿Estás seguro?',
        text: "Este perfil se eliminará definitivamente",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: '¡Si, eliminar!',
        cancelButtonText: 'Cancelar'
        }).then((result) => {
        if (result.isConfirmed) {
            this.submit();
        }
        }) 
    });
    $(document).ready(function() {
        $('input[name="buscarpor"]').on('keyup', function() {
            var query = $(this).val();
            var botonBuscar = $('#btn-buscar');
            if (query.trim() === '') {
                botonBuscar.prop('disabled', true);
            } else {
                botonBuscar.prop('disabled', false);
            }
        });
    });
</script>
<script>
    const btnAsignarCredito = document.getElementById('btnAsignarCredito');
    const checkboxes = document.querySelectorAll('.checkbox-seleccion');
    const fechas = document.querySelectorAll('.campo-fecha');
    const gerenteSelect = document.querySelector('[name="gerente"]');
    const documentoInput = document.querySelector('[name="documento"]');
    const documentolcambioInput = document.querySelector('[name="documentolcambio"]');

    function actualizarBoton() {
        const algunCheckboxSeleccionado = Array.from(checkboxes).some(checkbox => checkbox.checked);
        const algunaFechaSeleccionada = Array.from(fechas).some(fecha => fecha.value !== '');
        const gerenteSeleccionado = gerenteSelect && gerenteSelect.value !== '';
        const documentoSeleccionado = documentoInput && documentoInput.files.length > 0;
        const documentolcambioSeleccionado = documentolcambioInput && documentolcambioInput.files.length > 0;

        btnAsignarCredito.disabled = !(
            algunCheckboxSeleccionado &&
            algunaFechaSeleccionada &&
            gerenteSeleccionado &&
            documentoSeleccionado &&
            documentolcambioSeleccionado
        );
    }

    checkboxes.forEach(checkbox => checkbox.addEventListener('change', actualizarBoton));
    fechas.forEach(fecha => fecha.addEventListener('input', actualizarBoton));
    gerenteSelect.addEventListener('change', actualizarBoton);
    documentoInput.addEventListener('change', actualizarBoton);
    documentolcambioInput.addEventListener('change', actualizarBoton);

    // Inicializar estado del botón
    actualizarBoton();
</script>
@endsection