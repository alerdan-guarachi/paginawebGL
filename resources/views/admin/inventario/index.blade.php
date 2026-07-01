@extends('adminlte::page')

@section('content_header')
<div class="modal fade" id="crearProductoModal" tabindex="-1" aria-labelledby="crearProductoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="crearProductoModalLabel" style="font-weight: 900;">ELIGE UN TIPO DE INVENTARIO</h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center" style="margin-top: 30px; margin-bottom: 30px;">
                <div class="row">
                    <div class="col-12 col-md-6 mb-3 d-flex justify-content-center">
                        <button type="button" class="btn btn-custom2" style="width: 80%;" onclick="redirectTo('almacen')">
                            <div class="d-flex flex-column align-items-center justify-content-center">
                                <i class="fas fa-warehouse fa-5x mb-2"></i> 
                                <span class="h5 mb-0">ALMACEN</span>
                            </div>
                        </button>
                    </div>
                    <div class="col-12 col-md-6 mb-3 d-flex justify-content-center">
                        <button type="button" class="btn btn-custom2" style="width: 80%;" onclick="redirectTo('activosfijos')">
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
</div>
<script>
    function redirectTo(type) {
        if (type === 'almacen') {
            window.location.href = "{{ route('admin.inventario.create') }}";
        } else if (type === 'activosfijos') {
            window.location.href = "{{ route('admin.inventario.crearactivofijo') }}";
        }
    }
</script>
<!-- Botón para abrir el modal -->
{{-- <a type="button" class="btn float-right btn-outline-secondary btn-sm" data-toggle="modal" data-target="#crearProductoModal">
    AGREGAR INVENTARIO
</a> --}}

<a class="btn float-right btn-outline-secondary btn-sm" data-toggle="modal" data-target="#modalCodigo">
    CODIGO CAMBIO DE STOCK
