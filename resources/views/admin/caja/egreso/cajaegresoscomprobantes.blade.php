@extends('adminlte::page')

<link href="assets/img/logo.png" rel="icon">

@section('content_header')
@php
    $rolUsuario = auth()->user()->getRoleNames()->first();
    $tieneRolContable = auth()->user()->getRoleNames()->contains('CONTABLE');
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
        
        <a class="dropdown-item" href="{{ route('admin.caja.egreso.cajaegresos') }}">
            <i class="fas fa-hand-holding-usd mr-2 text-secondary"></i> CAJA DE EGRESOS
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
        <h4 class="font-weight-bold mb-3" style="text-transform: uppercase; letter-spacing: 1px;">Vista Bloqueada</h4>
        <p class="mb-4" style="font-size: 1.1rem;">NO HAS CERRADO TU CAJA EL DIA CORRESPONDIENTE, SOLICITA UN CÓDIGO DE DESBLOQUEO A ADMINISTRACIÓN.</p>
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
            <h3 class="font-weight-bold">CAJA DE EGRESOS CON COMPROBANTES</h3>
        </div>
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

                fetch('{{ route("permisoscodigo.cajaegresos") }}', {
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

<!-- Modal de Arqueo -->
<div class="modal fade" id="modalArqueo" tabindex="-1" aria-labelledby="modalArqueoLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalArqueoLabel">Arqueo de Caja</h5>
            </div>
            <div class="modal-body">
                <p>Monto Total: <span id="montototalDisplay">{{ session('montototal') }}</span></p>

                <form id="arqueoForm" method="POST" action="{{ route('guardar.arqueo.egreso') }}">
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

@if (!$mostrarVista && $tieneRolContable)
@else
<form action="{{ route('guardar.cajacentral.egresocomprobantes') }}" method="POST" id="guardarFormulario">
    @csrf
    <div class="card">
        <div class="card-body">
            <div class="row">
                <!-- Panel Izquierdo -->
                <div class="col-md-2 panel">
                    <div class="form-group" hidden>
                        <label>Ciudad de Operación</label>
                        <input type="text" class="form-control form-control-sm" name="ciudadregistro" value="{{ $sucursal }}" readonly>
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
                            <label>Tipo de Proveedor</label>
                            <select id="tipocliente" class="form-control form-control-sm" name="tipocliente" onchange="cambiarArea()">
                                <option value="" selected disabled></option>
                                <option value="medico">MEDICO</option>
                                <option value="proveedor">PROVEEDOR</option>
                            </select>
                            <input type="hidden" id="area" class="form-control form-control-sm" name="area" value="MEDICA">
                        </div>
                        
                        <script>
                        function cambiarArea() {
                            var tipoCliente = document.getElementById('tipocliente').value;
                            var areaInput = document.getElementById('area');
                            
                            if (tipoCliente === 'medico') {
                                areaInput.value = 'MEDICA';
                            } else if (tipoCliente === 'proveedor') {
                                areaInput.value = 'CUENTA POR PAGAR';
                            }
                        }
                        </script>
                    </div>

                    <label for="proveedorid">Nombre de Proveedor</label>
                    <div class="row">
                        <div class="form-group col-lg-12"> 
                            <select id="proveedorid" name="proveedorid" class="form-control form-control-sm">
                                <option value="" selected disabled></option>
                            </select>
                        </div>

                        <script>
                            // Asume que los datos de proveedores están disponibles como variables JS
                            var proveedores = @json($proveedores);
                            var proveedoresServicios = @json($proveedoresservicios);

                            // Función para cargar los proveedores según el tipo
                            function cargarProveedores(tipo) {
                                var selectProveedor = document.getElementById('proveedorid');
                                selectProveedor.innerHTML = '<option value="" selected disabled></option>'; // Limpiar opciones existentes

                                var opciones = [];
                                if (tipo === 'medico') {
                                    opciones = proveedores.map(function(proveedor) {
                                        return `<option value="${proveedor.proveedor}">${proveedor.proveedor}</option>`;
                                    });
                                } else if (tipo === 'proveedor') {
                                    opciones = proveedoresServicios.map(function(proveedor) {
                                        return `<option value="${proveedor.razonsocial}">${proveedor.razonsocial}</option>`;
                                    });
                                }

                                selectProveedor.innerHTML += opciones.join('');
                            }

                            // Agregar un evento para cuando cambie el tipo de proveedor
                            document.getElementById('tipocliente').addEventListener('change', function() {
                                cargarProveedores(this.value);
                            });
                        </script>
                        
                        
                        <div class="form-group col-lg-12">
                            <label>N. Factura 1</label>
                            <input type="text" id="nrofactura" name="nrofactura" class="form-control form-control-sm">
                        </div>
                        <div class="form-group col-lg-6">
                            <label>N. Factura 2</label>
                            <input type="text" id="nrofactura2" name="nrofactura2" class="form-control form-control-sm">
                        </div>
                        <div class="form-group col-lg-6">
                            <label>N. Factura 3</label>
                            <input type="text" id="nrofactura3" name="nrofactura3" class="form-control form-control-sm">
                        </div>
                        
                        <div class="form-group col-lg-6 d-flex justify-content-between" style="margin-top: -10px;">
                            <a id="buscarProveedor" class="btn-sm btn btn-secondary" disabled>Buscar</a>
                        </div>
                    </div>
                    
                    {{-- CAMBIO FECHA --}}
                        <div id="campoFechas" style="display: none;">
                            <div class="form-group">
                                <label>Registro</label>
                                <input type="datetime-local" name="created_at" id="created_at" class="form-control form-control-sm">
                                <input type="datetime-local" name="updated_at" id="updated_at" class="form-control" hidden>
                            </div>
                        </div>
                    {{-- FIN --}}

                    <div class="form-group">
                        <label>Tipo de Transacción</label>
                        <div>
                            <select class="form-control form-control-sm" id="tipoTransaccion1" name="tipotransaccion" required onchange="validarTipoTransaccion()">
                                <option disabled selected></option>
                                <option value="CHEQUE">CHEQUE</option>
                                <option value="DEPOSITO_BANCARIO" hidden>DEPÓSITO BANCARIO</option>
                                <option value="EFECTIVO" hidden>EFECTIVO</option>
                                <option value="TRANSFERENCIA_BANCARIA">TRANSFERENCIA BANCARIA</option>
                                {{-- <option value="RETIRO_BANCARIO">RETIRO BANCARIO</option> --}}
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
                    {{-- <div class="form-group">
                        <label>Nro. Factura</label>
                        <input type="text" id="nrofactura" name="nrofactura" class="form-control">
                    </div> --}}

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

                        <label>Bancarización</label>
                        <input type="text" id="nrobancarizacioncheque" name="nrobancarizacioncheque" class="form-control form-control-sm">

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
                                            <th style="width: 25%;">Detalle</th>
                                            <th style="width: 10%;">Fecha Asig.</th>
                                            <th hidden style="width: 0%;">Fecha de Batería</th>
                                            <th style="width: 20%;">Servicio</th>
                                            <th style="width: 9%;">Subtotal</th>
                                            <th style="width: 9%;">Descuento</th>
                                            <th style="width: 12%;">Total</th>  
                                            {{-- <th style="width: 5%;">Selec.</th> --}}
                                            <th style="width: 5%;">
                                                <label for="selectAll" style="display: inline-flex; align-items: center; justify-content: center; padding-left: 10px; margin-bottom: 0px;">
                                                    <input type="checkbox" id="selectAll" class="form-check-input" style="margin-right: 20px;">
                                                    Sel.
                                                </label>
                                            </th>
                                            <th style="width: 5%;">Comp.</th>
                                            <th style="width: 5%;">Fact.</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tablaRegistros">
                                    </tbody>
                                </table>
                            </div>

                            <div class="card-body card" style="background-color: #90909011">
                                <h5 class="text-center" style="margin-top: 0; font-weight: 700;">RESUMEN DE EGRESO</h5>
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
                                        <input type="text" class="form-control form-control-sm border border-dark" name="montototal" placeholder="Total" value="0" readonly>
                                    </div>
                                    <div class="form-group col-lg-3">
                                        <label>Registrar</label>
                                            <button class="btn-sm btn btn-secondary btn-block registrar-btn" id="imprimirReciboBtn" 
                                                    onclick="imprimirReciboSeleccionados()">
                                                GUARDAR REGISTRO
                                            </button>
                                            <input type="hidden" id="html_recibo" name="html_recibo">
                                        {{-- <div id="buttonContainer" style="display: flex; flex-direction: column; gap: 10px;">
                                            <a id="actualizarId" class="btn btn-secondary btn-block">
                                                INSERTAR DATOS
                                            </a>
                                            <button class="btn btn-success btn-block registrar-btn" id="imprimirReciboBtn" 
                                                    onclick="imprimirReciboSeleccionados()" disabled style="display: none;">
                                                GUARDAR REGISTRO
                                            </button>
                                            <input type="hidden" id="html_recibo" name="html_recibo">
                                        </div> --}}
                                        
                                        <script>
                                            // Agregar un temporizador para ocultar el botón "Guardar" después de 1 segundo si no se presiona
                                            let timer;
                                        
                                            document.getElementById('actualizarId').addEventListener('click', function () {
                                                fetch('{{ route('actualizar_id_egreso') }}') // Cambia esta ruta según tu controlador
                                                    .then(response => response.json())
                                                    .then(data => {
                                                        if (data.siguienteId) {
                                                            document.getElementById('siguienteId').value = data.siguienteId;
                                                        } else {
                                                            alert('No se pudo obtener el siguiente ID.');
                                                        }
                                                    })
                                                    .catch(error => console.error('Error:', error));
                                        
                                                // Ocultar "Generar Recibo" y mostrar "Guardar Registro"
                                                document.getElementById('actualizarId').style.display = 'none';
                                                document.getElementById('imprimirReciboBtn').style.display = 'inline-block';
                                        
                                                // Iniciar un temporizador para ocultar el botón "Guardar Registro" después de 1 segundo
                                                timer = setTimeout(function() {
                                                    console.log("No se presionó Guardar, ocultando el botón Guardar y mostrando Generar Recibo.");
                                                    document.getElementById('imprimirReciboBtn').style.display = 'none';
                                                    document.getElementById('actualizarId').style.display = 'inline-block';
                                                }, 1500);  // 1000 ms = 1 segundo
                                            });
                                        
                                            // Manejar el clic del botón "Guardar"
                                            document.getElementById('imprimirReciboBtn').addEventListener('click', function () {
                                                // Cancelar el temporizador si se presiona el botón "Guardar"
                                                clearTimeout(timer);
                                                
                                                // Después de presionar "Guardar", ocultar "Guardar" y mostrar "Generar Recibo"
                                                document.getElementById('imprimirReciboBtn').style.display = 'none';
                                                document.getElementById('actualizarId').style.display = 'inline-block';
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
<!-- Modal para ver comprobante -->
<div class="modal fade" id="modalComprobante" tabindex="-1" aria-labelledby="modalComprobanteLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg"> <!-- usa modal-lg o modal-xl para PDFs grandes -->
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalComprobanteLabel" style="font-weight: 900;">COMPROBANTE</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body text-center">
        <div id="comprobanteContainer" style="height: 80vh;">
          <!-- Aquí se insertará el iframe -->
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal para ver Factura -->
<div class="modal fade" id="modalFactura" tabindex="-1" aria-labelledby="modalFacturaLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg"> <!-- usa modal-lg o modal-xl para PDFs grandes -->
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalFacturaLabel" style="font-weight: 900;">FACTURA</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body text-center">
        <div id="FacturaContainer" style="height: 80vh;">
          <!-- Aquí se insertará el iframe -->
        </div>
      </div>
    </div>
  </div>
</div>

@stop

{{-- <td>${preciocompra}</td> --}}
{{-- <td>
    <input type="number" style="height: 25px;" class="form-control registro-descuento" 
        placeholder="0.00" 
        value="0.00" 
        data-preciocompra="${preciocompra}" 
        data-id="${registro.id}" step="0.01" />
</td> --}}
{{-- ${registro.id && registro.id.toString().endsWith('CP') ? 'disabled' : ''} --}}
@section('js')
{{-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script> --}}

{{-- BUSCAR REGISTROS DE PROGRAMACIONES DE CLIENTES --}}
<script>
    const baseURL = "{{ url('/') }}";
    document.addEventListener('DOMContentLoaded', function () {  
        const tipoclienteSelect = document.getElementById('tipocliente');
        const proveedoridInput = document.getElementById('proveedorid');
        const nrofacturaInput = document.getElementById('nrofactura');
        const nrofactura2Input = document.getElementById('nrofactura2');
        const nrofactura3Input = document.getElementById('nrofactura3');
        const buscarProveedorBtn = document.getElementById('buscarProveedor');

        function toggleBuscarButton() {
            const tipoclienteSeleccionado = tipoclienteSelect.value.trim();
            buscarProveedorBtn.disabled = !tipoclienteSeleccionado || !proveedoridInput.value.trim();
        }

        tipoclienteSelect.addEventListener('change', toggleBuscarButton);
        nrofacturaInput.addEventListener('change', toggleBuscarButton);
        nrofactura2Input.addEventListener('change', toggleBuscarButton);
        nrofactura3Input.addEventListener('change', toggleBuscarButton);
        proveedoridInput.addEventListener('input', toggleBuscarButton);

        buscarProveedorBtn.addEventListener('click', function () {
            const tipocliente = tipoclienteSelect.value;
            const nrofactura = nrofacturaInput.value;
            const nrofactura2 = nrofactura2Input.value;
            const nrofactura3 = nrofactura3Input.value;
            const proveedorid = proveedoridInput.value;

            fetch('{{ route("buscar.proveedor.egresocomprobantes") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
                body: JSON.stringify({ tipocliente, proveedorid: proveedorid, nrofactura: nrofactura , nrofactura2: nrofactura2, nrofactura3: nrofactura3}),
            })
            .then(response => response.json())
            .then(data => {
                const nombreInput = document.querySelector('input[placeholder="Nombre del proveedor"]');
                const idnombreInput = document.querySelector('input[placeholder="ID del proveedor"]');
                const nitInput = document.querySelector('input[placeholder="NIT del proveedor"]');
                const subtotalInput = document.querySelector('input[placeholder="Subtotal"]');
                const descuentoInput = document.querySelector('input[placeholder="Descuento"]');
                const totalInput = document.querySelector('input[placeholder="Total"]');
                const campoFechas = document.getElementById('campoFechas');
                // CAMBIO FECHA
                    if (data.permisoExistefecha) {
                        document.getElementById('campoFechas').style.display = 'block';
                    } else {
                        document.getElementById('campoFechas').style.display = 'none';
                    }
                //

                if (data.proveedor) {
                    nombreInput.value = data.proveedor.proveedor || data.proveedor.nombrecompleto || data.proveedor.razonsocial;
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
                const tabla = document.getElementById('tablaRegistros');
                tabla.innerHTML = '';
                if (data.registros.length > 0) {
                    data.registros.forEach((registro) => {
                        let preciocompra = parseFloat(registro.preciocompra.replace(',', '.')).toFixed(2);
                        const fila = `
                            <tr>
                                

                                <td>${registro.id}</td>
                                <td>${registro.accionnombre || ''} ${registro.tipoproveedor || ''} ${registro.cantidad > 0 ? registro.cantidad : ''} ${registro.detalleproducto || ''}
                                    ${registro.clienteitanombre ? ' - '  + registro.clienteitanombre : ''}
                                    ${registro.clienteauditorianombre ? ' - ' + registro.clienteauditorianombre : ''}
                                    ${registro.clientecomunnombre ? ' - ' + registro.clientecomunnombre : ''}</td>
                                <td hidden>${registro.fechabateria}</td>
                                <td>${registro.fechaasignada ? registro.fechaasignada : ''}</td>
                                <td>${registro.tramite || ''} ${registro.detalle || ''}  ${registro.tipoorden || ''} ${registro.ordenid || ''}</td>
                                <td>${(!preciocompra || parseFloat(preciocompra) === 0.00) ? registro.subtotal : preciocompra}</td>

                                <td>
                                    <input type="number" style="height: 25px;" class="form-control registro-descuento" 
                                        placeholder="0.00" 
                                        value="${(registro.descuento !== null && registro.descuento !== undefined && registro.descuento !== '') ? registro.descuento : '0.00'}" 
                                        data-preciocompra="${preciocompra}" 
                                        data-id="${registro.id}" step="0.01" />
                                </td>

                                <td>
                                    <input type="number" style="height: 25px;" class="form-control registro-pago" 
                                        placeholder="0.00" 
                                        value="${(!preciocompra || parseFloat(preciocompra) === 0.00) ? registro.montototal : preciocompra}" 
                                        data-preciocompra="${preciocompra}" 
                                        data-id="${registro.id}" step="0.01" />
                                </td>

                                <td>
                                    <input type="checkbox" style="height: 25px;" class="registro-checkbox" data-preciocompra="${(!preciocompra || parseFloat(preciocompra) === 0.00) ? registro.subtotal : preciocompra}" />
                                </td>
                                <td>
                                    ${registro.comprobante 
                                        ? `<a class="btn btn-sm btn-secondary ver-comprobante-btn" data-archivo="${registro.comprobante}">VER</a>` 
                                        : 'VACIO'}
                                </td>
                                <td>
                                    ${registro.factura 
                                        ? `<a class="btn btn-sm btn-secondary ver-factura-btn" data-archivo="${registro.factura}">VER</a>` 
                                        : 'VACIO'}
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

    document.addEventListener('click', function (e) {
        if (e.target && e.target.classList.contains('ver-comprobante-btn')) {
            const archivo = e.target.getAttribute('data-archivo');
            const ruta = `${baseURL}/comprobantescuentaspagar/${archivo}`;
            const extension = archivo.split('.').pop().toLowerCase();

            let html = '';
            if (['pdf'].includes(extension)) {
                html = `<iframe src="${ruta}" width="100%" height="100%" style="border:none;"></iframe>`;
            } else if (['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(extension)) {
                html = `<img src="${ruta}" alt="Comprobante" style="max-width:100%; max-height:100%;">`;
            } else {
                html = `<p>No se puede visualizar este tipo de archivo: ${archivo}</p>`;
            }

            document.getElementById('comprobanteContainer').innerHTML = html;
            const modal = new bootstrap.Modal(document.getElementById('modalComprobante'));
            modal.show();
        }
        if (e.target && e.target.classList.contains('ver-factura-btn')) {
        const archivo = e.target.getAttribute('data-archivo');
        const ruta = `${baseURL}/comprobantescuentaspagar/${archivo}`;
        const extension = archivo.split('.').pop().toLowerCase();

        let html = '';
        if (['pdf'].includes(extension)) {
            html = `<iframe src="${ruta}" width="100%" height="100%" style="border:none;"></iframe>`;
        } else if (['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(extension)) {
            html = `<img src="${ruta}" alt="Factura" style="max-width:100%; max-height:100%;">`;
        } else {
            html = `<p>No se puede visualizar este tipo de archivo: ${archivo}</p>`;
        }

        document.getElementById('FacturaContainer').innerHTML = html;
        const modal = new bootstrap.Modal(document.getElementById('modalFactura'));
        modal.show();
    }
    });

   

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
        const nombreCliente = document.getElementById('proveedornombre').value;
        const tipoTransaccion1 = document.getElementById('tipoTransaccion1').value;
        const idrecibo = document.getElementById('siguienteId').value;
        const nrofactura = document.getElementById('nrofactura').value;
        const nrofactura2 = document.getElementById('nrofactura2').value;
        const nrofactura3 = document.getElementById('nrofactura3').value;

        /* ATC */
        const nrotarjeta = document.getElementById('nrotarjeta').value;
        const nroap = document.getElementById('nroap').value;
        const nroref = document.getElementById('nroref').value;
        
        /* CHEQUE */
        const nrocheque = document.getElementById('nrocheque').value;
        const tipobancocheque = document.getElementById('tipobancocheque').value;
        const nrocuentadestinocheque = document.getElementById('nrocuentadestinocheque').value;
        const nrobancarizacioncheque = document.getElementById('nrobancarizacioncheque').value;

        /* DEPOSITO BANCARIO */
        const nrocuentadestinodeposito = document.getElementById('nrocuentadestinodeposito').value;
        const nrobancarizaciondeposito = document.getElementById('nrobancarizaciondeposito').value;

        /* TRANSFERENCIA BANCARIA */
        const nrocuentadestinotransferencia = document.getElementById('nrocuentadestinotransferencia').value;
        const nrobancarizaciontransferencia = document.getElementById('nrobancarizaciontransferencia').value;

        /* EFECTIVO */
        const tipocambio = document.getElementById('tipocambio').value;

        //CAMBIO FECHA
            const createdAt = document.getElementById('created_at').value;
            const fechaHora = createdAt
                ? new Date(createdAt).toLocaleString()
                : new Date().toLocaleString();
        //

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
                    <div class="info" style="margin-bottom: -1px;">
                        <strong>Nro. Factura:</strong> 
                        ${nrofactura ? nrofactura : ''}${nrofactura2 ? ' - ' + nrofactura2 : ''}${nrofactura3 ? ' - ' + nrofactura3 : ''}
                    </div>

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
                <div class="info" style="margin-top: -1px;"><strong>Bancarizacion:</strong> ${nrobancarizacioncheque}</div>
                <div class="linea" style="margin-top: -1px; margin-bottom: -1px;"></div>
            `;
        } else if (tipoTransaccion1 === 'DEPOSITO_BANCARIO') {
            reciboHTML += `
                <div class="info" style="margin-top: -1px; margin-bottom: -1px;"><strong>Nro Cuenta Origen:</strong> ${nrocuentadestinodeposito}</div>
                <div class="info" style="margin-top: -1px;"><strong>Bancarizacion:</strong> ${nrobancarizaciondeposito}</div>
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
                                <th style="text-align: left;">Detalle</th>
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
        /* const total = subtotal - descuento; */
        const totales = parseFloat(document.querySelector('input[placeholder="Total"]').value || 0);
        const total = totales;
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
            <div class="linea" style="margin-top: 50px;"></div>
        `;

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