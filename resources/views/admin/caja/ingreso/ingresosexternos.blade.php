@extends('adminlte::page')

<link href="assets/img/logo.png" rel="icon">

@section('content_header')
@php
    $rolUsuario = auth()->user()->getRoleNames()->first();
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
        
        <a class="dropdown-item" href="{{ route('admin.caja.ingreso.index') }}">
            <i class="fas fa-hand-holding-usd mr-2 text-secondary"></i> INGRESOS INTERNOS
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
            <h3 class="font-weight-bold">CAJA DE INGRESOS EXTERNOS</h3>
        </div>
        <div class="modal fade" id="modalCodigo" tabindex="-1" role="dialog" aria-labelledby="modalCodigoLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                <form id="formCodigo">
                    <div class="modal-header">
                    <h3 class="modal-title" id="modalCodigoLabel" style="font-weight: 900;">INGRESAR CODIGO</h3>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    </div>
                    <div class="modal-body">
                    <input type="text" id="codigoInput" name="codigo" class="form-control" placeholder="Ingrese el código" required>
                    <div id="codigoMensaje" class="mt-2 text-danger" style="display: none;"></div>
                    </div>
                    <div class="modal-footer">
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
                        location.reload();
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
    </div>
@endif
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
<style>
    .table td {
        padding: 5px 10px;;
    }
</style>
@if (!$mostrarVista && $rolUsuario === 'CONTABLE')

