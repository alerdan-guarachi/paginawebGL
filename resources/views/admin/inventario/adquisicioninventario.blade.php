@extends('adminlte::page')

@section('content_header')
{{-- <a class="btn float-right btn-outline-secondary" data-toggle="modal" data-target="#crearProductoModal">
    CREAR PRODUCTO
</a> --}}

<!-- Modal -->
{{-- <div class="modal fade" id="crearProductoModal" tabindex="-1" aria-labelledby="crearProductoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="crearProductoModalLabel" style="font-weight: 900;">ELIGE UN TIPO DE INVENTARIO</h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body text-center" style="margin-top: 30px;">
                <div class="row">
                    <div class="col-12 col-md-6 mb-3 d-flex justify-content-center">
                        <button type="button" class="btn btn-custom" style="width: 80%;" onclick="redirectTo('almacen')">
                            <div class="d-flex flex-column align-items-center justify-content-center">
                                <i class="fas fa-warehouse fa-5x mb-2"></i> 
                                <span class="h5 mb-0">ALMACÉN</span>
                            </div>
                        </button>
                    </div>
                    <div class="col-12 col-md-6 mb-3 d-flex justify-content-center">
                        <button type="button" class="btn btn-custom" style="width: 80%;" onclick="redirectTo('activosfijos')">
                            <div class="d-flex flex-column align-items-center justify-content-center">
                                <i class="fas fa-desktop fa-5x mb-2"></i>
                                <span class="h5 mb-0">ACTIVOS FIJOS</span>
                            </div>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> --}}
{{-- <script>
    function redirectTo(type) {
        if (type === 'almacen') {
            window.location.href = "{{ route('admin.inventario.create') }}";
        } else if (type === 'activosfijos') {
            window.location.href = "{{ route('admin.inventario.crearactivofijo') }}";
        }
    }
</script> --}}
<h1>ADQUISICIÓN DE INVENTARIO</h1>
@stop

