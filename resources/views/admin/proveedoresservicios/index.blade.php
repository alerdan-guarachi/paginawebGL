@extends('adminlte::page')
    
@section('content_header')
<a class="btn float-right btn-crear btn-sm" data-toggle="modal" data-target="#crearProductoModal">
    CREAR PROVEEDOR
</a>
<!-- MODAL CREAR PROVEEDOR -->
<div class="modal fade" id="crearProductoModal" tabindex="-1" aria-labelledby="crearProductoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title" id="crearProductoModalLabel" style="font-weight: 900;">CREAR PROVEEDOR</h1>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>                
            </div>
            <div class="modal-body text-center" style="margin-top: 30px; margin-bottom: 50px;">
                <div class="row">
                    <div class="col-12 col-md-4 mb-3 d-flex justify-content-center">
                        <button type="button" class="btn btn-custom" style="width: 80%;" onclick="redirectTo('general')">
                            <div class="d-flex flex-column align-items-center justify-content-center">
                                <i class="fas fa-street-view fa-5x mb-2"></i> 
                                <span class="h5 mb-0">GENERAL</span>
                            </div>
                        </button>
                    </div>
                    <div class="col-12 col-md-4 mb-3 d-flex justify-content-center">
                        <button type="button" class="btn btn-custom" style="width: 80%;" onclick="redirectTo('serviciobasico')">
                            <div class="d-flex flex-column align-items-center justify-content-center">
                                <i class="fas fa-plug fa-5x mb-2"></i>
                                <span class="h5 mb-0">SERVICIO BÁSICO</span>
                            </div>
                        </button>
                    </div>
                    <div class="col-12 col-md-4 mb-3 d-flex justify-content-center">
                        <button type="button" class="btn btn-custom" style="width: 80%;" onclick="redirectTo('serviciohumano')">
                            <div class="d-flex flex-column align-items-center justify-content-center">
                                <i class="fas fa-hands-helping fa-5x mb-2"></i>
                                <span class="h5 mb-0">SERVICIO HUMANO</span>
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
        if (type === 'general') {
            window.location.href = "{{ route('admin.proveedoresservicios.create') }}";
        } else if (type === 'serviciobasico') {
            window.location.href = "{{ route('admin.proveedoresservicios.crearprovserviciobasico') }}";
        } else if (type === 'serviciohumano') {
            window.location.href = "{{ route('admin.proveedoresservicios.crearprovserviciohumano') }}";
        }
    }
</script>
<h1>PROVEEDORES DE SERVICIOS</h1>
@stop

