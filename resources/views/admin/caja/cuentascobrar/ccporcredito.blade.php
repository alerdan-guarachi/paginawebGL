@extends('adminlte::page')
    
@section('content_header')
<h1>ASIGNACIÓN DE CRÉDITO</h1>
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
                    ASIGNAR CRÉDITO
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="tab-2" data-toggle="tab" href="#tab-content-2" role="tab" aria-controls="tab-content-2" aria-selected="true">
                    CARTA DE CRÉDITO PENDIENTES
                </a>
            </li>
            {{-- <li class="nav-item">
                <a class="nav-link" id="tab-3" data-toggle="tab" href="#tab-content-3" role="tab" aria-controls="tab-content-3" aria-selected="true">
                    CRÉDITOS APROBADOS
                </a>
            </li> --}}
        </ul>
    </div>
    <div class="card-body">
        <div class="tab-content" id="myTabContent">
            
            <div class="tab-pane fade show active" id="tab-content-1" role="tabpanel" aria-labelledby="tab-1"> 
                <!-- Formulario de búsqueda -->
                <form method="GET" action="{{ route('admin.caja.cuentascobrar.ccporcredito') }}">  
                    <label for="gerenteSelect">BUSCAR CLIENTE:</label>
                    <div class="row mb-3">
                        <div class="col-lg-2">
                            <select name="tipo_cliente" class="form-control">
                                <option value="" disabled selected>Tipo de Cliente</option>
                                <option value="CLIENTE ITA" {{ request('tipo_cliente') == 'CLIENTE ITA' ? 'selected' : '' }}>CLIENTE ITA</option>
                                <option value="CLIENTE BANCO" {{ request('tipo_cliente') == 'CLIENTE BANCO' ? 'selected' : '' }}>CLIENTE BANCO</option>
                                <option value="CLIENTE AUDITORIA" {{ request('tipo_cliente') == 'CLIENTE AUDITORIA' ? 'selected' : '' }}>CLIENTE AUDITORIA</option>
                                <option value="CLIENTE COMUN" {{ request('tipo_cliente') == 'CLIENTE COMUN' ? 'selected' : '' }}>CLIENTE COMUN</option>
                            </select>
                        </div>
                        <div class="col-lg-2">
                            <input type="text" name="search" class="form-control" placeholder="ID Cliente" value="{{ request('search') }}">
                        </div>
                        <div class="col-lg-2">
                            <button type="submit" class="btn btn-secondary">BUSCAR</button>
                        </div>
                    </div>
                </form>
                <form action="{{ route('actualizarRegistros') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    @if (request('search'))
                    <input type="hidden" name="idcliente" value="{{ request('search') }}">
                    <div class="row">
                        <div class="form-group col-lg-4">
                            <label for="gerenteSelect">USUARIO AUTORIZADOR:</label>
                            <select id="gerenteSelect" name="gerente" class="form-control">
                                <option value=""></option>
                                <option value="FABRICIO PRADO PARRADO">FABRICIO PRADO PARRADO</option>
                                <option value="DENISSE MAUREN LOPEZ FLORES">DENISSE MAUREN LOPEZ FLORES</option>
                            </select>
                        </div>
                        <div class="form-group col-lg-4">
                            <label for="documento">DOCUMENTO RESPALDO:</label>
                            <input type="file" id="documento" name="documento" class="form-control" accept=".pdf,.doc,.docx">
                        </div>

                        <div class="form-group col-lg-4">
                            <label for="documentolcambio">LETRA DE CAMBIO:</label>
                            <input type="file" id="documentolcambio" name="documentolcambio" class="form-control" accept=".pdf,.doc,.docx">
                        </div>
                    </div>
                    @endif
                    <div class="table-responsive">
                        <div class="card">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Tipo</th>
                                        <th>ID_Cli.</th>
                                        <th>Cliente</th>
                                        <th>Proveedor</th>
                                        <th>Est./Esp.</th>
                                        <th hidden>Fecha_Batería</th>
                                        <th>Servicio</th>
                                        <th>Precio</th>
                                        <th style="text-align: center">Total_Crédito</th>
                                        <th style="text-align: center">Cuotas</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if ($registros->isNotEmpty())
                                    @php
                                    $totalPrecio = number_format($registros->sum('precio'), 2);
                                    $cantidadCuotas = $registros->pluck('cantidadcuotas')->unique();
                                    $cantidadCuotas = $cantidadCuotas->count() == 1 ? $cantidadCuotas->first() : null;
                                    $rowCount = $registros->count();
                                @endphp
                                        @foreach ($registros as $index => $registro)
                                            <tr>
                                                <td>{{ $registro->id }}</td>
                                                <td>
                                                    @if ($registro->clienteitaid) ITA
                                                    @elseif ($registro->clienteauditoriaid) AUDITORIA
                                                    @elseif ($registro->clientecomunid) COMUN
                                                    @elseif ($registro->clientebancoid) BANCO
                                                    @else SIN TIPO
                                                    @endif
                                                </td>
                                                <td>
                                                    {{ $registro->clienteitaid ?? '' }}{{ $registro->clienteauditoriaid ?? '' }}{{ $registro->clientecomunid ?? '' }}{{ $registro->clientebancoid ?? '' }}
                                                </td>
                                                <td title="{{ $registro->clienteitanombre ?? '' }}{{ $registro->clienteauditorianombre ?? '' }}{{ $registro->clientecomunnombre ?? '' }}{{ $registro->clientenombre ?? '' }}" class="truncar">
                                                    {{ $registro->clienteitanombre ?? '' }}{{ $registro->clienteauditorianombre ?? '' }}{{ $registro->clientecomunnombre ?? '' }}{{ $registro->clientenombre ?? '' }}
                                                </td>
                                                <td title="{{ $registro->proveedornombre }} {{ $registro->proveedorasignado }}" class="truncar">{{ $registro->proveedornombre }} {{ $registro->proveedorasignado }}</td>
                                                <td title="{{ $registro->accionnombre }}" class="truncar">{{ $registro->accionnombre }}</td>
                                                <td hidden>{{ $registro->fechabateria }}</td>
                                                <td>
                                                    @if ($registro->tramite)
                                                        {{ $registro->tramite->tramite ?? 'VACIO' }}
                                                    @else
                                                        VACIO
                                                    @endif
                                                </td>
                                                <td>{{ $registro->precio }}</td>
                                                @if ($index == 0)
                                                    <td rowspan="{{ $rowCount }}" style="background-color: #FFFACD; text-align: center; vertical-align: middle;">
                                                        {{ $totalPrecio }}
                                                    </td>
                                                    <td rowspan="{{ $rowCount }}" style="background-color: #FFFACD; text-align: center; vertical-align: middle;">
                                                        {{ $cantidadCuotas ?? 'Variadas' }}
                                                    </td>
                                                @endif
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="10">NO SE ENCONTRARON REGISTROS</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>

                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Monto_Cuotas</th>
                                        <th>Fecha_Crédito</th>
                                        <th>Autorizador</th>
                                        <th>Doc_Respaldo</th>
                                        <th>Letra_Cambio</th>
                                        <th>Selec.</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if ($registros->isNotEmpty())
                                        @foreach ($registros as $registro)
                                        @php
                                        $cuotasTotales = $cantidadCuotas ?? 1;
                                    @endphp
                                            @for ($i = 0; $i < $cuotasTotales; $i++)
                                                <tr>
                                                    <td>
                                                        <input type="text" class="form-control form-control-sm" 
                                                            name="campo_monto[{{ $registro->id }}][{{ $i }}]" 
                                                            id="campo_monto_{{ $registro->id }}_{{ $i }}" value="" 
                                                            oninput="actualizarTotal(this)">
                                                    </td>
                                                   

