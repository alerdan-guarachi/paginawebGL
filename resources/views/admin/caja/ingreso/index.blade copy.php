@extends('adminlte::page')

<link href="assets/img/logo.png" rel="icon">


@section('content_header')
@php
    $rolUsuario = auth()->user()->getRoleNames()->first();
@endphp

@if (!$mostrarVista && $rolUsuario === 'CONTABLE')
    <div class="alert alert-danger text-center py-4" style="border-radius: 10px; background-color: #f8d7da; color: #842029; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);">
        <h4 class="font-weight-bold mb-3" style="text-transform: uppercase; letter-spacing: 1px;">Vista Bloqueada</h4>
        <p class="mb-4" style="font-size: 1.1rem;">NO HAS CERRADO TU CAJA EL DIA DE AYER. SOLICITA UN CÓDIGO DE DESBLOQUEO A ADMINISTRACIÓN.</p>
        <form action="{{ route('verificar.codigo') }}" method="POST" style="max-width: 500px; margin: 0 auto;">
            @csrf
            <div class="form-group mb-3">
                <label for="codigo" class="font-weight-bold" style="font-size: 1rem;">Ingresa el código para continuar:</label>
                <input type="text" id="codigo" name="codigo" class="form-control" placeholder="Código de autorización" required style="border-radius: 5px;">
            </div>
            <button type="submit" class="btn btn-outline-success btn-block" style="padding: 10px 20px; font-size: 1rem; border-radius: 5px;">VALIDAR CÓDIGO</button>
        </form>
    </div>
@else
    <div class="d-flex align-items-center justify-content-between mb-0">
        <div class="flex-grow-1">
            <h2 class="font-weight-bold">CAJA DE INGRESOS</h2>
        </div>
        <a class="btn btn-outline-secondary" data-toggle="modal" data-target="#consolidadosModal">
            CONSOLIDADOS
        </a>
        <a class="btn btn-outline-secondary" data-toggle="modal" data-target="#arqueoModal" style="margin-left: 10px;">
            ARQUEO
        </a>
    </div>
