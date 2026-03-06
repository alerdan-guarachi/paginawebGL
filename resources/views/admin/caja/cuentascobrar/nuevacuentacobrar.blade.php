@extends('adminlte::page')

@section('content_header')
@can('admin.caja.cuentascobrar.agregardetalles')
<a class="btn float-right btn-outline-secondary btn-sm" data-toggle="modal" data-target="#crearProductoModal">
    AÑADIR DETALLE
</a>
@endcan
<!-- MODAL CREAR PROVEEDOR -->
<div class="modal fade" id="crearProductoModal" tabindex="-1" aria-labelledby="crearProductoModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="fw-bold text-dark" style="font-weight: 900">AÑADIR DETALLE</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>                
            </div>
            <div class="modal-body">
                {!! Form::open(['route' => 'admin.caja.cuentascobrar.guardardetallecxc', 'method'=>'POST']) !!}
                {!! Form::hidden('usuarioregistroid', auth()->user()->id) !!}
                {!! Form::hidden('usuarioregistronombre', auth()->user()->name) !!}
                
                <div class="row">
                    <div class="form-group col-lg-12"> 
                        {!! Form::label('detalle2', 'Detalle:') !!}
                        {!! Form::text('detalle2', null, ['class' => 'form-control']) !!}
                        @error('detalle2')
                            <small class="text-danger fas fa-exclamation-circle">
                                {{$message}}
                            </small>
                        @enderror
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-lg-12"> 
                        {!! Form::label('precio', 'Precio:') !!}
                        {!! Form::number('precio', null, ['class' => 'form-control']) !!}
                        @error('precio')
                            <small class="text-danger fas fa-exclamation-circle">
                                {{$message}}
                            </small>
                        @enderror
                    </div>
                </div>
                {!! Form::submit('GUARDAR', ['class' => 'btn btn-outline-success btn-sm']) !!}
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>
<h1>NUEVA CUENTA POR COBRAR</h1>
@stop

