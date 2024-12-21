@extends('adminlte::page')

<link href="assets/img/logo.png" rel="icon">

@section('content_header')
    <div class="text-center mb-0">
        <h1 class="font-weight-bold"
            style="font-size: 1.8rem; color: #343a40; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 0;">
            CAJA DE INGRESOS
        </h1>
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
        }, 5000);
    </script>
@endif

@section('content')
<form action="{{ route('guardar.cajacentral') }}" method="POST">
    @csrf
    <div class="row">
        <!-- Panel Izquierdo -->
        <div class="col-md-2 panel" style="background-color: #efefef">
            <div class="form-group">
                <label>Ciudad de Operación</label>
                <input type="text" class="form-control" name="ciudadregistro" value="{{ $sucursal }}" readonly>
            </div>
            <div class="form-group">
                <label>Selecciona Tipo de Cliente</label>
                <select id="tipoCliente" class="form-control" name="tipocliente">
                    <option value="" selected disabled>Seleccione un tipo</option>
                    <option value="clienteitaid">Cliente ITA</option>
                    <option value="clienteauditoriaid">Cliente Auditoría</option>
                    <option value="clientecomunid">Cliente Común</option>
                    <option value="clientebancoid">Cliente Banco</option>
                </select>
            </div>

            <label for="clienteid">ID del Cliente</label>
            <div class="row">
                <div class="form-group col-lg-6">
                    <input type="text" id="clienteid" name="clienteid" class="form-control" placeholder="">
                </div>
                <div class="form-group col-lg-6">
                    <a id="buscarCliente" class="btn btn-primary" disabled>Buscar</a>
                </div>
            </div>
            <div class="form-group">
                <label>Tipo de Transacción</label>
                <div>
                    <select class="form-control" id="tipoTransaccion1" onchange="validarTipoTransaccion()">
                        <option disabled selected></option>
                        <option value="ATC">ATC</option>
                        <option value="CHEQUE">CHEQUE</option>
                        <option value="DEPOSITO_BANCARIO">DEPÓSITO BANCARIO</option>
                        <option value="EFECTIVO">EFECTIVO</option>
                        <option value="TRANSFERENCIA_BANCARIA">TRANSFERENCIAS BANCARIAS</option>
                    </select>
                    <div class="form-check mt-2">
                        <input type="checkbox" class="form-check-input" id="dobleTransaccion" onchange="validarTipoTransaccion()">
                        <label class="form-check-label" for="dobleTransaccion">Doble Tipo de Transac.</label>
                    </div>
                    <select class="form-control mt-2 d-none" id="tipoTransaccion2" onchange="validarTipoTransaccion()">
                        <option disabled selected></option>
                        <option value="ATC">ATC</option>
                        <option value="CHEQUE">CHEQUE</option>
                        <option value="DEPOSITO_BANCARIO">DEPÓSITO BANCARIO</option>
                        <option value="EFECTIVO">EFECTIVO</option>
                        <option value="TRANSFERENCIA_BANCARIA">TRANSFERENCIAS BANCARIAS</option>
                    </select>
                </div>
            </div>

            <div id="camposDinamicos">
            
            </div>
        </div>

        {{-- REGISTROS --}}
        <div class="col-md-10">
            <div class="card">
                <div class="card-header bg-dark text-white text-center">
                    <h5 style="visibility: visible; color: #ffffff; margin-bottom: 0;">Pagos pendientes</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="form-group col-lg-8">
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
                                    <th style="width: 5%;">ID Prog.</th>
                                    <th style="width: 35%;">Acción</th>
                                    <th hidden style="width: 0%;">Fecha de Batería</th>
                                    <th style="width: 20%;">Trámite</th>
                                    <th style="width: 10%;">Precio</th>
                                    <th style="width: 10%;">Descuento</th>
                                    <th style="width: 10%;">Pago</th>  
                                    <th style="width: 5%;">Selec.</th>
                                </tr>
                            </thead>
                            <tbody id="tablaRegistros">
                            </tbody>
                        </table>
                    </div>

                    <div class="card-body">
                        <h5 class="text-center" style="margin-top: 0;">Resumen de pago</h5>
                        <div class="row">
                            <div class="form-group col-lg-4">
                                <label>Subtotal</label>
                                <input type="text" class="form-control" name="subtotal" placeholder="Subtotal" value="0" readonly>
                            </div>
                            <div class="form-group col-lg-4">
                                <label>Descuento</label>
                                <input type="text" class="form-control" name="descuento" placeholder="Descuento" value="0">
                            </div>
                            <div class="form-group col-lg-4">
                                <label>Total</label>
                                <input type="text" class="form-control border border-dark" name="montototal" placeholder="Total" value="0" readonly>
                            </div>
                        </div>
                    </div>
                    <button class="btn btn-success btn-block registrar-btn" id="imprimirReciboBtn" onclick="imprimirReciboSeleccionados()" disabled>
                        Registrar
                    </button>
                </div>
            </div>
        </div>    
    </div>