{{-- @for ($i = 0; $i < $cuotasTotales; $i++)
    <tr>
        <td>
            <input type="text" class="form-control form-control-sm" 
                name="campo_monto[{{ $i }}]" 
                id="campo_monto_{{ $i }}" value="" 
                oninput="actualizarTotal(this)">
        </td> --}}
                                                    <td>
                                                        <input type="date" class="form-control form-control-sm" 
                                                            name="campo_fecha[{{ $registro->id }}][{{ $i }}]" value="">
                                                    </td>
                                                    <td title="{{ $registro->usuarioautorizador }}" class="truncar">
                                                        @if(empty($registro->usuarioautorizador))
                                                            <span class="badge bg-danger">VACÍO</span>
                                                        @else
                                                            {{ $registro->usuarioautorizador }}
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($registro->documentocredito)
                                                            <a href="{{ asset('creditos/'. $registro->documentocredito) }}" target="_blank" class="btn btn-sm btn-secondary" title="VER DOCUMENTO">
                                                                <i class="fa fa-eye"></i>
                                                            </a>
                                                        @else
                                                            <span class="badge bg-danger">VACÍO</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($registro->documentolcambio)
                                                            <a href="{{ asset('creditos/'. $registro->documentolcambio) }}" target="_blank" class="btn btn-sm btn-secondary" title="VER LETRA DE CAMBIO">
                                                                <i class="fa fa-eye"></i>
                                                            </a>
                                                        @else
                                                        <span class="badge bg-danger">VACÍO</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (!$registro->documentocredito)
                                                            <input type="checkbox" name="seleccionados[]" value="{{ $registro->id }}" class="checkbox-seleccion">
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endfor
                                        @endforeach
                                        <p><strong>SALDO RESTANTE: </strong><span id="totalRestante">{{ $totalPrecio }}</span></p>
                                        
                                        <script>
                                            let totalDisponibleStr = "{{ $totalPrecio }}";
                                            totalDisponibleStr = totalDisponibleStr.replace(/,/g, '');
                                            let totalDisponible = parseFloat(totalDisponibleStr);
                                        
                                            function actualizarTotal(campoActual) {
                                                let totalIngresado = 0;
                                                const montos = document.querySelectorAll('input[name^="campo_monto"]');
                                        
                                                montos.forEach(input => {
                                                    totalIngresado += parseFloat(input.value) || 0;
                                                });
                                                if (totalIngresado > totalDisponible) {
                                                    alert("EL MONTO INGRESADO EXCEDE AL SALDO TOTAL DEL CRÉDITO");
                                                    campoActual.value = "";
                                                    totalIngresado = 0;
                                                    montos.forEach(input => {
                                                        totalIngresado += parseFloat(input.value) || 0;
                                                    });
                                                }
                                                let totalRestante = totalDisponible - totalIngresado;
                                                document.getElementById("totalRestante").innerText = totalRestante.toFixed(2);
                                                document.getElementById("btnAsignarCredito").disabled = (totalRestante !== 0);
                                            }
                                        </script>
                                    @else
                                        <tr>
                                            <td colspan="6">NO SE ENCONTRARON REGISTROS</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="d-flex">
                        <button type="submit" class="btn btn-sm btn-outline-success ml-auto" id="btnAsignarCredito" disabled>ASIGNAR CREDITO</button>
                    </div>
                </form>
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
                    }
                
                    checkboxes.forEach(checkbox => checkbox.addEventListener('change', actualizarBoton));
                    fechas.forEach(fecha => fecha.addEventListener('input', actualizarBoton));
                    gerenteSelect.addEventListener('change', actualizarBoton);
                    documentoInput.addEventListener('change', actualizarBoton);
                    documentolcambioInput.addEventListener('change', actualizarBoton);
                
                    // Inicializar estado del botón
                    actualizarBoton();
                </script>
            </div>
            
            <div class="tab-pane fade" id="tab-content-2" role="tabpanel" aria-labelledby="tab-2">  
                <div class="table-responsive">
                    <form method="POST" action="{{ route('agregarcartacredito') }}" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="idcliente" value="{{ request('search') }}">
                        <div class="form-group">
                            <label for="cartacredito">Seleccionar Carta de Crédito:</label>
                            <input type="file" name="cartacredito" id="cartacredito" class="form-control" required>
                        </div>
                    
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Selec.</th>
                                    <th>ID</th>
                                    <th>ID_Bat.</th>
                                    <th>ID_Cli.</th>
                                    <th>Cliente</th>
                                    {{-- <th>Detalle</th> --}}
                                    <th>Proveedor</th>
                                    <th>Fecha_Crédito</th>
                                    <th>Monto_Cuota</th>
                                    <th>Usu_Autoriza.</th>
                                    <th>Respaldos</th>
                                    <th>Carta_Crédito</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($creditos as $credito)
                                    @if (is_null($credito->cartacredito) || $credito->cartacredito === '')
                                    <tr data-bateriaid="{{ $credito->bateriaid }}">
                                        <td>
                                            <input type="checkbox" class="chkRegistro" name="creditos[]" value="{{ $credito->id }}" data-bateriaid="{{ $credito->bateriaid }}">
                                        </td>
                                        <td>{{ $credito->id }}</td>
                                        <td>{{ $credito->bateriaid }}</td>
                                        <td>{{ $credito->clienteid }}</td>
                                        <td>{{ $credito->clientenombre }}</td>
                                        {{-- <td>{{ $credito->detalle }}</td> --}}
                                        <td>{{ $credito->proveedor }}</td>
                                        <td>{{ $credito->fechacredito }}</td>
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
                                        </td>
                                        <td>
                                            <span class="badge badge-danger">VACIO</span>
                                        </td>
                                    </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    
                        <button type="submit" class="btn btn-sm btn-outline-secondary">GUARDAR CARTA DE CRÉDITO</button>
                    </form>
                </div>
                
            </div>

            {{-- <div class="tab-pane fade" id="tab-content-3" role="tabpanel" aria-labelledby="tab-3"> 
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>ID_Bat.</th>
                                <th>ID_Cli.</th>
                                <th>Cliente</th>
                                <th>Detalle</th>
                                <th>Proveedor</th>
                                <th>Fecha_Crédito</th>
                                <th>Monto_Cuota</th>
                                <th>Usu_Autoriza.</th>
                                <th>Doc._Respaldos</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($creditos as $credito)
                            @if (!is_null($credito->cartacredito) || $credito->cartacredito === '')
                            <tr data-bateriaid="{{ $credito->bateriaid }}">
                                <td>{{ $credito->id }}</td>
                                <td>{{ $credito->bateriaid }}</td>
                                <td>{{ $credito->clienteid }}</td>
                                <td>{{ $credito->clientenombre }}</td>
                                <td>{{ $credito->detalle }}</td>
                                <td>{{ $credito->proveedor }}</td>
                                <td>{{ $credito->fechacredito }}</td>
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
                            </tr>
                            @endif
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div> --}}
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

@endsection