@section('css')
<link rel="stylesheet" href="{{ asset('css/opcionesmultiples.css') }}">
<style>
    table {
        white-space: nowrap;
    }
    .table td {
        padding: 5px 10px;
    }
    .table tbody tr:nth-child(odd) {
        background-color: #f2f2f2;
    }
    .btn-quitar {
        background-color:  #ffffff;
        color: #e71717;
        border-color: #e71717;
        border-radius: 5px;
        padding: 2px 8px;
        }
    .btn-quitar:hover {
        background-color: #e71717;
        color: #ffffff;
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
    <div class="card-body">
        <div class="card">
            <div class="card-body bg-light">
                <div class="container-fluid text-center p-1 bg-light">
                    <h4 class="fw-bold text-dark" style="font-weight: 900">DATOS DE NUEVA CUENTA POR COBRAR</h4>
                </div>
                <div class="row">
                    <div class="form-group col-lg-2">
                        {!! Form::label('tipoproveedor', 'Tipo_Usuario:') !!}
                        <select id="tipoproveedor" class="form-control">
                            <option value=""></option>
                            <option value="PROVEEDOR EXTERNO">PROVEEDOR EXTERNO</option>
                            <option value="PROVEEDOR INTERNO">PROVEEDOR INTERNO</option>
                            <option value="PROVEEDOR GENERAL">PROVEEDOR GENERAL</option>
                            <option value="PROVEEDOR MEDICO">PROVEEDOR MEDICO</option>
                            <option value="CLIENTE ITA">CLIENTE ITA</option>
                            <option value="CLIENTE AUDITORIA">CLIENTE AUDITORIA</option>
                            <option value="CLIENTE COMUN">CLIENTE COMUN</option>
                        </select>
                    </div>
                    <div class="form-group col-lg-1">
                        {!! Form::label('buscadorProveedor', 'Buscar:') !!}
                        <input type="text" id="buscadorProveedor" class="form-control" placeholder="Buscar...">
                    </div>
                    
                    <div class="form-group col-lg-3">
                        {!! Form::label('proveedornombre', 'Proveedor:') !!}
                        <select id="proveedor-select" class="form-control">
                            <option value=""></option>
                            @foreach ($proveedores as $p)
                                <option value="{{ $p->id }}" data-tipo="{{ $p->categoria }}" data-razon="{{ $p->razonsocial }}" data-tipotrans="{{ $p->tipotransaccion }}" data-ciudad="{{ $p->ciudad }}" data-ciudad2="{{ $p->ciudad2 }}" data-bancoorigen="{{ $p->bancoorigen }}">
                                    {{ $p->razonsocial }}
                                </option>
                            @endforeach
                            @foreach ($proveedormedico as $pm)
                                <option value="{{ $pm->id }}" data-tipo="PROVEEDOR MEDICO" data-razon="{{ $pm->proveedor }}" data-tipotrans="{{ $pm->mododepago }}" data-ciudad="{{ $pm->ciudad }}" data-ciudad2="{{ $pm->ciudad2 }}" data-bancoorigen="{{ $pm->bancoorigen }}">
                                    {{ $pm->proveedor }}
                                </option>
                            @endforeach
                            @foreach ($clientesIta as $c)
                                <option value="{{ $c->id }}" data-tipo="CLIENTE ITA" data-razon="{{ $c->nombrecompleto }}" data-ciudad="{{ $c->sucursal }}" data-ciudad2="{{ $c->ciudad2 }}">
                                    {{ $c->nombrecompleto }}
                                </option>
                            @endforeach
                            @foreach ($clientesAuditoria as $c)
                                <option value="{{ $c->id }}" data-tipo="CLIENTE AUDITORIA" data-razon="{{ $c->nombrecompleto }}" data-ciudad="{{ $c->sucursal }}" data-ciudad2="{{ $c->ciudad2 }}">
                                    {{ $c->nombrecompleto }}
                                </option>
                            @endforeach
                            @foreach ($clientesComunes as $c)
                                <option value="{{ $c->id }}" data-tipo="CLIENTE COMUN" data-razon="{{ $c->nombrecompleto }}" data-ciudad="{{ $c->sucursal }}" data-ciudad2="{{ $c->ciudad2 }}">
                                    {{ $c->nombrecompleto }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    {{-- <script>
                        document.getElementById('tipoproveedor').addEventListener('change', function () {
                            let tipoSeleccionado = this.value;
                            let selectProveedor = document.getElementById('proveedor-select');
                    
                            for (let option of selectProveedor.options) {
                                if (option.value === "") {
                                    option.hidden = false;
                                    continue;
                                }
                                if (option.getAttribute('data-tipo') === tipoSeleccionado) {
                                    option.hidden = false;
                                } else {
                                    option.hidden = true;
                                }
                            }
                            selectProveedor.value = "";
                        });
                    </script> --}}
                    <script>
                        const inputBuscador = document.getElementById('buscadorProveedor');
                        const select = document.getElementById('proveedor-select');
                        const tipoSelect = document.getElementById('tipoproveedor');
                    
                        inputBuscador.addEventListener('input', function () {
                            let filtro = this.value.toLowerCase();
                            let tipoSeleccionado = tipoSelect.value;
                    
                            let hayCoincidencias = false;
                    
                            for (let option of select.options) {
                                if (option.value === "") {
                                    option.hidden = false;
                                    continue;
                                }
                    
                                const texto = option.text.toLowerCase();
                                const tipo = option.getAttribute('data-tipo');
                    
                                const coincideTexto = texto.includes(filtro);
                                const coincideTipo = tipo === tipoSeleccionado;
                    
                                if (coincideTexto && coincideTipo) {
                                    option.hidden = false;
                                    hayCoincidencias = true;
                                } else {
                                    option.hidden = true;
                                }
                            }
                    
                            // Simula que el select se abre mostrando hasta 5 opciones
                            if (hayCoincidencias) {
                                select.size = 5;
                            } else {
                                select.size = 1;
                            }
                        });
                    
                        // Reducir tamaño al salir del input o del select
                        inputBuscador.addEventListener('blur', function () {
                            setTimeout(() => select.size = 1, 200); // espera por si el usuario hace clic en el select
                        });
                    
                        select.addEventListener('blur', function () {
                            setTimeout(() => select.size = 1, 200);
                        });
                    
                        tipoSelect.addEventListener('change', function () {
                            inputBuscador.value = '';
                            const tipoSeleccionado = this.value;
                    
                            for (let option of select.options) {
                                if (option.value === "") {
                                    option.hidden = false;
                                    continue;
                                }
                    
                                const tipo = option.getAttribute('data-tipo');
                                option.hidden = tipo !== tipoSeleccionado;
                            }
                    
                            select.value = '';
                            select.size = 1;
                        });
                    </script>
                    
                    <div class="form-group col-lg-1">
                        {!! Form::label('proveedorid', 'ID Prov.:') !!}
                        {!! Form::text('proveedorid', null, ['class' => 'form-control', 'readonly', 'id' => 'proveedorid']) !!}
                    </div>
                    <div class="form-group col-lg-2" hidden>
                        {!! Form::label('tipotransaccion2', 'Tipo Transacción:') !!}
                        {!! Form::text('tipotransaccion2', null, ['class' => 'form-control', 'readonly', 'id' => 'tipotransaccion2']) !!}
                    </div>
                                 
                    <div class="form-group col-lg-2" hidden>
                        {!! Form::label('plan', 'Plan:') !!}
                        <select name="plan" id="plan" class="form-control">
                            <option value="">Seleccione un plan</option>
                        </select>
                    </div>
                    <div class="form-group col-lg-1" hidden>
                        {!! Form::label('mes', 'Mes:') !!}
                        <input type="month" name="mes" id="mes" class="form-control">
                    </div>
                    {{-- <div class="form-group col-lg-5">
                        {!! Form::label('detalle', 'Detalle:') !!}
                        {!! Form::select('detalle', [
                            '' => '',
                            'ASESORAMIENTO LEGAL' => 'ASESORAMIENTO LEGAL',
                            'FORMULARIO DE APORTE INDEPENDIENTE' => 'FORMULARIO DE APORTE INDEPENDIENTE',
                            'PRESTAMO' => 'PRESTAMO',
                            'PAGO DE INTERES' => 'PAGO DE INTERES'
                        ], null, ['class' => 'form-control', 'id' => 'detalle']) !!}
                    </div> --}}
                    <div class="form-group col-lg-5">
                        {!! Form::label('detalle', 'Detalle:') !!}
                        <select id="detalle" name="detalle" class="form-control">
                            <option value=""></option>
                            @foreach ($detallescxc as $detalle)
                                <option value="{{ $detalle->detalle }}" data-precio="{{ $detalle->precio }}">{{ $detalle->detalle }}</option>
                            @endforeach
                        </select>
                    </div>
                    <script>
                        document.addEventListener('DOMContentLoaded', function () {
                            const detalleSelect = document.getElementById('detalle');
                            const precioInput   = document.getElementById('preciounitario');

                            detalleSelect.addEventListener('change', function () {
                            const selectedOption = this.options[this.selectedIndex];
                            const precio = selectedOption.getAttribute('data-precio');

                            // Asegúrate de que el precio no sea nulo, vacío o NaN
                            if (precio && !isNaN(precio) && parseFloat(precio) > 0) {
                                precioInput.value = parseFloat(precio).toFixed(2);
                            } else {
                                precioInput.value = '';
                            }

                            // Si usas alguna función como calcularTotalUnitario(), llámala aquí
                            if (typeof calcularTotalUnitario === 'function') {
                                calcularTotalUnitario();
                            }
                            });
                        });
                    </script>

                    <div class="form-group col-lg-2" hidden>
                        {!! Form::label('cantidad', 'Cantidad:') !!}
                        {!! Form::number('cantidad', null, ['class' => 'form-control', 'id' => 'cantidad']) !!}
                    </div>
                    <div class="form-group col-lg-3">
                        {!! Form::label('preciounitario', 'Precio Unitario:') !!}
                        {!! Form::number('preciounitario', null, ['class' => 'form-control', 'id' => 'preciounitario']) !!}
                    </div>
                    <div class="form-group col-lg-4">
                        {!! Form::label('descuentounitario', 'Descuento Unitario:') !!}
                        {!! Form::number('descuentounitario', null, ['class' => 'form-control', 'id' => 'descuentounitario']) !!}
                    </div>
                    <div class="form-group col-lg-4">
                        {!! Form::label('totalunitario', 'Total Unitario:') !!}
                        {!! Form::number('totalunitario', null, ['class' => 'form-control', 'id' => 'totalunitario', 'readonly']) !!}
                    </div>
                    <script> 
                        function calcularTotalUnitario() {
                            let cantidad = parseFloat(document.getElementById('cantidad').value) || 1;
                            let precioUnitario = parseFloat(document.getElementById('preciounitario').value) || 0;
                            let descuentoUnitario = parseFloat(document.getElementById('descuentounitario').value) || 0;
                            let totalUnitario = (cantidad * precioUnitario) - descuentoUnitario;
                            totalUnitario = totalUnitario < 0 ? 0 : totalUnitario;
                            document.getElementById('totalunitario').value = totalUnitario.toFixed(2);
                        }
                        document.getElementById('cantidad').addEventListener('input', calcularTotalUnitario);
                        document.getElementById('preciounitario').addEventListener('input', calcularTotalUnitario);
                        document.getElementById('descuentounitario').addEventListener('input', calcularTotalUnitario);
                    </script>
                    
                    {{-- <script>
                        const proveedorSelect = document.getElementById("proveedor-select");
                        const planSelect = document.getElementById("plan");
                        const detalleInput = document.getElementById("detalle");
                        const mesInput = document.getElementById("mes");
                    
                        proveedorSelect.addEventListener("change", function () {
                            const proveedorId = this.value;
                    
                            planSelect.innerHTML = '<option value="">Seleccione un plan</option>';
                            detalleInput.value = '';
                        });
                    
                        planSelect.addEventListener("change", actualizarDetalle);
                        mesInput.addEventListener("change", actualizarDetalle);
                    
                        function actualizarDetalle() {
                            const selectedOption = planSelect.options[planSelect.selectedIndex];
                            const planData = selectedOption.dataset.planinfo ? JSON.parse(selectedOption.dataset.planinfo) : null;

                    
                            const mesValor = mesInput.value;
                            if (mesValor) {
                                const [anio, mes] = mesValor.split('-');
                                const meses = ['ENERO', 'FEBRERO', 'MARZO', 'ABRIL', 'MAYO', 'JUNIO', 'JULIO', 'AGOSTO', 'SEPTIEMBRE', 'OCTUBRE', 'NOVIEMBRE', 'DICIEMBRE'];
                                const mesTexto = meses[parseInt(mes, 10) - 1];
                                partes.push(`MES DE ${mesTexto} ${anio}`);
                            }

                            detalleInput.value = partes.join(', ');

                            // 💰 MONTO FIJO
                            const precioInput = document.getElementById("preciounitario");

                            if (planData && planData.montofijo !== null && planData.montofijo !== '' && !isNaN(planData.montofijo)) {
                                precioInput.value = parseFloat(planData.montofijo).toFixed(2);
                            } else {
                                precioInput.value = '';
                            }
                            
                        }
                    </script> --}}
                    <div class="form-group col-lg-1 d-flex flex-column align-items-center">
                        {!! Form::label('agregar', 'Agregar:') !!}
                        <button type="button" id="btn-add" class="btn btn-outline-success">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <form id="pdf-form-2" action="{{ route('guardar.cuentacobrar') }}" method="POST">
            @csrf
            <div class="card">
                <div class="card-body bg-light">
                    <div class="container-fluid text-center p-1 bg-light">
                        <h4 class="fw-bold text-dark" style="font-weight: 900">CUENTA POR COBRAR</h4>
                    </div>
                    <div class="row">
                        <div class="col-lg-9">
                            <div class="card">
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered" id="data-table">
                                            <thead>
                                                <tr>
                                                    <th>Detalle</th>
                                                    <th hidden>Cantidad</th>
                                                    <th>Precio_P/U</th>
                                                    <th>Desc_P/U</th>
                                                    <th>Total_P/U</th>
                                                    <th>X</th>
                                                </tr>
                                            </thead>
                                            <tbody>

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="form-group col-lg-6" hidden>
                                            <label for="sucursal2" style="margin-bottom: -10px;">Ciudad_Reg.:</label>
                                            <input type="text" class="form-control" id="sucursal2" name="sucursal2" value="{{ $sucursal }}">
                                        </div>
                                        <div class="form-group col-lg-12">
                                            <label for="sucursalgasto2" style="margin-bottom: -10px;">Sucursal Mov.:</label>
                                            <select class="form-control" id="sucursalgasto2" name="sucursalgasto2" required>

                                            </select>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-lg-6">
                                            <label for="formapago2" style="margin-bottom: -10px;">Forma_Pago:</label>
                                            <select class="form-control" id="formapago2" name="formapago2" required>
                                                <option value="CONTADO">CONTADO</option>
                                                <option value="CREDITO">CREDITO</option>
                                            </select>
                                        </div>
                                        <div class="form-group col-lg-6">
                                            <label for="fechapagar2" style="margin-bottom: -10px;">Fecha_Cobro:</label>
                                            <input type="date" class="form-control" id="fechapagar2" name="fechapagar2" required>
                                        </div>
                                    </div>
                                    {{-- <div class="form-group col-lg-2">
                                        {!! Form::label('destino', 'Destino:') !!}
                                        <div id="destino-container">
                                            <input type="text" name="destino" id="destino" class="form-control" readonly>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group col-lg-2">
                                        {!! Form::label('bancoorigen', 'Nro.Cuenta:') !!}
                                        <select id="bancoorigen" class="form-control">
                                            
                                        </select>
                                    </div>      --}}
                                    
                                    <div class="row">
                                        <div class="form-group col-lg-12">
                                            <label for="proveedorNombre2" style="margin-bottom: -10px;">Proveedor:</label>
                                            <input type="text" class="form-control" id="proveedorNombre2" name="proveedorNombre2" readonly>
                                            <input type="hidden" class="form-control" id="proveedorId2" name="proveedorId2" readonly>
                                        </div>
                                        <div class="form-group col-lg-6" hidden>
                                            <label for="tipotransaccion3" style="margin-bottom: -10px;">Tipo_Transac:</label>
                                            <input type="text" class="form-control" id="tipotransaccion3" name="tipotransaccion3" readonly>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-lg-6">
                                            <label for="destino" style="margin-bottom: -10px;">Destino:</label>
                                            <div id="destino-container">
                                                <input type="text" name="destino" id="destino" class="form-control" readonly>
                                            </div>
                                        </div>
                                        <div class="form-group col-lg-6">
                                            <label for="bancoorigen" style="margin-bottom: -10px;">Banco_Destino:</label>
                                            <select id="bancoorigen" name="bancoorigen" class="form-control" required>

                                            </select>
                                        </div>
                                    </div>    
                                    <div class="row">
                                        <div class="form-group col-lg-4">
                                            <label for="subtotalgeneral" style="margin-bottom: -10px;">Subtotal:</label>
                                            <input type="number" class="form-control" id="subtotalgeneral" name="subtotalgeneral" readonly>
                                        </div>
                                        <div class="form-group col-lg-4">
                                            <label for="descuentogeneral" style="margin-bottom: -10px;">Desc.:</label>
                                            <input type="number" class="form-control" id="descuentogeneral" name="descuentogeneral" readonly>
                                        </div>
                                        <div class="form-group col-lg-4">
                                            <label for="montototalgeneral" style="margin-bottom: -10px;">Total:</label>
                                            <input type="number" class="form-control" id="montototalgeneral" name="montototalgeneral" readonly>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-lg-12">
                                            <label for="observacion" style="margin-bottom: -10px;">Observaciones:</label>
                                            <input type="text" class="form-control" id="observacion" name="observacion" required>
                                        </div>
                                    </div>
                                    <input type="hidden" name="ordenes_venta" id="ordenesVentaInput">
                                    <button type="submit" class="btn btn-outline-success">GENERAR CUENTA POR COBRAR</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @php
                        $cuentasFacturada = $cuentas->where('tipocuenta', 'FACTURADA')->pluck('numerocuenta')->values();
                        $cuentasNoFacturada = $cuentas->where('tipocuenta', 'NO FACTURADA')->pluck('numerocuenta')->values();
                        $cuentasTodas = $cuentas->pluck('numerocuenta')->values();
                    @endphp
                    <script>
                        const destinoContainer = document.getElementById('destino-container');
                        const bancoSelect = document.getElementById('bancoorigen');
                        const cuentas = {
                            facturada: @json($cuentasFacturada),
                            no_facturada: @json($cuentasNoFacturada),
                            todas: @json($cuentasTodas)
                        };

                        function actualizarBancoOrigen(valorDestino) {
                            let opciones = [];
                            valorDestino = valorDestino.trim().toUpperCase();

                            if (valorDestino === 'CUENTA NO FACTURADA') {
                                opciones = cuentas.no_facturada;
                                bancoSelect.disabled = false;
                            } else if (valorDestino === 'CUENTA FACTURADA') {
                                opciones = cuentas.facturada;
                                bancoSelect.disabled = false;
                            } else {
                                opciones = cuentas.todas;
                                bancoSelect.disabled = false;
                            }

                            bancoSelect.innerHTML = '<option value=""></option>';
                            opciones.forEach(cuenta => {
                                const opt = document.createElement('option');
                                opt.value = cuenta;
                                opt.textContent = cuenta;
                                bancoSelect.appendChild(opt);
                            });

                            if (opciones.length === 1) {
                                bancoSelect.value = opciones[0];
                            }
                        }

                        // Se ejecuta al cambiar el proveedor
                        document.getElementById('proveedor-select').addEventListener('change', function () {
                            const selectedOption = this.options[this.selectedIndex];
                            const destino = selectedOption.getAttribute('data-bancoorigen');

                            if (destino && destino.trim() !== '') {
                                // Crear input readonly con el valor destino
                                destinoContainer.innerHTML = `<input type="text" name="destino" id="destino" class="form-control" value="${destino}" readonly>`;
                                actualizarBancoOrigen(destino);
                            } else {
                                // Crear select editable si no hay destino
                                destinoContainer.innerHTML = `
                                    <select name="destino" id="destino" class="form-control">
                                        <option value=""></option>
                                        <option value="CUENTA FACTURADA">CUENTA FACTURADA</option>
                                        <option value="CUENTA NO FACTURADA">CUENTA NO FACTURADA</option>
                                    </select>
                                `;
                                document.getElementById('destino').addEventListener('change', function () {
                                    actualizarBancoOrigen(this.value);
                                });
                                bancoSelect.innerHTML = '<option value=""></option>';
                                bancoSelect.disabled = false;
                            }
                        });

                        let primerProveedor = null;
                        document.getElementById('proveedor-select').addEventListener('change', function () {
                            let selectedOption = this.options[this.selectedIndex];
                            let proveedorId = selectedOption.value;
                            let proveedorNombre = selectedOption.getAttribute('data-razon');
                            let tipoTransaccion = selectedOption.getAttribute('data-tipotrans');
                            let destino = selectedOption.getAttribute('data-bancoorigen');
                            let ciudad1 = selectedOption.getAttribute('data-ciudad');

                                let ciudad2 = selectedOption.getAttribute('data-ciudad2');
                                let sucursalSelect = document.getElementById('sucursalgasto2');
                                sucursalSelect.innerHTML = "";
                                if (ciudad1) {
                                    let option1 = document.createElement('option');
                                    option1.value = ciudad1;
                                    option1.text = ciudad1;
                                    sucursalSelect.appendChild(option1);
                                }
                                if (ciudad2 && ciudad2 !== ciudad1) {
                                    let option2 = document.createElement('option');
                                    option2.value = ciudad2;
                                    option2.text = ciudad2;
                                    sucursalSelect.appendChild(option2);
                                }
                    
                            document.getElementById('proveedorid').value = proveedorId;
                            document.getElementById('proveedorNombre2').value = proveedorNombre;
                            document.getElementById('proveedorId2').value = proveedorId;
                            document.getElementById('tipotransaccion2').value = tipoTransaccion;
                            document.getElementById('destino').value = destino;
                            document.getElementById('tipotransaccion3').value = tipoTransaccion;
    
                            // Luego de rellenar el campo destino:
                            if (primerProveedor === null) {
                                primerProveedor = proveedorId;
                            }
                        });
                        document.getElementById('btn-add').addEventListener('click', function () {
                            let proveedorId = document.getElementById('proveedorid').value;
                            let detalle = document.getElementById('detalle').value;
                            let cantidad = parseFloat(document.getElementById('cantidad').value) || 0;
                            let precioUnitario = parseFloat(document.getElementById('preciounitario').value) || 0;
                            let descuentoUnitario = parseFloat(document.getElementById('descuentounitario').value) || 0;
                            let totalUnitario = parseFloat(document.getElementById('totalunitario').value) || 0;
                            if (!proveedorId) {
                                alert("Por favor, seleccione un proveedor.");
                                return;
                            }
                            let tableBody = document.getElementById('data-table').getElementsByTagName('tbody')[0];
                            let newRow = document.createElement('tr');
                    
                            newRow.innerHTML = `
                                <td>${detalle}</td>
                                <td hidden>${cantidad}</td>
                                <td class="precio-unitario">${precioUnitario.toFixed(2)}</td>
                                <td class="descuento-unitario">${descuentoUnitario.toFixed(2)}</td>
                                <td class="total-unitario">${totalUnitario.toFixed(2)}</td>
                                <td>
                                    <button type="button" class="btn btn-quitar btn-sm btn-remove remove-item">X</button>
                                </td>
                            `;
                            tableBody.appendChild(newRow);
                            actualizarTotales();
                            newRow.querySelector('.btn-remove').addEventListener('click', function () {
                                newRow.remove();
                                actualizarTotales();
                            });
                            limpiarCampos();
                        });
                    
                        function actualizarTotales() {
                            let precios = document.querySelectorAll(".precio-unitario");
                            let descuentos = document.querySelectorAll(".descuento-unitario");
                            let totales = document.querySelectorAll(".total-unitario");
                            let subtotal = 0, descuento = 0, montototal = 0;
                    
                            precios.forEach(p => subtotal += parseFloat(p.textContent) || 0);
                            descuentos.forEach(d => descuento += parseFloat(d.textContent) || 0);
                            totales.forEach(t => montototal += parseFloat(t.textContent) || 0);
                            document.getElementById("subtotalgeneral").value = (montototal + descuento).toFixed(2);
                            document.getElementById("descuentogeneral").value = descuento.toFixed(2);
                            document.getElementById("montototalgeneral").value = montototal.toFixed(2);
                        }
                        function limpiarCampos() {
                            document.getElementById('detalle').value = "";
                            document.getElementById('cantidad').value = "";
                            document.getElementById('preciounitario').value = "";
                            document.getElementById('descuentounitario').value = "";
                            document.getElementById('totalunitario').value = "";
                        }
                    </script>
                </div>
            </div>
        </form> 
        <script>
            document.getElementById('pdf-form-2').addEventListener('submit', function (event) {
                let ordenesVenta = [];
                let tableRows = document.querySelectorAll("#data-table tbody tr");

                tableRows.forEach(row => {
                    let detalle = row.cells[0].textContent;
                    let cantidad = parseFloat(row.cells[1].textContent);
                    let precio_unitario = parseFloat(row.cells[2].textContent);
                    let descuento = parseFloat(row.cells[3].textContent);
                    let subtotal = parseFloat(row.cells[4].textContent);

                    ordenesVenta.push({
                        detalle,
                        cantidad,
                        precio_unitario,
                        descuento,
                        subtotal
                    });
                });

                document.getElementById('ordenesVentaInput').value = JSON.stringify(ordenesVenta);
            });

        </script>
            
    </div>
</div>
@stop

@section('js')
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @if (session('eliminar')=='ok')
    <script>
        Swal.fire(
      '¡Eliminado!',
      'El rol se eliminó con éxito',
      'success')
    </script>
    @endif

<script>

    $('.formulario-eliminar').submit(function(e){
        e.preventDefault();

        Swal.fire({
        title: '¿Estás seguro?',
        text: "El rol se eliminará definitivamente",
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
</script>
@endsection