</a>

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

        fetch('{{ route("permisoscodigo.cambiarstock") }}', {
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


@if(Auth::user()->name == 'CARLOS ALEJANDRO GUARACHI SANDOVAL')
    <a class="btn float-right btn-outline-secondary btn-sm"
       data-toggle="modal"
       data-target="#modalProblemas">
        VER PROBLEMAS
    </a>
@endif
<div class="modal fade" id="modalProblemas" tabindex="-1" role="dialog" aria-labelledby="modalProblemasLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>INVENTARIO</th>
                        <th>PORTAFOLIO</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($registrosNoCoinciden as $item)
                    <tr>
                        <td>{{ $item->codigo }}</td>
                        <td>{{ $item->nombre_inventario }}</td>
                        <td>{{ $item->nombre_portafolio }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<h1>ADMINISTRACION DE INVENTARIO</h1>
@stop

@section('css')
<link rel="stylesheet" href="{{ asset('css/opcionesmultiples.css') }}">
<style>
    .table td {
        padding: 5px 10px;
    }
    .btn-registrar {
        background-color:  #ffffff;
        color: #94c93b;
        border-color: #94c93b;
        border-radius: 5px;
        padding: 2px 20px;
        }
    .btn-registrar:hover {
        background-color: #94c93b;
        color: #ffffff;
        }
    .btn-actualizar {
        background-color:  #ffffff;
        color: #e8932b;
        border-color: #e8932b;
        border-radius: 5px;
        padding: 2px 6px;
        }
    .btn-actualizar:hover {
        background-color: #e8932b;
        color: #ffffff;
        }
    .truncar {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 180px;
    }
    .truncar2 {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 150px;
    }
    .btn-verregistros {
        background-color:  #ffffff;
        color: #787878;
        border-color: #787878;
        border-radius: 5px;
        padding: 2px 6px;
        }
    .btn-verregistros:hover {
        background-color: #787878;
        color: #ffffff;
        }
    .btn-custom2 {
        background-color:  #ffffff;
        color: #9d9d9d;
        border-color: #9d9d9d;
        border-radius: 20px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        transition: background-color 0.3s, transform 0.3s, box-shadow 0.3s;
        height: 200px;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0;
    }
    .btn-custom2:hover {
        background-color: #9d9d9d;
        color: #ffffff;
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        transform: scale(1.05);
    }
    .btn-custom2:disabled {
        background-color: #d6d6d6;
        color: #a0a0a0;
        cursor: not-allowed;
    }
    .circle {
        display: inline-block;
        width: 30px;
        height: 20px;
        line-height: 20px;
        border-radius: 50%;
        text-align: center;
        font-size: 14px;
        font-weight: bold;
        margin-left: 8px;
    }
    .nav-link.active .circle {
        background-color: #6e6e6e;
        color: #fff;
    }
    .nav-link .circle {
        background-color: #6e6e6e;
        color: #fff;
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
    <nav class="navbar float-right" style="margin-top: 10px;">
        <form class="form-inline">
            <input name="buscarpor" class="form-control mr-sm-2" type="search" placeholder="NOMBRE DEL PRODUCTO..." style="width: 400px;" aria-label="Search">
            <button class="btn btn-outline-secondary my-2 my-sm-0" type="submit">BUSCAR</button>
        </form>
    </nav>

    {{-- PESTAÑAS DE SECCIONES --}}
    <div class="card-header">
        <ul class="nav nav-tabs card-header-tabs" id="myTabs">
            <li class="nav-item">
                <a class="nav-link active" id="tab-4" data-toggle="tab" href="#tab-content-4" role="tab" aria-controls="tab-content-4" aria-selected="true">
                    STOCK BAJO
                    <?php if ($stockbajoCount > 0): ?>
                        <span class="circle"><?= $stockbajoCount ?></span>
                    <?php endif; ?>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="tab-3" data-toggle="tab" href="#tab-content-3" role="tab" aria-controls="tab-content-3" aria-selected="true">
                    PENDIENTES POR INGRESAR
                    <?php if ($detalleOrdenesCount > 0): ?>
                        <span class="circle"><?= $detalleOrdenesCount ?></span>
                    <?php endif; ?>
                </a>
            </li>   
            <li class="nav-item">
                <a class="nav-link" id="tab-5" data-toggle="tab" href="#tab-content-5" role="tab" aria-controls="tab-content-5" aria-selected="true">
                    DIRECTO A INGRESAR
                    <?php if ($detalleingresodirectoCount > 0): ?>
                        <span class="circle"><?= $detalleingresodirectoCount ?></span>
                    <?php endif; ?>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="tab-1" data-toggle="tab" href="#tab-content-1" role="tab" aria-controls="tab-content-1" aria-selected="true">
                    ALMACEN
                    <?php if ($almacenCount > 0): ?>
                        <span class="circle"><?= $almacenCount ?></span>
                    <?php endif; ?>
                </a>
            </li>     
            <li class="nav-item">
                <a class="nav-link" id="tab-2" data-toggle="tab" href="#tab-content-2" role="tab" aria-controls="tab-content-2" aria-selected="true">
                    ACTIVOS FIJOS
                    <?php if ($activosfijosCount > 0): ?>
                        <span class="circle"><?= $activosfijosCount ?></span>
                    <?php endif; ?>
                </a>
            </li>     
        </ul>
    </div>
    <div class="card-body">
        <div class="tab-content" id="myTabContent">
            {{-- STOCK BAJO --}}
            <div class="tab-pane fade show active" id="tab-content-4" role="tabpanel" aria-labelledby="tab-4">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead class="table-secondary">
                            <tr>
                                <th>Cod.Prod.</th>
                                <th>Tipo_Inv.</th>
                                <th>Producto</th>
                                <th>Mat.prima</th>
                                <th>Especif_Medida</th>
                                <th>Color</th>
                                <th>Marca</th>
                                <th>Pres.</th>
                                <th>Uni.</th>
                                <th>U.medida</th>
                                <th>Stock_Ini.</th>
                                <th>Stock_Act.</th>
                                <th>Cant.Min.</th>
                                <th>Ciudad_Invent.</th>
                                <th>Inventario</th>
                                <th>Depósito</th>
                                <th>Sección</th>
                                <th>Precio</th>
                                <th>Registros</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($bienesalmacenstockbajo as $bienes)
                                <tr>
                                    <td>{{$bienes->codigo}}</td>
                                    <td>{{$bienes->tipoinventario}}</td>
                                    <td title="{{ $bienes->nombreproducto }}" class="truncar">{{$bienes->nombreproducto}}</td>
                                    <td title="{{ $bienes->materiaprima }}" class="truncar2">{{$bienes->materiaprima}}</td>
                                    <td title="{{ $bienes->especificacionmedida }}" class="truncar">{{$bienes->especificacionmedida}}</td>
                                    <td>{{$bienes->color}}</td>
                                    <td title="{{ $bienes->marca }}" class="truncar">{{$bienes->marca}}</td>
                                    <td>{{$bienes->presentacion}}</td>
                                    <td>{{$bienes->unidades}}</td>
                                    <td>{{$bienes->unidadmedida}}</td>
                                    <td>{{$bienes->stockinicial}}</td>
                                    <td>{{$bienes->stockactual}}</td>
                                    <td>{{$bienes->minimocantidad}}</td>
                                    <td>{{$bienes->ciudad}}</td>
                                    <td title="{{ $bienes->inventario }}" class="truncar2">{{$bienes->inventario}}</td>
                                    <td title="{{ $bienes->deposito }}" class="truncar">{{$bienes->deposito}}</td>
                                    <td title="{{ $bienes->seccion }}" class="truncar2">{{$bienes->seccion}}</td>
                                    <td>{{$bienes->precio}}</td>
                                    <td class="justify-content-start">
                                        <button type="button" class="btn btn-verregistros btn-sm" data-toggle="modal" data-target="#historialModal{{$bienes->codigo}}" title="VER HISTORIAL ENTRADAS">
                                            <i class="fas fa-arrow-down"></i>
                                        </button>
                                        <button type="button" class="btn btn-verregistros btn-sm" data-toggle="modal" data-target="#historialsalidaModal{{$bienes->codigo}}" title="VER HISTORIAL SALIDAS">
                                            <i class="fas fa-arrow-up"></i> 
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody> 
                    </table>
                </div>
                <div class="d-flex justify-content-center mt-3">
                    {{ $bienesalmacenstockbajo->withQueryString()->fragment('tab-content-4')->links() }}
                </div>
            </div>

            {{-- PENDIENTES POR INGRESAR --}}
            <div class="tab-pane fade" id="tab-content-3" role="tabpanel" aria-labelledby="tab-3">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead class="table-secondary">
                            <tr>
                                <th>ID</th>
                                <th>Cod.</th>
                                <th>Detalle</th>
                                <th>Proveedor</th>
                                <th hidden>Proveedor ID</th>
                                <th>Cantidad</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($detalleOrdenes as $detalles)
                                @if($detalles->estado !== 'FINALIZADO' && $detalles->tipoorden === 'ORDEN DE COMPRA')
                                    <tr>
                                        <td>{{$detalles->id}}</td>
                                        <td>{{$detalles->codigo}}</td>
                                        <td>{{$detalles->detalle}}</td>
                                        <td>{{$detalles->proveedornombre}}</td>
                                        <td>{{$detalles->cantidad}}</td>
                                        @php
                                            $productoEnInventario = \App\Models\Inventario::where('codigo', $detalles->codigo)->first();
                                            $proveedor = \DB::table('proveedoresservicios')->where('id', $detalles->proveedorid)->first();
                                        @endphp
                                        <td>
                                            @if($productoEnInventario)
                                                <button class="btn btn-sm btn-actualizar" onclick="actualizarStock2('{{$detalles->codigo}}', {{$detalles->cantidad}} , {{$detalles->id}})">
                                                    REABASTECER
                                                </button>
                                            @else
                                                <a class="btn btn-sm btn-registrar" data-toggle="modal" data-target="#registroProductoModal" 
                                                    data-detalleordenid="{{$detalles->id}}" data-codigo="{{$detalles->codigo}}" data-nombreproducto="{{$detalles->detalle}}"
                                                    data-cantidad="{{$detalles->cantidad}}" data-preciounitario="{{$detalles->preciounitario}}" data-proveedorid="{{$detalles->proveedorid}}" 
                                                    data-proveedornombre="{{$detalles->proveedornombre}}" data-fechacomprar="{{$detalles->fechacomprar}}" data-totalunitario="{{$detalles->totalunitario}}"
                                                    data-emision="{{ $proveedor ? $proveedor->emision : '' }}">
                                                    AGREGAR
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- MODAL INGRESO NUEVO PRODUCTO -->
            <div class="modal fade" id="registroProductoModal" tabindex="-1" role="dialog" aria-labelledby="registroProductoModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title" id="registroProductoModalLabel" style="font-weight: 900">REGISTRAR PRODUCTO EN INVENTARIO</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form action="{{ route('inventario.registrarProducto') }}" method="POST">
                                @csrf
                                <input type="hidden" id="detalleordenid" name="detalleordenid">
                                <input type="hidden" id="proveedorid" name="proveedorid" onchange="toggleNroFactura()">
                                <input type="hidden" id="proveedornombre" name="proveedornombre">
                                
                                <div class="row">
                                    <div class="form-group col-lg-2">
                                        <label for="codigo">Código:</label>
                                        <input type="text" class="form-control" id="codigo" name="codigo" required readonly>
                                    </div>
                                    <div class="form-group col-lg-4">
                                        <label for="nombreproducto">Producto:</label>
                                        <input type="text" class="form-control" id="nombreproducto" name="nombreproducto" required readonly>
                                    </div>
                                    <div class="form-group col-lg-2">
                                        <label for="preciounitario">Precio Uni.:</label>
                                        <input type="number" class="form-control" id="preciounitario" name="preciounitario" required readonly>
                                    </div>
                                    <div class="form-group col-lg-2">
                                        <label for="cantidad">Cantidad:</label>
                                        <input type="number" class="form-control" id="cantidad" name="cantidad" required readonly>
                                    </div>
                                    <div class="form-group col-lg-2">
                                        <label for="totalunitario">Total:</label>
                                        <input type="number" class="form-control" id="totalunitario" name="totalunitario" required readonly>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="form-group col-lg-2">
                                        <label for="tipoinventario">Tipo Inv.:</label>
                                        <input type="text" class="form-control" id="tipoinventario" name="tipoinventario" required readonly>
                                    </div>
                                    <div class="form-group col-lg-3">
                                        <label for="seccion">Sección:</label>
                                        <input type="text" class="form-control" id="seccion" name="seccion" required readonly>
                                    </div>
                                    <div class="form-group col-lg-3">
                                        <label for="materiaprima">Mat. Prima:</label>
                                        <input type="text" class="form-control" id="materiaprima" name="materiaprima" required readonly>
                                    </div>
                                    <div class="form-group col-lg-2">
                                        <label for="especificacionmedida">Esp. Medida:</label>
                                        <input type="text" class="form-control" id="especificacionmedida" name="especificacionmedida" required readonly>
                                    </div>
                                    <div class="form-group col-lg-2">
                                        <label for="color">Color:</label>
                                        <input type="text" class="form-control" id="color" name="color" required readonly>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="form-group col-lg-3">
                                        <label for="marca">Marca:</label>
                                        <input type="text" class="form-control" id="marca" name="marca" required readonly>
                                    </div>
                                    <div class="form-group col-lg-3">
                                        <label for="unidadmedida">Unidad medida:</label>
                                        <input type="text" class="form-control" id="unidadmedida" name="unidadmedida" required readonly>
                                    </div>
                                    <div class="form-group col-lg-2">
                                        <label for="presentacion">Present.:</label>
                                        <input type="text" class="form-control" id="presentacion" name="presentacion" required readonly>
                                    </div>
                                    <div class="form-group col-lg-2">
                                        <label for="unidades">Unidades:</label>
                                        <input type="text" class="form-control" id="unidades" name="unidades" required readonly>
                                    </div>
                                    <div class="form-group col-lg-2">
                                        <label for="precio">Precio:</label>
                                        <input type="text" class="form-control" id="precio" name="precio" required readonly>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-lg-3">
                                        <label for="fechacompra">Fecha Compra:</label>
                                        <input type="date" class="form-control" id="fechacomprar" name="fechacompra" required readonly>
                                    </div>
                                    <div class="form-group col-lg-3">
                                        <label for="fecha">Fecha Entrada:</label>
                                        <input type="date" class="form-control" id="fecha" name="fecha" required>
                                    </div>
                                    <div class="form-group col-lg-3">
                                        <label for="inventario">Inventario:</label>
                                        <select class="form-control" id="inventario" name="inventario" required>
                                            <option value=""></option>
                                            <option value="PRINCIPAL">PRINCIPAL</option>
                                            <option value="ACTIVOS FIJOS">ACTIVOS FIJOS</option>
                                            <option value="AGOTADO">AGOTADO</option>
                                            <option value="ASIGNACION Y DEVOLUCION">ASIGNACION Y DEVOLUCION</option>
                                            <option value="STOCK DEPURADO">STOCK DEPURADO</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-lg-3">
                                        <label for="deposito">Depósito:</label>
                                        <select class="form-control" id="deposito" name="deposito" required>
                                            <option value=""></option>
                                            <option value="PRINCIPAL">PRINCIPAL</option>
                                            <option value="SECUNDARIO">SECUNDARIO</option>
                                            <option value="N/A">N/A</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-lg-2" id="nrofactura-group" style="display: none;">
                                        <label for="nrofactura">Nro. Factura:</label>
                                        <input type="number" class="form-control" id="nrofactura" name="nrofactura">
                                    </div>
                                    <div class="form-group col-lg-2">
                                        <label for="nrorecibo">Nro. Recibo:</label>
                                        <input type="number" class="form-control" id="nrorecibo" name="nrorecibo" required>
                                    </div>
                                    <div class="form-group col-lg-3">
                                        <label for="modelo">Modelo:</label>
                                        <input type="text" class="form-control" id="modelo" name="modelo">
                                    </div>
                                    <div class="form-group col-lg-3">
                                        <label for="serie">Serie:</label>
                                        <input type="text" class="form-control" id="serie" name="serie">
                                    </div>
                                    <div class="form-group col-lg-2">
                                        <label for="minimacantidad">Cant. Min.:</label>
                                        <input type="text" class="form-control" id="minimacantidad" name="minimacantidad" required>
                                    </div>
                                </div>
                                <script>  
                                    function toggleField(id) {
                                        let campo = document.getElementById(id);
                                        if (campo) {
                                            if (campo.style.display === "none") {
                                                campo.style.display = "block";
                                            } else {
                                                campo.style.display = "none";
                                                campo.value = "";
                                            }
                                        }
                                    }
                                </script>
                                
                                <div class="row">
                                    <div class="form-group col-lg-6">
                                        <a class="btn btn-sm btn-outline-secondary mb-2" onclick="toggleField('fechavencimiento')">AÑADIR FECHA DE VENCIMIENTO</a>
                                        <input type="date" id="fechavencimiento" name="fechavencimiento" class="form-control mb-2" style="display: none;">
                                    </div>
                                    <div class="form-group col-lg-6">
                                        <a class="btn btn-sm btn-outline-secondary mb-2" onclick="toggleField('garantia')">AÑADIR FECHA DE GARANTIA</a>
                                        <input type="date" id="garantia" name="garantia" class="form-control mb-2" style="display: none;">
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-registrar">REGISTRAR PRODUCTO</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <script> 
                $(document).ready(function() {
                    $('#registroProductoModal').on('show.bs.modal', function(event) {
                        var button = $(event.relatedTarget);
                        var detalleordenId = button.data('detalleordenid');
                        var codigo = button.data('codigo');
                        var proveedorid = button.data('proveedorid'); // Obtener el proveedorid
                        var emision = button.data('emision');  // Obtener el valor de emision
            
                        var modal = $(this);
                        modal.find('#detalleordenid').val(detalleordenId);
                        modal.find('#codigo').val(codigo);
                        modal.find('#proveedorid').val(proveedorid);
            
                        var urlTemplate = '{{ route("portfolioProveedores.getByCodigo", ":codigo") }}';
                        var url = urlTemplate.replace(':codigo', codigo);
                        
                        $.ajax({
                            url: url,
                            method: 'GET',
                            dataType: 'json',
                            success: function(data) {
                                modal.find('#tipoinventario').val(data.tipoinventario);
                                modal.find('#materiaprima').val(data.materiaprima);
                                modal.find('#especificacionmedida').val(data.especificacionmedida);
                                modal.find('#color').val(data.color);
                                modal.find('#marca').val(data.marca);
                                modal.find('#unidadmedida').val(data.unidadmedida);
                                modal.find('#presentacion').val(data.presentacion);
                                modal.find('#unidades').val(data.unidades);
                                modal.find('#precio').val(data.precio);
                                modal.find('#seccion').val(data.seccion);
                            },
                            error: function(err) {
                                console.error('Error al obtener los datos del portafolio:', err);
                            }
                        });
            
                        // Lógica para mostrar/ocultar el campo de Nro. Factura
                        if (emision === 'FACTURA') {
                            modal.find('#nrofactura-group').show();
                            modal.find('#nrofactura').prop('required', true); // Hacer obligatorio el campo
                        } else {
                            modal.find('#nrofactura-group').hide();
                            modal.find('#nrofactura').prop('required', false); // Quitar obligatoriedad
                        }
                    });
                });
            </script>
            <script>
                $(document).ready(function() {
                    $('#registroProductoModal').on('show.bs.modal', function(event) {
                        var button = $(event.relatedTarget);
                        var detalleordenId = button.data('detalleordenid');
                        var codigo = button.data('codigo');
                        var nombreproducto = button.data('nombreproducto');
                        var cantidad = button.data('cantidad');
                        var preciounitario = button.data('preciounitario');
                        var totalunitario = button.data('totalunitario');
                        var proveedorid = button.data('proveedorid');
                        var proveedornombre = button.data('proveedornombre');
                        var fechacomprar = button.data('fechacomprar');
                        var modal = $(this);
                        modal.find('#detalleordenid').val(detalleordenId);
                        modal.find('#codigo').val(codigo);
                        modal.find('#nombreproducto').val(nombreproducto);
                        modal.find('#cantidad').val(cantidad);
                        modal.find('#preciounitario').val(preciounitario);
                        modal.find('#totalunitario').val(totalunitario);
                        modal.find('#proveedorid').val(proveedorid);
                        modal.find('#proveedornombre').val(proveedornombre);
                        modal.find('#fechacomprar').val(fechacomprar);
                    });
                });
            </script>
            <script>
                function actualizarStock2(codigoProducto, cantidad, detalleId) {
                    $.ajax({
                        url: '{{ route("inventario.actualizarStock") }}',
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            codigo_producto: codigoProducto,
                            cantidad: cantidad,
                            detalle_id: detalleId
                        },
                        success: function(response) {
                            if(response.success) {
                                location.reload(); // Recarga para mostrar el mensaje de sesión
                            } else {
                                alert(response.message);
                            }
                        }
                    });
                }
            </script>

            {{-- DIRECTO A INGRESAR --}}
            <div class="tab-pane fade" id="tab-content-5" role="tabpanel" aria-labelledby="tab-5">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead class="table-secondary">
                            <tr>
                                <th>Cod.</th>
                                <th>Detalle</th>
                                <th>Proveedor</th>
                                <th hidden>Proveedor ID</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($detalleingresodirecto as $detalles)
                                @php
                                    $productoEnInventario = \App\Models\Inventario::where('codigo', $detalles->codigo)->first();
                                    $proveedor = \DB::table('proveedoresservicios')->where('id', $detalles->proveedorid)->first();
                                @endphp
                                <tr>
                                    <td>{{$detalles->id}}</td>
                                    <td>{{$detalles->nombreproducto}}</td>
                                    <td>{{$detalles->proveedornombre}}</td>
                                    <td>
                                        <a class="btn btn-sm btn-registrar" data-toggle="modal" data-target="#registroProductoModal2" 
                                            data-detalleordenid="{{$detalles->id}}" data-codigo="{{$detalles->id}}" data-nombreproducto="{{$detalles->nombreproducto}}"
                                            data-cantidad="{{$detalles->cantidad}}" data-preciounitario="{{$detalles->preciounitario}}" data-proveedorid="{{$detalles->proveedorid}}" 
                                            data-proveedornombre="{{$detalles->proveedornombre}}" data-fechacomprar="{{$detalles->fechacomprar}}" data-totalunitario="{{$detalles->totalunitario}}"
                                            data-emision="{{ $proveedor ? $proveedor->emision : '' }}">
                                            AGREGAR
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- MODAL DIRECTO INGRESO NUEVO PRODUCTO -->
            <div class="modal fade" id="registroProductoModal2" tabindex="-1" role="dialog" aria-labelledby="registroProductoModal2Label" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title" id="registroProductoModal2Label" style="font-weight: 900">REGISTRAR PRODUCTO DIRECTO A INVENTARIO</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form action="{{ route('inventario.registrarProducto') }}" method="POST">
                                @csrf
                                <input type="hidden" id="detalleordenid" name="detalleordenid">
                                <input type="hidden" id="proveedorid" name="proveedorid" onchange="toggleNroFactura()">
                                <input type="hidden" id="proveedornombre" name="proveedornombre">
                                
                                <div class="row">
                                    <div class="form-group col-lg-2">
                                        <label for="codigo">Código:</label>
                                        <input type="text" class="form-control" id="codigo" name="codigo" required readonly>
                                    </div>
                                    <div class="form-group col-lg-4">
                                        <label for="nombreproducto">Producto:</label>
                                        <input type="text" class="form-control" id="nombreproducto" name="nombreproducto" required readonly>
                                    </div>
                                    <div class="form-group col-lg-2">
                                        <label for="preciounitario">Precio Uni.:</label>
                                        <input type="number" class="form-control" id="preciounitario" name="preciounitario" required readonly>
                                    </div>
                                    <div class="form-group col-lg-2">
                                        <label for="cantidad">Cantidad:</label>
                                        <input type="number" class="form-control" id="cantidad" name="cantidad" required readonly>
                                    </div>
                                    <div class="form-group col-lg-2">
                                        <label for="totalunitario">Total:</label>
                                        <input type="number" class="form-control" id="precio" name="totalunitario" required readonly>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="form-group col-lg-2">
                                        <label for="tipoinventario">Tipo Inv.:</label>
                                        <input type="text" class="form-control" id="tipoinventario" name="tipoinventario" required readonly>
                                    </div>
                                    <div class="form-group col-lg-3">
                                        <label for="seccion">Sección:</label>
                                        <input type="text" class="form-control" id="seccion" name="seccion" required readonly>
                                    </div>
                                    <div class="form-group col-lg-3">
                                        <label for="materiaprima">Mat. Prima:</label>
                                        <input type="text" class="form-control" id="materiaprima" name="materiaprima" required readonly>
                                    </div>
                                    <div class="form-group col-lg-2">
                                        <label for="especificacionmedida">Esp. Medida:</label>
                                        <input type="text" class="form-control" id="especificacionmedida" name="especificacionmedida" required readonly>
                                    </div>
                                    <div class="form-group col-lg-2">
                                        <label for="color">Color:</label>
                                        <input type="text" class="form-control" id="color" name="color" required readonly>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="form-group col-lg-3">
                                        <label for="marca">Marca:</label>
                                        <input type="text" class="form-control" id="marca" name="marca" required readonly>
                                    </div>
                                    <div class="form-group col-lg-3">
                                        <label for="unidadmedida">Unidad medida:</label>
                                        <input type="text" class="form-control" id="unidadmedida" name="unidadmedida" required readonly>
                                    </div>
                                    <div class="form-group col-lg-2">
                                        <label for="presentacion">Present.:</label>
                                        <input type="text" class="form-control" id="presentacion" name="presentacion" required readonly>
                                    </div>
                                    <div class="form-group col-lg-2">
                                        <label for="unidades">Unidades:</label>
                                        <input type="text" class="form-control" id="unidades" name="unidades" required readonly>
                                    </div>
                                    <div class="form-group col-lg-2">
                                        <label for="precio">Precio:</label>
                                        <input type="text" class="form-control" id="precio" name="precio" required readonly>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-lg-3">
                                        <label for="fechacompra">Fecha Compra:</label>
                                        <input type="date" class="form-control" id="fechacomprar" name="fechacompra" required>
                                    </div>
                                    <div class="form-group col-lg-3">
                                        <label for="fecha">Fecha Entrada:</label>
                                        <input type="date" class="form-control" id="fecha" name="fecha" required>
                                    </div>
                                    <div class="form-group col-lg-3">
                                        <label for="inventario">Inventario:</label>
                                        <select class="form-control" id="inventario" name="inventario" required>
                                            <option value=""></option>
                                            <option value="PRINCIPAL">PRINCIPAL</option>
                                            <option value="ACTIVOS FIJOS">ACTIVOS FIJOS</option>
                                            <option value="AGOTADO">AGOTADO</option>
                                            <option value="ASIGNACION Y DEVOLUCION">ASIGNACION Y DEVOLUCION</option>
                                            <option value="STOCK DEPURADO">STOCK DEPURADO</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-lg-3">
                                        <label for="deposito">Depósito:</label>
                                        <select class="form-control" id="deposito" name="deposito" required>
                                            <option value=""></option>
                                            <option value="PRINCIPAL">PRINCIPAL</option>
                                            <option value="SECUNDARIO">SECUNDARIO</option>
                                            <option value="N/A">N/A</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-lg-2">
                                        <label for="nrofactura">Nro. Factura:</label>
                                        <input type="number" class="form-control" id="nrofactura" name="nrofactura" required>
                                    </div>
                                    <div class="form-group col-lg-2">
                                        <label for="nrorecibo">Nro. Recibo:</label>
                                        <input type="number" class="form-control" id="nrorecibo" name="nrorecibo" required>
                                    </div>
                                    <div class="form-group col-lg-3">
                                        <label for="modelo">Modelo:</label>
                                        <input type="text" class="form-control" id="modelo" name="modelo">
                                    </div>
                                    <div class="form-group col-lg-3">
                                        <label for="serie">Serie:</label>
                                        <input type="text" class="form-control" id="serie" name="serie">
                                    </div>
                                    <div class="form-group col-lg-2">
                                        <label for="minimacantidad">Cant. Min.:</label>
                                        <input type="text" class="form-control" id="minimacantidad" name="minimacantidad" required>
                                    </div>
                                </div>
                                <script>  
                                    function toggleField(id) {
                                        let campo = document.getElementById(id);
                                        if (campo) {
                                            if (campo.style.display === "none") {
                                                campo.style.display = "block";
                                            } else {
                                                campo.style.display = "none";
                                                campo.value = "";
                                            }
                                        }
                                    }
                                </script>
                                
                                <div class="row">
                                    <div class="form-group col-lg-6">
                                        <a class="btn btn-sm btn-outline-secondary mb-2" onclick="toggleField('fechavencimiento')">AÑADIR FECHA DE VENCIMIENTO</a>
                                        <input type="date" id="fechavencimiento" name="fechavencimiento" class="form-control mb-2" style="display: none;">
                                    </div>
                                    <div class="form-group col-lg-6">
                                        <a class="btn btn-sm btn-outline-secondary mb-2" onclick="toggleField('garantia')">AÑADIR FECHA DE GARANTIA</a>
                                        <input type="date" id="garantia" name="garantia" class="form-control mb-2" style="display: none;">
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-registrar">REGISTRAR PRODUCTO</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <script> 
                $(document).ready(function() {
                    $('#registroProductoModal2').on('show.bs.modal', function(event) {
                        var button = $(event.relatedTarget);
                        var detalleordenId = button.data('detalleordenid');
                        var codigo = button.data('codigo');
                        var proveedorid = button.data('proveedorid'); // Obtener el proveedorid
                        var emision = button.data('emision');  // Obtener el valor de emision
            
                        var modal = $(this);
                        modal.find('#detalleordenid').val(detalleordenId);
                        modal.find('#codigo').val(codigo);
                        modal.find('#proveedorid').val(proveedorid);
            
                        var urlTemplate = '{{ route("portfolioProveedores.getByCodigo", ":codigo") }}';
                        var url = urlTemplate.replace(':codigo', codigo);
                        
                        $.ajax({
                            url: url,
                            method: 'GET',
                            dataType: 'json',
                            success: function(data) {
                                modal.find('#tipoinventario').val(data.tipoinventario);
                                modal.find('#materiaprima').val(data.materiaprima);
                                modal.find('#especificacionmedida').val(data.especificacionmedida);
                                modal.find('#color').val(data.color);
                                modal.find('#marca').val(data.marca);
                                modal.find('#unidadmedida').val(data.unidadmedida);
                                modal.find('#presentacion').val(data.presentacion);
                                modal.find('#unidades').val(data.unidades);
                                modal.find('#precio').val(data.precio);
                                modal.find('#seccion').val(data.seccion);
                            },
                            error: function(err) {
                                console.error('Error al obtener los datos del portafolio:', err);
                            }
                        });
            
                        // Lógica para mostrar/ocultar el campo de Nro. Factura
                        if (emision === 'FACTURA') {
                            modal.find('#nrofactura-group').show();
                            modal.find('#nrofactura').prop('required', true); // Hacer obligatorio el campo
                        } else {
                            modal.find('#nrofactura-group').hide();
                            modal.find('#nrofactura').prop('required', false); // Quitar obligatoriedad
                        }
                    });
                });
            </script>
            <script>
                $(document).ready(function() {
                    $('#registroProductoModal2').on('show.bs.modal', function(event) {
                        var button = $(event.relatedTarget);
                        var detalleordenId = button.data('detalleordenid');
                        var codigo = button.data('codigo');
                        var nombreproducto = button.data('nombreproducto');
                        var cantidad = button.data('cantidad');
                        var preciounitario = button.data('preciounitario');
                        var totalunitario = button.data('totalunitario');
                        var proveedorid = button.data('proveedorid');
                        var proveedornombre = button.data('proveedornombre');
                        var fechacomprar = button.data('fechacomprar');
                        var modal = $(this);
                        modal.find('#detalleordenid').val(detalleordenId);
                        modal.find('#codigo').val(codigo);
                        modal.find('#nombreproducto').val(nombreproducto);
                        modal.find('#cantidad').val(cantidad);
                        modal.find('#preciounitario').val(preciounitario);
                        modal.find('#totalunitario').val(totalunitario);
                        modal.find('#proveedorid').val(proveedorid);
                        modal.find('#proveedornombre').val(proveedornombre);
                        modal.find('#fechacomprar').val(fechacomprar);
                    });
                });
            </script>
            
            {{-- ALMACEN --}}
            <div class="tab-pane fade" id="tab-content-1" role="tabpanel" aria-labelledby="tab-1">
                <form action="{{ route('inventario.anularproductoinventario') }}" method="POST" id="form_anular_tab1">
                    @csrf
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead class="table-secondary">
                                <tr>
                                    @can('admin.inventario.darbajaproductoinventario')
                                    <th>Sel.</th>
                                    @endcan
                                    <th>Cod.Prod.</th>
                                    <th>Producto</th>
                                    <th>Mat.prima</th>
                                    <th>Especif_Medida</th>
                                    <th>Color</th>
                                    <th>Marca</th>
                                    <th>Pres.</th>
                                    <th>Uni.</th>
                                    <th>U.medida</th>
                                    <th>Stock_Ini.</th>
                                    <th>Stock_Act.</th>
                                    <th>Cant.Min.</th>
                                    <th>Ciudad_Invent.</th>
                                    <th>Inventario</th>
                                    <th>Depósito</th>
                                    <th>Sección</th>
                                    <th>Precio</th>
                                    <th>Registros</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($bienesalmacen as $bienes)
                                    <tr>
                                        @can('admin.inventario.darbajaproductoinventario')
                                        <td class="text-center">
                                            <input type="checkbox" name="codigos[]" value="{{ $bienes->codigo }}" class="check_item_tab1">
                                        </td>
                                        @endcan
                                        <td>{{$bienes->codigo}}</td>
                                        <td title="{{ $bienes->nombreproducto }}" class="truncar">{{$bienes->nombreproducto}}</td>
                                        <td title="{{ $bienes->materiaprima }}" class="truncar2">{{$bienes->materiaprima}}</td>
                                        <td title="{{ $bienes->especificacionmedida }}" class="truncar">{{$bienes->especificacionmedida}}</td>
                                        <td>{{$bienes->color}}</td>
                                        <td title="{{ $bienes->marca }}" class="truncar">{{$bienes->marca}}</td>
                                        <td>{{$bienes->presentacion}}</td>
                                        <td>{{$bienes->unidades}}</td>
                                        <td>{{$bienes->unidadmedida}}</td>
                                        <td>{{$bienes->stockinicial}}</td>
                                        @php
                                            $updated = $bienes->updated_at;
                                            $esEditable = in_array($bienes->codigo, $codigosPermitidos) &&
                                                        (is_null($updated) || !\Carbon\Carbon::parse($updated)->isToday());
                                        @endphp
                                        <td>
                                            @if($esEditable)
                                                {{-- <form method="POST" action="{{ route('inventario.actualizarStockcodigo', $bienes->codigo) }}" class="d-flex align-items-center">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="text" name="stockactual" value="{{ $bienes->stockactual }}" class="form-control form-control-sm w-50 me-1">
                                                    <button type="submit" class="btn btn-success btn-sm" title="Actualizar Stock">
                                                        <i class="fas fa-save"></i>
                                                    </button>
                                                </form> --}}
                                                <div class="d-flex align-items-center">
                                                    <input type="text" min="1" name="stockactual_input_{{ $bienes->codigo }}"
                                                        id="stockactual_input_{{ $bienes->codigo }}"
                                                        value="{{ $bienes->stockactual }}"
                                                        class="form-control form-control-sm w-50 me-1">
                                                    <button type="button"
                                                            class="btn btn-success btn-sm"
                                                            onclick="actualizarStock('{{ $bienes->codigo }}', '{{ route('inventario.actualizarStockcodigo', $bienes->codigo) }}')"
                                                            title="Actualizar Stock">
                                                        <i class="fas fa-save"></i>
                                                    </button>
                                                </div>
                                            @else
                                                {{ $bienes->stockactual }}
                                            @endif
                                        </td>

                                        <td>{{$bienes->minimocantidad}}</td>
                                        <td>{{$bienes->ciudad}}</td>
                                        <td title="{{ $bienes->inventario }}" class="truncar2">{{$bienes->inventario}}</td>
                                        <td title="{{ $bienes->deposito }}" class="truncar">{{$bienes->deposito}}</td>
                                        <td title="{{ $bienes->seccion }}" class="truncar2">{{$bienes->seccion}}</td>
                                        <td>{{$bienes->precio}}</td>
                                        <td class="justify-content-start">
                                            <button type="button" class="btn btn-verregistros btn-sm" data-toggle="modal" data-target="#historialModal{{$bienes->codigo}}" title="VER HISTORIAL ENTRADAS">
                                                <i class="fas fa-arrow-down"></i>
                                            </button>
                                            <button type="button" class="btn btn-verregistros btn-sm" data-toggle="modal" data-target="#historialsalidaModal{{$bienes->codigo}}" title="VER HISTORIAL SALIDAS">
                                                <i class="fas fa-arrow-up"></i> 
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody> 
                        </table>
                    </div>
                    @can('admin.inventario.darbajaproductoinventario')
                    <button type="submit" class="btn btn-sm btn-outline-danger mt-2">
                        DAR DE BAJA SELECCIONADOS
                    </button>
                    @endcan
                </form>
                <div class="d-flex justify-content-center mt-3">
                {{ $bienesalmacen->withQueryString()->fragment('tab-content-1')->links() }}
                </div>
            </div>

            {{-- ACTIVOS FIJOS --}}
            <div class="tab-pane fade" id="tab-content-2" role="tabpanel" aria-labelledby="tab-2">
                <form action="{{ route('inventario.anularproductoinventario') }}" method="POST" id="form_anular_tab2">
                    @csrf
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead class="table-secondary">
                                <tr>
                                    @can('admin.inventario.darbajaproductoinventario')
                                    <th>Sel.</th>
                                    @endcan
                                    <th>Cod.Prod.</th>
                                    <th>Producto</th>
                                    <th>Mat.prima</th>
                                    <th>Especif_Medida</th>
                                    <th>Color</th>
                                    <th>Marca</th>
                                    <th>Pres.</th>
                                    <th>Uni.</th>
                                    <th>Modelo</th>
                                    <th>Serie</th>
                                    <th>Sección</th>
                                    <th>Uni.medida</th>
                                    <th>Stock_ini.</th>
                                    <th>Stock_act.</th>
                                    <th>Ciudad_Invent.</th>
                                    <th>Precio</th>
                                    <th>Registros</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($bienesactivosfijos as $bienes)
                                    <tr>
                                        @can('admin.inventario.darbajaproductoinventario')
                                        <td class="text-center">
                                            <input type="checkbox" name="codigos[]" value="{{ $bienes->codigo }}" class="check_item_tab2">
                                        </td>
                                        @endcan
                                        <td>{{$bienes->codigo}}</td>
                                        <td title="{{ $bienes->nombreproducto }}" class="truncar">{{$bienes->nombreproducto}}</td>
                                        <td title="{{ $bienes->materiaprima }}" class="truncar2">{{$bienes->materiaprima}}</td>
                                        <td title="{{ $bienes->especificacionmedida }}" class="truncar">{{$bienes->especificacionmedida}}</td>
                                        <td>{{$bienes->color}}</td>
                                        <td title="{{ $bienes->marca }}" class="truncar">{{$bienes->marca}}</td>
                                        <td>{{$bienes->presentacion}}</td>
                                        <td>{{$bienes->unidades}}</td>
                                        <td title="{{ $bienes->modelo }}" class="truncar">{{$bienes->modelo ?? 0}}</td>
                                        <td title="{{ $bienes->serie }}" class="truncar">{{$bienes->serie ?? 0}}</td>
                                        <td title="{{ $bienes->seccion }}" class="truncar2">{{$bienes->seccion}}</td>
                                        <td>{{$bienes->unidadmedida}}</td>
                                        <td>{{$bienes->stockinicial}}</td>
                                        @php
                                            $updated = $bienes->updated_at;
                                            $esEditable = in_array($bienes->codigo, $codigosPermitidos) &&
                                                        (is_null($updated) || !\Carbon\Carbon::parse($updated)->isToday());
                                        @endphp
                                        <td>
                                            @if($esEditable)
                                                {{-- <form method="POST" action="{{ route('inventario.actualizarStockcodigo', $bienes->codigo) }}" class="d-flex align-items-center">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="text" name="stockactual" value="{{ $bienes->stockactual }}" class="form-control form-control-sm w-50 me-1">
                                                    <button type="submit" class="btn btn-success btn-sm" title="Actualizar Stock">
                                                        <i class="fas fa-save"></i>
                                                    </button>
                                                </form> --}}
                                                <div class="d-flex align-items-center">
                                                    <input type="text" min="1" name="stockactual_input_{{ $bienes->codigo }}"
                                                        id="stockactual_input_{{ $bienes->codigo }}"
                                                        value="{{ $bienes->stockactual }}"
                                                        class="form-control form-control-sm w-50 me-1">
                                                    <button type="button"
                                                            class="btn btn-success btn-sm"
                                                            onclick="actualizarStock('{{ $bienes->codigo }}', '{{ route('inventario.actualizarStockcodigo', $bienes->codigo) }}')"
                                                            title="Actualizar Stock">
                                                        <i class="fas fa-save"></i>
                                                    </button>
                                                </div>
                                            @else
                                                {{ $bienes->stockactual }}
                                            @endif
                                        </td>
                                        <td>{{$bienes->ciudad}}</td>
                                        <td>{{$bienes->precio}}</td>
                                        <td class="justify-content-start">
                                            <button type="button" class="btn btn-verregistros btn-sm" data-toggle="modal" data-target="#historialModal{{$bienes->codigo}}" title="VER HISTORIAL ENTRADAS">
                                                <i class="fas fa-arrow-down"></i>
                                            </button>
                                            <button type="button" class="btn btn-verregistros btn-sm" data-toggle="modal" data-target="#historialsalidaModal{{$bienes->codigo}}" title="VER HISTORIAL SALIDAS">
                                                <i class="fas fa-arrow-up"></i> 
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody> 
                        </table>
                    </div>
                    @can('admin.inventario.darbajaproductoinventario')
                    <button type="submit" class="btn btn-sm btn-outline-danger mt-2">
                        DAR DE BAJA SELECCIONADOS
                    </button>
                    @endcan
                </form>
                <div class="d-flex justify-content-center mt-3">
                {{ $bienesactivosfijos->withQueryString()->fragment('tab-content-2')->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const hash = window.location.hash;
        if (hash) {
            const trigger = document.querySelector(`a[href="${hash}"]`);
            if (trigger) {
                new bootstrap.Tab(trigger).show();
            }
        }
    });
</script>

{{-- GUARDAR ENTRADAS --}}
<form action="{{ route('guardar.entrada') }}" method="POST">
    @csrf
    <div class="modal fade" id="entradaModal" tabindex="-1" role="dialog" aria-labelledby="entradaModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="entradaModalLabel" style="font-weight: 900; font-size:25px;">REGISTRAR ENTRADA</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-light text-center" style="border: 2px solid #6c757d;">
                        <strong class="text-dark">Producto:</strong> 
                        <span id="productoNombre" class="text-dark"></span> 
                        <br>
                        <strong class="text-dark">Código:</strong> 
                        <span id="codigoProducto" class="text-dark"></span>
                    </div>
                    <input type="hidden" id="codigoEntrada" name="codigoproducto">
                    <div class="row">
                        <div class="form-group col-lg-6">
                            <label for="nrofactura">Nro. de Factura:</label>
                            <input type="number" class="form-control" id="nrofactura" name="nrofactura">
                        </div>
                        <div class="form-group col-lg-6">
                            <label for="nrorecibo">Nro. de Recibo:</label>
                            <input type="number" class="form-control" id="nrorecibo" name="nrorecibo" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-lg-6">
                            <label for="fechacompra">Fecha de Compra:</label>
                            <input type="date" class="form-control" id="fechacompra" name="fechacompra" required>
                        </div>
                        <div class="form-group col-lg-6">
                            <label for="fecha">Fecha de Entrada:</label>
                            <input type="date" class="form-control" id="fecha" name="fecha" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-lg-6">
                            <label for="precio">Precio:</label>
                            <input type="number" class="form-control" id="precio" name="precio" required>
                        </div>
                        <div class="form-group col-lg-6">
                            <label for="cantidad">Cantidad:</label>
                            <input type="number" class="form-control" id="cantidad" name="cantidad" required>
                        </div>
                    </div>

                    <script>  
                        function toggleField(id) {
                            let campo = document.getElementById(id);
                            if (campo) {
                                if (campo.style.display === "none") {
                                    campo.style.display = "block";
                                } else {
                                    campo.style.display = "none";
                                    campo.value = "";
                                }
                            }
                        }
                    </script>
                    
                    <div class="row">
                        <div class="form-group col-lg-6">
                            <a class="btn btn-outline-secondary mb-2" onclick="toggleField('fechavencimiento')">Añadir Vencimiento</a>
                            <input type="date" id="fechavencimiento" name="fechavencimiento" class="form-control mb-2" style="display: none;">
                        </div>
                        <div class="form-group col-lg-6">
                            <a class="btn btn-outline-secondary mb-2" onclick="toggleField('garantia')">Añadir Garantía</a>
                            <input type="text" id="garantia" name="garantia" class="form-control mb-2" style="display: none;">
                        </div>
                    </div>
                    
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-outline-success">GUARDAR ENTRADA</button>
                    <button type="button" class="btn btn-outline-danger" data-dismiss="modal">CERRAR</button>
                </div>
            </div>
        </div>
    </div>
</form>

{{-- GUARDAR SALIDAS --}}
<form action="{{ route('guardar.salida') }}" method="POST">
    @csrf
    <div class="modal fade" id="salidaModal" tabindex="-1" role="dialog" aria-labelledby="salidaModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="entradaModalLabel" style="font-weight: 900; font-size:25px;">REGISTRAR SALIDA</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-light text-center" style="border: 2px solid #6c757d;">
                        <strong class="text-dark">Producto:</strong> 
                        <span id="productoNombre2" class="text-dark"></span> 
                        <br>
                        <strong class="text-dark">Código:</strong> 
                        <span id="codigoProducto2" class="text-dark"></span>
                    </div>

                    <input type="hidden" id="codigoSalida" name="codigoproducto">
                    <form id="salidaForm">
                        <div class="form-group">
                            <label for="usuarioreceptor">Usuario Receptor:</label>
                            <select class="form-control" id="usuarioreceptor" name="usuarioreceptor" required>
                                <option value="" disabled selected></option>
                                @foreach ($usuarios as $usuario)
                                    <option value="{{ $usuario->id }}">
                                        {{ $usuario->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="row">
                            <div class="form-group col-lg-6">
                                <label for="fecha">Fecha:</label>
                                <input type="date" class="form-control" id="fecha" name="fecha" required>
                            </div>
                            <div class="form-group col-lg-6">
                                <label for="cantidad">Cantidad:</label>
                                <input type="number" class="form-control" id="cantidad" name="cantidad" required>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-outline-danger">GUARDAR SALIDA</button>
                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">CERRAR</button>
                </div>
            </div>
        </div>
    </div>
</form>

@foreach ([$bienesalmacen, $bienesactivosfijos] as $bienesLista)
    @foreach ($bienesLista as $bienes)
        <div class="modal fade" id="historialModal{{$bienes->codigo}}" tabindex="-1" role="dialog" aria-labelledby="historialModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title font-weight-bold">HISTORIAL DE ENTRADAS - {{$bienes->nombreproducto}} - {{$bienes->codigo}}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Fecha_Entrada</th>
                                        <th>Nro_Factura</th>
                                        <th>Nro_Recibo</th>
                                        <th>Precio</th>
                                        <th>Cantidad</th>
                                        <th>Usuario_Registro</th>
                                        {{-- <th>Fecha_Registro</th> --}}
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $historialProducto = $historiales->where('codigoproducto', $bienes->codigo);
                                    @endphp
                                    @if ($historialProducto->count() > 0)
                                        @foreach ($historialProducto as $registro)
                                            <tr>
                                                <td>{{ $registro->id }}</td>
                                                <td>{{ $registro->fechamovimiento }}</td>
                                                <td>{{ $registro->nrofactura ?? 0 }}</td>
                                                <td>{{ $registro->nrorecibo ?? 0 }}</td>
                                                <td>{{ $registro->precio }}</td>
                                                <td>{{ $registro->cantidad }}</td>
                                                <td>{{ $registro->usuarioregistronombre }}</td>
                                                {{-- <td>{{ \Carbon\Carbon::parse($registro->created_at)->format('Y-m-d') }}</td> --}}
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="8" class="text-center">NO HAY ENTRADAS</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="historialsalidaModal{{$bienes->codigo}}" tabindex="-1" role="dialog" aria-labelledby="historialsalidaModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title font-weight-bold">HISTORIAL DE SALIDAS - {{$bienes->nombreproducto}} - {{$bienes->codigo}}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Fecha_Salida</th>
                                        <th>Cantidad</th>
                                        <th>Usuario_Receptor</th>
                                        <th>Usuario_Registro</th>
                                        {{-- <th>Fecha_Registro</th> --}}
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $historialProducto = $historialessalidas->where('codigoproducto', $bienes->codigo);
                                    @endphp
                                    @if ($historialProducto->count() > 0)
                                        @foreach ($historialProducto as $registro)
                                            <tr>
                                                <td>{{ $registro->id }}</td>
                                                <td>{{ $registro->fechamovimiento }}</td>
                                                <td>{{ $registro->cantidad }}</td>
                                                <td>{{ $registro->usuarioreceptor ?? '-' }}</td>
                                                <td>{{ $registro->usuarioregistronombre }}</td>
                                                {{-- <td>{{ \Carbon\Carbon::parse($registro->created_at)->format('Y-m-d') }}</td> --}}
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="8" class="text-center">NO HAY SALIDAS</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
@endforeach

@stop

@section('js')
<script>
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
        var codigo = button.data('codigo');

        $('#productoNombre').text(nombreproducto);
        $('#codigoProducto').text(codigo);
        $('#codigoEntrada').val(codigo);
        $('#idEntrada').val(button.data('id'));
    });

    $(document).ready(function () {
        $('#entradaModal').on('show.bs.modal', function (event) {
            let button = $(event.relatedTarget);
            let codigo = button.data('codigo');
            let nombre = button.data('nombre');
            $('#codigoProducto').text(codigo);
            $('#productoNombre').text(nombre);
            $('#codigoEntrada').val(codigo);
        });
    });

    $('#salidaModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget); 
        var id = button.data('id');
        var codigo = button.data('codigo'); 
        var modal = $(this);
        modal.find('#idSalida').val(id);
        modal.find('#codigoSalida').val(codigo);
    });

    $('#salidaModal').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget);
        var nombreproducto = button.data('nombreproducto');
        var codigo = button.data('codigo');
        $('#productoNombre2').text(nombreproducto);
        $('#codigoProducto2').text(codigo);
        $('#codigoSalida').val(codigo);
        $('#idSalida').val(button.data('id'));
    });

    $(document).ready(function () {
        $('#salidaModal').on('show.bs.modal', function (event) {
            let button = $(event.relatedTarget);
            let codigo = button.data('codigo');
            let nombre = button.data('nombre');
            $('#codigoProducto2').text(codigo);
            $('#productoNombre2').text(nombre);
            $('#codigoSalida').val(codigo);
        });
    });

</script>

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

<script>
async function actualizarStock(codigo, url) {
    const input = document.getElementById('stockactual_input_' + codigo);
    if (!input) return alert('Input no encontrado');
    const nuevoValor = input.value;

    // Validación simple
    if (nuevoValor === '' || isNaN(nuevoValor)) {
        return alert('Ingrese un número válido');
    }

    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    const formData = new FormData();
    formData.append('_method', 'PUT'); // si tu ruta espera PUT
    formData.append('_token', token);
    formData.append('stockactual', nuevoValor);

    try {
        const res = await fetch(url, {
            method: 'POST', // usamos POST + _method=PUT
            body: formData,
            credentials: 'same-origin',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        if (!res.ok) {
            const text = await res.text();
            console.error(text);
            return alert('Error al actualizar (ver consola)');
        }

        const data = await res.json().catch(()=>null);
        // Actualiza UI si lo deseas (por ejemplo mostrar toast o cambiar valor)
        alert('Stock actualizado correctamente');
        location.reload(); // 🔄 Recarga la página después de confirmar el alert
    } catch (err) {
        console.error(err);
        alert('Error de red al actualizar stock');
    }
}
</script>
@endsection