@endif
    <!-- Modal Consolidado-->
    <div class="modal fade" id="consolidadosModal" tabindex="-1" aria-labelledby="consolidadosModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content border-0 shadow-sm">
                <div class="modal-header text-center">
                    <h5 class="modal-title w-100 fw-bold" id="consolidadosModalLabel">CONSOLIDADOS DE CAJA</h5>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle">
                            <thead>
                                <tr class="border-bottom">
                                    <th class="text-start">Usuario</th>  <!-- Alineado a la izquierda -->
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
                                        <td class="text-center">{{ number_format($consolidado->consolidadoefectivo, 2) }}</td>
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
                                    <th colspan="6" class="text-end">Gran Total:</th>  <!-- Alineado a la derecha -->
                                    <th class="fw-bold text-center">{{ number_format($granTotal, 2) }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cerrar</button>
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
    </style>

    <!-- Modal Arqueo Total-->
    <div class="modal fade" id="arqueoModal" tabindex="-1" aria-labelledby="arqueoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content border-0 shadow-sm">
                <div class="modal-header w-100 text-center" style="display: block; text-align: center;">
                    <h6 class="modal-title w-100 fw-bold" style="font-size: 20px;" id="arqueoModalLabel">ARQUEO DE CAJA</h6>
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
                    <button type="button" class="btn btn-outline-secondary mt-2" data-dismiss="modal">Cerrar</button>
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


@if (!$mostrarVista && $rolUsuario === 'CONTABLE')

@else
<form action="{{ route('guardar.cajacentral') }}" method="POST" id="guardarFormulario">
    @csrf
    <div class="row">
        <!-- Panel Izquierdo -->
        <div class="col-md-2 panel">
            <div class="form-group" hidden>
                <label>Ciudad de Operación</label>
                <input type="text" class="form-control" name="ciudadregistro" value="{{ $sucursal }}" readonly>
            </div>
            <div class="form-group" hidden>
                <label for="siguienteId">Siguiente ID de Recibo</label>
                <input type="text" id="siguienteId" class="form-control" value="{{ $siguienteId }}" readonly>
            </div>

            <div class="form-group">
                <label>Tipo de Cliente</label>
                <select id="tipoCliente" class="form-control" name="tipocliente">
                    <option value="" selected disabled>Seleccione un tipo</option>
                    <option value="clienteitaid">Cliente ITA</option>
                    <option value="clienteauditoriaid">Cliente Auditoría</option>
                    <option value="clientecomunid">Cliente Común</option>
                    <option value="clientebancoid" hidden>Cliente Banco</option>
                </select>
                <input type="hidden" class="form-control" name="area" value="MEDICA">
            </div>

            {{-- <label for="clienteid">ID / CI del Cliente</label>
            <div class="row">
                <div class="form-group col-lg-12">
                    <input type="text" id="clienteid" name="clienteid" class="form-control" placeholder="">
                </div>
            </div>
            <div class="row">
                <div class="form-group col-lg-6 d-flex justify-content-between">
                    <a id="buscarCliente" class="btn btn-secondary" disabled>Buscar Hoy</a>
                </div>
                <div class="form-group col-lg-6 d-flex justify-content-between">
                    <a id="buscarClienteTodo" class="btn btn-secondary" disabled>Mostrar Todo</a>
                </div>
            </div>
            <div class="row">
                <div class="form-group col-lg-6">
                    <input type="date" id="fechaInicio" class="form-control" placeholder="Fecha de inicio">
                </div>
                <div class="form-group col-lg-6">
                    <input type="date" id="fechaFinal" class="form-control" placeholder="Fecha final">
                </div>
                <div class="form-group col-lg-12">
                    <a id="buscarPorFecha" class="btn btn-secondary" disabled>Buscar por Fechas</a>
                </div>
            </div> --}}

            <label for="clienteid">ID / CI del Cliente</label>
            <div class="row mb-3">
                <div class="form-group col-lg-12">
                    <input type="text" id="clienteid" name="clienteid" class="form-control" placeholder="">
                </div>
            </div>

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
                    <input type="date" id="fechaInicio" class="form-control" placeholder="Fecha de inicio">
                </div>
                <div class="form-group col-lg-6">
                    <input type="date" id="fechaFinal" class="form-control" placeholder="Fecha final">
                </div>
            </div>

            <div class="row mb-3" style="margin-top: -25px;">
                <div class="form-group col-lg-12">
                    <a id="buscarPorFecha" class="btn btn-sm btn-secondary w-100" disabled>Buscar por Fechas</a>
                </div>
            </div>
            
            <div class="form-group">
                <label>Tipo de Transacción</label>
                <div>
                    <select class="form-control" id="tipoTransaccion1" name="tipotransaccion" onchange="validarTipoTransaccion()">
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
                    <select class="form-control mt-2 d-none" id="tipoTransaccion2" name="tipotransaccion2" onchange="validarTipoTransaccion()">
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
                <input type="text" id="nrofactura" name="nrofactura" class="form-control">
            </div>

            <!-- ATC -->
            <div class="form-group atc-fields d-none">
                <label>Nro. Tarjeta</label>
                <input type="text" id="nrotarjeta" name="nrotarjeta" class="form-control">
                <label>AP.</label>
                <input type="text" id="nroap" name="nroap" class="form-control">
                <label>REF.</label>
                <input type="text" id="nroref" name="nroref" class="form-control">
            </div>
            
            <!-- CHEQUE -->
            <div class="form-group cheque-fields d-none">
                <label>Nro. Cheque</label>
                <input type="text" id="nrocheque" name="nrocheque" class="form-control">

                <div class="form-group mb-3">
                    <label for="tipobancocheque">Tipo Banco</label>
                    <select name="tipobancocheque" id="tipobancocheque" class="form-control">
                        <option value=""></option>
                        @foreach ($bancos as $banco)
                            <option value="{{ $banco->nombrebanco }}">{{ $banco->nombrebanco }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group mb-3">
                    <label for="bancoDestino">Nro. Banco Destino</label>
                    <select name="nrocuentadestinocheque" id="nrocuentadestinocheque" class="form-control">
                        <option value=""></option>
                        <option value="3000189269">3000189269</option>
                        <option value="2505314878">2505314878</option>
                        <option value="S/B">S/B</option>
                    </select>
                </div>
                
            </div>
            
            <!-- DEPOSITO BANCARIO -->
            <div class="form-group deposito-fields d-none">
                <div class="form-group mb-3">
                    <label for="bancoDestino">Nro. Banco Destino</label>
                    <select name="nrocuentadestinodeposito" id="nrocuentadestinodeposito" class="form-control">
                        <option value=""></option>
                        <option value="3000189269">3000189269</option>
                        <option value="2505314878">2505314878</option>
                        <option value="S/B">S/B</option>
                    </select>
                </div>

                <label>Bancarización</label>
                <input type="text" id="nrobancarizaciondeposito" name="nrobancarizaciondeposito" class="form-control">
            </div>
            
            <!-- TRANSFERENCIA BANCARIA -->
            <div class="form-group transferencia-fields d-none">
                <div class="form-group mb-3">
                    <label for="bancoDestino">Nro. Banco Destino</label>
                    <select name="nrocuentadestinotransferencia" id="nrocuentadestinotransferencia" class="form-control">
                        <option value=""></option>
                        <option value="3000189269">3000189269</option>
                        <option value="2505314878">2505314878</option>
                        <option value="S/B">S/B</option>
                    </select>
                </div>

                <label>Bancarización</label>
                <input type="text" id="nrobancarizaciontransferencia" name="nrobancarizaciontransferencia" class="form-control">
            </div>
            
            <!-- EFECTIVO -->
            <div class="form-group efectivo-fields d-none">
                <label>Tipo de Cambio</label>
                <select name="tipocambio" id="tipocambio" class="form-control">
                    <option value="Bs.">Bs.</option>
                    <option value="Usd.">Usd.</option>
                </select>
            </div>

        </div>

        {{-- REGISTROS --}}
        <div class="col-md-10">
            <div class="card">
                <div class="card-header bg-dark text-white text-center">
                    <h5 style="margin-bottom: 0;">PAGOS PENDIENTES</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="form-group col-lg-2">
                            <label>ID</label>
                            <input type="text" class="form-control" id="clienteid" name="clienteid" placeholder="ID del cliente" readonly>
                        </div>
                        <div class="form-group col-lg-6">
                            <label>Cliente</label>
                            <input type="text" id="clientenombre" name="clientenombre" class="form-control" placeholder="Nombre del cliente" readonly>
                        </div>
                        <div class="form-group col-lg-4">
                            <label>CI</label>
                            <input type="text" class="form-control" placeholder="CI del cliente" readonly>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered mt-3">
                            <thead>
                                <tr>
                                    <th style="width: 5%; text-align: center;">ID</th>
                                    <th style="width: 28%; text-align: center;">Est. / Esp.</th>
                                    <th style="width: 12%; text-align: center;">Prog.</th>
                                    <th hidden style="width: 0%;">Fecha de Batería</th>
                                    <th style="width: 20%; text-align: center;">Trámite</th>
                                    <th style="width: 10%; text-align: center;">Precio</th>
                                    <th style="width: 10%; text-align: center;">Descuento</th>
                                    <th style="width: 10%; text-align: center;">Pago</th>  
                                    <th style="width: 5%;">
                                        <label for="selectAll" style="display: inline-flex; align-items: center; justify-content: center; padding-left: 5px;">
                                            <input type="checkbox" id="selectAll" class="form-check-input" style="margin-right: 35px;">
                                            Selec.
                                        </label>
                                    </th>
                                </tr>
                            </thead>
                            <tbody id="tablaRegistros">
                            </tbody>
                        </table>
                    </div>

                    <div class="card-body card">
                        <h5 class="text-left" style="margin-top: 0;">RESUMEN DE PAGO</h5>
                        <div class="row">

                            <input type="hidden" class="form-control border border-dark" name="montoreal" id="montoreal" value="0" readonly>

                            <div class="form-group col-lg-3">
                                <label>Subtotal</label>
                                <input type="text" class="form-control" name="subtotal" placeholder="Subtotal" value="0" readonly>
                            </div>
                            <div class="form-group col-lg-3">
                                <label>Descuento</label>
                                <input type="text" class="form-control" name="descuento" placeholder="Descuento" value="0" readonly>
                            </div>
                            <div class="form-group col-lg-3">
                                <label>Total</label>
                                <input type="text" class="form-control border border-dark" name="montototal" placeholder="Total" value="0" readonly>
                            </div>
                            <div class="form-group col-lg-3">
                                <label>Registrar</label>
                                <div id="buttonContainer" style="display: flex; flex-direction: column; gap: 10px;">
                                    <a id="actualizarId" class="btn btn-secondary btn-block">
                                        INSERTAR DATOS
                                    </a>
                                    <button class="btn btn-success btn-block registrar-btn" id="imprimirReciboBtn" 
                                            onclick="imprimirReciboSeleccionados()" disabled style="display: none;">
                                        GUARDAR REGISTRO
                                    </button>
                                </div>
                                <script>
                                    let timer;
                                
                                    document.getElementById('actualizarId').addEventListener('click', function () {
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
                                        }, 1500);
                                    });

                                    document.getElementById('imprimirReciboBtn').addEventListener('click', function () {
                                        clearTimeout(timer);

                                        document.getElementById('imprimirReciboBtn').style.display = 'none';
                                        document.getElementById('actualizarId').style.display = 'inline-block';
                                    });
                                </script>
                                    <!-- Modal de Arqueo -->
                                <div class="modal fade" id="modalArqueo" tabindex="-1" aria-labelledby="modalArqueoLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="modalArqueoLabel">Arqueo de Caja</h5>
                                            </div>
                                            <div class="modal-body">
                                                <p>Monto Total: <span id="montototalDisplay">{{ session('montototal') }}</span></p>

                                                <form id="arqueoForm" method="POST" action="{{ route('guardar.arqueo') }}">
                                                    @csrf
                                                    <input type="hidden" name="saldoRestante" id="saldoRestanteHidden" value="{{ session('montototal') }}">
                                                    <input type="hidden" name="montototal" id="montototalHidden" value="{{ session('montototal') }}">

                                                    <div class="row">
                                                        <!-- Billetes (Izquierda) -->
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="billete200">Billetes de 200:</label>
                                                                <input type="number" name="billetecorte200" id="billetecorte200" class="form-control" value="0" data-billete="200">
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="billete100">Billetes de 100:</label>
                                                                <input type="number" name="billetecorte100" id="billetecorte100" class="form-control" value="0" data-billete="100">
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="billete50">Billetes de 50:</label>
                                                                <input type="number" name="billetecorte50" id="billetecorte50" class="form-control" value="0" data-billete="50">
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="billete20">Billetes de 20:</label>
                                                                <input type="number" name="billetecorte20" id="billetecorte20" class="form-control" value="0" data-billete="20">
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="billete10">Billetes de 10:</label>
                                                                <input type="number" name="billetecorte10" id="billetecorte10" class="form-control" value="0" data-billete="10">
                                                            </div>
                                                        </div>

                                                        <!-- Monedas (Derecha) -->
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="moneda5">Monedas de 5:</label>
                                                                <input type="number" name="monedacorte5" id="monedacorte5" class="form-control" value="0" data-moneda="5">
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="moneda2">Monedas de 2:</label>
                                                                <input type="number" name="monedacorte2" id="monedacorte2" class="form-control" value="0" data-moneda="2">
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="moneda1">Monedas de 1:</label>
                                                                <input type="number" name="monedacorte1" id="monedacorte1" class="form-control" value="0" data-moneda="1">
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="moneda050">Monedas de 0.50:</label>
                                                                <input type="number" name="monedacorte050" id="monedacorte050" class="form-control" value="0" data-moneda="0.50">
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="moneda020">Monedas de 0.20:</label>
                                                                <input type="number" name="monedacorte020" id="monedacorte020" class="form-control" value="0" data-moneda="0.20">
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="moneda010">Monedas de 0.10:</label>
                                                                <input type="number" name="monedacorte010" id="monedacorte010" class="form-control" value="0" data-moneda="0.10">
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="form-group">
                                                        <label for="saldoRestante">Saldo Restante:</label>
                                                        <span id="saldoRestanteDisplay">{{ session('montototal') }}</span>
                                                    </div>
                                                </form>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-primary" id="guardarArqueo" disabled>Guardar Arqueo</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <script>
                                    document.addEventListener('DOMContentLoaded', function () {
                                    const tipotransaccion = '{{ session('tipotransaccion') }}';
                                    const tipotransaccion2 = '{{ session('tipotransaccion2') }}';
                                    const montototal = parseFloat('{{ session('montototal') }}');
                                    
                                    if ((tipotransaccion === 'EFECTIVO' || tipotransaccion2 === 'EFECTIVO') && montototal) {
                                        $('#modalArqueo').modal('show');
                                    }

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
</form>
@endif
@stop


@section('js')
{{-- BUSCAR REGISTROS DE PROGRAMACIONES DE CLIENTES --}}
<script>
    /* document.addEventListener('DOMContentLoaded', function () {  
        const tipoClienteSelect = document.getElementById('tipoCliente');
        const clienteIdInput = document.getElementById('clienteid');
        const buscarClienteBtn = document.getElementById('buscarCliente');

        function toggleBuscarButton() {
            const tipoClienteSeleccionado = tipoClienteSelect.value.trim();
            buscarClienteBtn.disabled = !tipoClienteSeleccionado || !clienteIdInput.value.trim();
        }

        tipoClienteSelect.addEventListener('change', toggleBuscarButton);
        clienteIdInput.addEventListener('input', toggleBuscarButton);

        buscarClienteBtn.addEventListener('click', function () {
            const tipoCliente = tipoClienteSelect.value;
            const clienteId = clienteIdInput.value;

            fetch('{{ route("buscar.cliente") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
                body: JSON.stringify({ tipoCliente, clienteid: clienteId }),
            })
            .then(response => response.json())
            .then(data => {
                const nombreInput = document.querySelector('input[placeholder="Nombre del cliente"]');
                const idnombreInput = document.querySelector('input[placeholder="ID del cliente"]');
                const ciInput = document.querySelector('input[placeholder="CI del cliente"]');
                const subtotalInput = document.querySelector('input[placeholder="Subtotal"]');
                const descuentoInput = document.querySelector('input[placeholder="Descuento"]');
                const totalInput = document.querySelector('input[placeholder="Total"]');

                if (data.cliente) {
                    nombreInput.value = data.cliente.nombrecompleto;
                    idnombreInput.value = data.cliente.id;
                    ciInput.value = data.cliente.ci;
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
                                <td>${registro.accionnombre}</td>
                                <td hidden>${registro.fechabateria}</td>
                                <td>
                                    ${registro.fechaasignada ? registro.fechaasignada : ''}
                                    ${registro.fechacredito ? `(${registro.fechacredito})` : ''}
                                </td>

                                <td>${registro.tramite || 'Sin Trámite'}</td>
                                <td>${precio}</td>
                                <td>
                                    <input type="number" class="form-control registro-descuento" 
                                        placeholder="0.00" 
                                        value="0.00" 
                                        data-precio="${precio}" 
                                        data-id="${registro.id}" step="0.01" />
                                </td>
                                <td>
                                    <input type="number" class="form-control registro-pago" 
                                        placeholder="0.00" 
                                        value="${precio}" 
                                        data-precio="${precio}" 
                                        data-id="${registro.id}" step="0.01" />
                                </td>
                                <td>
                                    <input type="checkbox" class="registro-checkbox" data-precio="${precio}" />
                                </td>
                            </tr>
                        `;
                        tabla.innerHTML += fila;
                    });
                    actualizarEventosRegistro();
                } else {
                    tabla.innerHTML = '<tr><td colspan="6">NO SE ENCONTRARON REGISTROS</td></tr>';
                }
            })
            .catch(error => console.error('Error:', error));
    }); */

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
                actualizarTabla(data);
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
                actualizarTabla(data);
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
                actualizarTabla(data);
            })
            .catch(error => console.error('Error:', error));
        });

        // Función para actualizar la tabla con los resultados
        /* function actualizarTabla(data) {
            const nombreInput = document.querySelector('input[placeholder="Nombre del cliente"]');
            const idnombreInput = document.querySelector('input[placeholder="ID del cliente"]');
            const ciInput = document.querySelector('input[placeholder="CI del cliente"]');
            const subtotalInput = document.querySelector('input[placeholder="Subtotal"]');
            const descuentoInput = document.querySelector('input[placeholder="Descuento"]');
            const totalInput = document.querySelector('input[placeholder="Total"]');

            if (data.cliente) {
                nombreInput.value = data.cliente.nombrecompleto;
                idnombreInput.value = data.cliente.id;
                ciInput.value = data.cliente.ci;
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
                            <td>${registro.accionnombre}</td>
                            <td hidden>${registro.fechabateria}</td>
                            <td>
                                ${registro.fechaasignada ? registro.fechaasignada : ''}
                                ${registro.fechacredito ? `(${registro.fechacredito})` : ''}
                            </td>
                            <td>${registro.tramite || 'Sin Trámite'}</td>
                            <td>${precio}</td>
                            <td>
                                <input type="number" class="form-control registro-descuento" 
                                    placeholder="0.00" 
                                    value="0.00" 
                                    data-precio="${precio}" 
                                    data-id="${registro.id}" step="0.01" />
                            </td>
                            <td>
                                <input type="number" class="form-control registro-pago" 
                                    placeholder="0.00" 
                                    value="${precio}" 
                                    data-precio="${precio}" 
                                    data-id="${registro.id}" step="0.01" />
                            </td>
                            <td>
                                <input type="checkbox" class="registro-checkbox" data-precio="${precio}" />
                            </td>
                        </tr>
                    `;
                    tabla.innerHTML += fila;
                });
                actualizarEventosRegistro();
            } else {
                tabla.innerHTML = '<tr><td colspan="6">NO SE ENCONTRARON REGISTROS</td></tr>';
            }
        } */
        function actualizarTabla(data) {
            const nombreInput = document.querySelector('input[placeholder="Nombre del cliente"]');
            const idnombreInput = document.querySelector('input[placeholder="ID del cliente"]');
            const ciInput = document.querySelector('input[placeholder="CI del cliente"]');
            const subtotalInput = document.querySelector('input[placeholder="Subtotal"]');
            const descuentoInput = document.querySelector('input[placeholder="Descuento"]');
            const totalInput = document.querySelector('input[placeholder="Total"]');

            if (data.cliente) {
                nombreInput.value = data.cliente.nombrecompleto;
                idnombreInput.value = data.cliente.id;
                ciInput.value = data.cliente.ci;
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
                            <td>${registro.accionnombre}</td>
                            <td hidden>${registro.fechabateria}</td>
                            <td>
                                ${registro.fechaasignada ? registro.fechaasignada : ''}
                                ${registro.fechacredito ? `(${registro.fechacredito})` : ''}
                            </td>
                            <td>${registro.tramite || 'Sin Trámite'}</td>
                            <td>${precio}</td>
                            <td>
                                <input type="number" class="form-control registro-descuento" 
                                    placeholder="0.00" 
                                    value="0.00" 
                                    data-precio="${precio}" 
                                    data-id="${registro.id}" step="0.01" />
                            </td>
                            <td>
                                <input type="number" class="form-control registro-pago" 
                                    placeholder="0.00" 
                                    value="${precio}" 
                                    data-precio="${precio}" 
                                    data-id="${registro.id}" step="0.01" />
                            </td>
                            <td>
                                <input type="checkbox" class="registro-checkbox" data-precio="${precio}" />
                            </td>
                        </tr>
                    `;
                    tabla.innerHTML += fila;
                });
                actualizarEventosRegistro();
            } else {
                tabla.innerHTML = '<tr><td colspan="6">NO SE ENCONTRARON REGISTROS.</td></tr>';
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

    /* function actualizarEventosRegistro() { 
        const descuentos = document.querySelectorAll('.registro-descuento');
        const pagos = document.querySelectorAll('.registro-pago');
        const checkboxes = document.querySelectorAll('.registro-checkbox');

        // Asegúrate de que todos los eventos estén correctamente escuchados
        descuentos.forEach(input => input.addEventListener('input', calcularTotal));
        pagos.forEach(input => input.addEventListener('input', calcularTotal));
        checkboxes.forEach(checkbox => checkbox.addEventListener('change', calcularTotal));
    } */
    function actualizarEventosRegistro() {  
        const descuentos = document.querySelectorAll('.registro-descuento');
        const pagos = document.querySelectorAll('.registro-pago');
        const checkboxes = document.querySelectorAll('.registro-checkbox');

        // Asegúrate de que todos los eventos estén correctamente escuchados
        descuentos.forEach(input => input.addEventListener('input', calcularTotal));
        pagos.forEach(input => input.addEventListener('input', calcularTotal));
        checkboxes.forEach(checkbox => checkbox.addEventListener('change', calcularTotal));
    }

    /* function calcularTotal() {
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
        document.getElementById('montoreal').value = subtotal.toFixed(2);  // Total de los precios originales
        }

        // Asegúrate de llamar a actualizarEventosRegistro cuando se cargue el DOM
        document.addEventListener('DOMContentLoaded', function () {
            actualizarEventosRegistro();  // Inicializar los eventos
    }); */
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
        document.getElementById('montoreal').value = subtotal.toFixed(2);  // Total de los precios originales
    }

    document.getElementById('selectAll').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.registro-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;  // Marcar o desmarcar todos
        });
        calcularTotal();  // Recalcular el total cuando se cambian los checkboxes
    });

</script>

{{-- ACTUALIZAR CAMPOS DE TIPO DE TRANSACCION --}}
<script>
    function validarTipoTransaccion() {
        var tipoTransaccion1 = document.getElementById("tipoTransaccion1").value;
        var dobleTransaccion = document.getElementById("dobleTransaccion").checked;
        var tipoTransaccion2 = document.getElementById("tipoTransaccion2");

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
                    <div class="recibo"><strong>RECIBO N°${idrecibo}</strong></div>
                    <div class="fecha"><strong>Fecha y Hora:</strong> ${new Date().toLocaleString()}</div>
                    <div class="linea" style="margin-bottom: -1px;"></div>
                    <div class="info"><strong>Cliente:</strong> ${nombreCliente}</div>
                    <div class="info" style="margin-top: -3px;"><strong>Emitido por:</strong> ${nombreUsuario}</div>
                    <div class="linea" style="margin-top: -1px; margin-bottom: -1px;"></div>
                    <div class="info" style="margin-bottom: -1px;"><strong>Nro. Factura:</strong> ${nrofactura}</div>
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
                <div class="info" style="margin-top: -1px; margin-bottom: -1px;"><strong>Nro tarjeta:</strong> ${nrocuentadestinotransferencia}</div>
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
                                <th style="text-align: left;">Est./Esp.</th>
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
        const total = subtotal - descuento;
        const totalTextual = convertirNumeroATexto(total);
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

@stop