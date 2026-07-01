@extends('adminlte::page')

<link href="assets/img/logo.png" rel="icon">

@section('content_header')
@php
    $rolUsuario = auth()->user()->getRoleNames()->first();
    $tieneRolContable = auth()->user()->getRoleNames()->contains('CONTABLE');
    $tieneRolMaestro = auth()->user()->getRoleNames()->contains('MAESTRO');
    $tieneRolAdm = auth()->user()->getRoleNames()->contains('ADMINISTRADOR');
@endphp

<div class="dropdown float-right ml-2">
    <button class="btn btn-sm btn-outline-secondary dropdown-toggle shadow-sm"
            type="button"
            id="dropdownAcciones"
            data-toggle="dropdown"
            aria-haspopup="true"
            aria-expanded="false">
        <i class="fas fa-cogs mr-1"></i> ACCIONES
    </button>
    <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
        aria-labelledby="dropdownAcciones"
        style="min-width: 300px;">
        
        <a class="dropdown-item" data-toggle="modal" data-target="#arqueoModal">
            <i class="fas fa-comments-dollar mr-2 text-secondary"></i> MI ARQUEO DE HOY
        </a>
        <a class="dropdown-item" data-toggle="modal" data-target="#consolidadosporusuarioModal">
            <i class="fas fa-piggy-bank mr-2 text-secondary"></i> MI CONSOLIDADO
        </a>
        <a class="dropdown-item" data-toggle="modal" data-target="#aperturacaja">
            <i class="fas fa-cash-register mr-2 text-secondary"></i> APERTURAR CAJA DE HOY
        </a>
        @if ($tieneRolMaestro || $tieneRolAdm)
            <a class="dropdown-item" data-toggle="modal" data-target="#consolidadosModal">
                <i class="fas fa-coins mr-2 text-secondary"></i> CONSOLIDADOS GENERAL
            </a>
            <a class="dropdown-item" data-toggle="modal" data-target="#aperturacaja2">
                <i class="fas fa-donate mr-2 text-secondary"></i> APERTURAS DE CAJA GENERAL
            </a>
        @endif
        <a class="dropdown-item" href="{{ route('admin.caja.ingreso.ingresosexternos') }}">
            <i class="fas fa-hand-holding-usd mr-2 text-secondary"></i> INGRESOS EXTERNOS
        </a>
        <a class="dropdown-item" data-toggle="modal" data-target="#modalCodigo">
            <i class="fas fa-key mr-2 text-secondary"></i> CÓDIGOS DE PERMISO
        </a>
    </div>
</div>

<style>
    .dropdown-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 8px 18px;
        font-size: 15px;
        transition: all 0.2s ease;
        cursor: pointer !important;
    }
    .dropdown-item:hover {
        background-color: rgba(0, 0, 0, 0.08);
        transform: translateX(5px);
    }
    label {
        margin-bottom: 0;
    }
</style>

@if (!$mostrarVista && $tieneRolContable)
    <div class="alert alert-danger text-center py-4" style="border-radius: 10px; background-color: #f8d7da; color: #842029; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);">
        <h4 class="font-weight-bold mb-3" style="text-transform: uppercase; letter-spacing: 1px;">Caja Bloqueada</h4>
        <p class="mb-4" style="font-size: 1.1rem;">
            {{ $motivoBloqueo ?? 'TU CAJA ESTA BLOQUEADA, SOLICITA UN CÓDIGO DE DESBLOQUEO A ADMINISTRACIÓN.' }}
        </p>
        <form action="{{ route('verificar.codigo') }}" method="POST" style="max-width: 500px; margin: 0 auto;">
            @csrf
            <div class="form-group mb-3">
                <label for="codigo" class="font-weight-bold" style="font-size: 1rem;">Ingresa el código para continuar:</label>
                <input type="text" id="codigo" name="codigo" class="form-control" placeholder="Código de autorización" required style="border-radius: 5px;">
            </div>
            <button type="submit" class="btn btn-sm btn-success btn-block" style="padding: 5px 10px; font-size: 1rem; border-radius: 5px;">VALIDAR CÓDIGO</button>
        </form>
    </div>
