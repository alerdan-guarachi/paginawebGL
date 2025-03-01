@extends('adminlte::page')

@section('content_header')
<a class="btn float-right btn-outline-secondary" data-toggle="modal" data-target="#crearProductoModal">
    CREAR PRODUCTO
</a>

<!-- Modal -->
<div class="modal fade" id="crearProductoModal" tabindex="-1" aria-labelledby="crearProductoModalLabel" aria-hidden="true">
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
<h1>ADMINISTRACION DE INVENTARIO</h1>
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
        <form class="form-inline">
            <input name="buscarpor" class="form-control mr-sm-2" type="search" placeholder="Nombre del producto" aria-label="Search">
            <button class="btn btn-outline-secondary my-2 my-sm-0" type="submit">Buscar</button>
        </form>
    </nav>
        

        <!-- Botones -->
    {{-- <div id="buttons-container" class="text-center card-body card">
        <h2 style="margin-top: 30px; margin-bottom: 30px; font-weight: 900; font-size:25px;">ELIGE UN TIPO DE BIENES</h2>
        <div class="row">
            <div class="col-12 col-md-6 mb-3 d-flex justify-content-center">
                <button type="button" class="btn btn-custom" style="width: 80%;" onclick="showForm('almacen')">
                    <div class="d-flex flex-column align-items-center justify-content-center">
                        <i class="fas fa-warehouse fa-5x mb-2"></i> 
                        <span class="h5 mb-0">ALMACEN</span>
                    </div>
                </button>
            </div>
            <div class="col-12 col-md-6 mb-3 d-flex justify-content-center">
                <button type="button" class="btn btn-custom" style="width: 80%;" onclick="showForm('activosfijos')">
                    <div class="d-flex flex-column align-items-center justify-content-center">
                        <i class="fas fa-desktop fa-5x mb-2"></i>
                        <span class="h5 mb-0">ACTIVOS FIJOS</span>
                    </div>
                </button>
            </div>
        </div>
    </div> --}}

    <div class="card-header">
        <ul class="nav nav-tabs card-header-tabs" id="myTabs">
            <li class="nav-item">
                <a class="nav-link active" id="tab-1" data-toggle="tab" href="#tab-content-1" role="tab" aria-controls="tab-content-1" aria-selected="true">
                    ALMACEN
                </a>
            </li>     
            <li class="nav-item">
                <a class="nav-link" id="tab-2" data-toggle="tab" href="#tab-content-2" role="tab" aria-controls="tab-content-2" aria-selected="true">
                    ACTIVOS FIJOS
                </a>
            </li>     
        </ul>
    </div>
    <div class="card-body">
        <div class="tab-content" id="myTabContent">
            {{-- ALMACEN --}}
            <div class="tab-pane fade show active" id="tab-content-1" role="tabpanel" aria-labelledby="tab-1">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                {{-- <th>ID</th> --}}
                                <th>Cod.Producto</th>
                                <th>Producto</th>
                                <th>Mat.prima</th>
                                <th>Esp.medida</th>
                                <th>Color</th>
                                <th>Marca</th>
                                <th>U.medida</th>
                                <th>Stock_ini.</th>
                                <th>Stock_act.</th>
                                <th>Inventario</th>
                                <th>Depósito</th>
                                <th>Sección</th>
                                <th>Precio</th>
                                <th>Entrada</th>
                                <th>Registros</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($bienesalmacen as $bienes)
                                <tr>
                                    {{-- <td>{{$bienes->id}}</td> --}}
                                    <td>{{$bienes->codigo}}</td>
                                    <td>{{$bienes->nombreproducto}}</td>
                                    <td>{{$bienes->materiaprima}}</td>
                                    <td>{{$bienes->especificacionmedida}}</td>
                                    <td>{{$bienes->color}}</td>
                                    <td>{{$bienes->marca}}</td>
                                    <td>{{$bienes->unidadmedida}}</td>
                                    <td>{{$bienes->stockinicial}}</td>
                                    <td>{{$bienes->stockactual}}</td>
                                    <td>{{$bienes->inventario}}</td>
                                    <td>{{$bienes->deposito}}</td>
                                    <td>{{$bienes->seccion}}</td>
                                    <td>{{$bienes->precio}}</td>
                                    <td class="d-flex justify-content-start">
                                        <button type="button" class="btn btn-outline-success mr-2 btn-sm" data-toggle="modal" data-target="#entradaModal" title="REGISTRAR ENTRADA" data-id="{{$bienes->id}}" data-codigo="{{$bienes->codigo}}" data-nombreproducto="{{$bienes->nombreproducto}}">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                        {{-- <button type="button" class="btn btn-outline-danger mr-2 btn-sm" data-toggle="modal" data-target="#salidaModal" title="REGISTRAR SALIDA" data-id="{{$bienes->id}}" data-codigo="{{$bienes->codigo}}" data-nombreproducto="{{$bienes->nombreproducto}}">
                                            <i class="fas fa-minus"></i>
                                        </button> --}}
                                    </td>
                                    <td class="justify-content-start">
                                        <button type="button" class="btn btn-outline-secondary btn-sm" data-toggle="modal" data-target="#historialModal{{$bienes->codigo}}" title="VER HISTORIAL ENTRADAS">
                                            <i class="fas fa-arrow-down"></i>
                                        </button>
                                        <button type="button" class="btn btn-outline-secondary btn-sm" data-toggle="modal" data-target="#historialsalidaModal{{$bienes->codigo}}" title="VER HISTORIAL SALIDAS">
                                            <i class="fas fa-arrow-up"></i> 
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody> 
                    </table>
                </div>
            </div>

            {{-- ACTIVOS FIJOS --}}
            <div class="tab-pane fade" id="tab-content-2" role="tabpanel" aria-labelledby="tab-2">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                {{-- <th>ID</th> --}}
                                <th>Cod.Producto</th>
                                <th>Producto</th>
                                <th>Mat.prima</th>
                                <th>Esp.medida</th>
                                <th>Color</th>
                                <th>Marca</th>
                                <th>Modelo</th>
                                <th>Serie</th>
                                <th>Sección</th>
                                <th>Uni.medida</th>
                                <th>Stock_ini.</th>
                                <th>Stock_act.</th>
                                <th>Precio</th>
                                <th>Entrada</th>
                                <th>Registros</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($bienesactivosfijos as $bienes)
                                <tr>
                                    {{-- <td>{{$bienes->id}}</td> --}}
                                    <td>{{$bienes->codigo}}</td>
                                    <td>{{$bienes->nombreproducto}}</td>
                                    <td>{{$bienes->materiaprima}}</td>
                                    <td>{{$bienes->especificacionmedida}}</td>
                                    <td>{{$bienes->color}}</td>
                                    <td>{{$bienes->marca}}</td>
                                    <td>{{$bienes->modelo}}</td>
                                    <td>{{$bienes->serie}}</td>
                                    <td>{{$bienes->seccion}}</td>
                                    <td>{{$bienes->unidadmedida}}</td>
                                    <td>{{$bienes->stockinicial}}</td>
                                    <td>{{$bienes->stockactual}}</td>
                                    <td>{{$bienes->precio}}</td>
                                    <td class="d-flex justify-content-start">
                                        <button type="button" class="btn btn-outline-success mr-2 btn-sm" data-toggle="modal" data-target="#entradaModal" title="REGISTRAR ENTRADA" data-id="{{$bienes->id}}" data-codigo="{{$bienes->codigo}}" data-nombreproducto="{{$bienes->nombreproducto}}">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                        {{-- <button type="button" class="btn btn-outline-danger mr-2 btn-sm" data-toggle="modal" data-target="#salidaModal" title="REGISTRAR SALIDA" data-id="{{$bienes->id}}" data-codigo="{{$bienes->codigo}}" data-nombreproducto="{{$bienes->nombreproducto}}">
                                            <i class="fas fa-minus"></i>
                                        </button> --}}
                                    </td>
                                    <td class="justify-content-start">
                                        <button type="button" class="btn btn-outline-secondary btn-sm" data-toggle="modal" data-target="#historialModal{{$bienes->codigo}}" title="VER HISTORIAL ENTRADAS">
                                            <i class="fas fa-arrow-down"></i>
                                        </button>
                                        <button type="button" class="btn btn-outline-secondary btn-sm" data-toggle="modal" data-target="#historialsalidaModal{{$bienes->codigo}}" title="VER HISTORIAL SALIDAS">
                                            <i class="fas fa-arrow-up"></i> 
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody> 
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

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
                                        <th>Fecha Entrada</th>
                                        <th>Nro. Factura</th>
                                        <th>Nro. Recibo</th>
                                        <th>Precio</th>
                                        <th>Cantidad</th>
                                        <th>Usuario Registro</th>
                                        <th>Fecha Registro</th>
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
                                                <td>{{ $registro->nrofactura }}</td>
                                                <td>{{ $registro->nrorecibo }}</td>
                                                <td>{{ $registro->precio }} Bs.</td>
                                                <td>{{ $registro->cantidad }}</td>
                                                <td>{{ $registro->usuarioregistronombre }}</td>
                                                <td>{{ $registro->created_at }}</td>
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
                                        <th>Fecha Salida</th>
                                        <th>Cantidad</th>
                                        <th>Usuario Receptor</th>
                                        <th>Usuario Registro</th>
                                        <th>Fecha Registro</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $historialProducto = $historialessalidas->where('codigoproducto', $bienes->codigo);
                                    @endphp
                                    @if ($historialProducto->count() > 0)
                                        @foreach ($historialProducto as $registro)
                                            <tr>
                                                <td>{{ $registro->fechamovimiento }}</td>
                                                <td>{{ $registro->cantidad }}</td>
                                                <td>{{ $registro->usuarioreceptor ?? '-' }}</td>
                                                <td>{{ $registro->usuarioregistronombre }}</td>
                                                <td>{{ $registro->created_at }}</td>
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
    // Al abrir el modal de entrada
    $('#entradaModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget); 
        var id = button.data('id');
        var codigo = button.data('codigo'); 

        // Asignar los valores al modal
        var modal = $(this);
        modal.find('#idEntrada').val(id);
        modal.find('#codigoEntrada').val(codigo);
    });

    $('#entradaModal').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget); // Botón que disparó el modal
        var nombreproducto = button.data('nombreproducto'); // Extrae el nombre del producto
        var codigo = button.data('codigo'); // Extrae el código del producto

        // Coloca los valores en los campos del modal
        $('#productoNombre').text(nombreproducto);
        $('#codigoProducto').text(codigo);
        $('#codigoEntrada').val(codigo); // Asigna el código al campo oculto
        $('#idEntrada').val(button.data('id')); // Asigna el id del producto
    });

    $(document).ready(function () {
        // Asegúrate de llenar el campo oculto cuando el modal se abre
        $('#entradaModal').on('show.bs.modal', function (event) {
            let button = $(event.relatedTarget); // Botón que abrió el modal
            let codigo = button.data('codigo'); // Obtiene el código del producto desde un atributo data
            let nombre = button.data('nombre'); // Obtiene el nombre del producto

            $('#codigoProducto').text(codigo);
            $('#productoNombre').text(nombre);
            $('#codigoEntrada').val(codigo); // Guarda el código en el campo oculto
        });
    });
    
    // Al abrir el modal de salida
    $('#salidaModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget); 
        var id = button.data('id');
        var codigo = button.data('codigo'); 

        // Asignar los valores al modal
        var modal = $(this);
        modal.find('#idSalida').val(id);
        modal.find('#codigoSalida').val(codigo);
    });

    $('#salidaModal').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget); // Botón que disparó el modal
        var nombreproducto = button.data('nombreproducto'); // Extrae el nombre del producto
        var codigo = button.data('codigo'); // Extrae el código del producto

        // Coloca los valores en los campos del modal
        $('#productoNombre2').text(nombreproducto);
        $('#codigoProducto2').text(codigo);
        $('#codigoSalida').val(codigo); // Asigna el código al campo oculto
        $('#idSalida').val(button.data('id')); // Asigna el id del producto
    });

    $(document).ready(function () {
        // Asegúrate de llenar el campo oculto cuando el modal se abre
        $('#salidaModal').on('show.bs.modal', function (event) {
            let button = $(event.relatedTarget); // Botón que abrió el modal
            let codigo = button.data('codigo'); // Obtiene el código del producto desde un atributo data
            let nombre = button.data('nombre'); // Obtiene el nombre del producto

            $('#codigoProducto2').text(codigo);
            $('#productoNombre2').text(nombre);
            $('#codigoSalida').val(codigo); // Guarda el código en el campo oculto
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
@endsection