@else
<form action="{{ route('guardar.cajacentral.ingresoexterno') }}" method="POST" id="guardarFormulario">
    @csrf
    <div class="card">
        <div class="card-body">
            <div class="row">
                <!-- Panel Izquierdo -->
                <div class="col-md-2 panel">
                    <div class="form-group" hidden>
                        <label>Ciudad de Operación</label>
                        <input type="text" class="form-control" name="ciudadregistro" value="{{ $sucursal }}" readonly>
                    </div>
                    <div class="row">
                        {{-- <div class="form-group col-lg-4">
                            <label for="siguienteId">Recibo</label>
                            <input type="text" id="siguienteId" class="form-control" value="{{ $siguienteId }}" readonly>
                        </div> --}}
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
                            <label>Tipo Proveedor</label>
                            <select id="tipocliente" class="form-control form-control-sm" name="tipocliente">
                                <option value="" selected disabled></option>
                                <option value="medico">PROVEEDOR MÉDICO</option>
                            </select>
                            <input type="hidden" class="form-control" name="area" value="MEDICA">
                        </div>
                    </div>

                    <label for="proveedorid">Nombre Proveedor</label>
                    <div class="row">
                        <div class="form-group col-lg-12"> 
                            <select id="proveedorid" name="proveedorid" class="form-control form-control-sm">
                                <option value=""  selected disabled></option>
                                @foreach($proveedores as $proveedor)
                                    <option value="{{ $proveedor->proveedor }}">{{ $proveedor->proveedor }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3" style="margin-top: -10px;">
                        <div class="form-group col-lg-4 d-flex justify-content-between">
                            <a id="buscarProveedor" class="btn-sm btn btn-secondary" disabled>Buscar</a>
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

                    {{-- FACTURA --}}
                    <div class="form-group">
                        <label>Nro. Factura</label>
                        <input type="text" id="nrofactura" name="nrofactura" class="form-control form-control-sm">
                    </div>

                    <!-- ATC -->
                    <div class="form-group atc-fields d-none">
                        <label>Nro. Tarjeta</label>
                        <input type="text" id="nrotarjeta" name="nrotarjeta" class="form-control form-control-sm">
                        <label>AP.</label>
                        <input type="text" id="nroap" name="nroap" class="form-control form-control-sm">
                        <label>REF.</label>
                        <input type="text" id="nroref" name="nroref" class="form-control form-control-sm">
                    </div>

                    <!-- CHEQUE -->
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
                            <label for="bancoDestino">Nro. Banco Origen</label>
                            <select name="nrocuentadestinocheque" id="nrocuentadestinocheque" class="form-control form-control-sm">
                                <option value=""></option>
                                @foreach ($cuentas as $cuenta)
                                    <option value="{{ $cuenta->numerocuenta }}">{{ $cuenta->numerocuenta }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                    </div>
                    
                    <!-- DEPOSITO BANCARIO -->
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
                    
                    <!-- TRANSFERENCIA BANCARIA -->
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
                    
                    <!-- EFECTIVO -->
                    <div class="form-group efectivo-fields d-none">
                        <label>Tipo de Cambio</label>
                        <select name="tipocambio" id="tipocambio" class="form-control form-control-sm">
                            <option value="Bs.">Bs.</option>
                            <option value="Usd.">Usd.</option>
                        </select>
                    </div>
                </div>

                {{-- REGISTROS --}}
                <div class="col-md-10">
                    <div class="card">
                        <div class="card-header bg-secondary text-white text-center">
                            <h5 style="margin-top: -5px; margin-bottom: -5px; font-weight: 700;">PAGOS PENDIENTES</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="form-group col-lg-4">
                                    <label>ID</label>
                                    <input type="text" class="form-control form-control-sm" id="proveedorid" name="proveedorid" placeholder="ID del proveedor" readonly>
                                </div>
                                <div class="form-group col-lg-8">
                                    <label>Proveedor</label>
                                    <input type="text" id="proveedornombre" name="proveedornombre" class="form-control form-control-sm" placeholder="Nombre del proveedor" readonly>
                                </div>
                                <div class="form-group col-lg-4" hidden>
                                    <label>NIT</label>
                                    <input type="text" class="form-control form-control-sm" placeholder="NIT del proveedor" readonly>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-bordered mt-3 table-sm table-striped">
                                    <thead>
                                        <tr class="bg-secondary text-white" style="text-align: center;">
                                            <th style="width: 5%;">ID</th>
                                            <th style="width: 30%;">Detalle</th>
                                            <th style="width: 10%;">Fecha Asig.</th>
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

                                    <input type="hidden" class="form-control form-control-sm border border-dark" name="montoreal" id="montoreal" value="0" readonly>

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
                                        {{-- <label>Registrar</label>
                                        <div id="buttonContainer" style="display: flex; flex-direction: column; gap: 10px;">
                                            <a id="actualizarId" class="btn btn-secondary btn-block">
                                                INSERTAR DATOS
                                            </a>
                                            <button class="btn btn-success btn-block registrar-btn" id="imprimirReciboBtn" 
                                                    onclick="imprimirReciboSeleccionados()" disabled style="display: none;">
                                                GUARDAR REGISTRO
                                            </button>
                                            <input type="hidden" id="html_recibo" name="html_recibo">
                                        </div> --}}
                                        <label>Registrar</label>
                                            <button class="btn-sm btn btn-secondary btn-block registrar-btn" id="imprimirReciboBtn" 
                                                    onclick="imprimirReciboSeleccionados()">
                                                GUARDAR REGISTRO
                                            </button>
                                        {{-- <div id="buttonContainer" style="display: flex; flex-direction: column; gap: 10px;">
                                            <a id="actualizarId" class="btn btn-secondary btn-block" style="display: none;">
                                                INSERTAR DATOS
                                            </a>
                                            <button class="btn btn-success btn-block registrar-btn" id="imprimirReciboBtn" 
                                                    onclick="imprimirReciboSeleccionados()" disabled style="display: none;">
                                                GUARDAR REGISTRO
                                            </button>
                                        </div> --}}
                                        <input type="hidden" id="html_recibo" name="html_recibo">
                                        <a class="btn btn-secondary" id="abrirModalBtn" data-toggle="modal" data-target="#modalArqueo" style="display: none;">
                                            ARQUEO DE CAJA
                                        </a>
                                        <script>
                                            let timer;
                                        
                                            // Delegación de eventos para el botón 'actualizarId'
                                            document.addEventListener('click', function (event) {
                                                // Si el click se realizó sobre el botón con id 'actualizarId'
                                                if (event.target && event.target.id === 'actualizarId') {
                                                    fetch('{{ route('actualizar_id_externo') }}')
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
                                        
                                                // Si el click se realizó sobre el botón 'imprimirReciboBtn'
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
                                                                        <!-- Pago del Cliente -->
                                                                        <div class="form-group">
                                                                            <label for="montoPagado" class="mb-1" style="text-align: center; width: 100%; justify-content: center; background-color: #eeeded"><strong>PAGO DEL CLIENTE</strong></label>
                                                                            <input type="varchar" name="montoPagado" id="montoPagado" class="form-control form-control-lg text-center" value="0" style="height: 30px;">
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
                                                            <button class="btn btn-success btn-flotante btn-guardar"
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


@section('js')
{{-- BUSCAR REGISTROS DE PROGRAMACIONES DE CLIENTES --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {  
        const tipoclienteSelect = document.getElementById('tipocliente');
        const proveedoridInput = document.getElementById('proveedorid');
        const buscarProveedorBtn = document.getElementById('buscarProveedor');

        function toggleBuscarButton() {
            const tipoclienteSeleccionado = tipoclienteSelect.value.trim();
            buscarProveedorBtn.disabled = !tipoclienteSeleccionado || !proveedoridInput.value.trim();
        }

        tipoclienteSelect.addEventListener('change', toggleBuscarButton);
        proveedoridInput.addEventListener('input', toggleBuscarButton);

        buscarProveedorBtn.addEventListener('click', function () {
            const tipocliente = tipoclienteSelect.value;
            const proveedorid = proveedoridInput.value;

            fetch('{{ route("buscar.proveedor.ingresoexterno") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
                body: JSON.stringify({ tipocliente, proveedorid: proveedorid }),
            })
            .then(response => response.json())
            .then(data => {
                const nombreInput = document.querySelector('input[placeholder="Nombre del proveedor"]');
                const idnombreInput = document.querySelector('input[placeholder="ID del proveedor"]');
                const nitInput = document.querySelector('input[placeholder="NIT del proveedor"]');
                const subtotalInput = document.querySelector('input[placeholder="Subtotal"]');
                const descuentoInput = document.querySelector('input[placeholder="Descuento"]');
                const totalInput = document.querySelector('input[placeholder="Total"]');

                if (data.proveedor) {
                    nombreInput.value = data.proveedor.proveedor || data.proveedor.nombrecompleto;
                    idnombreInput.value = data.proveedor.id;
                    nitInput.value = data.proveedor.nit || data.proveedor.ci;
                } else {
                    alert('Proveedor no encontrado');
                    nombreInput.value = '';
                    idnombreInput.value = '';
                    nitInput.value = '';
                    subtotalInput.value = '0.00';
                    descuentoInput.value = '0.00';
                    totalInput.value = '0.00';
                }
                // 🔁 Permiso para mostrar fechas
                const campoFechas = document.getElementById('campoFechas');
                if (campoFechas) {
                    campoFechas.style.display = 'none'; // ocultar siempre primero
                }

                if (data.permisoExistefecha) {
                    if (campoFechas) {
                        campoFechas.style.display = 'block'; // mostrar si tiene permiso
                    }

                    // 🔄 Sincronizar created_at y updated_at
                    const createdAtInput = document.getElementById('created_at');
                    const updatedAtInput = document.getElementById('updated_at');
                    if (createdAtInput && updatedAtInput) {
                        createdAtInput.addEventListener('change', function () {
                            updatedAtInput.value = this.value;
                        });
                    }
                }

                const tabla = document.getElementById('tablaRegistros');
                tabla.innerHTML = '';
                if (data.registros.length > 0) {
                    data.registros.forEach((registro) => {
                        /* let precio = parseFloat(registro.precio.replace(',', '.'));
                        let precioCompraOriginal = parseFloat(registro.preciocompra.replace(',', '.'));

                        let preciocompra = (precio - precioCompraOriginal).toFixed(2); */

                        let precio = parseFloat(registro.precio.replace(',', '.')); // esto ya trae el saldo si existe
                        let precioCompraOriginal = parseFloat(registro.preciocompra.replace(',', '.'));

                        let preciocompra;

                        // Solo restamos si precio > preciocompra (o según tu regla)
                        if (precio < precioCompraOriginal) {
                            preciocompra = precio.toFixed(2); // saldo pendiente
                        } else {
                            preciocompra = (precio - precioCompraOriginal).toFixed(2);
                        }


                        const fila = `
                            <tr>
                                <td>${registro.id}</td>
                                <td>${registro.accionnombre || ''} ${registro.tipoproveedor || ''}
                                    ${registro.clienteitanombre ? ' - '  + registro.clienteitanombre : ''}
                                    ${registro.clientenombre ? ' - ' + registro.clientenombre : ''}
                                    ${registro.clienteauditorianombre ? ' - ' + registro.clienteauditorianombre : ''}
                                    ${registro.clientecomunnombre ? ' - ' + registro.clientecomunnombre : ''}</td>
                                <td hidden>${registro.fechabateria}</td>
                                <td>${registro.fechaasignada ? registro.fechaasignada : ''}</td>
                                <td>${registro.tramite || ''} ${registro.detalle || ''}</td>
                                <td>${preciocompra}</td>
                                <td>
                                    <input type="number" style="height: 25px;" class="form-control registro-descuento" 
                                        placeholder="0.00" 
                                        value="0.00" 
                                        data-preciocompra="${preciocompra}" 
                                        data-id="${registro.id}" step="0.01" />
                                </td>
                                <td>
                                    <input type="number" style="height: 25px;" class="form-control registro-pago" 
                                        placeholder="0.00" 
                                        value="${preciocompra}" 
                                        data-preciocompra="${preciocompra}" 
                                        data-id="${registro.id}" step="0.01" />
                                </td>
                                <td>
                                    <input type="checkbox" style="height: 25px;" class="registro-checkbox" data-preciocompra="${preciocompra}" />
                                </td>
                            </tr>
                        `;
                        tabla.innerHTML += fila;
                    });
                    actualizarEventosRegistro();
                /* } else {
                    tabla.innerHTML = '<tr><td colspan="6">NO SE ENCONTRARON REGISTROS</td></tr>';
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

            })
            .catch(error => console.error('Error:', error));
        }); */
                 } else {
                    tabla.innerHTML = '<tr><td colspan="8">NO SE ENCONTRARON REGISTROS</td></tr>';
                }
            })
            .catch(error => console.error('Error:', error));
        });

        // Deshabilitar el campo de pago si el descuento es diferente de 0
        function togglePagoField(descuento, pago) {
            const preciocompra = parseFloat(descuento.dataset.preciocompra);

            if (parseFloat(descuento.value) !== 0) {
                pago.disabled = true;  // Deshabilitar el campo pago si el descuento no es 0
            } else {
                pago.disabled = false;  // Habilitar el campo pago si el descuento es 0
                // Si el pago no está deshabilitado, actualizamos el valor del pago
                pago.value = (preciocompra - parseFloat(descuento.value)).toFixed(2);  // Restar el descuento al precio
            }
        }

        // Actualizar el valor del campo pago cuando el descuento cambia
        document.addEventListener('input', function(event) {
            if (event.target.classList.contains('registro-descuento')) {
                const descuento = event.target;
                const row = descuento.closest('tr');
                const pago = row.querySelector('.registro-pago');
                const preciocompra = parseFloat(descuento.dataset.preciocompra);

                // Verificar si el descuento es mayor que el precio y no permitir que el pago sea negativo
                const descuentoValor = parseFloat(descuento.value).toFixed(2);
                let nuevoPago = preciocompra - descuentoValor;

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
            const preciocompra = parseFloat(checkbox.dataset.preciocompra);  // Precio original del registro
            const descuento = parseFloat(row.querySelector('.registro-descuento').value || 0);  // Descuento
            const pago = parseFloat(row.querySelector('.registro-pago').value || 0);  // Pago

            // Solo sumar los precios, descuentos y pagos si el checkbox está marcado
            subtotal += preciocompra;  // Sumar solo el precio
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

        /* document.addEventListener('DOMContentLoaded', function () {
            actualizarEventosRegistro();
        });
        document.getElementById('selectAll').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.registro-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            calcularTotal();
        }); */
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
        const nombreCliente = document.getElementById('proveedornombre').value;
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
                    <div class="info"><strong>Proveedor:</strong> ${nombreCliente}</div>
                    <div class="info" style="margin-top: -3px;"><strong>Emitido por:</strong> ${nombreUsuario}</div>
                    <div class="linea" style="margin-top: -1px; margin-bottom: -1px;"></div>
                    <div class="info" style="margin-bottom: -1px;"><strong>Nro. Factura:</strong> ${nrofactura ?? 0}</div>
                    <div class="info"><strong>Tipo de Transaccion:</strong> ${tipoTransaccion1}</div>
        `;

        // Mostrar solo secciones relevantes
        if (tipoTransaccion1 === 'ATC') {
            reciboHTML += `
                <div class="info" style="margin-top: -1px; margin-bottom: -1px;"><strong>Nro tarjeta:</strong> ${nrotarjeta}</div>
                <div class="info" style="margin-top: -1px; margin-bottom: -1px;"><strong>AP:</strong> ${nroap}</div>
                <div class="info" style="margin-bottom: -1px;"><strong>REF:</strong> ${nroref}</div>
                <div class="linea" style="margin-top: -1px; margin-bottom: -1px;"></div>
            `;
        } else if (tipoTransaccion1 === 'CHEQUE') {
            reciboHTML += `
                <div class="info" style="margin-top: -1px; margin-bottom: -1px;"><strong>Nro. Cheque:</strong> ${nrocheque}</div>
                <div class="info" style="margin-top: -1px; margin-bottom: -1px;"><strong>Tipo Banco:</strong> ${tipobancocheque}</div>
                <div class="info" style="margin-bottom: -1px;"><strong>Nro. Cuenta Origen:</strong> ${nrocuentadestinocheque}</div>
                <div class="linea" style="margin-top: -1px; margin-bottom: -1px;"></div>
            `;
        } else if (tipoTransaccion1 === 'DEPOSITO_BANCARIO') {
            reciboHTML += `
                <div class="info" style="margin-top: -1px; margin-bottom: -1px;"><strong>Nro Cuenta Origen:</strong> ${nrocuentadestinodeposito}</div>
                <div class="info" style="margin-top: -1px;"><strong>Bancarización:</strong> ${nrobancarizaciondeposito}</div>
                <div class="linea" style="margin-top: -1px; margin-bottom: -1px;"></div>
            `;
        } else if (tipoTransaccion1 === 'TRANSFERENCIA_BANCARIA') {
            reciboHTML += `
                <div class="info" style="margin-top: -1px; margin-bottom: -1px;"><strong>Nro. Cuenta Origen:</strong> ${nrocuentadestinotransferencia}</div>
                <div class="info" style="margin-top: -1px;"><strong>Bancarizacion:</strong> ${nrobancarizaciontransferencia}</div>
                <div class="linea" style="margin-top: -1px; margin-bottom: -1px;"></div>
            `;
        } else if (tipoTransaccion1 === 'EFECTIVO') {
            reciboHTML += `
                <div class="info" style="margin-top: -1px;"><strong>Tipo de cambio:</strong> ${tipocambio}</div>
                <div class="linea" style="margin-top: -1px; margin-bottom: -1px;"></div>
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
            const preciocompra = parseFloat(fila.children[5].innerText.trim()).toFixed(2);

            reciboHTML += `
                <tr>
                    <td>${accion}</td>
                    <td class="precio">${preciocompra}</td>
                </tr>
            `;
            subtotal += parseFloat(preciocompra);
        });
        reciboHTML += `</tbody></table>`;

        const descuento = parseFloat(document.querySelector('input[placeholder="Descuento"]').value || 0);
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
            <div class="linea" style="margin-top: 50px;"></div>
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

        const reciboHTML = generarReciboSeleccionados();
        if (reciboHTML) {
            // Asignar el HTML generado al campo oculto del formulario
            document.getElementById('html_recibo').value = reciboHTML;

            // Habilitar el botón de envío
            document.getElementById('imprimirReciboBtn').disabled = false;

            // Enviar el formulario
            document.getElementById('guardarFormulario').submit();
        } else {
            alert('No se generó el recibo correctamente');
        }


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