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
<h1>COMPRAS</h1>
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
        }, 5000);
    </script>
@endif
<div class="card">
    <div class="card-body">
        <div class="card">
            <div class="card-body bg-light">
                <div class="container-fluid text-center p-1 bg-light">
                    <h4 class="fw-bold text-dark" style="font-weight: 900">INVENTARIO</h4>
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
                                        <th>Stock</th>
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
                        <h4 class="fw-bold text-dark" style="font-weight: 900">PRE - ÓRDEN</h4>
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
                                        <div class="form-group col-lg-6">
                                            <label for="sucursal" style="margin-bottom: -10px;">Sucursal:</label>
                                            <select class="form-control" id="sucursal" name="sucursal" required>
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
                                                <option value="EFECTIVO">EFECTIVO</option>
                                                <option value="TRANSFERENCIA BANCARIA">TRANSFERENCIA BANCARIA</option>
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
        
                    let precio = parseFloat(row.children[4].textContent) || 0;
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
                    <td>${producto.id}</td>
                    <td>${producto.nombre}</td>
                    <td>${producto.color}</td>
                    <td>${producto.cantidad}</td>
                    <td>${producto.precio}</td>
                    <td><input type="number" class="form-control cantidad" value="1" min="1" style="width: 50px; height: 25px;"></td>
                    <td><input type="number" class="form-control subtotal"  style="width: 90px; height: 25px;" value="${producto.precio}" readonly></td>
                    <td><input type="number" class="form-control descuento"  style="width: 80px; height: 25px;" value="0"></td>
                    <td><input type="number" class="form-control total"  style="width: 90px; height: 25px;" value="${producto.precio}" readonly></td>
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

    document.addEventListener('DOMContentLoaded', function() {
        document.addEventListener('keypress', function(event) {
            if (event.key === 'Enter') {
                event.preventDefault();
            }
        });
    });

</script>
@endsection