@else
    <div class="d-flex align-items-center justify-content-between mb-0">
        <div class="flex-grow-1">
            <h3 class="font-weight-bold">CAJA DE INGRESOS INTERNOS</h3>
        </div>
        <a class="btn btn-outline-warning btn-sm" id="btnVerCreditos" 
            data-toggle="modal" data-target="#modalCreditos" 
            style="display: {{ $tieneCredito->isEmpty() ? 'none' : 'block' }}; margin-right: 10px; margin-top: -10px;">
            VER CRÉDITOS
        </a>
        <style>
            @keyframes pulsate {
                0% {
                    transform: scale(1);
                }
                50% {
                    transform: scale(1.1);
                }
                100% {
                    transform: scale(1);
                }
            }
            #btnVerCreditos {
                animation: pulsate 1.5s infinite;
            }
        </style>

        <div class="modal fade" id="modalCodigo" tabindex="-1" role="dialog" aria-labelledby="modalCodigoLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form id="formCodigo">
                        <div class="modal-header">
                            <h3 class="modal-title" id="modalCodigoLabel" style="font-weight: 900;">Ingresar Código</h3>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <input type="text" id="codigoInput" name="codigo" class="form-control" placeholder="Ingrese el código" required>
                            <div id="codigoMensaje" class="mt-2 text-danger" style="display: none;"></div>
                        </div>
                        <div class="text-center">
                            <button type="submit" class="btn btn-sm btn-outline-secondary">VALIDAR</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <script>
            document.getElementById('formCodigo').addEventListener('submit', function(e) {
                e.preventDefault();
                const codigo = document.getElementById('codigoInput').value.trim();
                const mensaje = document.getElementById('codigoMensaje');
                mensaje.style.display = 'none';

                fetch('{{ route("permisoscodigo.expirar") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    body: JSON.stringify({ codigo: codigo })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        $('#modalCodigo').modal('hide');
                        alert('Código validado correctamente');
                        location.reload(); // Recarga la página después de validar
                    } else {
                        mensaje.textContent = data.message;
                        mensaje.style.display = 'block';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    mensaje.textContent = 'Ocurrió un error al procesar la solicitud.';
                    mensaje.style.display = 'block';
                });
            });
        </script>

        {{-- MODAL CREDITOS DEL CLIENTE --}}
        <div class="modal fade" id="modalCreditos" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="aperturacajaLabel">CRÉDITOS DEL CLIENTE</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    {{-- @if($tieneCredito->isEmpty())
                        <p>No hay créditos disponibles.</p>
                    @else
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>ID_Cli.</th>
                                    <th>Cliente</th>
                                    <th>Detalle</th>
                                    <th>Monto_Cuota</th>
                                    <th>Fecha_Crédito</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($creditos as $credito)
                                    <tr>
                                        <td>{{ $credito->clienteid }}</td>
                                        <td>{{ $credito->clientenombre }}</td>
                                        <td>{{ $credito->detalle }}</td>
                                        <td>{{ $credito->montocuota }}</td>
                                        <td>{{ $credito->fechacredito }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif --}}
                </div>
            </div>
            </div>
        </div>
    </div>
@endif
    {{-- MODAL CONSOLIDADO GENERAL --}}
    <div class="modal fade" id="consolidadosModal" tabindex="-1" aria-labelledby="consolidadosModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content border-0 shadow-sm">
                <div class="modal-header text-center">
                    <h5 class="modal-title w-100 fw-bold" id="consolidadosModalLabel">CONSOLIDADOS GENERAL DE CAJA</h5>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle table-bordered table-sm">
                            <thead class="bg-secondary text-white">
                                <tr class="border-bottom">
                                    <th class="text-start">Usuario</th>
                                    <th class="text-center">Efectivo</th>
                                    <th class="text-center">Depósito</th>
                                    <th class="text-center">Transf.</th>
                                    <th class="text-center">Cheque</th>
                                    <th class="text-center">ATC</th>
                                    <th class="text-center">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $granTotal = 0; @endphp
                                @foreach($consolidados as $consolidado)
                                    @php
                                        $total = $consolidado->consolidadoefectivo + $consolidado->consolidadodeposito + 
                                                $consolidado->consolidadotransferencia + $consolidado->consolidadocheque + 
                                                $consolidado->consolidadoatc;
                                        $granTotal += $total;
                                    @endphp
                                    <tr>
                                        <td class="text-start">{{ $consolidado->usuarioconsolidadonombre }}</td>  <!-- Alineado a la izquierda -->
                                        {{-- <td class="text-center">{{ number_format($consolidado->consolidadoefectivo, 2) }}</td> --}}
                                        <td class="text-center {{ is_null($consolidado->actualizaciondeposito) || \Carbon\Carbon::parse($consolidado->actualizaciondeposito)->toDateString() != now()->toDateString() ? 'text-danger' : '' }}">
                                            {{ number_format($consolidado->consolidadoefectivo, 2) }}
                                        </td>   
                                        <td class="text-center">{{ number_format($consolidado->consolidadodeposito, 2) }}</td>
                                        <td class="text-center">{{ number_format($consolidado->consolidadotransferencia, 2) }}</td>
                                        <td class="text-center">{{ number_format($consolidado->consolidadocheque, 2) }}</td>
                                        <td class="text-center">{{ number_format($consolidado->consolidadoatc, 2) }}</td>

                                        <td class="fw-bold text-center">{{ number_format($total, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="border-top">
                                    <th colspan="6" class="text-end">Gran Total:</th>
                                    <th class="fw-bold text-center">{{ number_format($granTotal, 2) }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <div class="text-center">
                        <button type="button" class="btn-sm btn btn-outline-secondary" data-dismiss="modal">CERRAR</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL CONSOLIDADO POR USUARIO --}}
    <div class="modal fade" id="consolidadosporusuarioModal" tabindex="-1" aria-labelledby="consolidadosporusuarioModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content border-0 shadow-sm">
                <div class="modal-header w-100 text-center" style="display: block; text-align: center;">
                    <h6 class="modal-title w-100 fw-bold" style="font-size: 20px;" id="consolidadosporusuarioModalLabel">MI CONSOLIDADO DE CAJA</h5>
                    <h5 class="modal-title w-100 fw-bold">{{ auth()->user()->name }}</h5>
                    {{-- <p class="text-muted mt-2 mb-0">{{ now()->format('d/m/Y') }}</p> --}}
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle table-bordered table-sm">
                            <thead class="bg-secondary text-white">
                                <tr class="border-bottom">
                                    <th class="text-start">Usuario</th>
                                    <th class="text-center">Efectivo</th>
                                    <th class="text-center">Depósito</th>
                                    <th class="text-center">Transf.</th>
                                    <th class="text-center">Cheque</th>
                                    <th class="text-center">ATC</th>
                                    <th class="text-center">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($consolidadosusuario as $consolidadousu)
                                    @php
                                        $total = $consolidadousu->consolidadoefectivo + $consolidadousu->consolidadodeposito + 
                                                $consolidadousu->consolidadotransferencia + $consolidadousu->consolidadocheque + 
                                                $consolidadousu->consolidadoatc;
                                    @endphp
                                    <tr>
                                        <td class="text-start">{{ $consolidadousu->usuarioconsolidadonombre }}</td>
                                        <td class="text-center {{ is_null($consolidadousu->actualizaciondeposito) || \Carbon\Carbon::parse($consolidadousu->actualizaciondeposito)->toDateString() != now()->toDateString() ? 'text-danger' : '' }}">
                                            {{ number_format($consolidadousu->consolidadoefectivo, 2) }}
                                        </td>                                                                             
                                        <td class="text-center">{{ number_format($consolidadousu->consolidadodeposito, 2) }}</td>
                                        <td class="text-center">{{ number_format($consolidadousu->consolidadotransferencia, 2) }}</td>
                                        <td class="text-center">{{ number_format($consolidadousu->consolidadocheque, 2) }}</td>
                                        <td class="text-center">{{ number_format($consolidadousu->consolidadoatc, 2) }}</td>

                                        <td class="fw-bold text-center">{{ number_format($total, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="text-center">
                        <button type="button" class="btn-sm btn btn-outline-secondary" data-dismiss="modal">CERRAR</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <style>
        .modal-title {
            font-size: 1.6rem;
            text-transform: uppercase;
            margin-bottom: 0;
            letter-spacing: 0.5px;
        }

        .table th, .table td {
            vertical-align: middle;
        }

        .modal-footer {
            justify-content: right;
        }

        .fw-bold {
            font-weight: bold !important;
        }
        .table td {
            padding: 5px 10px;;
        }
    </style>

    {{-- MODAL ARQUEO POR USUARIO --}}
    <div class="modal fade" id="arqueoModal" tabindex="-1" aria-labelledby="arqueoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content border-0 shadow-sm">
                <div class="modal-header w-100 text-center" style="display: block; text-align: center;">
                    <h6 class="modal-title w-100 fw-bold" style="font-size: 20px;" id="arqueoModalLabel">MI ARQUEO DE CAJA DE HOY</h6>
                    <h5 class="modal-title w-100 fw-bold">{{ auth()->user()->name }}</h5>
                    <p class="text-muted mt-2 mb-0">{{ now()->format('d/m/Y') }}</p>
                </div>
                <div class="modal-body">
                    <div class="row">
                        @php $total = 0; @endphp
                        <div class="col-md-6">
                            <div class="p-3 border border-secondary rounded mb-3">
                                <h6 class="text-center fw-bold" style="background: #ececec">BILLETES</h6>
                                <table class="table table-striped table-hover align-middle">
                                    <thead>
                                        <tr class="text-center">
                                            <th>Corte</th>
                                            <th>Cantidad</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($arqueos as $arqueo)
                                            @php
                                                $billetes = [
                                                    ['denom' => 200, 'cantidad' => $arqueo->billetecorte200],
                                                    ['denom' => 100, 'cantidad' => $arqueo->billetecorte100],
                                                    ['denom' => 50, 'cantidad' => $arqueo->billetecorte50],
                                                    ['denom' => 20, 'cantidad' => $arqueo->billetecorte20],
                                                    ['denom' => 10, 'cantidad' => $arqueo->billetecorte10],
                                                ];
                                            @endphp
                                            @foreach($billetes as $billete)
                                                <tr>
                                                    <td class="text-center">Bs. {{ $billete['denom'] }}</td>
                                                    <td class="text-center">{{ $billete['cantidad'] }}</td>
                                                    @php $total += $billete['denom'] * $billete['cantidad']; @endphp
                                                </tr>
                                            @endforeach
                                            <!-- Fila en blanco adicional -->
                                            <tr>
                                                <td colspan="2" class="text-center">&nbsp;</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
    
                        <!-- Recuadro de Monedas -->
                        <div class="col-md-6">
                            <div class="p-3 border border-secondary rounded">
                                <h6 class="text-center fw-bold" style="background: #ececec">MONEDAS</h6>
                                <table class="table table-striped table-hover align-middle">
                                    <thead>
                                        <tr class="text-center">
                                            <th>Corte</th>
                                            <th>Cantidad</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($arqueos as $arqueo)
                                            @php
                                                $monedas = [
                                                    ['denom' => 5, 'cantidad' => $arqueo->monedacorte5],
                                                    ['denom' => 2, 'cantidad' => $arqueo->monedacorte2],
                                                    ['denom' => 1, 'cantidad' => $arqueo->monedacorte1],
                                                    ['denom' => 0.50, 'cantidad' => $arqueo->monedacorte050],
                                                    ['denom' => 0.20, 'cantidad' => $arqueo->monedacorte020],
                                                    ['denom' => 0.10, 'cantidad' => $arqueo->monedacorte010],
                                                ];
                                            @endphp
                                            @foreach($monedas as $moneda)
                                                <tr>
                                                    <td class="text-center">Bs. {{ $moneda['denom'] }}</td>
                                                    <td class="text-center">{{ $moneda['cantidad'] }}</td>
                                                    @php $total += $moneda['denom'] * $moneda['cantidad']; @endphp
                                                </tr>
                                            @endforeach
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
    
                <!-- Footer con el Total -->
                <div class="modal-footer d-flex flex-column align-items-center" style="margin-top: -20px;">
                    <div class="p-3 mb-2 w-30 text-center" style="border: 2px solid #a9a9a9; background-color: #f4f4f4; border-radius: 5px;">
                        <span class="fw-bold">Total:</span> 
                        <span class="text-dark fw-bold">{{ number_format($total, 2, '.', ',') }} Bs.</span>
                    </div>
                    <button type="button" class="btn-sm btn btn-outline-secondary mt-2" data-dismiss="modal">CERRAR</button>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL APERTURA DE CAJA POR USUARIO --}}
    <div class="modal fade" id="aperturacaja" tabindex="-1" role="dialog" aria-labelledby="aperturacajaLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="aperturacajaLabel">APERTURAR CAJA DE HOY</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    @if (!$registroapertura) <!-- Si no existe el registro -->
                        <form action="{{ route('apertura.guardar') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group">
                                <label for="archivo">Subir Apertura</label>
                                <input type="file" name="archivo" id="archivo" class="form-control" accept=".jpg, .jpeg, .png" required>
                            </div>
                            <div class="text-center">
                                <button type="submit" class="btn btn-outline-secondary btn-sm">
                                    GUARDAR APERTURA
                                </button>
                            </div>
                        </form>
                    @else <!-- Si existe el registro -->
                        <h5>REGISTROS DE APERTURA:</h5>
                        <ul>
                            <li>
                                <a href="{{ asset('aperturacaja/' . $registroapertura->usuarioaperturaid . '/' . $registroapertura->documentoapertura) }}" target="_blank">VER APERTURA DE HOY</a>
                            </li>
                        </ul>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL APERTURAS DE CAJA GENERAL --}}
    <div class="modal fade" id="aperturacaja2" tabindex="-1" aria-labelledby="aperturacaja2Label" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content border-0 shadow-sm">
                <div class="modal-header w-100 text-center" style="display: block; text-align: center;">
                    <h5 class="modal-title w-100 fw-bold" id="aperturacaja2Label">APERTURAS DE CAJA GENERAL</h5>
                    <p class="text-muted mt-2 mb-0">ÚLTIMAS 10 APERTURAS</p>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-sm">
                            <thead class="bg-secondary text-white">
                                <tr class="border-bottom">
                                    <th>Usuario Apertura</th>
                                    <th>Fecha Apertura</th>
                                    <th class="text-center">Archivo</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($aperturascajas as $aperturas)
                                    <tr>
                                        <td>{{ $aperturas->usuarioaperturanombre }}</td>
                                        <td>{{ $aperturas->created_at->format('d-m-Y / H:i') }}</td>
                                        <td class="text-center">
                                            <a type="submit" class="btn-sm btn btn-secondary" href="{{ asset('aperturacaja/' . $aperturas->usuarioaperturaid . '/' . $aperturas->documentoapertura) }}" target="_blank"><i class="fas fa-eye"></i></a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="text-center">
                        <button type="button" class="btn-sm btn btn-outline-secondary" data-dismiss="modal">CERRAR</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
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

@if (session('infoerror'))
    <div id="alert-infoerror" class="alert alert-danger">
        <strong>{{ session('infoerror') }}</strong>
    </div>
    <script>
        setTimeout(function() {
            $('#alert-infoerror').fadeOut('fast');
        }, 3000);
    </script>
@endif

@if (!$mostrarVista && $tieneRolContable)

@else
<form action="{{ route('guardar.cajacentral') }}" method="POST" id="guardarFormulario">
    @csrf
    <div class="card">
        <div class="card-body">
            <div class="row">
                {{-- PANEL IZQUIERDO --}}
                <div class="col-md-2 panel">
                    <div class="form-group" hidden>
                        <label>Ciudad de Operación</label>
                        <input type="text" class="form-control" name="ciudadregistro" value="{{ $sucursal }}">
                    </div>
                    <div class="row">
                        <div class="form-group col-lg-4">
                            <label for="siguienteId">Recibo</label>
                            <input type="text" id="siguienteId" class="form-control form-control-sm" readonly>
                        </div>
                        <script>
                            function actualizarSiguienteId() {
                                fetch("{{ url('/recibos/siguiente-id') }}")
                                    .then(response => response.json())
                                    .then(data => {
                                        document.getElementById('siguienteId').value = data.siguienteId;
                                    })
                                    .catch(error => console.error('Error al obtener el siguiente ID:', error));
                            }
                        
                            setInterval(actualizarSiguienteId, 1000);
                            actualizarSiguienteId();
                        </script>
                        <div class="form-group col-lg-8">
                            <label>Tipo Cliente/Prov.</label>
                            <select id="tipoCliente" class="form-control form-control-sm" name="tipocliente" onchange="cambiarArea()">
                                <option value="" selected disabled>Seleccione un tipo</option>
                                <option value="clienteitaid">CLIENTES ITA</option>
                                <option value="clienteauditoriaid">CLIENTES AUDITORIA</option>
                                <option value="clientecomunid">CLIENTES COMUNES</option>
                                <option value="proveedorid">PROVEEDORES DE SERVICIOS</option>
                                <option value="medicoid">PROVEEDORES MÉDICOS</option>
                            </select>
                            <input type="hidden" id="area" class="form-control" name="area" value="MEDICA">
                        </div>
                    </div>
                    <label for="clienteid">Nombre Cliente/Prov.</label>
                    <div class="row mb-3">
                        <div class="form-group col-lg-12">
                            <input list="clientes" id="clienteSearch" class="form-control form-control-sm" placeholder="Buscar cliente/proveedor..." oninput="actualizarID()">
                            <datalist id="clientes"></datalist>
                            <input type="hidden" id="clienteid" name="clienteid">
                        </div>
                    </div>
                    <script>
                        const clientesIta = @json($clientesIta);
                        const clientesAuditoria = @json($clientesAuditoria);
                        const clientesComunes = @json($clientesComunes);
                        const proveedores = @json($proveedores);
                        const medicos = @json($medicos);

                        function cambiarArea() {
                            const tipoCliente = document.getElementById("tipoCliente").value;
                            const datalist = document.getElementById("clientes");
                            const clienteSearch = document.getElementById("clienteSearch");
                            var areaInput = document.getElementById('area');
                                
                                if (tipoCliente === 'clienteitaid' || tipoCliente === 'clienteauditoriaid' || tipoCliente === 'clientecomunid') {
                                    areaInput.value = 'MEDICA';
                                } else if (tipoCliente === 'proveedorid' || tipoCliente === 'medicoid') {
                                    areaInput.value = 'CUENTA POR COBRAR';
                                }

                            datalist.innerHTML = '';

                            let clientes = [];

                            if (tipoCliente === 'clienteitaid') {
                                clientes = clientesIta;
                            } else if (tipoCliente === 'clienteauditoriaid') {
                                clientes = clientesAuditoria;
                            } else if (tipoCliente === 'clientecomunid') {
                                clientes = clientesComunes;
                            } else if (tipoCliente === 'proveedorid') {
                                clientes = proveedores;
                            } else if (tipoCliente === 'medicoid') {
                                clientes = medicos;
                            }

                            clientes.forEach(cliente => {
                                const option = document.createElement("option");
                                option.value = cliente.nombrecompleto || cliente.razonsocial || cliente.proveedor;
                                option.dataset.id = cliente.id;
                                datalist.appendChild(option);
                            });
                        }

                        function actualizarID() {
                            const clienteSearch = document.getElementById("clienteSearch");
                            const datalist = document.getElementById("clientes");
                            const options = datalist.querySelectorAll("option");

                            options.forEach(option => {
                                if (option.value === clienteSearch.value) {
                                    document.getElementById("clienteid").value = option.dataset.id;
                                }
                            });
                        }
                    </script>
                    <div class="row mb-3" style="margin-top: -25px;">
                        <div class="form-group col-lg-6">
                            <a id="buscarCliente" class="btn btn-sm btn-secondary w-100" disabled>Buscar Hoy</a>
                        </div>
                        <div class="form-group col-lg-6">
                            <a id="buscarClienteTodo" class="btn btn-sm btn-secondary w-100" disabled>Mostrar Todo</a>
                        </div>
                    </div>
                    <div class="row mb-3" style="margin-top: -20px;">
                        <div class="form-group col-lg-6">
                            <input type="date" id="fechaInicio" class="form-control form-control-sm" placeholder="Fecha de inicio">
                        </div>
                        <div class="form-group col-lg-6">
                            <input type="date" id="fechaFinal" class="form-control form-control-sm" placeholder="Fecha final">
                        </div>
                    </div>
                    <div class="row mb-3" style="margin-top: -25px;">
                        <div class="form-group col-lg-12">
                            <a id="buscarPorFecha" class="btn btn-sm btn-secondary w-100" disabled>Buscar por Fechas</a>
                        </div>
                    </div>
                    <div id="campoFechas" style="display: none;">
                        <div class="form-group">
                            <label>Registro</label>
                            <input type="datetime-local" name="created_at" id="created_at" class="form-control form-control-sm">
                            <input type="datetime-local" name="updated_at" id="updated_at" class="form-control" hidden>
                        </div>
                    </div>
                    <div class="form-group" style="margin-top: -20px;">
                        <label>Cod. Autorización</label>
                        <input type="text" id="codautorizacion" name="codautorizacion" class="form-control form-control-sm">
                    </div>
                    <div class="form-group">
                        <label>Tipo de Transacción</label>
                        <div>
                            <select class="form-control form-control-sm" id="tipoTransaccion1" name="tipotransaccion" required onchange="validarTipoTransaccion()">
                                <option disabled selected></option>
                                <option value="ATC">ATC</option>
                                <option value="CHEQUE">CHEQUE</option>
                                <option value="DEPOSITO_BANCARIO">DEPÓSITO BANCARIO</option>
                                <option value="EFECTIVO">EFECTIVO</option>
                                <option value="TRANSFERENCIA_BANCARIA">TRANSFERENCIA BANCARIA</option>
                            </select>
                            <div class="form-check mt-2" hidden>
                                <input type="checkbox" class="form-check-input" id="dobleTransaccion" onchange="validarTipoTransaccion()">
                                <label class="form-check-label" for="dobleTransaccion">Doble Tipo de Transac.</label>
                            </div>
                            <select class="form-control form-control-sm mt-2 d-none" id="tipoTransaccion2" name="tipotransaccion2" onchange="validarTipoTransaccion()">
                                <option disabled selected></option>
                                <option value="ATC">ATC</option>
                                <option value="CHEQUE">CHEQUE</option>
                                <option value="DEPOSITO_BANCARIO">DEPÓSITO BANCARIO</option>
                                <option value="EFECTIVO">EFECTIVO</option>
                                <option value="TRANSFERENCIA_BANCARIA">TRANSFERENCIA BANCARIA</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Nro. Factura</label>
                        <input type="text" id="nrofactura" name="nrofactura" class="form-control form-control-sm">
                    </div>
                    <div class="form-group atc-fields d-none">
                        <label>Nro. Tarjeta</label>
                        <input type="text" id="nrotarjeta" name="nrotarjeta" class="form-control form-control-sm" maxlength="16" oninput="formatearTarjeta(this)">
                        <script>
                            function formatearTarjeta(input) {
                                let valor = input.value.replace(/\D/g, '');
                                if (valor.length === 6) {
                                    valor += 'XXXXXX';
                                } else if (valor.length > 6 && !valor.includes('XXXXXX')) {
                                    valor = valor.substring(0, 6) + 'XXXXXX' + valor.substring(6);
                                }
                                if (valor.length > 12) {
                                    valor = valor.substring(0, 12) + valor.substring(12, 16);
                                }

                                input.value = valor;
                            }
                        </script>
                        <label>AP.</label>
                        <input type="text" id="nroap" name="nroap" class="form-control form-control-sm">
                        <label>REF.</label>
                        <input type="text" id="nroref" name="nroref" class="form-control form-control-sm">
                    </div>
                    <div class="form-group cheque-fields d-none">
                        <label>Nro. Cheque</label>
                        <input type="text" id="nrocheque" name="nrocheque" class="form-control form-control-sm">
                        <div class="form-group mb-3">
                            <label for="tipobancocheque">Tipo Banco</label>
                            <select name="tipobancocheque" id="tipobancocheque" class="form-control form-control-sm">
                                <option value=""></option>
                                @foreach ($bancos as $banco)
                                    <option value="{{ $banco->nombrebanco }}">{{ $banco->nombrebanco }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label for="bancoDestino">Nro. Banco Destino</label>
                            <select name="nrocuentadestinocheque" id="nrocuentadestinocheque" class="form-control form-control-sm">
                                <option value=""></option>
                                @foreach ($cuentas as $cuenta)
                                    <option value="{{ $cuenta->numerocuenta }}">{{ $cuenta->numerocuenta }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group deposito-fields d-none">
                        <div class="form-group mb-3">
                            <label for="bancoDestino">Nro. Banco Origen</label>
                            <select name="nrocuentadestinodeposito" id="nrocuentadestinodeposito" class="form-control form-control-sm">
                                <option value=""></option>
                                @foreach ($cuentas as $cuenta)
                                    <option value="{{ $cuenta->numerocuenta }}">{{ $cuenta->numerocuenta }}</option>
                                @endforeach
                            </select>
                        </div>
                        <label>Bancarización</label>
                        <input type="text" id="nrobancarizaciondeposito" name="nrobancarizaciondeposito" class="form-control form-control-sm">
                    </div>
                    <div class="form-group transferencia-fields d-none">
                        <div class="form-group mb-3">
                            <label for="bancoDestino">Nro. Banco Origen</label>
                            <select name="nrocuentadestinotransferencia" id="nrocuentadestinotransferencia" class="form-control form-control-sm">
                                <option value=""></option>
                                @foreach ($cuentas as $cuenta)
                                    <option value="{{ $cuenta->numerocuenta }}">{{ $cuenta->numerocuenta }}</option>
                                @endforeach
                            </select>
                        </div>
                        <label>Bancarización</label>
                        <input type="text" id="nrobancarizaciontransferencia" name="nrobancarizaciontransferencia" class="form-control form-control-sm">
                    </div>
                    <div class="form-group efectivo-fields d-none">
                        <label>Tipo de Cambio</label>
                        <select name="tipocambio" id="tipocambio" class="form-control form-control-sm">
                            <option value="Bs.">Bs.</option>
                            <option value="Usd.">Usd.</option>
                        </select>
                    </div>
                </div>

                {{-- PANEL DERECHO --}}
                <div class="col-md-10">
                    <div class="card">
                        <div class="card-header bg-secondary text-white text-center">
                            <h5 style="margin-top: -5px; margin-bottom: -5px; font-weight: 700;">PAGOS PENDIENTES</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="form-group col-lg-2">
                                    <label>ID</label>
                                    <input type="text" class="form-control form-control-sm" id="clienteid" name="clienteid" placeholder="ID del cliente/proveedor" readonly>
                                </div>
                                <div class="form-group col-lg-6">
                                    <label>Cliente/Proveedor</label>
                                    <input type="text" id="clientenombre" name="clientenombre" class="form-control form-control-sm" placeholder="Nombre del cliente/proveedor" readonly>
                                </div>
                                <div class="form-group col-lg-4">
                                    <label>CI/NIT</label>
                                    <input type="text" class="form-control form-control-sm" placeholder="CI/NIT del cliente/proveedor" readonly>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-bordered mt-3 table-sm table-striped">
                                    <thead>
                                        <tr class="bg-secondary text-white" style="text-align: center;">
                                            <th style="width: 5%;">ID</th>
                                            <th style="width: 28%;">Detalle</th>
                                            <th style="width: 12%;">Prog.</th>
                                            <th hidden style="width: 0%;">Fecha de Batería</th>
                                            <th style="width: 20%;">Servicio</th>
                                            <th style="width: 9%;">Precio</th>
                                            <th style="width: 9%;">Descuento</th>
                                            <th style="width: 12%;">Pago</th>  
                                            <th style="width: 5%;">
                                                <label for="selectAll" style="display: inline-flex; align-items: center; justify-content: center; padding-left: 10px; margin-bottom: 0px;">
                                                    <input type="checkbox" id="selectAll" class="form-check-input" style="margin-right: 20px;">
                                                    Sel.
                                                </label>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody id="tablaRegistros">
                                    </tbody>
                                </table>
                            </div>
                            <div class="card-body card" style="background-color: #90909011">
                                <h5 class="text-center" style="margin-top: 0; font-weight: 700;">RESUMEN DE INGRESO</h5>
                                <div class="row">
                                    <input type="hidden" class="form-control border border-dark" name="montoreal" id="montoreal" value="0" readonly>
                                    <div class="form-group col-lg-3">
                                        <label>Subtotal</label>
                                        <input type="text" class="form-control form-control-sm" name="subtotal" placeholder="Subtotal" value="0" readonly>
                                    </div>
                                    <div class="form-group col-lg-3">
                                        <label>Descuento</label>
                                        <input type="text" class="form-control form-control-sm" name="descuento" placeholder="Descuento" value="0" readonly>
                                    </div>
                                    <div class="form-group col-lg-3">
                                        <label>Total</label>
                                        <input type="text" class="form-control form-control-sm border border-dark" name="montototal" id="montototal" placeholder="Total" value="0" readonly>
                                    </div>
                                    <div class="form-group col-lg-3">
                                        <label>Registrar</label>
                                            <button class="btn-sm btn btn-secondary btn-block registrar-btn" id="imprimirReciboBtn" 
                                                    onclick="imprimirReciboSeleccionados()">
                                                GUARDAR REGISTRO
                                            </button>
                                            <a class="btn-sm btn btn-secondary" id="abrirModalBtn" data-toggle="modal" data-target="#modalArqueo" style="display: none;">
                                                ARQUEO DE CAJA
                                            </a>

                                        <script>
                                            let timer;
                                            document.addEventListener('click', function (event) {
                                                if (event.target && event.target.id === 'actualizarId') {
                                                    fetch('{{ route('actualizar_id') }}')
                                                        .then(response => response.json())
                                                        .then(data => {
                                                            if (data.siguienteId) {
                                                                document.getElementById('siguienteId').value = data.siguienteId;
                                                            } else {
                                                                alert('No se pudo obtener el siguiente ID.');
                                                            }
                                                        })
                                                        .catch(error => console.error('Error:', error));
                                        
                                                    document.getElementById('actualizarId').style.display = 'none';
                                                    document.getElementById('imprimirReciboBtn').style.display = 'inline-block';
                                        
                                                    timer = setTimeout(function() {
                                                        console.log("No se presionó Guardar, ocultando el botón Guardar y mostrando Generar Recibo.");
                                                        document.getElementById('imprimirReciboBtn').style.display = 'none';
                                                        document.getElementById('actualizarId').style.display = 'inline-block';
                                                    }, 2000);
                                                }
                                                if (event.target && event.target.id === 'imprimirReciboBtn') {
                                                    clearTimeout(timer);
                                        
                                                    document.getElementById('imprimirReciboBtn').style.display = 'none';
                                                    document.getElementById('actualizarId').style.display = 'inline-block';
                                                }
                                            });
                                        </script>
                            
                                        <div class="modal fade" id="modalArqueo" tabindex="-1" aria-labelledby="modalArqueoLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content">
                                                    <div class="modal-header" style="text-align: center; width: 100%; justify-content: center; margin-bottom:-25px;">
                                                        <h5 class="modal-title" id="modalArqueoLabel" style="font-weight: 900; margin: 0;">
                                                            ARQUEO DE CAJA
                                                        </h5>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="card shadow-sm border p-2" style="background-color: #eeeded">
                                                            <h6 class="card-title fw-bold mb-1" style="text-align: center; width: 100%; justify-content: center; margin-bottom:-10px;">RESUMEN DE MOVIMIENTO</h6>
                                                            <div class="card-body p-2 text-center" style="background-color: #ffffff">
                                                                <div class="row align-items-center">
                                                                    <div class="col-8 text-center">
                                                                        <div class="d-flex justify-content-between">
                                                                            <div class="flex-fill">
                                                                                <small>MONTO REAL</small>
                                                                                <p class="h6 mb-0"><strong id="montoRealModal">0.00</strong></p>
                                                                            </div>
                                                                            <div class="flex-fill">
                                                                                <small>MONTO TOTAL</small>
                                                                                <p class="h2 mb-0"><strong id="montoTotalModal">0.00</strong></p>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-4 text-center">
                                                                        <div class="d-flex flex-column">
                                                                            <div>
                                                                                <small>Dif. Contra</small>
                                                                                <input type="text" name="diferenciacontra" id="diferenciacontra" class="form-control form-control-sm text-center mx-auto" style="width: 60px;" value="0.00" readonly>
                                                                            </div>
                                                                            <div class="mt-1">
                                                                                <small>Dif. Favor</small>
                                                                                <input type="text" name="diferenciafavor" id="diferenciafavor" class="form-control form-control-sm text-center mx-auto" style="width: 60px;" value="0.00" readonly>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="row">
                                                            <div class="col-lg-6">
                                                                <div class="card shadow-sm border p-2" style="background-color: #eeeded">
                                                                    <div class="card-body p-2" style="background-color: #ffffff">
                                                                        <div class="form-group">
                                                                            <label for="montoPagado" class="mb-1" style="text-align: center; width: 100%; justify-content: center; background-color: #eeeded"><strong>PAGO DEL CLIENTE</strong></label>
                                                                            <input type="number" name="montoPagado" id="montoPagado" class="form-control form-control-lg text-center" value="0" style="height: 30px;">
                                                                        </div>
                                                                
                                                                        <!-- Sección de Billetes y Monedas -->
                                                                        <div class="row mt-3">
                                                                            <!-- Billetes -->
                                                                            <div class="col-lg-6">
                                                                                <div class="border p-2">
                                                                                    <h6 class="text-center mb-2"><strong>Billetes</strong></h6>
                                                                                    <div class="form-group">
                                                                                        <label for="billete200" class="mb-1">200 Bs.</label>
                                                                                        <input type="number" name="billetecorte200" id="billetecorte200" class="form-control form-control-sm text-center" value="0" data-billete="200" style="height: 25px;">
                                                                                    </div>
                                                                                    <div class="form-group">
                                                                                        <label for="billete100" class="mb-1">100 Bs.</label>
                                                                                        <input type="number" name="billetecorte100" id="billetecorte100" class="form-control form-control-sm text-center" value="0" data-billete="100" style="height: 25px;">
                                                                                    </div>
                                                                                    <div class="form-group">
                                                                                        <label for="billete50" class="mb-1">50 Bs.</label>
                                                                                        <input type="number" name="billetecorte50" id="billetecorte50" class="form-control form-control-sm text-center" value="0" data-billete="50" style="height: 25px;">
                                                                                    </div>
                                                                                    <div class="form-group">
                                                                                        <label for="billete20" class="mb-1">20 Bs.</label>
                                                                                        <input type="number" name="billetecorte20" id="billetecorte20" class="form-control form-control-sm text-center" value="0" data-billete="20" style="height: 25px;">
                                                                                    </div>
                                                                                    <div class="form-group">
                                                                                        <label for="billete10" class="mb-1">10 Bs.</label>
                                                                                        <input type="number" name="billetecorte10" id="billetecorte10" class="form-control form-control-sm text-center" value="0" data-billete="10" style="height: 25px;">
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                
                                                                            <!-- Monedas -->
                                                                            <div class="col-lg-6">
                                                                                <div class="border p-2">
                                                                                    <h6 class="text-center mb-2"><strong>Monedas</strong></h6>
                                                                                    <div class="form-group">
                                                                                        <label for="moneda5" class="mb-1">5 Bs.</label>
                                                                                        <input type="number" name="monedacorte5" id="monedacorte5" class="form-control form-control-sm text-center" value="0" data-moneda="5" style="height: 25px;">
                                                                                    </div>
                                                                                    <div class="form-group">
                                                                                        <label for="moneda2" class="mb-1">2 Bs.</label>
                                                                                        <input type="number" name="monedacorte2" id="monedacorte2" class="form-control form-control-sm text-center" value="0" data-moneda="2" style="height: 25px;">
                                                                                    </div>
                                                                                    <div class="form-group">
                                                                                        <label for="moneda1" class="mb-1">1 Bs.</label>
                                                                                        <input type="number" name="monedacorte1" id="monedacorte1" class="form-control form-control-sm text-center" value="0" data-moneda="1" style="height: 25px;">
                                                                                    </div>
                                                                                    <div class="form-group">
                                                                                        <label for="moneda050" class="mb-1">0.50 Bs.</label>
                                                                                        <input type="number" name="monedacorte050" id="monedacorte050" class="form-control form-control-sm text-center" value="0" data-moneda="0.50" style="height: 25px;">
                                                                                    </div>
                                                                                    <div class="form-group">
                                                                                        <label for="moneda020" class="mb-1">0.20 Bs.</label>
                                                                                        <input type="number" name="monedacorte020" id="monedacorte020" class="form-control form-control-sm text-center" value="0" data-moneda="0.20" style="height: 25px;">
                                                                                    </div>
                                                                                    <div class="form-group">
                                                                                        <label for="moneda010" class="mb-1">0.10 Bs.</label>
                                                                                        <input type="number" name="monedacorte010" id="monedacorte010" class="form-control form-control-sm text-center" value="0" data-moneda="0.10" style="height: 25px;">
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                
                                                                        <!-- Monto Restante Arqueo-->
                                                                        <div class="form-group mt-3">
                                                                            <label for="montoTotalDisplay" class="mb-1"><strong>Monto Restante Arqueo:</strong></label>
                                                                            <input type="text" id="montoTotalDisplay" class="form-control form-control-lg text-center" value="0" readonly>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="col-lg-6">
                                                                <div class="card shadow-sm border p-2" style="background-color: #eeeded">
                                                                    <div class="card-body p-2" style="background-color: #ffffff">
                                                                        <!-- Pago del Cliente -->
                                                                        <div class="form-group">
                                                                            <label for="cambio" class="mb-1" style="text-align: center; width: 100%; justify-content: center; background-color: #eeeded"><strong>CAMBIO AL CLIENTE</strong></label>
                                                                            <input type="number" name="cambio" id="cambio" class="form-control form-control-lg text-center" value="0" style="height: 30px;" readonly>
                                                                        </div>
                                                                
                                                                        <!-- Sección de Billetes y Monedas -->
                                                                        <div class="row mt-3">
                                                                            <!-- Billetes -->
                                                                            <div class="col-lg-6">
                                                                                <div class="border p-2">
                                                                                    <h6 class="text-center mb-2"><strong>Billetes</strong></h6>
                                                                                    <div class="form-group">
                                                                                        <label for="billete200" class="mb-1">200 Bs.</label>
                                                                                        <div class="row">
                                                                                            <div class="col-lg-6">
                                                                                                <input type="number" name="arqueobilletecorte200" id="arqueobilletecorte200" class="form-control form-control-sm text-center" value="{{ $arqueo->billetecorte200 ?? 0 }}" style="height: 25px;" readonly>
                                                                                            </div>
                                                                                            <div class="col-lg-6">
                                                                                                <input type="number" name="cambiobilletecorte200" id="cambiobilletecorte200" class="form-control form-control-sm text-center" value="0" style="height: 25px;">
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="form-group">
                                                                                        <label for="billete100" class="mb-1">100 Bs.</label>
                                                                                        <div class="row">
                                                                                            <div class="col-lg-6">
                                                                                                <input type="number" name="arqueobilletecorte100" id="arqueobilletecorte100" class="form-control form-control-sm text-center" value="{{ $arqueo->billetecorte100 ?? 0 }}" style="height: 25px;" readonly>
                                                                                            </div>
                                                                                            <div class="col-lg-6">
                                                                                                <input type="number" name="cambiobilletecorte100" id="cambiobilletecorte100" class="form-control form-control-sm text-center" value="0" style="height: 25px;">
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="form-group">
                                                                                        <label for="billete50" class="mb-1">50 Bs.</label>
                                                                                        <div class="row">
                                                                                            <div class="col-lg-6">
                                                                                                <input type="number" name="arqueobilletecorte50" id="arqueobilletecorte50" class="form-control form-control-sm text-center" value="{{ $arqueo->billetecorte50 ?? 0 }}" style="height: 25px;" readonly>
                                                                                            </div>
                                                                                            <div class="col-lg-6">
                                                                                                <input type="number" name="cambiobilletecorte50" id="cambiobilletecorte50" class="form-control form-control-sm text-center" value="0" style="height: 25px;">
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="form-group">
                                                                                        <label for="billete20" class="mb-1">20 Bs.</label>
                                                                                        <div class="row">
                                                                                            <div class="col-lg-6">
                                                                                                <input type="number" name="arqueobilletecorte20" id="arqueobilletecorte20" class="form-control form-control-sm text-center" value="{{ $arqueo->billetecorte20 ?? 0 }}" style="height: 25px;" readonly>
                                                                                            </div>
                                                                                            <div class="col-lg-6">
                                                                                                <input type="number" name="cambiobilletecorte20" id="cambiobilletecorte20" class="form-control form-control-sm text-center" value="0" style="height: 25px;">
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="form-group">
                                                                                        <label for="billete10" class="mb-1">10 Bs.</label>
                                                                                        <div class="row">
                                                                                            <div class="col-lg-6">
                                                                                                <input type="number" name="arqueobilletecorte10" id="arqueobilletecorte10" class="form-control form-control-sm text-center" value="{{ $arqueo->billetecorte10 ?? 0 }}" style="height: 25px;" readonly>
                                                                                            </div>
                                                                                            <div class="col-lg-6">
                                                                                                <input type="number" name="cambiobilletecorte10" id="cambiobilletecorte10" class="form-control form-control-sm text-center" value="0" style="height: 25px;">
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                
                                                                            <!-- Monedas -->
                                                                            <div class="col-lg-6">
                                                                                <div class="border p-2">
                                                                                    <h6 class="text-center mb-2"><strong>Monedas</strong></h6>
                                                                                    <div class="form-group">
                                                                                        <label for="moneda5" class="mb-1">5 Bs.</label>
                                                                                        <div class="row">
                                                                                            <div class="col-lg-6">
                                                                                                <input type="number" name="arqueomoneda5" id="arqueomoneda5" class="form-control form-control-sm text-center" value="{{ $arqueo->monedacorte5 ?? 0 }}" style="height: 25px;" readonly>
                                                                                            </div>
                                                                                            <div class="col-lg-6">
                                                                                                <input type="number" name="cambiomoneda5" id="cambiomoneda5" class="form-control form-control-sm text-center" value="0" style="height: 25px;">
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="form-group">
                                                                                        <label for="moneda2" class="mb-1">2 Bs.</label>
                                                                                        <div class="row">
                                                                                            <div class="col-lg-6">
                                                                                                <input type="number" name="arqueomoneda2" id="arqueomoneda2" class="form-control form-control-sm text-center" value="{{ $arqueo->monedacorte2 ?? 0 }}" style="height: 25px;" readonly>
                                                                                            </div>
                                                                                            <div class="col-lg-6">
                                                                                                <input type="number" name="cambiomoneda2" id="cambiomoneda2" class="form-control form-control-sm text-center" value="0" style="height: 25px;">
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="form-group">
                                                                                        <label for="moneda1" class="mb-1">1 Bs.</label>
                                                                                        <div class="row">
                                                                                            <div class="col-lg-6">
                                                                                                <input type="number" name="arqueomoneda1" id="arqueomoneda1" class="form-control form-control-sm text-center" value="{{ $arqueo->monedacorte1 ?? 0 }}" style="height: 25px;" readonly>
                                                                                            </div>
                                                                                            <div class="col-lg-6">
                                                                                                <input type="number" name="cambiomoneda1" id="cambiomoneda1" class="form-control form-control-sm text-center" value="0" style="height: 25px;">
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="form-group">
                                                                                        <label for="moneda050" class="mb-1">0.50 Bs.</label>
                                                                                        <div class="row">
                                                                                            <div class="col-lg-6">
                                                                                                <input type="number" name="arqueomoneda050" id="arqueomoneda050" class="form-control form-control-sm text-center" value="{{ $arqueo->monedacorte050 ?? 0 }}" style="height: 25px;" readonly>
                                                                                            </div>
                                                                                            <div class="col-lg-6">
                                                                                                <input type="number" name="cambiomoneda050" id="cambiomoneda050" class="form-control form-control-sm text-center" value="0" style="height: 25px;">
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="form-group">
                                                                                        <label for="moneda020" class="mb-1">0.20 Bs.</label>
                                                                                        <div class="row">
                                                                                            <div class="col-lg-6">
                                                                                                <input type="number" name="arqueomoneda020" id="arqueomoneda020" class="form-control form-control-sm text-center" value="{{ $arqueo->monedacorte020 ?? 0 }}" style="height: 25px;" readonly>
                                                                                            </div>
                                                                                            <div class="col-lg-6">
                                                                                                <input type="number" name="cambiomoneda020" id="cambiomoneda020" class="form-control form-control-sm text-center" value="0" style="height: 25px;">
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="form-group">
                                                                                        <label for="moneda010" class="mb-1">0.10 Bs.</label>
                                                                                        <div class="row">
                                                                                            <div class="col-lg-6">
                                                                                                <input type="number" name="arqueomoneda010" id="arqueomoneda010" class="form-control form-control-sm text-center" value="{{ $arqueo->monedacorte010 ?? 0 }}" style="height: 25px;" readonly>
                                                                                            </div>
                                                                                            <div class="col-lg-6">
                                                                                                <input type="number" name="cambiomoneda010" id="cambiomoneda010" class="form-control form-control-sm text-center" value="0" style="height: 25px;">
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                
                                                                        <!-- Monto Restante Cambio-->
                                                                        <div class="form-group mt-3">
                                                                            <label for="montocambio" class="mb-1"><strong>Monto Restante Cambio:</strong></label>
                                                                            <input type="text" name="montocambio" id="montocambio" class="form-control form-control-lg text-center" value="0" readonly>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    {{-- <div id="buttonContainer" style="display: flex; flex-direction: column; gap: 10px;">
                                                        <a id="actualizarId" class="btn btn-secondary btn-block">
                                                            INSERTAR DATOS
                                                        </a>
                                                        <button class="btn btn-success btn-block registrar-btn" id="imprimirReciboBtn" 
                                                                onclick="imprimirReciboSeleccionados()">
                                                            GUARDAR REGISTRO
                                                        </button>
                                                    </div> --}}

                                                    {{-- CAMBIO DE BOTON DE INSERTAR DATOS EN EFECTIVO --}}
                                                    <style>
                                                        .button-container {
                                                            position: relative;
                                                            margin-bottom: 20px;
                                                            width: 100%;
                                                            display: flex;
                                                            justify-content: center;
                                                        }
                                                    
                                                        .boton-wrapper {
                                                            position: relative;
                                                            width: 200px;
                                                            height: 45px;
                                                        }
                                                    
                                                        .btn-flotante {
                                                            position: absolute;
                                                            top: 0;
                                                            left: 0;
                                                            width: 100%;
                                                        }
                                                    
                                                        .btn-insertar {
                                                            z-index: 2;
                                                        }
                                                    
                                                        .btn-guardar {
                                                            z-index: 1;
                                                            transition: z-index 0.3s ease;
                                                        }
                                                    </style>
                                                    <div class="button-container">
                                                        <div class="boton-wrapper">
                                                            {{-- <a id="actualizarId" class="btn btn-secondary btn-flotante btn-insertar">
                                                                INSERTAR DATOS
                                                            </a> --}}
                                                            <button class="btn-sm btn btn-secondary btn-flotante btn-guardar"
                                                                onclick="imprimirReciboSeleccionados()"
                                                                {{-- disabled --}}>
                                                                GUARDAR REGISTRO
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <script>
                                                        document.querySelectorAll('.button-container').forEach(container => {
                                                            const insertarBtn = container.querySelector('.btn-insertar');
                                                            const guardarBtn = container.querySelector('.btn-guardar');
                                                    
                                                            insertarBtn.addEventListener('click', function () {
                                                                guardarBtn.disabled = false;
                                                                guardarBtn.style.zIndex = 4;
                                                                setTimeout(() => {
                                                                    guardarBtn.style.zIndex = 1;
                                                                }, 1800);
                                                            });
                                                        });
                                                    </script>

                                                    {{--  CALCULAR MONTOS TOTALES Y REDONDEO --}}
                                                    <script>
                                                        document.getElementById('montoPagado').addEventListener('input', function() {
                                                            var montoPagado = parseFloat(this.value) || 0;
                                                            document.getElementById('montoTotalDisplay').value = montoPagado.toFixed(2);
                                                        });

                                                        function actualizarMontoRestante() {
                                                            var montoPagado = parseFloat(document.getElementById('montoPagado').value) || 0;
                                                            var totalBilletesYMonedas = 0;

                                                            // Obtener los valores de los billetes y monedas
                                                            var inputsBilletesYMonedas = document.querySelectorAll('input[data-billete], input[data-moneda]');
                                                            inputsBilletesYMonedas.forEach(function(input) {
                                                                var cantidad = parseFloat(input.value) || 0;
                                                                var valor = parseFloat(input.getAttribute('data-billete')) || parseFloat(input.getAttribute('data-moneda')) || 0;
                                                                totalBilletesYMonedas += cantidad * valor;
                                                            });

                                                            // Calcular el monto restante
                                                            var montoRestante = montoPagado - totalBilletesYMonedas;
                                                            document.getElementById('montoTotalDisplay').value = montoRestante.toFixed(2);

                                                            // Habilitar o deshabilitar el botón de impresión según el monto restante
                                                            var botonGuardar = document.getElementById('imprimirReciboBtn');
                                                            botonGuardar.disabled = (montoRestante.toFixed(2) !== "0.00");
                                                        }

                                                        // Agregar eventos a los inputs de billetes y monedas
                                                        document.querySelectorAll('input[data-billete], input[data-moneda]').forEach(function(input) {
                                                            input.addEventListener('input', actualizarMontoRestante);
                                                        });
                                                    
                                                        document.getElementById('abrirModalBtn').addEventListener('click', function() {
                                                            var montoTotal = parseFloat(document.getElementById('montototal').value) || 0;
                                                            var montoRedondeado = redondearMonto(montoTotal);
                                                            var diferencia = Math.abs(montoTotal - montoRedondeado);
                                                    
                                                            // Ajustar los valores de diferencia
                                                            if (montoRedondeado > montoTotal) {
                                                                document.getElementById('diferenciacontra').value = '0';
                                                                document.getElementById('diferenciafavor').value = diferencia.toFixed(2);
                                                            } else {
                                                                document.getElementById('diferenciacontra').value = diferencia.toFixed(2);
                                                                document.getElementById('diferenciafavor').value = '0';
                                                            }
                                                    
                                                            // Mostrar montos en el modal
                                                            document.getElementById('montoTotalModal').textContent = montoRedondeado.toFixed(2);
                                                            document.getElementById('montoRealModal').textContent = montoTotal.toFixed(2);
                                                    
                                                            // Actualizar monto restante arqueo
                                                            actualizarMontoRestante();
                                                        });
                                                    
                                                        // Función de redondeo (a múltiplos de 0.10)
                                                        function redondearMonto(monto) {
                                                            return Math.round(monto * 10) / 10;
                                                        }
                                                    
                                                        // Calcular el cambio según el pago ingresado
                                                        document.getElementById('montoPagado').addEventListener('input', function() {
                                                            var montoTotalRedondeado = parseFloat(document.getElementById('montoTotalModal').textContent) || 0;
                                                            var montoPagado = parseFloat(document.getElementById('montoPagado').value) || 0;
                                                            var cambio = montoPagado - montoTotalRedondeado;
                                                            var montocambio = montoPagado - montoTotalRedondeado;
                                                            document.getElementById('cambio').value = cambio.toFixed(2);
                                                            document.getElementById('montocambio').value = montocambio.toFixed(2);
                                                        });
                                                    </script>
                                                    
                                                    {{-- CALCULAR MONTO DE CAMBIOS --}}
                                                    <script>
                                                        // Función para calcular el cambio al cliente y monto restante
                                                        function calcularCambio() {
                                                            var montoPagado = parseFloat(document.getElementById('montoPagado').value) || 0;
                                                            var montoDeuda = 400.00; // Monto de la deuda
                                                            
                                                            // Calcular el cambio
                                                            var cambio = montoPagado - montoDeuda;
                                                            // Actualizar el valor de "Monto Restante Cambio" (campo visible en el formulario)
                                                            document.getElementById('montocambio').value = cambio.toFixed(2);
                                                        }

                                                        // Función para recalcular el monto restante de cambio basado en los billetes y monedas
                                                        function recalcularMontoRestanteCambio() {
                                                            var billete200 = parseInt(document.getElementById('cambiobilletecorte200').value) || 0;
                                                            var billete100 = parseInt(document.getElementById('cambiobilletecorte100').value) || 0;
                                                            var billete50 = parseInt(document.getElementById('cambiobilletecorte50').value) || 0;
                                                            var billete20 = parseInt(document.getElementById('cambiobilletecorte20').value) || 0;
                                                            var billete10 = parseInt(document.getElementById('cambiobilletecorte10').value) || 0;
                                                            var moneda5 = parseInt(document.getElementById('cambiomoneda5').value) || 0;
                                                            var moneda2 = parseInt(document.getElementById('cambiomoneda2').value) || 0;
                                                            var moneda1 = parseInt(document.getElementById('cambiomoneda1').value) || 0;
                                                            var moneda050 = parseInt(document.getElementById('cambiomoneda050').value) || 0;
                                                            var moneda020 = parseInt(document.getElementById('cambiomoneda020').value) || 0;
                                                            var moneda010 = parseInt(document.getElementById('cambiomoneda010').value) || 0;

                                                            // Calcular el total de billetes y monedas entregados por el cliente
                                                            var totalProporcionado = (billete200 * 200) + (billete100 * 100) + (billete50 * 50) + (billete20 * 20) +
                                                                                    (billete10 * 10) + (moneda5 * 5) + (moneda2 * 2) + (moneda1 * 1) +
                                                                                    (moneda050 * 0.5) + (moneda020 * 0.2) + (moneda010 * 0.1);

                                                            // Obtener el cambio inicial
                                                            var cambioInicial = parseFloat(document.getElementById('cambio').value) || 0;

                                                            // Calcular el monto restante de cambio
                                                            var montoRestanteCambio = cambioInicial - totalProporcionado;

                                                            // Actualizar el campo de "Monto Restante Cambio"
                                                            document.getElementById('montocambio').value = montoRestanteCambio.toFixed(2);
                                                        }

                                                        // Evento para recalcular el cambio y monto restante cuando se cambia el monto pagado
                                                        document.getElementById('montoPagado').addEventListener('input', function() {
                                                            calcularCambio();
                                                            recalcularMontoRestanteCambio();
                                                        });

                                                        // Evento para recalcular el monto restante de cambio cuando se cambian los billetes y monedas
                                                        document.querySelectorAll('input[id^="cambiobilletecorte"], input[id^="cambiomoneda"]').forEach(function(input) {
                                                            input.addEventListener('input', recalcularMontoRestanteCambio);
                                                        });

                                                    </script>
                                                    
                                                    {{-- MANEJO DE ARQUEO PARA CAMBIO AL CLIENTE --}}
                                                    <script>
                                                        // Función para verificar si se debe habilitar o deshabilitar los campos de billetes y monedas
                                                        function verificarCambio() {
                                                            // Array con los IDs de los campos de arqueo para billetes y monedas
                                                            var arqueoIds = [
                                                                'arqueobilletecorte200', 'arqueobilletecorte100', 'arqueobilletecorte50',
                                                                'arqueobilletecorte20', 'arqueobilletecorte10', 'arqueomoneda5', 'arqueomoneda2',
                                                                'arqueomoneda1', 'arqueomoneda050', 'arqueomoneda020', 'arqueomoneda010'
                                                            ];
                                                    
                                                            // Iterar sobre los campos de arqueo y actualizar el estado de los campos de cambio
                                                            arqueoIds.forEach(function(id) {
                                                                // Identificar los campos de arqueo y cambio correspondientes
                                                                var arqueoValue = parseFloat(document.getElementById(id).value) || 0;
                                                                var cambioFieldId = id.replace('arqueo', 'cambio'); // Cambiar "arqueo" por "cambio" en el ID
                                                                var cambioField = document.getElementById(cambioFieldId);
                                                    
                                                                // Verificar si el campo arqueo tiene un valor menor o igual a cero
                                                                /* if (arqueoValue <= 0) {
                                                                    // Deshabilitar el campo de cambio y restablecer su valor
                                                                    if (cambioField) {
                                                                        cambioField.disabled = true;
                                                                        cambioField.value = "0";  // Restablecer el valor a cero
                                                                    }
                                                                } else {
                                                                    // Habilitar el campo de cambio si el valor es mayor a cero
                                                                    if (cambioField) {
                                                                        cambioField.disabled = false;
                                                    
                                                                        // Validar que el valor del campo de cambio no sea mayor que el del campo de arqueo
                                                                        var cambioValue = parseFloat(cambioField.value) || 0;
                                                                        if (cambioValue > arqueoValue) {
                                                                            // Limitar el valor de cambio al valor del campo de arqueo
                                                                            cambioField.value = arqueoValue;
                                                                        }
                                                                    }
                                                                } */
                                                            });
                                                        }
                                                    
                                                        // Llamar a la función cuando se carga la página para establecer el estado inicial
                                                        window.onload = function() {
                                                            verificarCambio();
                                                        };
                                                    
                                                        // Event listener para verificar el cambio cuando se modifiquen los valores de los campos de arqueo
                                                        document.querySelectorAll('[id^="arqueo"]').forEach(function(element) {
                                                            element.addEventListener('input', function() {
                                                                verificarCambio();
                                                            });
                                                        });
                                                    
                                                        // Event listener para verificar el cambio cuando se modifiquen los valores de los campos de cambio
                                                        document.querySelectorAll('[id^="cambio"]').forEach(function(element) {
                                                            element.addEventListener('input', function() {
                                                                verificarCambio();
                                                            });
                                                        });
                                                    </script>
                                                </div>
                                            </div>
                                        </div>
                                        <script>
                                            document.addEventListener('DOMContentLoaded', function () {
                                            const tipotransaccion = '{{ session('tipotransaccion') }}';
                                            const tipotransaccion2 = '{{ session('tipotransaccion2') }}';
                                            const montototal = parseFloat('{{ session('montototal') }}');
                                            
                                            /* if ((tipotransaccion === 'EFECTIVO' || tipotransaccion2 === 'EFECTIVO') && montototal) {
                                                $('#modalArqueo').modal('show');
                                            } */

                                            const inputs = document.querySelectorAll('#arqueoForm input');
                                            const saldoRestanteDisplay = document.getElementById('saldoRestanteDisplay');
                                            let saldoRestante = montototal;

                                            let valoresPrevios = {
                                                'billetecorte200': 0,
                                                'billetecorte100': 0,
                                                'billetecorte50': 0,
                                                'billetecorte20': 0,
                                                'billetecorte10': 0,
                                                'monedacorte5': 0,
                                                'monedacorte2': 0,
                                                'monedacorte1': 0,
                                                'monedacorte050': 0,
                                                'monedacorte020': 0,
                                                'monedacorte010': 0
                                            };

                                            const actualizarSaldo = () => {
                                                saldoRestanteDisplay.innerText = saldoRestante.toFixed(2);
                                                const guardarButton = document.getElementById('guardarArqueo');
                                                if (saldoRestante <= 0.09) {
                                                    guardarButton.disabled = false;
                                                } else {
                                                    guardarButton.disabled = true;
                                                }
                                            };

                                            inputs.forEach(input => {
                                                input.addEventListener('input', function () {
                                                    const tipo = input.id;
                                                    const cantidad = parseFloat(input.value) || 0;
                                                    const valorPrevia = valoresPrevios[tipo];
                                                    const tipoValor = input.dataset.billete || input.dataset.moneda;

                                                    const diferencia = (cantidad - valorPrevia) * tipoValor;
                                                    saldoRestante -= diferencia;
                                                    valoresPrevios[tipo] = cantidad;

                                                    actualizarSaldo();
                                                });
                                            });

                                            document.getElementById('guardarArqueo').addEventListener('click', function () {
                                                document.getElementById('saldoRestanteHidden').value = saldoRestante.toFixed(2);
                                                document.getElementById('arqueoForm').submit();
                                            });
                                            });
                                        </script>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="programacionIds" id="programacionIds">
                            <input type="hidden" name="descuentos" id="descuentos">
                            <input type="hidden" name="pagos" id="pagos">
                        </div>
                    </div>
                </div>    
            </div>
        </div>
    </div>
</form>
@endif
@stop

{{-- <td>
    <input type="number" style="height: 25px;" class="form-control registro-descuento" 
        placeholder="0.00" 
        value="0.00" 
        data-precio="${precio}" 
        data-id="${registro.id}" step="0.01" />
</td> --}}
@section('js')
{{-- BUSCAR REGISTROS DE PROGRAMACIONES DE CLIENTES --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {   
        const tipoClienteSelect = document.getElementById('tipoCliente');
        const clienteIdInput = document.getElementById('clienteid');
        const buscarClienteBtn = document.getElementById('buscarCliente');
        const buscarClienteTodoBtn = document.getElementById('buscarClienteTodo');
        const buscarPorFechaBtn = document.getElementById('buscarPorFecha');
        const fechaInicioInput = document.getElementById('fechaInicio');
        const fechaFinalInput = document.getElementById('fechaFinal');

        function toggleBuscarButton() {
            const tipoClienteSeleccionado = tipoClienteSelect.value.trim();
            const clienteIdInputVal = clienteIdInput.value.trim();

            buscarClienteBtn.disabled = !tipoClienteSeleccionado || !clienteIdInputVal;
            buscarClienteTodoBtn.disabled = !tipoClienteSeleccionado || !clienteIdInputVal;
            buscarPorFechaBtn.disabled = !tipoClienteSeleccionado || !clienteIdInputVal || !fechaInicioInput.value || !fechaFinalInput.value;
        }

        tipoClienteSelect.addEventListener('change', toggleBuscarButton);
        clienteIdInput.addEventListener('input', toggleBuscarButton);
        fechaInicioInput.addEventListener('change', toggleBuscarButton);
        fechaFinalInput.addEventListener('change', toggleBuscarButton);

        // Función para buscar solo los registros de hoy
        buscarClienteBtn.addEventListener('click', function () {
            const tipoCliente = tipoClienteSelect.value;
            const clienteId = clienteIdInput.value;

            fetch('{{ route("buscar.cliente") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
                body: JSON.stringify({ tipoCliente, clienteid: clienteId, buscarHoy: true })
            })
            .then(response => response.json())
            .then(data => {
                actualizarTabla(data, data.permitirDescuento, data.permisoExistefecha);
                const campoFechas = document.getElementById('campoFechas');
                if (campoFechas) {
                    // 🔴 Ocultar siempre primero
                    campoFechas.style.display = 'none';
                }

                // ✅ Mostrar solo si el permiso lo permite
                if (data.permisoExistefecha) {
                    if (campoFechas) {
                        campoFechas.style.display = 'block';
                    }

                    // Sincronizar updated_at con created_at
                    const createdAtInput = document.getElementById('created_at');
                    const updatedAtInput = document.getElementById('updated_at');
                    if (createdAtInput && updatedAtInput) {
                        createdAtInput.addEventListener('change', function () {
                            updatedAtInput.value = this.value;
                        });
                    }
                }
            })
            .catch(error => console.error('Error:', error));
        });

        // Función para mostrar todos los registros
        buscarClienteTodoBtn.addEventListener('click', function () {
            const tipoCliente = tipoClienteSelect.value;
            const clienteId = clienteIdInput.value;

            fetch('{{ route("buscar.cliente") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
                body: JSON.stringify({ tipoCliente, clienteid: clienteId, buscarHoy: false })

            })
            .then(response => response.json())
            .then(data => {
                actualizarTabla(data, data.permitirDescuento, data.permisoExistefecha);
                const campoFechas = document.getElementById('campoFechas');
                if (campoFechas) {
                    // 🔴 Ocultar siempre primero
                    campoFechas.style.display = 'none';
                }

                // ✅ Mostrar solo si el permiso lo permite
                if (data.permisoExistefecha) {
                    if (campoFechas) {
                        campoFechas.style.display = 'block';
                    }

                    // Sincronizar updated_at con created_at
                    const createdAtInput = document.getElementById('created_at');
                    const updatedAtInput = document.getElementById('updated_at');
                    if (createdAtInput && updatedAtInput) {
                        createdAtInput.addEventListener('change', function () {
                            updatedAtInput.value = this.value;
                        });
                    }
                }

            // CREDITOS
                let modalBody = document.querySelector('#modalCreditos .modal-body'); 
                if (data.tieneCredito && data.creditos.length > 0) {
                    // Inicializamos variables para agrupar los detalles y el monto total
                    let detallesUnicos = [];
                    let montoTotal = 0;

                    // Recorrer los créditos y agrupar detalles, y calcular el total de las cuotas
                    data.creditos.forEach(credito => {
                        // Evitar detalles repetidos
                        if (!detallesUnicos.includes(credito.detalle)) {
                            detallesUnicos.push(credito.detalle);
                        }
                        // Sumar el monto de la cuota
                        montoTotal += parseFloat(credito.montocuota);
                    });

                    // Crear el HTML para los detalles y monto total
                    let html = `
                        <div>
                            <strong>Detalles:</strong> ${detallesUnicos.join(', ')} <br>
                            <strong>Total Monto Crédito:</strong> ${montoTotal.toFixed(2)}
                        </div>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>ID_Cli.</th>
                                    <th>Cliente</th>
                                    <th>Cuota</th>
                                    <th>Fecha_Crédito</th>
                                </tr>
                            </thead>
                            <tbody>`;

                    // Agregar filas para los créditos
                    data.creditos.forEach(credito => {
                        html += `<tr>
                                    <td>${credito.clienteid}</td>
                                    <td>${credito.clientenombre}</td>
                                    <td>${credito.montocuota}</td>
                                    <td>${credito.fechacredito}</td>
                                </tr>`;
                    });

                    html += `   </tbody>
                            </table>`;
                    
                    modalBody.innerHTML = html;
                } else {
                    modalBody.innerHTML = `<p>No hay créditos disponibles.</p>`;
                }

                document.getElementById('btnVerCreditos').style.display = data.tieneCredito ? 'block' : 'none';

            //
            })

            .catch(error => console.error('Error:', error));
        });

        // Función para buscar por rango de fechas
        buscarPorFechaBtn.addEventListener('click', function () {
            const tipoCliente = tipoClienteSelect.value;
            const clienteId = clienteIdInput.value;
            const fechaInicio = fechaInicioInput.value;
            const fechaFinal = fechaFinalInput.value;

            fetch('{{ route("buscar.cliente") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
                body: JSON.stringify({ tipoCliente, clienteid: clienteId, buscarPorFechas: true, fechaInicio, fechaFinal })
            })
            .then(response => response.json())
            .then(data => {
                actualizarTabla(data, data.permitirDescuento, data.permisoExistefecha);
                const campoFechas = document.getElementById('campoFechas');
                if (campoFechas) {
                    // 🔴 Ocultar siempre primero
                    campoFechas.style.display = 'none';
                }

                // ✅ Mostrar solo si el permiso lo permite
                if (data.permisoExistefecha) {
                    if (campoFechas) {
                        campoFechas.style.display = 'block';
                    }

                    // Sincronizar updated_at con created_at
                    const createdAtInput = document.getElementById('created_at');
                    const updatedAtInput = document.getElementById('updated_at');
                    if (createdAtInput && updatedAtInput) {
                        createdAtInput.addEventListener('change', function () {
                            updatedAtInput.value = this.value;
                        });
                    }
                }
            })
            .catch(error => console.error('Error:', error));
        });

        // Función para actualizar la tabla con los resultados
        /* function actualizarTabla(data) { */
        function actualizarTabla(data, permitirDescuento = false) {

            const nombreInput = document.querySelector('input[placeholder="Nombre del cliente/proveedor"]');
            const idnombreInput = document.querySelector('input[placeholder="ID del cliente/proveedor"]');
            const ciInput = document.querySelector('input[placeholder="CI/NIT del cliente/proveedor"]');
            const subtotalInput = document.querySelector('input[placeholder="Subtotal"]');
            const descuentoInput = document.querySelector('input[placeholder="Descuento"]');
            const totalInput = document.querySelector('input[placeholder="Total"]');

            if (data.cliente) {
                nombreInput.value = data.cliente.nombrecompleto || data.cliente.razonsocial || data.cliente.proveedor;
                idnombreInput.value = data.cliente.id;
                ciInput.value = data.cliente.ci || data.cliente.nit;
            } else {
                alert('Cliente no encontrado');
                nombreInput.value = '';
                idnombreInput.value = '';
                ciInput.value = '';
                subtotalInput.value = '0.00';
                descuentoInput.value = '0.00';
                totalInput.value = '0.00';
            }

            const tabla = document.getElementById('tablaRegistros');
            tabla.innerHTML = '';
            if (data.registros.length > 0) {
                data.registros.forEach((registro) => {
                    let precio = parseFloat(registro.precio.replace(',', '.')).toFixed(2);
                    const fila = `
                        <tr>
                            <td>${registro.id}</td>
                            <td>${registro.accionnombre || ''} ${registro.nrosesion ? registro.nrosesion : ''} ${registro.cantidad > 0 ? registro.cantidad : ''} ${registro.detalleproducto || ''}</td>
                            <td hidden>${registro.fechabateria}</td>
                            <td>
                                ${registro.fechaasignada ? registro.fechaasignada : ''}
                                ${registro.fechacredito ? `(${registro.fechacredito})` : ''}
                            </td>
                            <td>${registro.tramite || ''} ${registro.detalle || ''}  ${registro.tipoorden || ''}</td>
                            <td>${precio}</td>
                            <td>
                                <input type="number" style="height: 25px;" class="form-control registro-descuento" 
                                    placeholder="0.00" 
                                    value="0.00" 
                                    data-precio="${precio}" 
                                    data-id="${registro.id}" step="0.01"
                                    ${permitirDescuento ? '' : 'disabled'} />
                            </td>
                            <td>
                                <input type="number" style="height: 25px;" class="form-control registro-pago" 
                                    placeholder="0.00" 
                                    value="${precio}" 
                                    data-precio="${precio}" 
                                    data-id="${registro.id}" step="0.01" />
                            </td>
                            <td>
                                <input type="checkbox" style="height: 25px;" class="registro-checkbox" data-precio="${precio}" />
                            </td>
                        </tr>
                    `;
                    tabla.innerHTML += fila;
                });
                actualizarEventosRegistro();
            } else {
                tabla.innerHTML = '<tr><td colspan="8">NO SE ENCONTRARON REGISTROS.</td></tr>';
            }

            // Lógica para el checkbox de seleccionar todo
            const selectAllCheckbox = document.getElementById('selectAll');
            const checkboxes = document.querySelectorAll('.registro-checkbox');

            selectAllCheckbox.addEventListener('change', function() {
                checkboxes.forEach(checkbox => {
                    checkbox.checked = selectAllCheckbox.checked;
                });
            });

            // Lógica para actualizar el checkbox "Seleccionar todo" si todos los checkboxes están seleccionados
            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    if (Array.from(checkboxes).every(cb => cb.checked)) {
                        selectAllCheckbox.checked = true;
                    } else {
                        selectAllCheckbox.checked = false;
                    }
                });
            });
        }

        // Deshabilitar el campo de pago si el descuento es diferente de 0
        function togglePagoField(descuento, pago) {
            const precio = parseFloat(descuento.dataset.precio);

            if (parseFloat(descuento.value) !== 0) {
                pago.disabled = true;  // Deshabilitar el campo pago si el descuento no es 0
            } else {
                pago.disabled = false;  // Habilitar el campo pago si el descuento es 0
                // Si el pago no está deshabilitado, actualizamos el valor del pago
                pago.value = (precio - parseFloat(descuento.value)).toFixed(2);  // Restar el descuento al precio
            }
        }

        // Actualizar el valor del campo pago cuando el descuento cambia
        document.addEventListener('input', function(event) {
            if (event.target.classList.contains('registro-descuento')) {
                const descuento = event.target;
                const row = descuento.closest('tr');
                const pago = row.querySelector('.registro-pago');
                const precio = parseFloat(descuento.dataset.precio);

                // Verificar si el descuento es mayor que el precio y no permitir que el pago sea negativo
                const descuentoValor = parseFloat(descuento.value).toFixed(2);
                let nuevoPago = precio - descuentoValor;

                // Asegurarse de que el pago nunca sea menor que 0
                if (nuevoPago < 0) {
                    nuevoPago = 0;
                }

                pago.value = nuevoPago.toFixed(2);  // Calculamos el nuevo pago con los dos decimales

                // Llamamos a la función para deshabilitar o habilitar el campo pago
                togglePagoField(descuento, pago);
            }
        });

        // Inicializar el estado de los campos de pago cuando la página carga
        const descuentos = document.querySelectorAll('.registro-descuento');
            const pagos = document.querySelectorAll('.registro-pago');
            descuentos.forEach((descuento, index) => {
                const pago = pagos[index];
                togglePagoField(descuento, pago);  // Establecer el estado correcto de cada fila
        });

        // Modificar el envío de datos en el formulario
        document.getElementById('guardarFormulario').addEventListener('submit', function(event) {
            // Obtener todos los campos de descuento y pago
            const descuentos = document.querySelectorAll('.registro-descuento');
            const pagos = document.querySelectorAll('.registro-pago');
            const programacionIds = [];
            const descuentosTotales = [];
            const pagosTotales = [];

            // Solo procesar los registros cuyo checkbox esté marcado
            const checkboxes = document.querySelectorAll('.registro-checkbox:checked');
            
            checkboxes.forEach(function(checkbox) {
                const row = checkbox.closest('tr');  // Obtener la fila más cercana
                const descuento = row.querySelector('.registro-descuento');
                const pago = row.querySelector('.registro-pago');

                const descuentoValor = descuento.value;
                const pagoValor = pago.value;
                programacionIds.push(descuento.getAttribute('data-id'));
                descuentosTotales.push(descuentoValor);
                pagosTotales.push(pagoValor);
            });

            // Pasar estos datos al formulario para que se envíen al servidor
            document.getElementById('programacionIds').value = programacionIds.join(',');
            document.getElementById('descuentos').value = descuentosTotales.join(',');
            document.getElementById('pagos').value = pagosTotales.join(',');
        });
    });

    function actualizarEventosRegistro() {  
        const descuentos = document.querySelectorAll('.registro-descuento');
        const pagos = document.querySelectorAll('.registro-pago');
        const checkboxes = document.querySelectorAll('.registro-checkbox');

        // Asegúrate de que todos los eventos estén correctamente escuchados
        descuentos.forEach(input => input.addEventListener('input', calcularTotal));
        pagos.forEach(input => input.addEventListener('input', calcularTotal));
        checkboxes.forEach(checkbox => checkbox.addEventListener('change', calcularTotal));
    }

    function calcularTotal() {
        const checkboxes = document.querySelectorAll('.registro-checkbox:checked');
        
        let subtotal = 0;  // Sumar los precios (sin descuento)
        let descuentoTotal = 0;  // Sumar los descuentos
        let totalPagoGeneral = 0;  // Sumar los pagos seleccionados

        // Iterar sobre los registros seleccionados
        checkboxes.forEach(checkbox => {
            const row = checkbox.closest('tr');
            const precio = parseFloat(checkbox.dataset.precio);  // Precio original del registro
            const descuento = parseFloat(row.querySelector('.registro-descuento').value || 0);  // Descuento
            const pago = parseFloat(row.querySelector('.registro-pago').value || 0);  // Pago

            // Solo sumar los precios, descuentos y pagos si el checkbox está marcado
            subtotal += precio;  // Sumar solo el precio
            descuentoTotal += descuento;  // Sumar solo el descuento
            totalPagoGeneral += pago;  // Sumar solo el pago
        });

        // El total es la suma de los pagos (no afectado por el descuento)
        const total = totalPagoGeneral;

        // Actualizar los campos del formulario con los valores calculados
        document.querySelector('input[placeholder="Subtotal"]').value = subtotal.toFixed(2);  // Suma de los precios
        document.querySelector('input[placeholder="Descuento"]').value = descuentoTotal.toFixed(2);  // Suma de los descuentos
        document.querySelector('input[placeholder="Total"]').value = total.toFixed(2);  // Total general (solo pagos)
        document.getElementById('montoreal').value = subtotal.toFixed(2);
    }

        document.addEventListener('change', function(event) {
            if (event.target.classList.contains('registro-checkbox')) {
                calcularTotal();  // Llama a la función cada vez que cambia un checkbox individual
            }
        });

        document.getElementById('selectAll').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.registro-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;  // Marcar o desmarcar todos
            });
            calcularTotal();  // Asegurar que se actualice el total al seleccionar todo
        });