@section('css')
{{-- <link rel="stylesheet" href="{{ asset('css/proveedoresservicios.css') }}"> --}}
<style>
    h1, th {
        color:#94c93b; 
        font-family: "Segoe UI";
        font-weight: 900;
        }
    .btn-verperfil {
        background-color:  #ffffff;
        color: #94c93b;
        border-color: #94c93b;
        border-radius: 5px;
        padding: 2px 6px;
        }
    .btn-verperfil:hover {
        background-color: #94c93b;
        color: #ffffff;
        }
    .btn-verinventario {
        background-color:  #ffffff;
        color: #faa625;
        border-color: #faa625;
        border-radius: 5px;
        padding: 2px 9px;
        }
    .btn-verinventario:hover {
        background-color: #faa625;
        color: #ffffff;
        }
    .btn-crear {
        background-color:  #ffffff;
        color: #94c93b;
        border-color: #94c93b;
        border-radius: 5px;
        padding: 5px 10px;
        }
    .btn-crear:hover {
        background-color: #94c93b;
        color: #ffffff;
        }
    .btn-regresar {
        background-color: #ffffff;
        color: #2926e2;
        border-color: #2926e2;
        border-radius: 5px;
        padding: 5px 10px;
        }
    .btn-regresar:hover {
        background-color: #2926e2;
        color: #ffffff;
        }
    .nav-tabs {
        display: flex;
        justify-content: space-between;
    }
    .nav-tabs .nav-item {
        flex: 1;
    }
    .nav-tabs .nav-link {
        display: block;
        text-align: center;
        width: 100%;
        font-weight: bold;
        font-size: 14px;
        color: #faa625;
        background-color: #fef4e7;
    }
    .nav-tabs .nav-link.active {
        font-weight: bold;
        font-size: 14px;
        color: #94c93b;
        background-color: #ffffff;
    }
    .circle {
        display: inline-block;
        width: 20px;
        height: 20px;
        line-height: 20px;
        border-radius: 50%;
        text-align: center;
        font-size: 14px;
        font-weight: bold;
        margin-left: 8px;
    }
    .nav-link.active .circle {
        background-color: #94c93b;
        color: #fff;
    }
    .nav-link .circle {
        background-color: #faa625;
        color: #fff;
    }
    .btn-buscar {
        background-color: #ffffff;
        color: #faa625;
        border-color: #faa625;
        border-radius: 5px;
        padding: 2px 10px;
        }
    .btn-buscar:hover {
        background-color: #faa625;
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
        {{-- PERFIL USUARIO AUTENTICADO --}}
        <p style="margin-bottom: -7px; margin-left: 10px; font-weight: bold;">TU PERFIL</p>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th style="width: 10%;">ID</th>
                    <th style="width: 30%;">Proveedor</th>
                    <th style="width: 20%;">CI</th>
                    <th style="width: 20%;">Sucursal</th>
                    <th style="width: 10%;">Estado</th>
                    <th style="width: 10%;"></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($soloproveedorservicio as $proveedoresservicios)
                    <tr>
                        <td>{{$proveedoresservicios->id}}</td>
                        <td>{{$proveedoresservicios->razonsocial}}</td>
                        <td>{{$proveedoresservicios->ci}}</td>
                        <td>{{$proveedoresservicios->sucursal}}</td>
                        <td>
                            @if ($proveedoresservicios->estado == 'ACTIVO')
                                <span class="badge badge-success">{{ $proveedoresservicios->estado }}</span>
                            @else
                                <span class="badge badge-danger">{{ $proveedoresservicios->estado }}</span>
                            @endif
                        </td>
                        <td width="10px">
                            @can('admin.proveedoresservicios.index')
                                <a class="btn btn-sm btn-verperfil fas fa-eye" href="{{route('admin.proveedoresservicios.show', $proveedoresservicios)}}" title="VER PROVEEDOR"></a>
                            @endcan

                            <a class="btn btn-sm btn-verinventario" 
                                data-toggle="modal" 
                                data-target="#modalInventarios" 
                                data-id="{{ $proveedoresservicios->id }}" 
                                title="VER INVENTARIOS">
                                <i class="fas fa-clipboard-list"></i>
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{-- BUSCADOR --}}
        <div class="card-header d-flex">
            <p style="margin-top: 20px; margin-left: -10px; font-weight: bold;">TODOS LOS PROVEEDORES</p>
            <form class="form-inline">
                <div class="input-group" style="margin-left: 20px;">
                    <input name="buscarpor" class="form-control" type="search" placeholder="NOMBRE DEL PROVEEDOR" aria-label="Buscar proveedor">
                    <div class="input-group-append">
                        <button class="btn btn-buscar" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
        
        {{-- PESTAÑAS PROVEEDORES --}}
        <div class="card-header">
            <ul class="nav nav-tabs card-header-tabs" id="myTabs">
                <li class="nav-item">
                    <a class="nav-link active" id="tab-1" data-toggle="tab" href="#tab-content-1" role="tab" aria-controls="tab-content-1" aria-selected="true">
                        GENERALES
                    </a>
                </li>     
                <li class="nav-item">
                    <a class="nav-link" id="tab-2" data-toggle="tab" href="#tab-content-2" role="tab" aria-controls="tab-content-2" aria-selected="true">
                        SERVICIOS BASICOS
                    </a>
                </li> 
                <li class="nav-item">
                    <a class="nav-link" id="tab-3" data-toggle="tab" href="#tab-content-3" role="tab" aria-controls="tab-content-3" aria-selected="true">
                        SERVICIOS HUMANOS
                    </a>
                </li>     
            </ul>
        </div>

        
        <div class="tab-content" id="myTabContent">
            {{-- PROVEEDORES GENERALES --}}
            <div class="tab-pane fade show active" id="tab-content-1" role="tabpanel" aria-labelledby="tab-1">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th style="width: 10%;">ID</th>
                                <th style="width: 30%;">Proveedor</th>
                                <th style="width: 20%;">CI</th>
                                <th style="width: 20%;">Sucursal</th>
                                <th style="width: 10%;">Estado</th>
                                <th style="width: 10%;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($todosproveedoresservicios as $proveedoresservicios)
                                @if ($proveedoresservicios->categoria === 'PROVEEDOR GENERAL')
                                    <tr>
                                        <td>{{$proveedoresservicios->id}}</td>
                                        <td>{{$proveedoresservicios->razonsocial}}</td>
                                        <td>{{$proveedoresservicios->ci}}</td>
                                        <td>{{$proveedoresservicios->sucursal}}</td>
                                        <td>
                                            @if ($proveedoresservicios->estado == 'ACTIVO')
                                                <span class="badge badge-success">{{ $proveedoresservicios->estado }}</span>
                                            @else
                                                <span class="badge badge-danger">{{ $proveedoresservicios->estado }}</span>
                                            @endif
                                        </td>
                                        <td width="10px">
                                            @can('admin.proveedoresservicios.show')
                                                <a class="btn btn-sm btn-verperfil fas fa-eye" href="{{route('admin.proveedoresservicios.show', $proveedoresservicios)}}" title="VER PROVEEDOR"></a>
                                            @endcan

                                            <a class="btn btn-sm btn-verinventario" 
                                               data-toggle="modal" 
                                               data-target="#modalInventarios" 
                                               data-id="{{ $proveedoresservicios->id }}" 
                                               title="VER INVENTARIOS">
                                               <i class="fas fa-clipboard-list"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- PROVEEDORES SERVICIOS BASICOS --}}
            <div class="tab-pane fade" id="tab-content-2" role="tabpanel" aria-labelledby="tab-2">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th style="width: 10%;">ID</th>
                                <th style="width: 30%;">Proveedor</th>
                                <th style="width: 20%;">CI</th>
                                <th style="width: 20%;">Sucursal</th>
                                <th style="width: 10%;">Estado</th>
                                <th style="width: 10%;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($todosproveedoresservicios as $proveedoresservicios)
                                @if ($proveedoresservicios->categoria === 'PROVEEDOR SERVICIO BASICO')
                                    <tr>
                                        <td>{{$proveedoresservicios->id}}</td>
                                        <td>{{$proveedoresservicios->razonsocial}}</td>
                                        <td>{{$proveedoresservicios->ci}}</td>
                                        <td>{{$proveedoresservicios->sucursal}}</td>
                                        <td>
                                            @if ($proveedoresservicios->estado == 'ACTIVO')
                                                <span class="badge badge-success">{{ $proveedoresservicios->estado }}</span>
                                            @else
                                                <span class="badge badge-danger">{{ $proveedoresservicios->estado }}</span>
                                            @endif
                                        </td>
                                        <td width="10px">
                                            @can('admin.proveedoresservicios.show')
                                                <a class="btn btn-sm btn-verperfil fas fa-eye" href="{{route('admin.proveedoresservicios.show', $proveedoresservicios)}}" title="VER PROVEEDOR"></a>
                                            @endcan

                                            <a class="btn btn-sm btn-verinventario" 
                                               data-toggle="modal" 
                                               data-target="#modalInventarios" 
                                               data-id="{{ $proveedoresservicios->id }}" 
                                               title="VER INVENTARIOS">
                                               <i class="fas fa-clipboard-list"></i>
                                            </a>
                                        </td>
                                        
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- PROVEEDORES SERVICIOS HUMANOS --}}
            <div class="tab-pane fade" id="tab-content-3" role="tabpanel" aria-labelledby="tab-3">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th style="width: 10%;">ID</th>
                                <th style="width: 30%;">Proveedor</th>
                                <th style="width: 20%;">CI</th>
                                <th style="width: 20%;">Sucursal</th>
                                <th style="width: 10%;">Estado</th>
                                <th style="width: 10%;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($todosproveedoresservicios as $proveedoresservicios)
                                @if ($proveedoresservicios->categoria === 'PROVEEDOR SERVICIO HUMANO')
                                    <tr>
                                        <td>{{$proveedoresservicios->id}}</td>
                                        <td>{{$proveedoresservicios->razonsocial}}</td>
                                        <td>{{$proveedoresservicios->ci}}</td>
                                        <td>{{$proveedoresservicios->sucursal}}</td>
                                        <td>
                                            @if ($proveedoresservicios->estado == 'ACTIVO')
                                                <span class="badge badge-success">{{ $proveedoresservicios->estado }}</span>
                                            @else
                                                <span class="badge badge-danger">{{ $proveedoresservicios->estado }}</span>
                                            @endif
                                        </td>
                                        <td width="10px">
                                            @can('admin.proveedoresservicios.show')
                                                <a class="btn btn-sm btn-verperfil fas fa-eye" href="{{route('admin.proveedoresservicios.show', $proveedoresservicios)}}" title="VER PROVEEDOR"></a>
                                            @endcan

                                            <a class="btn btn-sm btn-verinventario" 
                                               data-toggle="modal" 
                                               data-target="#modalInventarios" 
                                               data-id="{{ $proveedoresservicios->id }}" 
                                               title="VER INVENTARIOS">
                                               <i class="fas fa-clipboard-list"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>      

