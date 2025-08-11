@extends('adminlte::page')

@section('content_header')

<h1>NUEVAS PRE-ÓRDENES</h1>
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
    <div class="card-header"> 
        <ul class="nav nav-tabs card-header-tabs" id="mainTabs">
            <li class="nav-item">
                <a class="nav-link active tab-rounded" id="tab-1" data-toggle="tab" href="#tab-content-1" role="tab" aria-controls="tab-content-1" aria-selected="true">
                    <i class="fas fa-box"></i> PRE-ÓRDENES DE COMPRA
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link tab-rounded" id="tab-2" data-toggle="tab" href="#tab-content-2" role="tab" aria-controls="tab-content-2" aria-selected="true">
                    <i class="fas fa-box"></i> PRE-ÓRDENES DE SERVICIO
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link tab-rounded" id="tab-4" data-toggle="tab" href="#tab-content-4" role="tab" aria-controls="tab-content-4" aria-selected="true">
                    <i class="fas fa-cogs"></i> PRE-ÓRDENES DE PERSONAL
                </a>
            </li>
            {{-- <li class="nav-item">
                <a class="nav-link tab-rounded" id="tab-3" data-toggle="tab" href="#tab-content-3" role="tab" aria-controls="tab-content-3" aria-selected="true">
                    <i class="fas fa-concierge-bell"></i> PRE-ÓRDENES DE VENTA
                </a>
            </li> --}}
        </ul>
    </div>
    
    <style>
        .nav-link.active.tab-rounded {
            background-color: #94c93b !important;
            color: white !important;
        }
        .nav-link.tab-rounded {
            border-radius: 100px !important;
        }
    </style>    

    <div class="card-body">
        <div class="tab-content">
            {{-- PREORDENES DE COMPRA --}}
            <div class="tab-pane fade show active" id="tab-content-1" role="tabpanel" aria-labelledby="tab-1">
                <div class="card">
                    <div class="card-body bg-light">
                        <div class="container-fluid text-center p-1 bg-light">
                            <h4 class="fw-bold text-dark" style="font-weight: 900">PORTAFOLIO DE PROVEEDORES</h4>
                        </div>
                        <div class="d-flex justify-content-center mt-1">
                            <form class="d-flex align-items-center border rounded p-1 shadow-sm bg-white" id="search-form" style="width: 500px;">
                                <input name="buscarpor" id="buscarpor" class="form-control border-0 shadow-none flex-grow-1" 
                                    type="search" placeholder="BUSCAR PRODUCTO..." aria-label="Search">
                            </form>
                        </div>
                        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
                        <script>
                            $(document).ready(function() {
                                $("#buscarpor").on("keyup", function() {
                                    let query = $(this).val();
        
                                    $.ajax({
                                        url: "{{ route('admin.inventario.adquisicioninventario') }}",
                                        type: "GET",
                                        data: { buscarpor: query },
                                        success: function(response) {
                                            $("#tabla-bienes").html(response);
                                        }
                                    });
                                });
                            });
                        </script>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead style="background-color: white">
                                            <tr>
                                                <th>Código</th>
                                                <th>Tipo</th>
                                                <th>Nombre</th>
                                                <th>Especif.</th>
                                                <th>Color</th>
                                                <th>Cant.</th>
                                                <th>Precio</th>
                                                <th>Proveedor</th>
                                                <th style="background-color: #f8ffed">Stock</th>
                                                <th>Selec</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tabla-bienes">
                                            @include('admin.inventario.partials.tabla')
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <br>
                <form id="pdf-form" action="{{ route('generar.preordencompra') }}" method="POST">
                    @csrf
                    <div class="card">
                        <div class="card-body bg-light">
                            <div class="container-fluid text-center p-1 bg-light">
                                <h4 class="fw-bold text-dark" style="font-weight: 900">PRE - ÓRDEN DE COMPRA</h4>
                            </div>
                            <div class="row">
                                <div class="col-lg-9">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-striped" id="selected-items-table">
                                                    <thead>
                                                        <tr>
                                                            <th>Cod.</th>
                                                            <th>Nombre</th>
                                                            <th>Color</th>
                                                            <th>Und.</th>
                                                            <th>Precio</th>
                                                            <th>Cant.</th>
                                                            <th>Subtotal</th>
                                                            <th>Desc.</th>
                                                            <th>Total</th>
                                                            <th>X</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody></tbody>
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
                                                    <label for="sucursal" style="margin-bottom: -10px;">Ciudad_Reg.:</label>
                                                    <input type="text" class="form-control" id="sucursal" name="sucursal" value="{{ $sucursal }}">
                                                </div>
                                                <div class="form-group col-lg-6">
                                                    <label for="sucursalgasto" style="margin-bottom: -10px;">Sucursal.:</label>
                                                    <select class="form-control" id="sucursalgasto" name="sucursalgasto" required>
                                                        <option value="SANTA CRUZ">SANTA CRUZ</option>
                                                        <option value="COCHABAMBA">COCHABAMBA</option>
                                                    </select>
                                                </div>
                                                <div class="form-group col-lg-6">
                                                    <label for="fechacomprar" style="margin-bottom: -10px;">Fecha_Comp:</label>
                                                    <input type="date" class="form-control" id="fechacomprar" name="fechacomprar" required>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="form-group col-lg-6">
                                                    <label for="formapago" style="margin-bottom: -10px;">Forma_Pago:</label>
                                                    <select class="form-control" id="formapago" name="formapago" required>
                                                        <option value="CONTADO">CONTADO</option>
                                                        <option value="CREDITO">CREDITO</option>
                                                    </select>
                                                </div>
                                                <div class="form-group col-lg-6">
                                                    <label for="fechapagar" style="margin-bottom: -10px;">Fecha_Pago:</label>
                                                    <input type="date" class="form-control" id="fechapagar" name="fechapagar" required>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="form-group col-lg-6">
                                                    <label for="proveedorNombre" style="margin-bottom: -10px;">Proveedor:</label>
                                                    <input type="text" class="form-control" id="proveedorNombre" name="proveedorNombre" readonly>
                                                    <input type="hidden" class="form-control" id="proveedorId" name="proveedorId" readonly>
                                                </div>
                                                <div class="form-group col-lg-6">
                                                    <label for="tipotransaccion" style="margin-bottom: -10px;">Tipo_transac:</label>
                                                    <select class="form-control" id="tipotransaccion" name="tipotransaccion" required>
                                                        <option value="TRANSFERENCIA BANCARIA">TRANSFERENCIA BANCARIA</option>
                                                        {{-- <option value="EFECTIVO">EFECTIVO</option> --}}
                                                        <option value="DEPOSITO BANCARIO">DEPOSITO BANCARIO</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="form-group col-lg-4">
                                                    <label for="subtotal" style="margin-bottom: -10px;">Subtotal:</label>
                                                    <input type="number" class="form-control" id="subtotal" name="subtotal" readonly>
                                                </div>
                                                <div class="form-group col-lg-4">
                                                    <label for="descuento" style="margin-bottom: -10px;">Desc.:</label>
                                                    <input type="number" class="form-control" id="descuento" name="descuento" readonly>
                                                </div>
                                                <div class="form-group col-lg-4">
                                                    <label for="montototal" style="margin-bottom: -10px;">Total:</label>
                                                    <input type="number" class="form-control" id="montototal" name="montototal" readonly>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="form-group col-lg-12">
                                                    <label for="observacion" style="margin-bottom: -10px;">Observaciones:</label>
                                                    <input type="text" class="form-control" id="observacion" name="observacion">
                                                </div>
                                            </div>
                                            <input type="hidden" name="ordenes_compra" id="ordenesCompraInput">
                                            <button type="submit" class="btn btn-outline-success">GENERAR PRE-ORDEN</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form> 
                
                <script>
                    document.querySelector("#pdf-form").addEventListener("submit", function (e) {
                        let ordenesCompra = [];
                        document.querySelectorAll("#selected-items-table tbody tr").forEach(row => {
                            let orden = {
                                id: row.children[0].textContent.trim(),
                                detalle: row.children[1].textContent.trim(),
                                cantidad: row.querySelector(".cantidad").value.trim(),
                                precio_unitario: row.children[4].textContent.trim(),
                                descuento: row.querySelector(".descuento").value.trim(),
                                subtotal: row.querySelector(".subtotal").value.trim(),
                                fecha_pago: new Date().toISOString().split("T")[0]
                            };
                            ordenesCompra.push(orden);
                        });
                        document.querySelector("#ordenesCompraInput").value = JSON.stringify(ordenesCompra);
                    });
                </script>
                <script>
                    let proveedorPermitido = null;
                    let productosSeleccionados = new Set();
                
                    document.addEventListener("DOMContentLoaded", function () {
                    if (!document.referrer.includes(window.location.hostname)) {
                        localStorage.removeItem("selectedProducts");
                    }
        
                            document.addEventListener("DOMContentLoaded", function () {
                                let storedProducts = JSON.parse(localStorage.getItem("selectedProducts")) || [];
                                if (storedProducts.length > 0) {
                                    proveedorPermitido = storedProducts[0].proveedorid;
                                    document.querySelector("#proveedorNombre").value = storedProducts[0].proveedornombre;
                                    document.querySelector("#proveedorId").value = storedProducts[0].proveedorid;
                                    storedProducts.forEach(item => agregarProductoATabla(item));
                                }
                        });
                        document.addEventListener("change", function (e) {
                            if (e.target.classList.contains("select-item")) {
                                let productoId = e.target.dataset.id;
                                let proveedorId = e.target.dataset.proveedorid;
                                let proveedorNombre = e.target.closest("tr").children[7].textContent;
        
                                if (e.target.checked) {
                                    if (productosSeleccionados.has(productoId)) {
                                        alert("Este producto ya ha sido seleccionado.");
                                        e.target.checked = false;
                                        return;
                                    }
        
                                    if (proveedorPermitido === null) {
                                        proveedorPermitido = proveedorId;
                                        document.querySelector("#proveedorNombre").value = proveedorNombre;
                                        document.querySelector("#proveedorId").value = proveedorId;
                                    } else if (proveedorPermitido !== proveedorId) {
                                        alert("Solo puedes agregar productos del mismo proveedor.");
                                        e.target.checked = false;
                                        return;
                                    }
        
                                    let producto = {
                                        id: productoId,
                                        cantidad: e.target.dataset.cantidad,
                                        nombre: e.target.dataset.nombre,
                                        color: e.target.dataset.color,
                                        precio: e.target.dataset.precio,
                                        proveedorid: proveedorId,
                                        proveedornombre: proveedorNombre,
                                    };
        
                                    agregarProductoATabla(producto);
                                    guardarProductosEnLocalStorage();
                                } else {
                                    eliminarProductoDeTabla(productoId);
                                }
                            }
                        });
                        document.addEventListener("click", function(e) {
                            if (e.target.classList.contains("remove-item")) {
                                let productoId = e.target.dataset.id;
                                eliminarProductoDeTabla(productoId);
                            }
                        });
        
                        document.addEventListener("input", function (e) {
                            let row = e.target.closest("tr");
                            if (!row) return;

                            let precioUnitario = parseFloat(row.querySelector(".cantidad").dataset.precioUnitario) || 0;
                            let cantidad = parseFloat(row.querySelector(".cantidad").value) || 1;
                            let descuento = parseFloat(row.querySelector(".descuento").value) || 0;

                            let subtotal = precioUnitario * cantidad;
                            row.querySelector(".subtotal").value = subtotal.toFixed(2);

                            let total = subtotal - descuento;
                            row.querySelector(".total").value = total.toFixed(2);

                            updateTotals();
                        });

                    });
                
                    function agregarProductoATabla(producto) {
                        let table = document.querySelector("#selected-items-table tbody");

                        // Calculamos el precio unitario
                        let precioUnitario = parseFloat(producto.precio) / parseFloat(producto.cantidad);

                        let row = document.createElement("tr");
                        row.innerHTML = `
                            <td>${producto.id}</td>
                            <td>${producto.nombre}</td>
                            <td>${producto.color}</td>
                            <td>${producto.cantidad}</td>
                            <td>${producto.precio}</td> <!-- PRECIO TOTAL -->
                            <td><input type="number" class="form-control cantidad" value="1" min="1" style="width: 70px; height: 25px;" data-precio-unitario="${precioUnitario}"></td>
                            <td><input type="number" class="form-control subtotal"  style="width: 90px; height: 25px;" value="${precioUnitario.toFixed(2)}" readonly></td>
                            <td><input type="number" class="form-control descuento"  style="width: 80px; height: 25px;" value="0"></td>
                            <td><input type="number" class="form-control total"  style="width: 90px; height: 25px;" value="${precioUnitario.toFixed(2)}" readonly></td>
                            <td><button class="btn btn-quitar btn-sm remove-item" data-id="${producto.id}">X</button></td>
                        `;

                        table.appendChild(row);
                        productosSeleccionados.add(producto.id);
                        updateTotals();
                    }

                
                    function eliminarProductoDeTabla(productoId) {
                        let rows = document.querySelectorAll("#selected-items-table tbody tr");
                        rows.forEach(row => {
                            if (row.querySelector(".remove-item")?.dataset.id === productoId) {
                                row.remove();
                                productosSeleccionados.delete(productoId);
                            }
                        });
                
                        document.querySelector(`.select-item[data-id="${productoId}"]`).checked = false;
                
                        if (productosSeleccionados.size === 0) {
                            proveedorPermitido = null;
                            document.querySelector("#proveedorNombre").value = "";
                            document.querySelector("#proveedorId").value = "";
                        }
                
                        guardarProductosEnLocalStorage();
                        updateTotals();
                    }
                
                    function guardarProductosEnLocalStorage() {
                        let productos = [];
                        document.querySelectorAll("#selected-items-table tbody tr").forEach(row => {
                            let producto = {
                                id: row.querySelector(".remove-item").dataset.id,
                                cantidad: row.children[0].textContent,
                                nombre: row.children[1].textContent,
                                color: row.children[2].textContent,
                                precio: row.children[3].textContent,
                                proveedorid: proveedorPermitido,
                                proveedornombre: document.querySelector("#proveedorNombre").value
                            };
                            productos.push(producto);
                        });
                        localStorage.setItem("selectedProducts", JSON.stringify(productos));
                    }
                
                    function updateTotals() {
                        let subtotalGeneral = 0;
                        let descuentoGeneral = 0;
                        let totalGeneral = 0;
        
                        document.querySelectorAll("#selected-items-table tbody tr").forEach(row => {
                            let subtotal = parseFloat(row.querySelector(".subtotal").value) || 0;
                            let descuento = parseFloat(row.querySelector(".descuento").value) || 0;
                            let total = parseFloat(row.querySelector(".total").value) || 0;
                            
                            subtotalGeneral += subtotal;
                            descuentoGeneral += descuento;
                            totalGeneral += total;
                        });
                        document.querySelector("#subtotal").value = subtotalGeneral.toFixed(2);
                        document.querySelector("#descuento").value = descuentoGeneral.toFixed(2);
                        document.querySelector("#montototal").value = totalGeneral.toFixed(2);
                    }
                </script>
            </div>

            {{-- PREORDENES DE SERVICIO --}}
            <div class="tab-pane fade" id="tab-content-2" role="tabpanel" aria-labelledby="tab-2">
                <div class="card">
                    <div class="card-body bg-light">
                        <div class="container-fluid text-center p-1 bg-light">
                            <h4 class="fw-bold text-dark" style="font-weight: 900">NUEVA PRE - ÓRDEN DE SERVICIO</h4>
                        </div>
                        <div class="row">
                            <div class="form-group col-lg-2">
                                {!! Form::label('proveedornombre', 'Proveedor:') !!}
                                <select id="proveedor-select" class="form-control">
                                    <option value=""></option>
                                    @foreach ($proveedores as $proveedor)
                                        <option value="{{ $proveedor->id }}" data-razon="{{ $proveedor->razonsocial }}" data-tipotrans="{{ $proveedor->tipotransaccion }}" data-ciudad="{{ $proveedor->ciudad }}" data-ciudad2="{{ $proveedor->ciudad2 }}">
                                            {{ $proveedor->razonsocial }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <script>
                                document.addEventListener("DOMContentLoaded", function () {
                                    let proveedorSelect = document.getElementById("proveedor-select");
                                    proveedorSelect.addEventListener("change", function () {
                                        if (this.value) {
                                            this.setAttribute("disabled", "disabled");
                                        }
                                    });
                                });
                            </script>
                            
                            <div class="form-group col-lg-2" hidden>
                                {!! Form::label('proveedorid', 'ID Proveedor:') !!}
                                {!! Form::text('proveedorid', null, ['class' => 'form-control', 'readonly', 'id' => 'proveedorid']) !!}
                            </div>
                            <div class="form-group col-lg-2">
                                {!! Form::label('tipotransaccion2', 'Tipo Transacción:') !!}
                                {!! Form::text('tipotransaccion2', null, ['class' => 'form-control', 'readonly', 'id' => 'tipotransaccion2']) !!}
                            </div>
                            <div class="form-group col-lg-2">
                                {!! Form::label('plan', 'Plan/Motivo:') !!}
                                <select name="plan" id="plan" class="form-control">
                                    <option value="">Seleccione un plan</option>
                                </select>
                            </div>
                            <div class="form-group col-lg-1">
                                {!! Form::label('mes', 'Mes:') !!}
                                <input type="month" name="mes" id="mes" class="form-control">
                            </div>
                            <div class="form-group col-lg-5">
                                {!! Form::label('detalle', 'Detalle:') !!}
                                {!! Form::text('detalle', null, ['class' => 'form-control', 'id' => 'detalle']) !!}
                            </div>
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
                            
                            <script>
                                const planesPorProveedor = @json($planes->groupBy('proveedorid'));
                            
                                const proveedorSelect = document.getElementById("proveedor-select");
                                const planSelect = document.getElementById("plan");
                                const detalleInput = document.getElementById("detalle");
                                const mesInput = document.getElementById("mes");
                                let textoFijo = '';
                            
                                proveedorSelect.addEventListener("change", function () {
                                    const proveedorId = this.value;
                            
                                    planSelect.innerHTML = '<option value="">Seleccione un plan</option>';
                                    detalleInput.value = '';
                                    textoFijo = '';
                            
                                    if (planesPorProveedor[proveedorId]) {
                                        planesPorProveedor[proveedorId].forEach(plan => {
                                            const option = document.createElement("option");
                                            option.value = plan.id;
                                            let texto = plan.plan;
                            
                                            if (plan.contrato && plan.contrato != 0) texto += ` - NRO. CONTRATO ${plan.contrato}`;
                                            if (plan.linea && plan.linea != 0) texto += ` - NRO. LINEA ${plan.linea}`;
                                            if (plan.servicio && plan.servicio != 0) texto += ` - NRO. SERVICIO ${plan.servicio}`;
                                            if (plan.cuenta && plan.cuenta != 0) texto += ` - NRO. CUENTA ${plan.cuenta}`;
                                            if (plan.codigo && plan.codigo != 0) texto += ` - NRO. CODIGO ${plan.codigo}`;
                                            if (plan.motivo && plan.motivo != 0) texto += ` ${plan.motivo}`;
                                            if (plan.ciudad && plan.ciudad != 0) texto += ` - CIUDAD ${plan.ciudad}`;
                            
                                            option.textContent = texto;
                                            option.dataset.planinfo = JSON.stringify(plan);
                                            planSelect.appendChild(option);
                                        });
                                    }
                                });
                            
                                planSelect.addEventListener("change", actualizarDetalle);
                                mesInput.addEventListener("change", actualizarDetalle);
                            
                                function actualizarDetalle() {
                                    const selectedOption = planSelect.options[planSelect.selectedIndex];
                                    const planData = selectedOption.dataset.planinfo ? JSON.parse(selectedOption.dataset.planinfo) : null;
                            
                                    let partes = [];
                            
                                    if (planData) {
                                        if (planData.codigo && planData.codigo != 0) partes.push(`NRO. CODIGO ${planData.codigo}`);
                                        if (planData.contrato && planData.contrato != 0) partes.push(`NRO. CONTRATO ${planData.contrato}`);
                                        if (planData.linea && planData.linea != 0) partes.push(`NRO. LINEA ${planData.linea}`);
                                        if (planData.cuenta && planData.cuenta != 0) partes.push(`NRO. CUENTA ${planData.cuenta}`);
                                        if (planData.servicio && planData.servicio != 0) partes.push(`NRO. SERVICIO ${planData.servicio}`);
                                        if (planData.motivo && planData.motivo != 0) partes.push(`${planData.motivo}`);
                                        if (planData.ciudad && planData.ciudad != 0) partes.push(`CIUDAD ${planData.ciudad}`);
                                    }
                            
                                    const mesValor = mesInput.value;
                                    if (mesValor) {
                                        const [anio, mes] = mesValor.split('-');
                                        const meses = ['ENERO', 'FEBRERO', 'MARZO', 'ABRIL', 'MAYO', 'JUNIO', 'JULIO', 'AGOSTO', 'SEPTIEMBRE', 'OCTUBRE', 'NOVIEMBRE', 'DICIEMBRE'];
                                        const mesTexto = meses[parseInt(mes, 10) - 1];
                                        partes.push(`MES DE ${mesTexto} ${anio}`);
                                    }
                            
                                    textoFijo = partes.join(', ') + '';
                                    detalleInput.value = textoFijo;
                                    detalleInput.setSelectionRange(detalleInput.value.length, detalleInput.value.length);
                                    detalleInput.focus();
                            
                                    // 💰 MONTO FIJO
                                    const precioInput = document.getElementById("preciounitario");
                                    if (planData && planData.montofijo !== null && planData.montofijo !== '' && !isNaN(planData.montofijo)) {
                                        precioInput.value = parseFloat(planData.montofijo).toFixed(2);
                                    } else {
                                        precioInput.value = '';
                                    }
                                }
                            
                                // 🔒 Protección del texto fijo
                                detalleInput.addEventListener('keydown', function (e) {
                                    const cursorPos = detalleInput.selectionStart;
                            
                                    // Evita borrar o escribir dentro del texto fijo
                                    if (cursorPos < textoFijo.length &&
                                        !['ArrowRight', 'ArrowLeft', 'Tab'].includes(e.key)) {
                                        e.preventDefault();
                                        detalleInput.setSelectionRange(detalleInput.value.length, detalleInput.value.length);
                                    }
                            
                                    // Bloquea backspace y delete sobre la parte fija
                                    if ((e.key === 'Backspace' && cursorPos <= textoFijo.length) ||
                                        (e.key === 'Delete' && cursorPos < textoFijo.length)) {
                                        e.preventDefault();
                                    }
                                });
                            
                                detalleInput.addEventListener('click', function () {
                                    if (detalleInput.selectionStart < textoFijo.length) {
                                        detalleInput.setSelectionRange(detalleInput.value.length, detalleInput.value.length);
                                    }
                                });
                            
                                // Previene pegar sobre la parte protegida
                                detalleInput.addEventListener('paste', function (e) {
                                    if (detalleInput.selectionStart < textoFijo.length) {
                                        e.preventDefault();
                                        detalleInput.setSelectionRange(detalleInput.value.length, detalleInput.value.length);
                                    }
                                });
                            </script>

                            <div class="form-group col-lg-1 d-flex flex-column align-items-center">
                                {!! Form::label('agregar', 'Agregar:') !!}
                                <button type="button" id="btn-add" class="btn btn-outline-success">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <form id="pdf-form-2" action="{{ route('generar.preordenservicio') }}" method="POST">
                    @csrf
                    <div class="card">
                        <div class="card-body bg-light">
                            <div class="container-fluid text-center p-1 bg-light">
                                <h4 class="fw-bold text-dark" style="font-weight: 900">PRE - ÓRDEN DE SERVICIO</h4>
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
                                                {{-- <div class="form-group col-lg-12">
                                                    <label for="sucursalgasto2" style="margin-bottom: -10px;">Sucursal.:</label>
                                                    <select class="form-control" id="sucursalgasto2" name="sucursalgasto2" required>
                                                        <option value="SANTA CRUZ">SANTA CRUZ</option>
                                                        <option value="COCHABAMBA">COCHABAMBA</option>
                                                    </select>
                                                </div> --}}
                                                <div class="form-group col-lg-12">
                                                    <label for="sucursalgasto2" style="margin-bottom: -10px;">Sucursal.:</label>
                                                    <select class="form-control" id="sucursalgasto2" name="sucursalgasto2" required readonly>
                                                        <!-- Las opciones se llenarán dinámicamente -->
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
                                                    <label for="fechapagar2" style="margin-bottom: -10px;">Fecha_Pago:</label>
                                                    <input type="date" class="form-control" id="fechapagar2" name="fechapagar2" required>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="form-group col-lg-6">
                                                    <label for="proveedorNombre2" style="margin-bottom: -10px;">Proveedor:</label>
                                                    <input type="text" class="form-control" id="proveedorNombre2" name="proveedorNombre2" readonly>
                                                    <input type="hidden" class="form-control" id="proveedorId2" name="proveedorId2" readonly>
                                                </div>
                                                <div class="form-group col-lg-6">
                                                    <label for="tipotransaccion3" style="margin-bottom: -10px;">Tipo_Transac:</label>
                                                    <input type="text" class="form-control" id="tipotransaccion3" name="tipotransaccion3" readonly>
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
                                                    <input type="text" class="form-control" id="observacion" name="observacion">
                                                </div>
                                            </div>
                                            <input type="hidden" name="ordenes_venta" id="ordenesVentaInput">
                                            <button type="submit" class="btn btn-outline-success">GENERAR PRE-ORDEN</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <script>
                                let primerProveedor = null;
                            
                                document.getElementById('proveedor-select').addEventListener('change', function () {
                                    let selectedOption = this.options[this.selectedIndex];
                                    let proveedorId = selectedOption.value;
                                    let proveedorNombre = selectedOption.getAttribute('data-razon');
                                    let tipoTransaccion = selectedOption.getAttribute('data-tipotrans');
                                    let ciudad1 = selectedOption.getAttribute('data-ciudad');

                                    //RELLENAR SUCURSAL DE GASTO
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
                                    //
                            
                                    document.getElementById('proveedorid').value = proveedorId;
                                    document.getElementById('proveedorNombre2').value = proveedorNombre;
                                    document.getElementById('proveedorId2').value = proveedorId;
                                    document.getElementById('tipotransaccion2').value = tipoTransaccion;
                                    document.getElementById('tipotransaccion3').value = tipoTransaccion;
                            
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
                                    if (primerProveedor !== proveedorId) {
                                        alert("Solo puede agregar productos del mismo proveedor.");
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
        
            {{-- PREORDENES DE PERSONAL --}}
            <div class="tab-pane fade" id="tab-content-4" role="tabpanel" aria-labelledby="tab-4">
                <div class="card">
                    <div class="card-body bg-light">
                        <div class="container-fluid text-center p-1 bg-light">
                            <h4 class="fw-bold text-dark" style="font-weight: 900">NUEVA PRE - ÓRDEN DE PERSONAL</h4>
                        </div>
                        <div class="row">
                            <div class="form-group col-lg-2">
                                {!! Form::label('proveedornombre2', 'Proveedor:') !!}
                                <select id="proveedor-select2" class="form-control">
                                    <option value=""></option>
                                    @foreach ($proveedorespersonal as $proveedor2)
                                        <option value="{{ $proveedor2->id }}" data-razon="{{ $proveedor2->razonsocial }}" data-tipotrans="{{ $proveedor2->tipotransaccion }}" data-categoria="{{ $proveedor2->categoria }}" data-ciudad="{{ $proveedor2->ciudad }}" data-ciudad2="{{ $proveedor2->ciudad2 }}">
                                            {{ $proveedor2->razonsocial }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <!-- <script>
                                document.addEventListener("DOMContentLoaded", function () {
                                    let proveedorSelect = document.getElementById("proveedor-select2");
                                    proveedorSelect.addEventListener("change", function () {
                                        if (this.value) {
                                            this.setAttribute("disabled", "disabled");
                                        }
                                    });
                                });
                            </script> -->
                            <div class="form-group col-lg-2" hidden>
                                {!! Form::label('proveedorid2', 'ID Proveedor:') !!}
                                {!! Form::text('proveedorid2', null, ['class' => 'form-control', 'readonly', 'id' => 'proveedorid2']) !!}
                            </div>
                            <div class="form-group col-lg-2">
                                {!! Form::label('tipotransaccion22', 'Tipo Transacción:') !!}
                                {!! Form::text('tipotransaccion22', null, ['class' => 'form-control', 'readonly', 'id' => 'tipotransaccion22']) !!}
                            </div>
                            <div class="form-group col-lg-2">
                                {!! Form::label('motivo', 'Motivo:') !!}
                                <select name="motivo" id="motivo" class="form-control">
                                    <option value=""></option>
                                </select>
                            </div>
                            <div class="form-group col-lg-1">
                                {!! Form::label('mes', 'Mes:') !!}
                                <input type="month" name="mes2" id="mes2" class="form-control">
                            </div>
                            <div class="form-group col-lg-5">
                                {!! Form::label('detalle2', 'Detalle:') !!}
                                {!! Form::text('detalle2', null, ['class' => 'form-control', 'id' => 'detalle2']) !!}
                            </div>
                            <div class="form-group col-lg-2" hidden>
                                {!! Form::label('cantidad2', 'Cantidad:') !!}
                                {!! Form::number('cantidad2', null, ['class' => 'form-control', 'id' => 'cantidad2']) !!}
                            </div>
                            <div class="form-group col-lg-3">
                                {!! Form::label('preciounitario2', 'Precio Unitario:') !!}
                                {!! Form::number('preciounitario2', null, ['class' => 'form-control', 'id' => 'preciounitario2']) !!}
                            </div>
                            <div class="form-group col-lg-4">
                                {!! Form::label('descuentounitario2', 'Descuento Unitario:') !!}
                                {!! Form::number('descuentounitario2', null, ['class' => 'form-control', 'id' => 'descuentounitario2']) !!}
                            </div>
                            <div class="form-group col-lg-4">
                                {!! Form::label('totalunitario2', 'Total Unitario:') !!}
                                {!! Form::number('totalunitario2', null, ['class' => 'form-control', 'id' => 'totalunitario2', 'readonly']) !!}
                            </div>
                            <script>
                                function calcularTotalUnitario() {
                                    let cantidad = parseFloat(document.getElementById('cantidad2').value) || 1;
                                    let precioUnitario = parseFloat(document.getElementById('preciounitario2').value) || 0;
                                    let descuentoUnitario = parseFloat(document.getElementById('descuentounitario2').value) || 0;
                                    let totalUnitario = (cantidad * precioUnitario) - descuentoUnitario;
                                    totalUnitario = totalUnitario < 0 ? 0 : totalUnitario;
                                    document.getElementById('totalunitario2').value = totalUnitario.toFixed(2);
                                }
                                document.getElementById('cantidad2').addEventListener('input', calcularTotalUnitario);
                                document.getElementById('preciounitario2').addEventListener('input', calcularTotalUnitario);
                                document.getElementById('descuentounitario2').addEventListener('input', calcularTotalUnitario);
                            </script>
                            <script>
                                const proveedorSelect2 = document.getElementById('proveedor-select2');
                                const motivoSelect = document.getElementById('motivo');
                                const mesInput2 = document.getElementById('mes2');
                                const detalle2Input = document.getElementById('detalle2');
                                proveedorSelect2.addEventListener('change', function () {
                                    const selectedOption = this.options[this.selectedIndex];
                                    const categoria = selectedOption.getAttribute('data-categoria');
                                    const razonsocial = selectedOption.getAttribute('data-razon');
                                    motivoSelect.innerHTML = '';
                                    if (categoria === 'PROVEEDOR EXTERNO') {
                                        if (razonsocial === 'DENISSE MAUREN LOPEZ FLORES') {
                                            motivoSelect.innerHTML = `
                                                <option value=""></option>
                                                <option value="PAGO DE SERVICIOS PRESTADOS">PAGO DE SERVICIOS PRESTADOS</option>
                                                <option value="ALMUERZO">ALMUERZO</option>
                                                <option value="CENA">CENA</option>
                                                <option value="DEVOLUCION DE VIATICO">DEVOLUCION DE VIATICO</option>
                                                <option value="DEVOLUCION DE FACTURA">DEVOLUCION DE FACTURA</option>
                                                <option value="CREDITO FISCAL">CREDITO FISCAL</option>
                                                <option value="PRESTAMO69">PRESTAMO69</option>
                                            `;
                                        } else {
                                            motivoSelect.innerHTML = `
                                                <option value=""></option>
                                                <option value="PAGO DE SERVICIOS PRESTADOS">PAGO DE SERVICIOS PRESTADOS</option>
                                                <option value="ALMUERZO">ALMUERZO</option>
                                                <option value="CENA">CENA</option>
                                                <option value="DEVOLUCION DE VIATICO">DEVOLUCION DE VIATICO</option>
                                                <option value="DEVOLUCION DE FACTURA">DEVOLUCION DE FACTURA</option>
                                            `;
                                        }
                                    } else if (categoria === 'PROVEEDOR INTERNO') {
                                        if (razonsocial === 'FABRICIO ORLANDO PRADO PARRADO') {
                                            motivoSelect.innerHTML = `
                                                <option value=""></option>
                                                <option value="SUELDO">SUELDO</option>
                                                <option value="TRANSPORTE">TRANSPORTE</option>
                                                <option value="AGUINALDO">AGUINALDO</option>
                                                <option value="BONO">BONO</option>
                                                <option value="RETROACTIVO">RETROACTIVO</option>
                                                <option value="FINIQUITO">FINIQUITO</option>
                                                <option value="QUINQUENIO">QUINQUENIO</option>
                                                <option value="VIATICO">VIATICO</option>
                                                <option value="ALMUERZO">ALMUERZO</option>
                                                <option value="CENA">CENA</option>
                                                <option value="DEVOLUCION DE VIATICO">DEVOLUCION DE VIATICO</option>
                                                <option value="DEVOLUCION DE FACTURA">DEVOLUCION DE FACTURA</option>
                                                <option value="CREDITO FISCAL">CREDITO FISCAL</option>
                                            `;
                                        } else if (razonsocial === 'JHOSELINE EVA VELASQUEZ ESCOBAR') {
                                                motivoSelect.innerHTML = `
                                                    <option value=""></option>
                                                    <option value="SUELDO">SUELDO</option>
                                                    <option value="TRANSPORTE">TRANSPORTE</option>
                                                    <option value="AGUINALDO">AGUINALDO</option>
                                                    <option value="BONO">BONO</option>
                                                    <option value="RETROACTIVO">RETROACTIVO</option>
                                                    <option value="FINIQUITO">FINIQUITO</option>
                                                    <option value="QUINQUENIO">QUINQUENIO</option>
                                                    <option value="VIATICO">VIATICO</option>
                                                    <option value="ALMUERZO">ALMUERZO</option>
                                                    <option value="CENA">CENA</option>
                                                    <option value="DEVOLUCION DE VIATICO">DEVOLUCION DE VIATICO</option>
                                                    <option value="DEVOLUCION DE FACTURA">DEVOLUCION DE FACTURA</option>
                                                    <option value="PRESTAMO69">PRESTAMO69</option>
                                                `;
                                            } else {
                                            motivoSelect.innerHTML = `
                                                <option value=""></option>
                                                <option value="SUELDO">SUELDO</option>
                                                <option value="TRANSPORTE">TRANSPORTE</option>
                                                <option value="AGUINALDO">AGUINALDO</option>
                                                <option value="BONO">BONO</option>
                                                <option value="RETROACTIVO">RETROACTIVO</option>
                                                <option value="FINIQUITO">FINIQUITO</option>
                                                <option value="QUINQUENIO">QUINQUENIO</option>
                                                <option value="VIATICO">VIATICO</option>
                                                <option value="ALMUERZO">ALMUERZO</option>
                                                <option value="CENA">CENA</option>
                                                <option value="DEVOLUCION DE VIATICO">DEVOLUCION DE VIATICO</option>
                                                <option value="DEVOLUCION DE FACTURA">DEVOLUCION DE FACTURA</option>
                                            `;
                                        }
                                    }
                                    actualizarDetalle2();
                                });
                                motivoSelect.addEventListener('change', actualizarDetalle2);
                                mesInput2.addEventListener('change', actualizarDetalle2);
                                function actualizarDetalle2() {
                                    const motivo = motivoSelect.value;
                                    const mesValor = mesInput2.value;
                                    const motivoBloqueados = [
                                        'SUELDO', 'TRANSPORTE', 'AGUINALDO', 'BONO',
                                        'RETROACTIVO', 'FINIQUITO', 'QUINQUENIO',
                                        'VIATICO', 'PAGO DE SERVICIOS PRESTADOS'
                                    ];
                                
                                    let partes = [];
                                    if (motivo) {
                                        partes.push(motivo);
                                    }
                                    if (mesValor) {
                                        const [anio, mes] = mesValor.split('-');
                                        const meses = ['ENERO', 'FEBRERO', 'MARZO', 'ABRIL', 'MAYO', 'JUNIO', 'JULIO', 'AGOSTO', 'SEPTIEMBRE', 'OCTUBRE', 'NOVIEMBRE', 'DICIEMBRE'];
                                        const mesTexto = meses[parseInt(mes, 10) - 1];
                                        partes.push(`MES DE ${mesTexto} ${anio}`);
                                    }
                                
                                    const textoBase = partes.join(', ');
                                    detalle2Input.value = textoBase;
                                
                                    if (motivoBloqueados.includes(motivo)) {
                                        detalle2Input.setAttribute('readonly', true);
                                    } else {
                                        detalle2Input.removeAttribute('readonly');
                                        detalle2Input.setAttribute('maxlength', 255);
                                    }
                                
                                    detalle2Input.dataset.fixedPart = textoBase;
                                    detalle2Input.addEventListener('input', function () {
                                        const fixed = this.dataset.fixedPart || '';
                                        if (!this.value.startsWith(fixed)) {
                                            this.value = fixed;
                                        } else if (this.value.length > fixed.length + 1000) {
                                            this.value = this.value.substring(0, fixed.length + 1000);
                                        }
                                    });
                                    // 👉 Lógica de asignación de precio según razon social y motivo
                                    const selectedProveedor = proveedorSelect2.options[proveedorSelect2.selectedIndex];
                                    const razonsocial = selectedProveedor.getAttribute('data-razon') || '';
                                    const precioUnitario2 = document.getElementById('preciounitario2');
                                
                                    if (motivo === 'PAGO DE SERVICIOS PRESTADOS') {
                                        if (razonsocial === 'DENISSE MAUREN LOPEZ FLORES') {
                                            precioUnitario2.value = (7000).toFixed(2);
                                        } else {
                                            precioUnitario2.value = (2750).toFixed(2);
                                        }
                                        precioUnitario2.setAttribute('readonly', true);
                                    } else {
                                        precioUnitario2.value = '';
                                        precioUnitario2.removeAttribute('readonly');
                                    }
                                }


                            </script>
                            <div class="form-group col-lg-1 d-flex flex-column align-items-center">
                                {!! Form::label('agregar', 'Agregar:') !!}
                                <button type="button" id="btn-add2" class="btn btn-outline-success">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <form id="pdf-form-22" action="{{ route('generar.preordenpersonal') }}" method="POST">
                    @csrf
                    <div class="card">
                        <div class="card-body bg-light">
                            <div class="container-fluid text-center p-1 bg-light">
                                <h4 class="fw-bold text-dark" style="font-weight: 900">PRE - ÓRDEN DE PERSONAL</h4>
                            </div>
                            <div class="row">
                                <div class="col-lg-9">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table class="table table-bordered" id="data-table2">
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
                                                    <label for="sucursal22" style="margin-bottom: -10px;">Ciudad_Reg.:</label>
                                                    <input type="text" class="form-control" id="sucursal22" name="sucursal22" value="{{ $sucursal }}">
                                                </div>
                                                {{-- <div class="form-group col-lg-12">
                                                    <label for="sucursalgasto22" style="margin-bottom: -10px;">Sucursal.:</label>
                                                    <select class="form-control" id="sucursalgasto22" name="sucursalgasto22" required>
                                                        <option value="SANTA CRUZ">SANTA CRUZ</option>
                                                        <option value="COCHABAMBA">COCHABAMBA</option>
                                                    </select>
                                                </div> --}}
                                                <div class="form-group col-lg-12">
                                                    <label for="sucursalgasto22" style="margin-bottom: -10px;">Sucursal.:</label>
                                                    <select class="form-control" id="sucursalgasto22" name="sucursalgasto22" required readonly>
                                                        <!-- Las opciones se llenarán dinámicamente -->
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="form-group col-lg-6">
                                                    <label for="formapago22" style="margin-bottom: -10px;">Forma_Pago:</label>
                                                    <select class="form-control" id="formapago22" name="formapago22" required>
                                                        <option value="CONTADO">CONTADO</option>
                                                        <option value="CREDITO">CREDITO</option>
                                                    </select>
                                                </div>
                                                <div class="form-group col-lg-6">
                                                    <label for="fechapagar22" style="margin-bottom: -10px;">Fecha_Pago:</label>
                                                    <input type="date" class="form-control" id="fechapagar22" name="fechapagar22" required>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="form-group col-lg-6">
                                                    <label for="proveedorNombre22" style="margin-bottom: -10px;">Proveedor:</label>
                                                    <input type="text" class="form-control" id="proveedorNombre22" name="proveedorNombre22" readonly>
                                                    <input type="hidden" class="form-control" id="proveedorId22" name="proveedorId22" readonly>
                                                </div>
                                                <div class="form-group col-lg-6">
                                                    <label for="tipotransaccion32" style="margin-bottom: -10px;">Tipo_Transac:</label>
                                                    <input type="text" class="form-control" id="tipotransaccion32" name="tipotransaccion32" readonly>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="form-group col-lg-4">
                                                    <label for="subtotalgeneral2" style="margin-bottom: -10px;">Subtotal:</label>
                                                    <input type="number" class="form-control" id="subtotalgeneral2" name="subtotalgeneral2" readonly>
                                                </div>
                                                <div class="form-group col-lg-4">
                                                    <label for="descuentogeneral2" style="margin-bottom: -10px;">Desc.:</label>
                                                    <input type="number" class="form-control" id="descuentogeneral2" name="descuentogeneral2" readonly>
                                                </div>
                                                <div class="form-group col-lg-4">
                                                    <label for="montototalgeneral2" style="margin-bottom: -10px;">Total:</label>
                                                    <input type="number" class="form-control" id="montototalgeneral2" name="montototalgeneral2" readonly>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="form-group col-lg-12">
                                                    <label for="observacion2" style="margin-bottom: -10px;">Observaciones:</label>
                                                    <input type="text" class="form-control" id="observacion2" name="observacion2">
                                                </div>
                                            </div>
                                            <input type="hidden" name="ordenes_venta" id="ordenesVentaInput22">
                                            <button type="submit" class="btn btn-outline-success">GENERAR PRE-ORDEN</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <script>
                                let primerProveedor2 = null;
                            
                                document.getElementById('proveedor-select2').addEventListener('change', function () {
                                    let selectedOption = this.options[this.selectedIndex];
                                    let proveedorId = selectedOption.value;
                                    let proveedorNombre = selectedOption.getAttribute('data-razon');
                                    let tipoTransaccion = selectedOption.getAttribute('data-tipotrans');
                            
                                    //RELLENAR SUCURSAL DE GASTO
                                    let ciudad1 = selectedOption.getAttribute('data-ciudad');
                                    let ciudad2 = selectedOption.getAttribute('data-ciudad2');
                                        let sucursalSelect = document.getElementById('sucursalgasto22');
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
                                    //

                                    document.getElementById('proveedorid2').value = proveedorId;
                                    document.getElementById('proveedorNombre22').value = proveedorNombre;
                                    document.getElementById('proveedorId22').value = proveedorId;
                                    document.getElementById('tipotransaccion22').value = tipoTransaccion;
                                    document.getElementById('tipotransaccion32').value = tipoTransaccion;
                            
                                    if (primerProveedor2 === null) {
                                        primerProveedor2 = proveedorId;
                                    }
                                });
                            
                                document.getElementById('btn-add2').addEventListener('click', function () {
                                    let proveedorId = document.getElementById('proveedorid2').value;
                                    let detalle = document.getElementById('detalle2').value;
                                    let cantidad = parseFloat(document.getElementById('cantidad2').value) || 0;
                                    let precioUnitario = parseFloat(document.getElementById('preciounitario2').value) || 0;
                                    let descuentoUnitario = parseFloat(document.getElementById('descuentounitario2').value) || 0;
                                    let totalUnitario = parseFloat(document.getElementById('totalunitario2').value) || 0;
                                    if (!proveedorId) {
                                        alert("Por favor, seleccione un proveedor.");
                                        return;
                                    }
                                    if (primerProveedor2 !== proveedorId) {
                                        alert("Solo puede agregar productos del mismo proveedor.");
                                        return;
                                    }
                                    let tableBody = document.getElementById('data-table2').getElementsByTagName('tbody')[0];
                                    let newRow = document.createElement('tr');
                                    newRow.innerHTML = `
                                        <td>${detalle}</td>
                                        <td hidden>${cantidad}</td>
                                        <td class="precio-unitario2">${precioUnitario.toFixed(2)}</td>
                                        <td class="descuento-unitario2">${descuentoUnitario.toFixed(2)}</td>
                                        <td class="total-unitario2">${totalUnitario.toFixed(2)}</td>
                                        <td>
                                            <button type="button" class="btn btn-quitar btn-sm btn-remove remove-item">X</button>
                                        </td>
                                    `;
                                    tableBody.appendChild(newRow);
                                    actualizarTotales2();
                                    newRow.querySelector('.btn-remove').addEventListener('click', function () {
                                        newRow.remove();
                                        actualizarTotales2();
                                    });
                            
                                    limpiarCampos2();
                                });
                                function actualizarTotales2() {
                                    let precios = document.querySelectorAll(".precio-unitario2");
                                    let descuentos = document.querySelectorAll(".descuento-unitario2");
                                    let totales = document.querySelectorAll(".total-unitario2");
                                    let subtotal = 0, descuento = 0, montototal = 0;
                            
                                    precios.forEach(p => subtotal += parseFloat(p.textContent) || 0);
                                    descuentos.forEach(d => descuento += parseFloat(d.textContent) || 0);
                                    totales.forEach(t => montototal += parseFloat(t.textContent) || 0);
                                    document.getElementById("subtotalgeneral2").value = (montototal + descuento).toFixed(2);
                                    document.getElementById("descuentogeneral2").value = descuento.toFixed(2);
                                    document.getElementById("montototalgeneral2").value = montototal.toFixed(2);
                                }
                                function limpiarCampos2() {
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
                    document.getElementById('pdf-form-22').addEventListener('submit', function (event) {
                        let ordenesVenta = [];
                        let tableRows = document.querySelectorAll("#data-table2 tbody tr");
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
                        document.getElementById('ordenesVentaInput22').value = JSON.stringify(ordenesVenta);
                    });

                </script>
            </div>

            {{-- PREORDENES DE VENTA --}}
            {{-- <div class="tab-pane fade" id="tab-content-3">
                <div class="card">
                    <div class="card-body bg-light">
                        <div class="container-fluid text-center p-1 bg-light">
                            <h4 class="fw-bold text-dark" style="font-weight: 900">NUEVA PRE - ÓRDEN DE VENTA</h4>
                        </div>
                        <div class="row">
                            <div class="form-group col-lg-2">
                                {!! Form::label('proveedornombre22', 'Proveedor:') !!}
                                <select id="proveedor-select22" class="form-control">
                                    <option value=""></option>
                                    @foreach ($proveedorespersonal as $proveedor22)
                                        <option value="{{ $proveedor22->id }}" data-razon="{{ $proveedor22->razonsocial }}" data-tipotrans="{{ $proveedor22->tipotransaccion }}" data-categoria="{{ $proveedor22->categoria }}">
                                            {{ $proveedor22->razonsocial }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <script>
                                document.addEventListener("DOMContentLoaded", function () {
                                    let proveedorSelect = document.getElementById("proveedor-select22");
                                    proveedorSelect.addEventListener("change", function () {
                                        if (this.value) {
                                            this.setAttribute("disabled", "disabled");
                                        }
                                    });
                                });
                            </script>
                            <div class="form-group col-lg-2" hidden>
                                {!! Form::label('proveedorid22', 'ID Proveedor:') !!}
                                {!! Form::text('proveedorid22', null, ['class' => 'form-control', 'readonly', 'id' => 'proveedorid22']) !!}
                            </div>
                            <div class="form-group col-lg-2">
                                {!! Form::label('tipotransaccion22', 'Tipo Transacción:') !!}
                                {!! Form::text('tipotransaccion222', null, ['class' => 'form-control', 'readonly', 'id' => 'tipotransaccion222']) !!}
                            </div>
                            <div class="form-group col-lg-8">
                                {!! Form::label('detalle22', 'Detalle:') !!}
                                {!! Form::text('detalle22', null, ['class' => 'form-control', 'id' => 'detalle22']) !!}
                            </div>
                            <div class="form-group col-lg-2">
                                {!! Form::label('cantidad22', 'Cantidad:') !!}
                                {!! Form::number('cantidad22', null, ['class' => 'form-control', 'id' => 'cantidad22']) !!}
                            </div>
                            <div class="form-group col-lg-3">
                                {!! Form::label('preciounitario22', 'Precio Unitario:') !!}
                                {!! Form::number('preciounitario22', null, ['class' => 'form-control', 'id' => 'preciounitario22']) !!}
                            </div>
                            <div class="form-group col-lg-3">
                                {!! Form::label('descuentounitario22', 'Descuento Unitario:') !!}
                                {!! Form::number('descuentounitario22', null, ['class' => 'form-control', 'id' => 'descuentounitario22']) !!}
                            </div>
                            <div class="form-group col-lg-3">
                                {!! Form::label('totalunitario22', 'Total Unitario:') !!}
                                {!! Form::number('totalunitario22', null, ['class' => 'form-control', 'id' => 'totalunitario22']) !!}
                            </div>
                            <script>
                                function calcularTotalUnitario() {
                                    let cantidad = parseFloat(document.getElementById('cantidad22').value) || 0;
                                    let precioUnitario = parseFloat(document.getElementById('preciounitario22').value) || 0;
                                    let descuentoUnitario = parseFloat(document.getElementById('descuentounitario22').value) || 0;
                                    let totalUnitario = (cantidad * precioUnitario) - descuentoUnitario;
                                    totalUnitario = totalUnitario < 0 ? 0 : totalUnitario;
                                    document.getElementById('totalunitario22').value = totalUnitario.toFixed(2);
                                }
                                document.getElementById('cantidad22').addEventListener('input', calcularTotalUnitario);
                                document.getElementById('preciounitario22').addEventListener('input', calcularTotalUnitario);
                                document.getElementById('descuentounitario22').addEventListener('input', calcularTotalUnitario);
                            </script>
                            <script>
                                const proveedorSelect2 = document.getElementById('proveedor-select22');
                                const motivoSelect = document.getElementById('motivo');
                                const mesInput2 = document.getElementById('mes22');
                                const detalle2Input = document.getElementById('detalle22');
                                proveedorSelect2.addEventListener('change', function () {
                                    const selectedOption = this.options[this.selectedIndex];
                                    const categoria = selectedOption.getAttribute('data-categoria');
                                    motivoSelect.innerHTML = '';
                                    if (categoria === 'PROVEEDOR EXTERNO') {
                                        motivoSelect.innerHTML = `
                                            <option value=""></option>
                                            <option value="PAGO DE SERVICIOS PRESTADOS">PAGO DE SERVICIOS PRESTADOS</option>
                                        `;
                                    } else if (categoria === 'PROVEEDOR INTERNO') {
                                        motivoSelect.innerHTML = `
                                            <option value=""></option>
                                            <option value="SUELDO">SUELDO</option>
                                            <option value="TRANSPORTE">TRANSPORTE</option>
                                            <option value="AGUINALDO">AGUINALDO</option>
                                        `;
                                    }
                                    actualizarDetalle2();
                                });
                                motivoSelect.addEventListener('change', actualizarDetalle2);
                                mesInput2.addEventListener('change', actualizarDetalle2);
                                function actualizarDetalle2() {
                                    const motivo = motivoSelect.value;
                                    const mesValor = mesInput2.value;
                                    let partes = [];
                                    if (motivo) {
                                        partes.push(motivo);
                                    }
                                    if (mesValor) {
                                        const [anio, mes] = mesValor.split('-');
                                        const meses = ['ENERO', 'FEBRERO', 'MARZO', 'ABRIL', 'MAYO', 'JUNIO', 'JULIO', 'AGOSTO', 'SEPTIEMBRE', 'OCTUBRE', 'NOVIEMBRE', 'DICIEMBRE'];
                                        const mesTexto = meses[parseInt(mes, 10) - 1];
                                        partes.push(`MES DE ${mesTexto} ${anio}`);
                                    }
                                    detalle2Input.value = partes.join(', ');
                                }
                            </script>
                            <div class="form-group col-lg-1 d-flex flex-column align-items-center">
                                {!! Form::label('agregar', 'Agregar:') !!}
                                <button type="button" id="btn-add2" class="btn btn-outline-success">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <form id="pdf-form-22" action="{{ route('generar.preordenpersonal') }}" method="POST">
                    @csrf
                    <div class="card">
                        <div class="card-body bg-light">
                            <div class="container-fluid text-center p-1 bg-light">
                                <h4 class="fw-bold text-dark" style="font-weight: 900">PRE - ÓRDEN DE VENTA</h4>
                            </div>
                            <div class="row">
                                <div class="col-lg-9">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table class="table table-bordered" id="data-table2">
                                                    <thead>
                                                        <tr>
                                                            <th>Detalle</th>
                                                            <th>Cantidad</th>
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
                                                <div class="form-group col-lg-12">
                                                    <label for="sucursal222" style="margin-bottom: -10px;">Sucursal:</label>
                                                    <select class="form-control" id="sucursal222" name="sucursal222" required>
                                                        <option value="SANTA CRUZ">SANTA CRUZ</option>
                                                        <option value="COCHABAMBA">COCHABAMBA</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="form-group col-lg-6">
                                                    <label for="formapago222" style="margin-bottom: -10px;">Forma_Pago:</label>
                                                    <select class="form-control" id="formapago222" name="formapago222" required>
                                                        <option value="CONTADO">CONTADO</option>
                                                        <option value="CREDITO">CREDITO</option>
                                                    </select>
                                                </div>
                                                <div class="form-group col-lg-6">
                                                    <label for="fechapagar222" style="margin-bottom: -10px;">Fecha_Pago:</label>
                                                    <input type="date" class="form-control" id="fechapagar222" name="fechapagar222" required>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="form-group col-lg-6">
                                                    <label for="proveedorNombre222" style="margin-bottom: -10px;">Proveedor:</label>
                                                    <input type="text" class="form-control" id="proveedorNombre222" name="proveedorNombre222" readonly>
                                                    <input type="hidden" class="form-control" id="proveedorId222" name="proveedorId222" readonly>
                                                </div>
                                                <div class="form-group col-lg-6">
                                                    <label for="tipotransaccion322" style="margin-bottom: -10px;">Tipo_Transac:</label>
                                                    <input type="text" class="form-control" id="tipotransaccion322" name="tipotransaccion322" readonly>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="form-group col-lg-4">
                                                    <label for="subtotalgeneral22" style="margin-bottom: -10px;">Subtotal:</label>
                                                    <input type="number" class="form-control" id="subtotalgeneral22" name="subtotalgeneral22" readonly>
                                                </div>
                                                <div class="form-group col-lg-4">
                                                    <label for="descuentogeneral22" style="margin-bottom: -10px;">Desc.:</label>
                                                    <input type="number" class="form-control" id="descuentogeneral22" name="descuentogeneral22" readonly>
                                                </div>
                                                <div class="form-group col-lg-4">
                                                    <label for="montototalgeneral22" style="margin-bottom: -10px;">Total:</label>
                                                    <input type="number" class="form-control" id="montototalgeneral22" name="montototalgeneral22" readonly>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="form-group col-lg-12">
                                                    <label for="observacion22" style="margin-bottom: -10px;">Observaciones:</label>
                                                    <input type="text" class="form-control" id="observacion22" name="observacion22">
                                                </div>
                                            </div>
                                            <input type="hidden" name="ordenes_venta2" id="ordenesVentaInput222">
                                            <button type="submit" class="btn btn-outline-success">GENERAR PRE-ORDEN</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <script>
                                let primerProveedor2 = null;
                            
                                document.getElementById('proveedor-select22').addEventListener('change', function () {
                                    let selectedOption = this.options[this.selectedIndex];
                                    let proveedorId = selectedOption.value;
                                    let proveedorNombre = selectedOption.getAttribute('data-razon');
                                    let tipoTransaccion = selectedOption.getAttribute('data-tipotrans');
                            
                                    document.getElementById('proveedorid22').value = proveedorId;
                                    document.getElementById('proveedorNombre222').value = proveedorNombre;
                                    document.getElementById('proveedorId222').value = proveedorId;
                                    document.getElementById('tipotransaccion222').value = tipoTransaccion;
                                    document.getElementById('tipotransaccion322').value = tipoTransaccion;
                            
                                    if (primerProveedor22 === null) {
                                        primerProveedor22 = proveedorId;
                                    }
                                });
                            
                                document.getElementById('btn-add22').addEventListener('click', function () {
                                    let proveedorId = document.getElementById('proveedorid22').value;
                                    let detalle = document.getElementById('detalle22').value;
                                    let cantidad = parseFloat(document.getElementById('cantidad22').value) || 0;
                                    let precioUnitario = parseFloat(document.getElementById('preciounitario22').value) || 0;
                                    let descuentoUnitario = parseFloat(document.getElementById('descuentounitario22').value) || 0;
                                    let totalUnitario = parseFloat(document.getElementById('totalunitario22').value) || 0;
                                    if (!proveedorId) {
                                        alert("Por favor, seleccione un proveedor.");
                                        return;
                                    }
                                    if (primerProveedor22 !== proveedorId) {
                                        alert("Solo puede agregar productos del mismo proveedor.");
                                        return;
                                    }
                                    let tableBody = document.getElementById('data-table22').getElementsByTagName('tbody')[0];
                                    let newRow = document.createElement('tr');
                                    newRow.innerHTML = `
                                        <td>${detalle}</td>
                                        <td>${cantidad}</td>
                                        <td class="precio-unitario22">${precioUnitario.toFixed(2)}</td>
                                        <td class="descuento-unitario22">${descuentoUnitario.toFixed(2)}</td>
                                        <td class="total-unitario22">${totalUnitario.toFixed(2)}</td>
                                        <td>
                                            <button type="button" class="btn btn-quitar btn-sm btn-remove remove-item">X</button>
                                        </td>
                                    `;
                                    tableBody.appendChild(newRow);
                                    actualizarTotales2();
                                    newRow.querySelector('.btn-remove').addEventListener('click', function () {
                                        newRow.remove();
                                        actualizarTotales2();
                                    });
                            
                                    limpiarCampos2();
                                });
                                function actualizarTotales2() {
                                    let precios = document.querySelectorAll(".precio-unitario22");
                                    let descuentos = document.querySelectorAll(".descuento-unitario22");
                                    let totales = document.querySelectorAll(".total-unitario22");
                                    let subtotal = 0, descuento = 0, montototal = 0;
                            
                                    precios.forEach(p => subtotal += parseFloat(p.textContent) || 0);
                                    descuentos.forEach(d => descuento += parseFloat(d.textContent) || 0);
                                    totales.forEach(t => montototal += parseFloat(t.textContent) || 0);
                                    document.getElementById("subtotalgeneral22").value = (montototal + descuento).toFixed(2);
                                    document.getElementById("descuentogeneral22").value = descuento.toFixed(2);
                                    document.getElementById("montototalgeneral22").value = montototal.toFixed(2);
                                }
                                function limpiarCampos2() {
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
                    document.getElementById('pdf-form-222').addEventListener('submit', function (event) {
                        let ordenesVenta = [];
                        let tableRows = document.querySelectorAll("#data-table22 tbody tr");
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
                        document.getElementById('ordenesVentaInput222').value = JSON.stringify(ordenesVenta);
                    });

                </script>
            </div> --}}
        </div> 
    </div>
</div>

<script>
    // Función para limpiar los campos después de agregar
        /* function limpiarCampos() {
            document.getElementById('proveedor-select').selectedIndex = 0;
            document.getElementById('proveedorid').value = '';
            document.getElementById('tipotransaccion2').value = '';
            document.getElementById('detalle').value = '';
            document.getElementById('cantidad').value = '';
            document.getElementById('preciounitario').value = '';
            document.getElementById('descuentounitario').value = '';
            document.getElementById('totalunitario').value = '';
        } */
</script>
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