@section('css')
<link rel="stylesheet" href="{{ asset('css/opcionesmultiples.css') }}">
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
<div class="card">
    <nav class="navbar float-right">
        <form class="form-inline" id="search-form">
            <input name="buscarpor" id="buscarpor" class="form-control mr-sm-2" type="search" placeholder="Nombre del producto" aria-label="Search">
            <button class="btn btn-outline-secondary my-2 my-sm-0" type="submit">Buscar</button>
        </form>
    </nav>

    <!-- Tabla de resultados -->
    <div class="card-body">
        <div class="row">
            <div class="col-lg-12">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Tipo</th>
                                <th>Código</th>
                                <th>Sección</th>
                                <th>Nombre</th>
                                <th>Color</th>
                                <th>Stock</th>
                                <th>Precio</th>
                                <th>Proveedor</th>
                                <th>Selec</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($bienesalmacen as $bienes)
                                <tr>
                                    <td>{{ $bienes->id }}</td>
                                    <td>{{ $bienes->tipoinventario }}</td>
                                    <td>{{ $bienes->codigo }}</td>
                                    <td>{{ $bienes->seccion }}</td>
                                    <td>{{ $bienes->nombreproducto }}</td>
                                    <td>{{ $bienes->color }}</td>
                                    <td>{{ $bienes->stockactual }}</td>
                                    <td>{{ $bienes->precio }}</td>
                                    <td>{{ $bienes->proveedornombre }}</td>
                                    <td hidden>{{ $bienes->proveedorid }}</td>
                                    <td><input type="checkbox" class="select-item" data-id="{{ $bienes->id }}" data-seccion="{{ $bienes->seccion }}" data-nombre="{{ $bienes->nombreproducto }}" data-color="{{ $bienes->color }}" data-precio="{{ $bienes->precio }}" data-proveedorid="{{ $bienes->proveedorid }}"></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <form id="pdf-form" action="{{ route('generar.ordencompra') }}" method="POST">
            @csrf
            <div class="card">
                <div class="row">
                    <div class="col-lg-9">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="selected-items-table">
                                <thead>
                                    <tr>
                                        <th>Sección</th>
                                        <th>Nombre</th>
                                        <th>Color</th>
                                        <th>Precio</th>
                                        <th>Cantidad</th>
                                        <th>Subtotal</th>
                                        <th>Descuento</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                    
                    <div class="col-lg-3">
                        <div class="form-group col-lg-12">
                            <label for="sucursal">Sucursal:</label>
                            <select class="form-control" id="sucursal" name="sucursal">
                                <option value="SANTA CRUZ">SANTA CRUZ</option>
                                <option value="COCHABAMBA">COCHABAMBA</option>
                            </select>
                        </div>
                        <div class="form-group col-lg-12">
                            <label for="tipotransaccion">Tipo transacción:</label>
                            <select class="form-control" id="tipotransaccion" name="tipotransaccion">
                                <option value="EFECTIVO">EFECTIVO</option>
                                <option value="TRANSFERENCIA BANCARIA">TRANSFERENCIA BANCARIA</option>
                                <option value="DEPOSITO BANCARIO">DEPOSITO BANCARIO</option>
                            </select>
                        </div>
                        <div class="form-group col-lg-12">
                            <label for="formapago">Forma de Pago:</label>
                            <select class="form-control" id="formapago" name="formapago">
                                <option value="CONTADO">CONTADO</option>
                                <option value="CREDITO">CREDITO</option>
                            </select>
                        </div>
                        <div class="form-group col-lg-12">
                            <label for="fechacomprar">Fecha de Pago:</label>
                            <input type="date" class="form-control" id="fechacomprar" name="fechacomprar">
                        </div>

                        <div class="form-group col-lg-12">
                            <div class="form-group">
                                <label for="proveedorNombre">Proveedor:</label>
                                <input type="text" class="form-control" id="proveedorNombre" name="proveedorNombre" readonly>
                            </div>
                            <div class="form-group" hidden>
                                <label for="proveedorId">ID Proveedor:</label>
                                <input type="text" class="form-control" id="proveedorId" name="proveedorId" readonly>
                            </div>
                        </div>
                        <div class="form-group col-lg-12">
                            <div class="form-group">
                                <label for="subtotal">Subtotal:</label>
                                <input type="number" class="form-control" id="subtotal" name="subtotal">
                            </div>
                        </div>
                        <div class="form-group col-lg-12">
                            <div class="form-group">
                                <label for="descuento">Descuento:</label>
                                <input type="number" class="form-control" id="descuento" name="descuento">
                            </div>
                        </div>
                        <div class="form-group col-lg-12">
                            <div class="form-group">
                                <label for="montototal">Monto Total:</label>
                                <input type="number" class="form-control" id="montototal" name="montototal" readonly>
                            </div>
                        </div>
                        <div class="form-group col-lg-12">
                            <div class="form-group">
                                <label for="observacion">OBSERVACIONES:</label>
                                <input type="text" class="form-control" id="observacion" name="observacion">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <input type="hidden" name="ordenes_compra" id="ordenesCompraInput">
            <button type="submit" class="btn btn-primary">Descargar PDF</button>
        </form>
        
        
        <script>
            document.querySelector("#pdf-form").addEventListener("submit", function (e) {
                let ordenesCompra = [];
                document.querySelectorAll("#selected-items-table tbody tr").forEach(row => {
                    let orden = {
                        detalle: row.children[1].textContent.trim(),
                        cantidad: row.querySelector(".cantidad").value.trim(),
                        precio_unitario: row.children[3].textContent.trim(),
                        descuento: row.querySelector(".descuento").value.trim(),
                        subtotal: row.querySelector(".subtotal").value.trim(),
                        fecha_pago: new Date().toISOString().split("T")[0]
                    };
                    ordenesCompra.push(orden);
                });
                document.querySelector("#ordenesCompraInput").value = JSON.stringify(ordenesCompra);
            });
        </script>

        {{-- <script>
            let proveedorPermitido = null;
            let productosSeleccionados = new Set();
        
            document.addEventListener("DOMContentLoaded", function () {
                // Restaurar productos seleccionados si existen en localStorage
                let storedProducts = JSON.parse(localStorage.getItem("selectedProducts")) || [];
                if (storedProducts.length > 0) {
                    proveedorPermitido = storedProducts[0].proveedorid;
                    document.querySelector("#proveedorNombre").value = storedProducts[0].proveedornombre;
                    storedProducts.forEach(item => agregarProductoATabla(item));
                }
        
                document.querySelectorAll(".select-item").forEach(item => {
                    item.addEventListener("change", function () {
                        let productoId = this.dataset.id;
                        let proveedorId = this.dataset.proveedorid;
                        let proveedorNombre = this.closest("tr").children[8].textContent; // Nombre del proveedor en la tabla
        
                        if (this.checked) {
                            if (productosSeleccionados.has(productoId)) {
                                alert("Este producto ya ha sido seleccionado.");
                                this.checked = false;
                                return;
                            }
        
                            if (proveedorPermitido === null) {
                                proveedorPermitido = proveedorId;
                                document.querySelector("#proveedorNombre").value = proveedorNombre;
                            } else if (proveedorPermitido !== proveedorId) {
                                alert("Solo puedes agregar productos del mismo proveedor.");
                                this.checked = false;
                                return;
                            }
        
                            let producto = {
                                id: productoId,
                                seccion: this.dataset.seccion,
                                nombre: this.dataset.nombre,
                                color: this.dataset.color,
                                precio: this.dataset.precio,
                                proveedorid: proveedorId,
                                proveedornombre: proveedorNombre
                            };
        
                            agregarProductoATabla(producto);
                            guardarProductosEnLocalStorage();
                        } else {
                            eliminarProductoDeTabla(productoId);
                        }
                    });
                });
        
                document.addEventListener("click", function (e) {
                    if (e.target.classList.contains("remove-item")) {
                        let productoId = e.target.dataset.id;
                        eliminarProductoDeTabla(productoId);
                    }
                });
        
                document.addEventListener("input", function (e) {
                    let row = e.target.closest("tr");
                    if (!row) return;
        
                    let precio = parseFloat(row.children[3].textContent) || 0;
                    let cantidad = parseFloat(row.querySelector(".cantidad").value) || 1;
                    let descuento = parseFloat(row.querySelector(".descuento").value) || 0;
        
                    let subtotal = precio * cantidad;
                    row.querySelector(".subtotal").value = subtotal.toFixed(2);
        
                    let total = subtotal - descuento;
                    row.querySelector(".total").value = total.toFixed(2);
        
                    updateTotals();
                });
            });
        
            function agregarProductoATabla(producto) {
                let table = document.querySelector("#selected-items-table tbody");
        
                let row = document.createElement("tr");
                row.innerHTML = `
                    <td>${producto.seccion}</td>
                    <td>${producto.nombre}</td>
                    <td>${producto.color}</td>
                    <td>${producto.precio}</td>
                    <td><input type="number" class="form-control cantidad" value="1" min="1"></td>
                    <td><input type="number" class="form-control subtotal" value="${producto.precio}" readonly></td>
                    <td><input type="number" class="form-control descuento" value="0"></td>
                    <td><input type="number" class="form-control total" value="${producto.precio}" readonly></td>
                    <td><button class="btn btn-danger btn-sm remove-item" data-id="${producto.id}">X</button></td>
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
                }
        
                guardarProductosEnLocalStorage();
                updateTotals();
            }
        
            function guardarProductosEnLocalStorage() {
                let productos = [];
                document.querySelectorAll("#selected-items-table tbody tr").forEach(row => {
                    let producto = {
                        id: row.querySelector(".remove-item").dataset.id,
                        seccion: row.children[0].textContent,
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
                let totalGeneral = 0;
                document.querySelectorAll("#selected-items-table tbody .total").forEach(input => {
                    totalGeneral += parseFloat(input.value) || 0;
                });
                document.querySelector("#montototal").value = totalGeneral.toFixed(2);
            }
        </script> --}}

        <script>
            let proveedorPermitido = null;
            let productosSeleccionados = new Set();
        
            document.addEventListener("DOMContentLoaded", function () {
                // Restaurar productos seleccionados si existen en localStorage
                let storedProducts = JSON.parse(localStorage.getItem("selectedProducts")) || [];
                if (storedProducts.length > 0) {
                    proveedorPermitido = storedProducts[0].proveedorid;
                    document.querySelector("#proveedorNombre").value = storedProducts[0].proveedornombre;
                    document.querySelector("#proveedorId").value = storedProducts[0].proveedorid;  // Asigna el ID
                    storedProducts.forEach(item => agregarProductoATabla(item));
                }
        
                document.querySelectorAll(".select-item").forEach(item => {
                    item.addEventListener("change", function () {
                        let productoId = this.dataset.id;
                        let proveedorId = this.dataset.proveedorid;
                        let proveedorNombre = this.closest("tr").children[8].textContent; // Nombre del proveedor en la tabla
        
                        if (this.checked) {
                            if (productosSeleccionados.has(productoId)) {
                                alert("Este producto ya ha sido seleccionado.");
                                this.checked = false;
                                return;
                            }
        
                            if (proveedorPermitido === null) {
                                proveedorPermitido = proveedorId;
                                document.querySelector("#proveedorNombre").value = proveedorNombre;
                                document.querySelector("#proveedorId").value = proveedorId;  // Asigna el ID
                            } else if (proveedorPermitido !== proveedorId) {
                                alert("Solo puedes agregar productos del mismo proveedor.");
                                this.checked = false;
                                return;
                            }
        
                            let producto = {
                                id: productoId,
                                seccion: this.dataset.seccion,
                                nombre: this.dataset.nombre,
                                color: this.dataset.color,
                                precio: this.dataset.precio,
                                proveedorid: proveedorId,
                                proveedornombre: proveedorNombre
                            };
        
                            agregarProductoATabla(producto);
                            guardarProductosEnLocalStorage();
                        } else {
                            eliminarProductoDeTabla(productoId);
                        }
                    });
                });
        
                document.addEventListener("click", function (e) {
                    if (e.target.classList.contains("remove-item")) {
                        let productoId = e.target.dataset.id;
                        eliminarProductoDeTabla(productoId);
                    }
                });
        
                document.addEventListener("input", function (e) {
                    let row = e.target.closest("tr");
                    if (!row) return;
        
                    let precio = parseFloat(row.children[3].textContent) || 0;
                    let cantidad = parseFloat(row.querySelector(".cantidad").value) || 1;
                    let descuento = parseFloat(row.querySelector(".descuento").value) || 0;
        
                    let subtotal = precio * cantidad;
                    row.querySelector(".subtotal").value = subtotal.toFixed(2);
        
                    let total = subtotal - descuento;
                    row.querySelector(".total").value = total.toFixed(2);
        
                    updateTotals();
                });
            });
        
            function agregarProductoATabla(producto) {
                let table = document.querySelector("#selected-items-table tbody");
        
                let row = document.createElement("tr");
                row.innerHTML = `
                    <td>${producto.seccion}</td>
                    <td>${producto.nombre}</td>
                    <td>${producto.color}</td>
                    <td>${producto.precio}</td>
                    <td><input type="number" class="form-control cantidad" value="1" min="1"></td>
                    <td><input type="number" class="form-control subtotal" value="${producto.precio}" readonly></td>
                    <td><input type="number" class="form-control descuento" value="0"></td>
                    <td><input type="number" class="form-control total" value="${producto.precio}" readonly></td>
                    <td><button class="btn btn-danger btn-sm remove-item" data-id="${producto.id}">X</button></td>
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
                    document.querySelector("#proveedorId").value = "";  // Limpia el ID
                }
        
                guardarProductosEnLocalStorage();
                updateTotals();
            }
        
            function guardarProductosEnLocalStorage() {
                let productos = [];
                document.querySelectorAll("#selected-items-table tbody tr").forEach(row => {
                    let producto = {
                        id: row.querySelector(".remove-item").dataset.id,
                        seccion: row.children[0].textContent,
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

    // Recorre cada fila de la tabla de productos seleccionados
    document.querySelectorAll("#selected-items-table tbody tr").forEach(row => {
        // Extrae los valores de cada input; si no hay valor, se toma 0
        let subtotal = parseFloat(row.querySelector(".subtotal").value) || 0;
        let descuento = parseFloat(row.querySelector(".descuento").value) || 0;
        let total = parseFloat(row.querySelector(".total").value) || 0;
        
        subtotalGeneral += subtotal;
        descuentoGeneral += descuento;
        totalGeneral += total;
    });
    
    // Actualiza los campos de los totales generales
    document.querySelector("#subtotal").value = subtotalGeneral.toFixed(2);
    document.querySelector("#descuento").value = descuentoGeneral.toFixed(2);
    document.querySelector("#montototal").value = totalGeneral.toFixed(2);
}

        </script>
        
    </div>

</div>


    {{-- <form action="{{ route('generar.ordencompra') }}" method="POST">
        @csrf
        <div class="modal fade" id="entradaModal" tabindex="-1" role="dialog" aria-labelledby="entradaModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="entradaModalLabel" style="font-weight: 900; font-size:25px;">GENERAR ORDEN DE COMPRA</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-light" style="border: 2px solid #6c757d;">
                            <strong class="text-dark">Código:</strong> 
                            <span id="codigoProducto"class="text-dark"></span>
                            <br>
                            <strong class="text-dark">Sección:</strong> 
                            <span id="seccion"class="text-dark"></span> 
                            <br>
                            <strong class="text-dark">Producto:</strong> 
                            <span id="productoNombre"class="text-dark"></span> 
                            <br>
                            <strong class="text-dark">Precio:</strong> 
                            <span id="precio"class="text-dark"></span> 
                            <br>
                            <strong class="text-dark">Proveedor:</strong> 
                            <span id="proveedorNombre"class="text-dark"></span> 

                            <span id="proveedorId" class="text-dark" hidden></span> 
                        </div>
                        <input type="hidden" id="codigoEntrada" name="codigoproducto">
                        <div class="row">
                            <div class="form-group col-lg-6">
                                <label for="nrofactura">Cantidad:</label>
                                <input type="number" class="form-control" id="nrofactura" name="nrofactura">
                            </div>
                            <div class="form-group col-lg-6">
                                <label for="subtotal">Subtotal:</label>
                                <input type="number" class="form-control" id="subtotal" name="subtotal" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-lg-6">
                                <label for="descuento">Descuento:</label>
                                <input type="number" class="form-control" id="descuento" name="descuento">
                            </div>
                            <div class="form-group col-lg-6">
                                <label for="total">Total:</label>
                                <input type="number" class="form-control" id="total" name="total">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-lg-6">
                                <label for="fechacompra">Fecha a Comprar:</label>
                                <input type="date" class="form-control" id="fechacompra" name="fechacompra" required>
                            </div>

                            <div class="form-group col-lg-6">
                                <label for="formapago">Forma de Pago:</label>
                                <select class="form-control" id="formapago" name="formapago">
                                    <option value="CONTADO">CONTADO</option>
                                    <option value="CREDITO">CREDITO</option>
                                </select>
                            </div>
                            
                            <div class="form-group col-lg-6">
                                <label for="fechacompra">Fecha a Comprar:</label>
                                <input type="date" class="form-control" id="fechacompra" name="fechacompra" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-outline-success">SOLICITAR</button>
                        <button type="button" class="btn btn-outline-danger" data-dismiss="modal">CERRAR</button>
                    </div>
                </div>
            </div>
        </div>
    </form> --}}
@stop

@section('js')
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

{{-- <script>
    // Al abrir el modal de entrada
    $('#entradaModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget); 
        var id = button.data('id');
        var codigo = button.data('codigo'); 

        var modal = $(this);
        modal.find('#idEntrada').val(id);
        modal.find('#codigoEntrada').val(codigo);
    });

    $('#entradaModal').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget);
        var nombreproducto = button.data('nombreproducto');
        var proveedorid = button.data('proveedorid');
        var proveedornombre = button.data('proveedornombre');
        var precio = button.data('precio');
        var seccion = button.data('seccion');
        var codigo = button.data('codigo');

        $('#productoNombre').text(nombreproducto);
        $('#proveedorId').text(proveedorid);
        $('#proveedorNombre').text(proveedornombre);
        $('#precio').text(precio);
        $('#seccion').text(seccion);
        $('#codigoProducto').text(codigo);
        $('#codigoEntrada').val(codigo);
        $('#idEntrada').val(button.data('id'));
    });

    $(document).ready(function () {
        $('#entradaModal').on('show.bs.modal', function (event) {
            let button = $(event.relatedTarget);
            let codigo = button.data('codigo');
            let nombre = button.data('nombre');
            let provnombre = button.data('provnombre');
            let provid = button.data('provid');
            let prec = button.data('prec');
            let secc = button.data('secc');

            $('#codigoProducto').text(codigo);
            $('#productoNombre').text(nombre);
            $('#proveedorId').text(provid);
            $('#proveedorNombre').text(provnombre);
            $('#precios').text(prec);
            $('#seccion').text(secc);
            $('#codigoEntrada').val(codigo);
        });
    });
</script> --}}


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