<div class="modal fade" id="modalInventarios" tabindex="-1" aria-labelledby="modalInventariosLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalInventariosLabel" style="font-weight: 900">BATERIA E INVENTARIO DEL PROVEEDOR</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>         
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-12"> 
                        <div class="table-responsive">
                            <table class="table">
                                <tbody id="modalproveedorserviciosBody">
                                </tbody>
                            </table>
                        </div>
                    </div>
                    

                    <div class="col-lg-12">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Producto</th>
                                        <th>Marca</th>
                                        <th>Precio</th>
                                    </tr>
                                </thead>
                                <tbody id="modalInventariosBody">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script> 
    document.addEventListener("DOMContentLoaded", function() {
        const inventarios = @json($inventarios); // Convertimos el inventario a JSON
        const bateriaproveedorservicios = @json($bateriaproveedorservicios); // Convertimos el inventario a JSON
    
        document.querySelectorAll('[data-target="#modalInventarios"]').forEach(button => {
            button.addEventListener("click", function() {
                const proveedorId = this.getAttribute("data-id");
                const modalBodyInventarios = document.getElementById("modalInventariosBody");
                const modalBodyProveedorServicios = document.getElementById("modalproveedorserviciosBody");
    
                // Limpiar contenido previo
                modalBodyInventarios.innerHTML = "";
                modalBodyProveedorServicios.innerHTML = "";

                // Filtrar los inventarios correspondientes al proveedor
                const inventarioFiltrado = inventarios.filter(item => item.proveedorid == proveedorId);
    
                if (inventarioFiltrado.length > 0) {
                    inventarioFiltrado.forEach(item => {
                        let row = `<tr>
                            <td>${item.id}</td>
                            <td>${item.nombreproducto}</td>
                            <td>${item.marca}</td>
                            <td>${item.precio}</td>
                        </tr>`;
                        modalBodyInventarios.innerHTML += row;
                    });
                } else {
                    modalBodyInventarios.innerHTML = `<tr><td colspan="3" class="text-center">NO HAY REGISTROS PARA ESTE PROVEEDOR</td></tr>`;
                }

                // Filtrar los proveedores de servicios correspondientes
                const proveedorServicioFiltrado = bateriaproveedorservicios.filter(item => item.proveedorid == proveedorId);
    
                if (proveedorServicioFiltrado.length > 0) {
                    // Variable para añadir las filas dinámicamente
                    let rows = "";
                    
                    // Variables de los títulos para cada columna
                    let compraProducto = [];
                    let compraServicio = [];
                    let ventaProducto = [];

                    // Recorrer los registros filtrados
                    proveedorServicioFiltrado.forEach(item => {
                        if (item.compraproducto) compraProducto.push(item.compraproducto);
                        if (item.compraservicio) compraServicio.push(item.compraservicio);
                        if (item.ventaproducto) ventaProducto.push(item.ventaproducto);
                    });

                    // Agregar título "Compra Producto" si existen valores
                    if (compraProducto.length > 0) {
                        rows += `<tr><td colspan="4"><strong>Adquisición de Productos:</strong> ${compraProducto.join(", ")}</td></tr>`;
                    }

                    // Agregar título "Compra Servicio" si existen valores
                    if (compraServicio.length > 0) {
                        rows += `<tr><td colspan="4"><strong>Adquisición de Servicios:</strong> ${compraServicio.join(", ")}</td></tr>`;
                    }

                    // Agregar título "Venta Producto" si existen valores
                    if (ventaProducto.length > 0) {
                        rows += `<tr><td colspan="4"><strong>Venta de Productos:</strong> ${ventaProducto.join(", ")}</td></tr>`;
                    }

                    // Insertar las filas en el cuerpo de la tabla
                    modalBodyProveedorServicios.innerHTML = rows;
                } else {
                    modalBodyProveedorServicios.innerHTML = `<tr><td colspan="4" class="text-center">NO HAY PRODUCTOS DE SERVICIO PARA ESTE PROVEEDOR</td></tr>`;
                }
            });
        });
    });
</script>


@stop

@section('js')
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script type="text/javascript" src="https://jeremyfagis.github.io/dropify/dist/js/dropify.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://jeremyfagis.github.io/dropify/dist/css/dropify.min.css"> 
<script>
$('.dropify').dropify();
</script>
    @if (session('eliminar')=='ok')
    <script>
        Swal.fire(
      '¡Eliminado!',
      'El perfil se eliminó con éxito',
      'success')
    </script>
    @endif

<script>
    $('.formulario-eliminar').submit(function(e){
        e.preventDefault();

        Swal.fire({
        title: '¿Estás seguro?',
        text: "Este perfil se eliminará definitivamente",
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