</form>
@stop

@section('js')

{{-- BUSCAR REGISTROS DE PROGRAMACIONES DE CLIENTES --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
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
            const ciInput = document.querySelector('input[placeholder="CI del cliente"]');
            const subtotalInput = document.querySelector('input[placeholder="Subtotal"]');
            const descuentoInput = document.querySelector('input[placeholder="Descuento"]');
            const totalInput = document.querySelector('input[placeholder="Total"]');

            if (data.cliente) {
                nombreInput.value = data.cliente.nombrecompleto;
                ciInput.value = data.cliente.ci;
            } else {
                alert('Cliente no encontrado');
                nombreInput.value = '';
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
                            <td>${registro.tramite || 'Sin Trámite'}</td>
                            <td>${precio}</td>
                            <td>
                                <input type="number" class="form-control registro-descuento" 
                                    placeholder="0.00" 
                                    value="0.00" 
                                    data-precio="${precio}" 
                                    data-id="${registro.id}" />
                            </td>
                            <td>
                                <input type="number" class="form-control registro-pago" 
                                    placeholder="0.00" 
                                    value="${precio}" 
                                    data-precio="${precio}" 
                                    data-id="${registro.id}" />
                            </td>
                            <td>
                                <input type="checkbox" class="registro-checkbox" data-precio="${precio}" />
                            </td>
                        </tr>`;
                    tabla.innerHTML += fila;
                });
                actualizarEventosRegistro();
            } else {
                tabla.innerHTML = '<tr><td colspan="6">No se encontraron registros</td></tr>';
                subtotalInput.value = '0.00';
                descuentoInput.value = '0.00';
                totalInput.value = '0.00';
            }
        })
        .catch(error => console.error('Error:', error));

        });
    });

    function actualizarEventosRegistro() {
        const descuentos = document.querySelectorAll('.registro-descuento');
        const pagos = document.querySelectorAll('.registro-pago');
        const checkboxes = document.querySelectorAll('.registro-checkbox');
        const descuentoInput = document.querySelector('input[placeholder="Descuento"]');

        descuentos.forEach(input => input.addEventListener('input', calcularTotal));
        pagos.forEach(input => input.addEventListener('input', calcularTotal));
        checkboxes.forEach(checkbox => checkbox.addEventListener('change', calcularTotal));
        descuentoInput.addEventListener('input', calcularTotal);
    }

    function calcularTotal() {
        const checkboxes = document.querySelectorAll('.registro-checkbox:checked');
        let subtotal = 0;
        let descuentoTotal = 0;
        let total = 0;

        checkboxes.forEach(checkbox => {
            const row = checkbox.closest('tr');
            const precio = parseFloat(checkbox.dataset.precio);
            const descuento = parseFloat(row.querySelector('.registro-descuento').value || 0);
            const pago = parseFloat(row.querySelector('.registro-pago').value || 0);

            descuentoTotal += descuento;
            subtotal += pago; // Se suma el valor del pago (editado o igual al precio).
        });

        total = subtotal - descuentoTotal;

        document.querySelector('input[placeholder="Subtotal"]').value = subtotal.toFixed(2);
        document.querySelector('input[placeholder="Descuento"]').value = descuentoTotal.toFixed(2);
        document.querySelector('input[placeholder="Total"]').value = total.toFixed(2);
    }

</script>

{{-- ACTUALIZAR CAMPOS DE TIPO DE TRANSACCION --}}
<script>
    const camposDinamicos = document.getElementById('camposDinamicos');
    const tipoTransaccion1 = document.getElementById('tipoTransaccion1');
    const tipoTransaccion2 = document.getElementById('tipoTransaccion2');
    const dobleTransaccion = document.getElementById('dobleTransaccion');

    function actualizarCampos(tipo, id) {
        const campos = {
            ATC: `
                <div class="form-group">
                    <label>Nro. Tarjeta</label>
                    <input type="text" name="nrotarjeta${id}" class="form-control">
                </div>
                <div class="form-group">
                    <label>AP.</label>
                    <input type="text" name="ap${id}" class="form-control">
                </div>
                <div class="form-group">
                    <label>REF.</label>
                    <input type="text" name="ref${id}" class="form-control">
                </div>
                <div class="form-group">
                    <label>Monto</label>
                    <input type="text" name="monto${id}" class="form-control">
                </div>
            `,
            CHEQUE: `
                <div class="form-group">
                    <label>Nro. Cheque</label>
                    <input type="text" name="nrocheque${id}" class="form-control">
                </div>
                <div class="form-group">
                    <label>Tipo Banco</label>
                    <input type="text" name="tipobanco${id}" class="form-control">
                </div>
                <div class="form-group">
                    <label>Monto</label>
                    <input type="text" name="monto${id}" class="form-control">
                </div>
            `,
            DEPOSITO_BANCARIO: `
                <div class="form-group">
                    <label>Nro. Cuenta Origen</label>
                    <input type="text" name="nrocuentaorigen${id}" class="form-control">
                </div>
                <div class="form-group">
                    <label>Nro. Cuenta Destino</label>
                    <input type="text" name="nrocuentadestino${id}" class="form-control">
                </div>
                <div class="form-group">
                    <label>Bancarización</label>
                    <input type="text" name="bancarizacion${id}" class="form-control">
                </div>
            `,
            TRANSFERENCIA_BANCARIA: `
                <div class="form-group">
                    <label>Nro. Cuenta Origen</label>
                    <input type="text" name="nrocuentaorigen${id}" class="form-control">
                </div>
                <div class="form-group">
                    <label>Nro. Cuenta Destino</label>
                    <input type="text" name="nrocuentadestino${id}" class="form-control">
                </div>
            `,
            EFECTIVO: `
                <div class="form-group">
                    <label>T. Cambio</label>
                    <div class="form-check">
                        <input type="radio" class="form-check-input" name="t_cambio${id}" value="Bs">
                        <label class="form-check-label">Bs.</label>
                    </div>
                    <div class="form-check">
                        <input type="radio" class="form-check-input" name="t_cambio${id}" value="USD">
                        <label class="form-check-label">USD</label>
                    </div>
                </div>
            `
        };
        return campos[tipo] || '';
    }

    tipoTransaccion1.addEventListener('change', () => {
        camposDinamicos.innerHTML = actualizarCampos(tipoTransaccion1.value, 1);
    });

    tipoTransaccion2.addEventListener('change', () => {
        const camposAdicionales = document.getElementById('camposAdicionales');
        if (!camposAdicionales) {
            const div = document.createElement('div');
            div.id = 'camposAdicionales';
            camposDinamicos.appendChild(div);
        }
        camposAdicionales.innerHTML = actualizarCampos(tipoTransaccion2.value, 2);
    });
    dobleTransaccion.addEventListener('change', () => {
        if (dobleTransaccion.checked) {
            tipoTransaccion2.classList.remove('d-none');
        } else {
            tipoTransaccion2.classList.add('d-none');
            const camposAdicionales = document.getElementById('camposAdicionales');
            if (camposAdicionales) camposAdicionales.innerHTML = '';
        }
    });
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
        const logoUrl = "{{ asset('img/logo.png') }}";
        const nombreCliente = document.getElementById('clientenombre').value;
        // Obtener tipo de transacción
        const tipoTransaccion1 = document.getElementById('tipoTransaccion1').value;
        let tipoTransaccion2 = '';
        if (document.getElementById('dobleTransaccion').checked) {
            tipoTransaccion2 = document.getElementById('tipoTransaccion2').value;
        }
        const nombreUsuario = "{{ auth()->user()->name }}";
        
        // Iniciar reciboHTML
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
                        width: 200px;
                        /* margin: 0 auto; */
                        padding: 20px;
                        border: 0px solid #000;
                        text-align: left;
                    }
                    .logo {
                        text-align: center;
                        margin-bottom: 10px;
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
                        line-height: 0.5;
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
                        font-size: 15px;
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
                    <!-- Logo -->
                    <div class="logo">
                        <img src="${logoUrl}" alt="Logo de la empresa">
                    </div>

                    <div class="recibo"><strong>RECIBO N° 2423 </strong></div>

                    <!-- Fecha y hora -->
                    <div class="fecha"><strong>Fecha y Hora:</strong> ${new Date().toLocaleString()}</div>

                    <!-- Línea -->
                    <div class="linea"></div>

                    <!-- Cliente y Emitido por -->
                    <div class="info"><strong>Cliente:</strong> ${nombreCliente}</div>
                    <div class="info"><strong>Emitido por:</strong> ${nombreUsuario}</div>
                    <div class="info"><strong>Tipo de Transacción:</strong> ${tipoTransaccion1}`;
                        if (tipoTransaccion2) {
                            reciboHTML += ` - ${tipoTransaccion2}`;
                        }
                        reciboHTML += `</div>
                    <!-- Línea -->
                    <div class="linea"></div>

                    <!-- Tabla de acciones -->
                    <table class="tabla">
                        <tr>
                            <td><strong>Est./Esp.</strong></td>
                            <td class="precio"><strong>Precio</strong></td>
                        </tr>
        `;

        const checkboxes = document.querySelectorAll('.registro-checkbox:checked');
        if (checkboxes.length === 0) {
            alert('No hay registros seleccionados para imprimir.');
            return null;
        }

        let subtotal = 0;
        checkboxes.forEach(checkbox => {
            const fila = checkbox.closest('tr');
            const accion = fila.children[1].innerText.trim();
            const precio = parseFloat(fila.children[4].innerText.trim()).toFixed(2);

            // Dividir texto largo en varias líneas
            const lineasAccion = dividirTextoEnLineas(accion, 30);
            lineasAccion.forEach((linea, index) => {
                if (index === 0) {
                    reciboHTML += `
                        <tr>
                            <td>${linea}</td>
                            <td class="precio">${precio}</td>
                        </tr>
                    `;
                } else {
                    reciboHTML += `
                        <tr>
                            <td>${linea}</td>
                            <td></td>
                        </tr>
                    `;
                }
            });

            subtotal += parseFloat(precio);
        });

        const descuento = parseFloat(document.querySelector('input[placeholder="Descuento"]').value || 0);
        const total = subtotal - descuento;

        const totalTextual = convertirNumeroATexto(total);

        reciboHTML += `
                    </table>
                    <div class="linea"></div> <!-- Línea antes de los totales -->

                    <!-- Subtotal, Descuento, Total -->
                    <table class="tabla">
                        <tr>
                            <td><strong>Subtotal:</strong></td>
                            <td class="precio">${subtotal.toFixed(2)}</td>
                        </tr>
                        <tr>
                            <td><strong>Descuento:</strong></td>
                            <td class="precio">${descuento.toFixed(2)}</td>
                        </tr>
                        <tr>
                            <td><strong>Total:</strong></td>
                            <td class="precio">${total.toFixed(2)}</td>
                        </tr>
                    </table>

                    <div class="linea"></div>
                    <div class="info"><strong>Son:</strong> ${totalTextual}</div>

                    <!-- Línea -->
                    <div class="linea"></div>

                    <!-- Firma -->
                    <div class="firma">
                        Firma del cliente: _______________
                    </div>
                </div>
            </body>
            </html>
        `;

        return reciboHTML;
    }

    function imprimirReciboSeleccionados() {
        const reciboHTML = generarReciboSeleccionados();
        if (reciboHTML) {
            const ventana = window.open('', '_blank');
            ventana.document.write(reciboHTML);
            ventana.document.close();
            ventana.print();
            ventana.close();
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

{{-- HABILITAR BOTON DE REGISTRAR --}}
<script>
    function validarTipoTransaccion() {
    const tipoTransaccion1 = document.getElementById('tipoTransaccion1').value;
    const tipoTransaccion2 = document.getElementById('tipoTransaccion2').value;
    const imprimirReciboBtn = document.getElementById('imprimirReciboBtn');

    // Verifica si el primer tipo de transacción está seleccionado
    if (tipoTransaccion1 && (tipoTransaccion2 || !document.getElementById('dobleTransaccion').checked)) {
        imprimirReciboBtn.disabled = false; // Habilita el botón
    } else {
        imprimirReciboBtn.disabled = true; // Deshabilita el botón
    }
    }

</script>

@stop

{{-- <script>
    function generarReciboSeleccionados() {
    let recibo = '----------------------------------------\n';
    recibo += '              GOOD LIFE S.R.L.\n';
    recibo += `    Fecha: ${new Date().toLocaleString()}\n`;
    recibo += '----------------------------------------\n';
    recibo += 'Est./Esp.                          Precio\n';
    recibo += '----------------------------------------\n';

    const checkboxes = document.querySelectorAll('.registro-checkbox:checked');
    if (checkboxes.length === 0) {
        alert('No hay registros seleccionados para imprimir.');
        return null;
    }

    let subtotal = 0;
    checkboxes.forEach(checkbox => {
        const fila = checkbox.closest('tr');
        const accion = fila.children[0].innerText.trim();
        const precio = parseFloat(fila.children[3].innerText.trim()).toFixed(2);

        // Dividir texto largo en varias líneas
        const lineasAccion = dividirTextoEnLineas(accion, 30); // Máximo 30 caracteres por línea
        lineasAccion.forEach((linea, index) => {
            if (index === 0) {
                recibo += `${linea.padEnd(30, ' ')}${precio.padStart(10, ' ')}\n`;
            } else {
                recibo += `${linea.padEnd(30, ' ')}\n`; // Líneas siguientes no necesitan espacio para el precio
            }
        });

        subtotal += parseFloat(precio);
    });

    const descuento = parseFloat(document.querySelector('input[placeholder="Descuento"]').value || 0);
    const total = subtotal - descuento;

    recibo += '----------------------------------------\n';
    recibo += `Subtotal:                     ${subtotal.toFixed(2).padStart(10, ' ')}\n`;
    recibo += `Descuento:                    ${descuento.toFixed(2).padStart(10, ' ')}\n`;
    recibo += `Total:                        ${total.toFixed(2).padStart(10, ' ')}\n`;
    recibo += '----------------------------------------\n';
    recibo += 'Gracias por su compra\n';

    return recibo;
    }

    function dividirTextoEnLineas(texto, maxCaracteres) {
        const lineas = [];
        while (texto.length > 0) {
            lineas.push(texto.substring(0, maxCaracteres));
            texto = texto.substring(maxCaracteres);
        }
        return lineas;
    }
    function imprimirReciboSeleccionados() {
    const recibo = generarReciboSeleccionados();
    if (recibo) {
        const ventana = window.open('', '_blank');
        ventana.document.write(`<pre>${recibo}</pre>`);
        ventana.document.close();
        ventana.print();
        ventana.close();
        }
    }
</script> --}}

{{-- <button class="btn btn-success btn-block" id="imprimirReciboBtn" onclick="imprimirReciboSeleccionados()" disabled>Imprimir Recibo</button> --}}