</script>

{{-- ACTUALIZAR CAMPOS DE TIPO DE TRANSACCION --}}
<script>
    function validarTipoTransaccion() {
        var tipoTransaccion1 = document.getElementById("tipoTransaccion1").value;
        var dobleTransaccion = document.getElementById("dobleTransaccion").checked;
        var tipoTransaccion2 = document.getElementById("tipoTransaccion2");

        // Mostrar u ocultar botones según la selección de tipo de transacción
        var abrirModalBtn = document.getElementById("abrirModalBtn");
        var imprimirReciboBtn = document.getElementById("imprimirReciboBtn");
        var actualizarId = document.getElementById("actualizarId");

        /* if (tipoTransaccion1 === "EFECTIVO") {
            abrirModalBtn.style.display = "block"; // Mostrar botón para abrir modal
            imprimirReciboBtn.style.display = "none"; // Ocultar botón guardar registro
            actualizarId.style.display = "none"; // Ocultar botón insertar datos
        } else {
            abrirModalBtn.style.display = "none"; // Ocultar botón para abrir modal
            imprimirReciboBtn.style.display = "block";
            actualizarId.style.display = "block"; // Mostrar botón insertar datos
        } */

        if (tipoTransaccion1 === "EFECTIVO") {
            abrirModalBtn.style.display = "block";
            imprimirReciboBtn.style.display = "none";
        } else {
            abrirModalBtn.style.display = "none";
            imprimirReciboBtn.style.display = "block";
        }

        // Limpiar todos los campos de transacciones antes de mostrar los nuevos
        document.querySelectorAll(".atc-fields, .cheque-fields, .deposito-fields, .transferencia-fields, .efectivo-fields").forEach(function (element) {
            element.classList.add("d-none"); // Ocultar todos los campos de transacción
            element.querySelectorAll("input, select").forEach(function (input) {
                input.value = ""; // Limpiar los valores de los campos de entrada
            });
        });

        // Mostrar los campos de la transacción 1 seleccionada
        if (tipoTransaccion1 === "ATC") {
            document.querySelector(".atc-fields").classList.remove("d-none");
        } else if (tipoTransaccion1 === "CHEQUE") {
            document.querySelector(".cheque-fields").classList.remove("d-none");
        } else if (tipoTransaccion1 === "DEPOSITO_BANCARIO") {
            document.querySelector(".deposito-fields").classList.remove("d-none");
        } else if (tipoTransaccion1 === "TRANSFERENCIA_BANCARIA") {
            document.querySelector(".transferencia-fields").classList.remove("d-none");
        } else if (tipoTransaccion1 === "EFECTIVO") {
            document.querySelector(".efectivo-fields").classList.remove("d-none");
            abrirModalBtn.style.display = "block"; // Mostrar botón para abrir modal
            imprimirReciboBtn.style.display = "none"; // Ocultar botón guardar registro
            actualizarId.style.display = "none"; // Ocultar botón insertar datos
        }

        // Habilitar o deshabilitar el segundo select
        if (dobleTransaccion) {
            tipoTransaccion2.classList.remove("d-none");
        } else {
            tipoTransaccion2.classList.add("d-none");
            tipoTransaccion2.value = ""; // Limpiar valor del segundo select
        }

        // Filtrar opciones en el segundo select basado en la selección del primer select
        var opcionesTipo2 = tipoTransaccion2.querySelectorAll("option");
        opcionesTipo2.forEach(function (opcion) {
            if (opcion.value !== "" && opcion.value === tipoTransaccion1) {
                opcion.style.display = "none"; // Ocultar la opción seleccionada del primer select
            } else {
                opcion.style.display = "block"; // Mostrar el resto de las opciones
            }
        });

        // Mostrar campos de la segunda transacción si está marcada la opción de doble transacción
        if (dobleTransaccion && tipoTransaccion2.value) {
            var tipoTransaccion2Value = tipoTransaccion2.value;
            if (tipoTransaccion2Value === "ATC") {
                document.querySelector(".atc-fields").classList.remove("d-none");
            } else if (tipoTransaccion2Value === "CHEQUE") {
                document.querySelector(".cheque-fields").classList.remove("d-none");
            } else if (tipoTransaccion2Value === "DEPOSITO_BANCARIO") {
                document.querySelector(".deposito-fields").classList.remove("d-none");
            } else if (tipoTransaccion2Value === "TRANSFERENCIA_BANCARIA") {
                document.querySelector(".transferencia-fields").classList.remove("d-none");
            } else if (tipoTransaccion2Value === "EFECTIVO") {
                document.querySelector(".efectivo-fields").classList.remove("d-none");
            }
        }

        // Habilitar el botón solo si se ha seleccionado al menos una transacción
        var imprimirReciboBtn = document.getElementById("imprimirReciboBtn");
        if (tipoTransaccion1 && (dobleTransaccion && tipoTransaccion2.value || !dobleTransaccion)) {
            imprimirReciboBtn.disabled = false;  // Habilitar el botón
        } else {
            imprimirReciboBtn.disabled = true;  // Deshabilitar el botón
        }
    }
</script>

{{-- GENERAR RECIBO --}}
<script>
    function convertirNumeroATexto(numero) {
        const numerosTexto = [
            'Cero', 'Uno', 'Dos', 'Tres', 'Cuatro', 'Cinco', 'Seis', 'Siete', 'Ocho', 'Nueve',
            'Diez', 'Once', 'Doce', 'Trece', 'Catorce', 'Quince', 'Dieciséis', 'Diecisiete', 'Dieciocho', 'Diecinueve',
            'Veinte', 'Veintiuno', 'Veintidós', 'Veintitrés', 'Veinticuatro', 'Veinticinco', 'Veintiséis', 'Veintisiete', 'Veintiocho', 'Veintinueve',
            'Treinta', 'Treinta y uno', 'Treinta y dos', 'Treinta y tres', 'Treinta y cuatro', 'Treinta y cinco', 'Treinta y seis', 'Treinta y siete', 'Treinta y ocho', 'Treinta y nueve',
            'Cuarenta', 'Cuarenta y uno', 'Cuarenta y dos', 'Cuarenta y tres', 'Cuarenta y cuatro', 'Cuarenta y cinco', 'Cuarenta y seis', 'Cuarenta y siete', 'Cuarenta y ocho', 'Cuarenta y nueve',
            'Cincuenta', 'Cincuenta y uno', 'Cincuenta y dos', 'Cincuenta y tres', 'Cincuenta y cuatro', 'Cincuenta y cinco', 'Cincuenta y seis', 'Cincuenta y siete', 'Cincuenta y ocho', 'Cincuenta y nueve',
            'Sesenta', 'Sesenta y uno', 'Sesenta y dos', 'Sesenta y tres', 'Sesenta y cuatro', 'Sesenta y cinco', 'Sesenta y seis', 'Sesenta y siete', 'Sesenta y ocho', 'Sesenta y nueve',
            'Setenta', 'Setenta y uno', 'Setenta y dos', 'Setenta y tres', 'Setenta y cuatro', 'Setenta y cinco', 'Setenta y seis', 'Setenta y siete', 'Setenta y ocho', 'Setenta y nueve',
            'Ochenta', 'Ochenta y uno', 'Ochenta y dos', 'Ochenta y tres', 'Ochenta y cuatro', 'Ochenta y cinco', 'Ochenta y seis', 'Ochenta y siete', 'Ochenta y ocho', 'Ochenta y nueve',
            'Noventa', 'Noventa y uno', 'Noventa y dos', 'Noventa y tres', 'Noventa y cuatro', 'Noventa y cinco', 'Noventa y seis', 'Noventa y siete', 'Noventa y ocho', 'Noventa y nueve'
        ];

        const centenas = [
            '', 'Cien', 'Doscientos', 'Trescientos', 'Cuatrocientos', 'Quinientos', 'Seiscientos', 'Setecientos', 'Ochocientos', 'Novecientos'
        ];

        // Dividir en parte entera y decimal
        const partes = numero.toFixed(2).split('.');
        const parteEntera = parseInt(partes[0], 10);
        const parteDecimal = parseInt(partes[1], 10);

        let texto = '';

        // Convertir la parte entera
        if (parteEntera < 100) {
            texto = numerosTexto[parteEntera];
        } else if (parteEntera < 1000) {
            const centenasParte = Math.floor(parteEntera / 100);
            const resto = parteEntera % 100;
            texto = centenas[centenasParte] + (resto ? ' ' + numerosTexto[resto] : '');
        } else {
            texto = convertirMiles(partes[0]); // Llama a una función adicional si es necesario para manejar miles o millones
        }

        // Agregar la parte decimal
        if (parteDecimal > 0) {
            texto += ` con ${numerosTexto[parteDecimal]} centavos`;
        }

        return texto;
    }

    function convertirMiles(numero) {
        const miles = Math.floor(numero / 1000);
        const resto = numero % 1000;
        let textoMiles = `${convertirNumeroATexto(miles)} mil`;

        if (resto > 0) {
            textoMiles += ` ${convertirNumeroATexto(resto)}`;
        }

        return textoMiles;
    }

    function generarReciboSeleccionados() {
        const logoUrl = "{{ asset('img/logo3.png') }}";
        const nombreCliente = document.getElementById('clientenombre').value;
        const tipoTransaccion1 = document.getElementById('tipoTransaccion1').value;
        const idrecibo = document.getElementById('siguienteId').value;
        const nrofactura = document.getElementById('nrofactura').value;

        /* ATC */
        const nrotarjeta = document.getElementById('nrotarjeta').value;
        const nroap = document.getElementById('nroap').value;
        const nroref = document.getElementById('nroref').value;
        
        /* CHEQUE */
        const nrocheque = document.getElementById('nrocheque').value;
        const tipobancocheque = document.getElementById('tipobancocheque').value;
        const nrocuentadestinocheque = document.getElementById('nrocuentadestinocheque').value;

        /* DEPOSITO BANCARIO */
        const nrocuentadestinodeposito = document.getElementById('nrocuentadestinodeposito').value;
        const nrobancarizaciondeposito = document.getElementById('nrobancarizaciondeposito').value;

        /* TRANSFERENCIA BANCARIA */
        const nrocuentadestinotransferencia = document.getElementById('nrocuentadestinotransferencia').value;
        const nrobancarizaciontransferencia = document.getElementById('nrobancarizaciontransferencia').value;

        /* EFECTIVO */
        const tipocambio = document.getElementById('tipocambio').value;
        const montoPagado = document.getElementById('montoPagado').value;
        const cambio = document.getElementById('cambio').value;

        const createdAt = document.getElementById('created_at').value;
        const fechaHora = createdAt
            ? new Date(createdAt).toLocaleString()
            : new Date().toLocaleString();


        // Tipo de Transacción 2 (opcional)
        let tipoTransaccion2 = '';
        if (document.getElementById('dobleTransaccion').checked) {
            tipoTransaccion2 = document.getElementById('tipoTransaccion2').value;
        }

        const nombreUsuario = "{{ auth()->user()->name }}";

        // Iniciar HTML del recibo
        let reciboHTML = `
            <html>
            <head>
                <meta charset="UTF-8">
                <style>
                    body {
                        font-family: monospace;
                        text-align: center;
                        margin: 0;
                        padding: 0;
                    }
                    .recibo-container {
                        width: 250px;
                        padding: 20px;
                        border: 0px solid #000;
                        text-align: left;
                    }
                    .logo {
                        text-align: center;
                        margin-bottom: 10px;
                        margin-top: -10px;
                    }
                    .logo img {
                        max-width: 140px;
                        height: auto;
                    }
                    .linea {
                        border-top: 1px solid #000;
                        margin: 10px 0;
                    }
                    .tabla {
                        width: 100%;
                        margin-top: 10px;
                    }
                    .tabla td {
                        text-align: left;
                        padding: 3px 0px;
                        line-height: 0.8;
                        margin-top: -5px;
                        margin-bottom: -5px;
                    }
                    .tabla .precio {
                        text-align: right;
                    }
                    .firma {
                        margin-top: 20px;
                        padding-top: 10px;
                        margin-bottom: 10px;
                        text-align: center;
                    }
                    .info {
                        text-align: left;
                        font-size: 13px;
                        margin: 5px 0;
                        line-height: 1.2;
                    }
                    .fecha {
                        font-size: 12px;
                        margin: 5px 0;
                        line-height: 1.0;
                        text-align: center;
                    }
                    .recibo {
                        font-size: 17px;
                        margin: 5px 0;
                        line-height: 1.0;
                        text-align: center;
                    }
                </style>
            </head>
            <body>
                <div class="recibo-container">
                    <div class="logo"><img src="${logoUrl}" alt="Logo de la empresa"></div>
                    <div class="recibo"><strong>RECIBO Nro.${idrecibo}</strong></div>
                    <div class="fecha"><strong>Fecha y Hora:</strong> ${fechaHora}</div>
                    <div class="linea" style="margin-bottom: -1px;"></div>
                    <div class="info"><strong>Cliente:</strong> ${nombreCliente}</div>
                    <div class="info" style="margin-top: -3px;"><strong>Emitido por:</strong> ${nombreUsuario}</div>
                    <div class="linea" style="margin-top: -1px; margin-bottom: -1px;"></div>
                    <div class="info" style="margin-bottom: -1px;"><strong>Nro. Factura:</strong> ${nrofactura  ?? 0}</div>
                    <div class="info" style="margin-top: -1px; margin-bottom: -1px;"><strong>Tipo de Transacción:</strong> ${tipoTransaccion1}</div>
        `;

        // Mostrar solo secciones relevantes
        if (tipoTransaccion1 === 'ATC') {
            reciboHTML += `
                <div class="info" style="margin-top: -1px; margin-bottom: -1px;"><strong>Nro tarjeta:</strong> ${nrotarjeta}</div>
                <div class="info" style="margin-top: -1px; margin-bottom: -1px;"><strong>AP:</strong> ${nroap}</div>
                <div class="info" style="margin-bottom: -1px;"><strong>REF:</strong> ${nroref}</div>
                <div class="linea" style="margin-top: -1px;"></div>
            `;
        } else if (tipoTransaccion1 === 'CHEQUE') {
            reciboHTML += `
                <div class="info" style="margin-top: -1px; margin-bottom: -1px;"><strong>Nro Cheque:</strong> ${nrocheque}</div>
                <div class="info" style="margin-top: -1px; margin-bottom: -1px;"><strong>Tipo Banco:</strong> ${tipobancocheque}</div>
                <div class="info" style="margin-top: -1px;"><strong>Nro Cuenta Destino:</strong> ${nrocuentadestinocheque}</div>
                <div class="linea" style="margin-top: -1px;"></div>
            `;
        } else if (tipoTransaccion1 === 'DEPOSITO_BANCARIO') {
            reciboHTML += `
                <div class="info" style="margin-top: -1px; margin-bottom: -1px;"><strong>Nro Cuenta Destino:</strong> ${nrocuentadestinodeposito}</div>
                <div class="info" style="margin-top: -1px;"><strong>Bancarización:</strong> ${nrobancarizaciondeposito}</div>
                <div class="linea" style="margin-top: -1px;"></div>
            `;
        } else if (tipoTransaccion1 === 'TRANSFERENCIA_BANCARIA') {
            reciboHTML += `
                <div class="info" style="margin-top: -1px; margin-bottom: -1px;"><strong>Nro Cuenta Origen:</strong> ${nrocuentadestinotransferencia}</div>
                <div class="info" style="margin-top: -1px;"><strong>Bancarización:</strong> ${nrobancarizaciontransferencia}</div>
                <div class="linea" style="margin-top: -1px;"></div>
            `;
        } else if (tipoTransaccion1 === 'EFECTIVO') {
            reciboHTML += `
                <div class="info" style="margin-top: -1px;"><strong>Tipo de cambio:</strong> ${tipocambio}</div>
                
                <div class="linea" style="margin-top: -1px;"></div>
            `;
        }

        // Agregar productos y montos
        const checkboxes = document.querySelectorAll('.registro-checkbox:checked');
        if (checkboxes.length === 0) {
            alert('No hay registros seleccionados para imprimir.');
            return null;
        }

        let subtotal = 0;
        reciboHTML += `<table class="tabla"><thead>
                            <tr>
                                <th style="text-align: left;">Detalle</th>
                                <th style="text-align: right;">Precio</th>
                            </tr>
                        </thead>
                        <tbody>`;
        checkboxes.forEach(checkbox => {
            const fila = checkbox.closest('tr');
            const accion = fila.children[1].innerText.trim();
            const precio = parseFloat(fila.children[5].innerText.trim()).toFixed(2);

            reciboHTML += `
                <tr>
                    <td>${accion}</td>
                    <td class="precio">${precio}</td>
                </tr>
            `;
            subtotal += parseFloat(precio);
        });
        reciboHTML += `</tbody></table>`;

        const descuento = parseFloat(document.querySelector('input[placeholder="Descuento"]').value || 0);
        /* const total = subtotal - descuento; */
        const totales = parseFloat(document.querySelector('input[placeholder="Total"]').value || 0);
        const total = totales;
        const totalTextual = convertirNumeroATexto(total);
        if (tipoTransaccion1 === 'EFECTIVO') {
            reciboHTML += `
                <div class="linea"></div>
                <table class="tabla">
                    <tr><td><strong>Subtotal:</strong></td><td class="precio">${subtotal.toFixed(2)}</td></tr>
                    <tr><td><strong>Descuento:</strong></td><td class="precio">${descuento.toFixed(2)}</td></tr>
                    <tr><td><strong>Total:</strong></td><td class="precio">${total.toFixed(2)}</td></tr>
                </table>
                <div class="linea" style="margin-bottom: -1px;"></div>
                <div class="info"><strong>Son:</strong> ${totalTextual}</div>

                <div class="linea" style="margin-top: -1px;"></div>
                <table class="tabla">
                    <tr><td><strong>Monto Pago:</strong></td><td class="precio">${montoPagado}</td></tr>
                    <tr><td><strong>Monto Cambio:</strong></td><td class="precio">${(montoPagado - total).toFixed(2)}</td></tr>
                </table>
                <div class="linea" style="margin-bottom: -1px;"></div>

                <div class="firma">Firma del cliente:</div>
                <div class="firma" style="margin-top: -2px;">_______________</div>
                <div class="linea" style="margin-top: 30px;"></div>
            `;
        } else {
            reciboHTML += `
                <div class="linea"></div>
                <table class="tabla">
                    <tr><td><strong>Subtotal:</strong></td><td class="precio">${subtotal.toFixed(2)}</td></tr>
                    <tr><td><strong>Descuento:</strong></td><td class="precio">${descuento.toFixed(2)}</td></tr>
                    <tr><td><strong>Total:</strong></td><td class="precio">${total.toFixed(2)}</td></tr>
                </table>
                <div class="linea" style="margin-bottom: -1px;"></div>
                <div class="info"><strong>Son:</strong> ${totalTextual}</div>
                <div class="linea" style="margin-top: -1px; margin-bottom: -1px;"></div>
                <div class="firma">Firma del cliente:</div>
                <div class="firma" style="margin-top: -2px;">_______________</div>
                <div class="linea" style="margin-top: 30px;"></div>
            `;
        }

        reciboHTML += `
                </div>
            </body>
            </html>
        `;

        return reciboHTML;
    }

    function imprimirReciboSeleccionados() { 
        /* GENERAR EL RECIBO COMO HTML */
        const reciboHTML = generarReciboSeleccionados();
        if (reciboHTML) {
            // Crear un archivo Blob con el contenido HTML
            const blob = new Blob([reciboHTML], { type: 'text/html' });

            // Crear un enlace para descargar el archivo
            const enlace = document.createElement('a');
            enlace.href = URL.createObjectURL(blob);
            enlace.download = 'recibo.html'; // Nombre del archivo descargado
            enlace.click(); // Simular un clic en el enlace para descargar
        }

        /* GUARDAR DATOS EN LA TABLA DETALLERECIBO */
        const checkboxes = document.querySelectorAll('.registro-checkbox:checked');
        const programacionIds = [];

        checkboxes.forEach(checkbox => {
            const row = checkbox.closest('tr');
            const programacionId = row.querySelector('td:first-child').textContent;
            programacionIds.push(programacionId);
        });

        if (programacionIds.length > 0) {
            document.getElementById('programacionIds').value = programacionIds.join(',');
            document.getElementById('guardarFormulario').submit();
        } else {
            alert('Debes seleccionar al menos un registro');
        }
    }

    function dividirTextoEnLineas(texto, maxCaracteres) {
        const lineas = [];
        while (texto.length > 0) {
            lineas.push(texto.substring(0, maxCaracteres));
            texto = texto.substring(maxCaracteres);
        }
        return lineas;
    }
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
@stop