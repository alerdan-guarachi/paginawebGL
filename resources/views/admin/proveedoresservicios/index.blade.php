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
    .btn-agregar {
        background-color:  #ffffff;
        color: #faa625;
        border-color: #faa625;
        border-radius: 5px;
        padding: 5px 10px;
        }
    .btn-agregar:hover {
        background-color: #faa625;
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
    .btn-custom {
        background-color:  #ffffff;
        color: #94c93b;
        border-color: #94c93b;
        border-radius: 20px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        transition: background-color 0.3s, transform 0.3s, box-shadow 0.3s;
        height: 200px;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0;
    }
    .btn-custom:hover {
        background-color: #94c93b;
        color: #ffffff;
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        transform: scale(1.15);
    }
    .btn-custom:disabled {
        background-color: #d6d6d6;
        color: #a0a0a0;
        cursor: not-allowed;
    }

</style>
<style>
    table {
        white-space: nowrap;
    }
    .table td {
        padding: 5px;
    }
    .table tbody tr:nth-child(odd) {
        background-color: #f2f2f2;
    }

</style>
<style>
    .color-box {
        width: 35px;
        height: 35px;
        margin: 5px;
        border: 2px solid #ddd;
        cursor: pointer;
        transition: transform 0.2s;
    }
    .color-box:hover {
        transform: scale(1.5);
    }
    .selected {
        border: 3px solid black !important;
    }
    .color-selector {
        width: 50px;
        height: 38px;
        border: 2px solid #ddd;
        text-align: center;
        padding: 0;
        font-size: 1.2rem;
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
                                data-nombre="{{ $proveedoresservicios->razonsocial }}"
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
                                               data-nombre="{{ $proveedoresservicios->razonsocial }}"
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
                                               data-nombre="{{ $proveedoresservicios->razonsocial }}"
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
                                               data-nombre="{{ $proveedoresservicios->razonsocial }}"
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
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalInventariosLabel" style="font-weight: 900">PORTAFOLIO DEL PROVEEDOR: <span id="modalProveedorNombre"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>         
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-12" hidden> 
                        <div class="table-responsive">
                            <table class="table">
                                <tbody id="modalproveedorserviciosBody">
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="form-group" style="margin-top: -15px; margin-bottom: 0px;">
                        <a class="btn btn-agregar btn-sm" id="btnAgregarRegistro">
                            AGREGAR NUEVO
                        </a>
                    </div>
                    
                    <div id="formNuevoRegistro" style="display: none;">
                        <div class="card">
                            <div class="card-body" style="background-color: #f5f5f5">
                                {!! Form::open(['route' => 'admin.proveedoresservicios.guardarnuevoproducto', 'method'=>'POST']) !!}
                                    <div class="row">
                                        <input type="hidden" id="proveedorid" name="proveedorid">
                                        <input type="hidden" id="proveedornombre" name="proveedornombre">
                                        <div class="col-md-2 form-group">
                                            <label for="ciudad">Ciudad</label>
                                            <select id="ciudad" name="ciudad" class="form-control">
                                                <option value="" disabled selected></option>
                                                <option value="SANTA CRUZ">SANTA CRUZ</option>
                                                <option value="COCHABAMBA">COCHABAMBA</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2 form-group">
                                            <label for="tipoinventario">Tipo Inventario</label>
                                            <select name="tipo_inventario" id="tipo_inventario"  class="form-control">
                                                <option value="" disabled selected></option>
                                                <option value="ALMACEN">ALMACEN</option>
                                                <option value="ACTIVO FIJO">ACTIVO FIJO</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2 form-group">
                                            <label for="seccion">Sección</label>
                                            <select name="seccion" id="seccion" class="form-control">
                                                <option value="" disabled selected></option>
                                            </select>
                                        </div>
                                        <div class="col-md-2 form-group">
                                            <label>Producto</label>
                                            <input type="text" class="form-control" id="nombreproducto" name="nombreproducto" required>
                                        </div>
                                        <div class="col-md-2 form-group">
                                            <label for="materiaprima">Mat. Prima</label>
                                            <select name="materia_prima" id="materia_prima" class="form-control">
                                                <option value="" disabled selected></option>
                                            </select>
                                        </div>
                                        <div class="col-md-2 form-group">
                                            <label>Especif. Medida</label>
                                            <input type="text" class="form-control" id="especificacionmedida" name="especificacionmedida">
                                        </div>
                                        <div class="form-group col-md-2">
                                            {!! Form::label('color', 'Color:') !!}
                                            <div class="d-flex align-items-center">
                                                {!! Form::text('color', null, ['class' => 'form-control', 'id' => 'colorInput', 'readonly' => 'readonly', 'placeholder' => '']) !!}
                                                <div class="dropdown ml-2">
                                                    <button class="btn btn-secondary dropdown-toggle color-selector" type="button" id="colorDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                        ▼
                                                    </button>
                                                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="colorDropdown">
                                                        <div class="d-flex flex-wrap">
                                                            <div class="col-4 p-1">
                                                                <div class="color-box" data-color="#000000"></div>
                                                                <div class="color-box" data-color="#FFFFFF"></div>
                                                                <div class="color-box" data-color="#FF0000"></div>
                                                                <div class="color-box" data-color="#008000"></div>
                                                                <div class="color-box" data-color="#0000FF"></div>
                                                            </div>
                                                            <div class="col-4 p-1">
                                                                <div class="color-box" data-color="#FFFF00"></div>
                                                                <div class="color-box" data-color="#FFA500"></div>
                                                                <div class="color-box" data-color="#800080"></div>
                                                                <div class="color-box" data-color="#FFC0CB"></div>
                                                                <div class="color-box" data-color="#808080"></div>
                                                            </div>
                                                            <div class="col-4 p-1">
                                                                <div class="color-box" data-color="#A52A2A"></div>
                                                                <div class="color-box" data-color="#00BFFF"></div>
                                                                <div class="color-box" data-color="#FFD700"></div>
                                                                <div class="color-box" data-color="#008B8B"></div>
                                                                <div class="color-box" data-color="#B22222"></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            @error('color')
                                            <small class="text-danger fas fa-exclamation-circle">
                                                {{$message}}
                                            </small>
                                            @enderror
                                        </div>
                                        <div class="col-md-2 form-group">
                                            <label for="marca">Marca</label>
                                            <select name="marca" id="marca" class="form-control">
                                                <option value="" disabled selected></option>
                                            </select>
                                        </div>
                                        <div class="col-md-2 form-group">
                                            <label for="unidadmedida">Unidad Medida</label>
                                            <select name="unidad_medida" id="unidad_medida" class="form-control">
                                                <option value="" disabled selected></option>
                                            </select>
                                        </div>
                                        <div class="col-md-1 form-group">
                                            <label>Present.</label>
                                            <input type="text" class="form-control" id="presentacion" name="presentacion">
                                        </div>
                                        <div class="col-md-1 form-group">
                                            <label>Unidades</label>
                                            <input type="number" class="form-control" id="unidades" name="unidades" required>
                                        </div>
                                        <div class="col-md-1 form-group">
                                            <label>Cantidad</label>
                                            <input type="number" class="form-control" id="cantidad" name="cantidad" required>
                                        </div>
                                        <div class="col-md-1 form-group">
                                            <label>Precio</label>
                                            <input type="number" step="0.01" class="form-control" id="precio" name="precio" required>
                                        </div>
                                        <div class="col-md-2 form-group">
                                            <label>Modelo</label>
                                            <input type="text" class="form-control" name="modelo" id="modelo" placeholder="" disabled>
                                        </div>
                                    </div>
                                    {!! Form::submit('REGISTRAR PRODUCTO', ['class' => ' float-right btn btn-sm btn-crear']) !!}
                                {!! Form::close() !!}
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <div class="col-lg-12">
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Cód.</th>
                                                <th>Producto</th>
                                                <th>Tipo_Inv.</th>
                                                <th>Sección</th>
                                                <th>M_Prima</th>
                                                <th>Especif.</th>
                                                <th>Color</th>
                                                <th>Marca</th>
                                                <th>Cant.</th>
                                                <th>Precio</th>
                                                <th>Ciudad</th>
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
    </div>
</div>
<script> 
    document.addEventListener("DOMContentLoaded", function() {
        const inventarios = @json($inventarios);
        const bateriaproveedorservicios = @json($bateriaproveedorservicios);
    
        document.querySelectorAll('[data-target="#modalInventarios"]').forEach(button => {
            button.addEventListener("click", function() {
                const proveedorId = this.getAttribute("data-id");

                const proveedorNombre = this.getAttribute("data-nombre");
            
                document.getElementById("modalProveedorNombre").textContent = proveedorNombre;

                document.getElementById("proveedorid").value = proveedorId;
                document.getElementById("proveedornombre").value = proveedorNombre;

                const modalBodyInventarios = document.getElementById("modalInventariosBody");
                const modalBodyProveedorServicios = document.getElementById("modalproveedorserviciosBody");

                modalBodyInventarios.innerHTML = "";
                modalBodyProveedorServicios.innerHTML = "";
                const inventarioFiltrado = inventarios.filter(item => item.proveedorid == proveedorId);
    
                if (inventarioFiltrado.length > 0) {
                    inventarioFiltrado.forEach(item => {
                        let row = `<tr>
                            <td>${item.id}</td>
                            <td>${item.nombreproducto}</td>
                            <td>${item.tipoinventario}</td>
                            <td>${item.seccion}</td>
                            <td>${item.materiaprima}</td>
                            <td>${item.especificacionmedida}</td>
                            <td>${item.color}</td>
                            <td>${item.marca}</td>
                            <td>${item.cantidad}</td>
                            <td>${item.precio}</td>
                            <td>${item.ciudad}</td>
                        </tr>`;
                        modalBodyInventarios.innerHTML += row;
                    });
                } else {
                    modalBodyInventarios.innerHTML = `<tr><td colspan="11" class="text-center">NO HAY PORTAFOLIO PARA ESTE PROVEEDOR</td></tr>`;
                }

                const proveedorServicioFiltrado = bateriaproveedorservicios.filter(item => item.proveedorid == proveedorId);
                if (proveedorServicioFiltrado.length > 0) {
                    let rows = "";
                    let compraProducto = [];
                    let compraServicio = [];
                    let ventaProducto = [];
                    proveedorServicioFiltrado.forEach(item => {
                        if (item.compraproducto) compraProducto.push(item.compraproducto);
                        if (item.compraservicio) compraServicio.push(item.compraservicio);
                        if (item.ventaproducto) ventaProducto.push(item.ventaproducto);
                    });
                    if (compraProducto.length > 0) {
                        rows += `<tr><td colspan="11"><strong>Adquisición de Productos:</strong> ${compraProducto.join(", ")}</td></tr>`;
                    }
                    if (compraServicio.length > 0) {
                        rows += `<tr><td colspan="11"><strong>Adquisición de Servicios:</strong> ${compraServicio.join(", ")}</td></tr>`;
                    }
                    if (ventaProducto.length > 0) {
                        rows += `<tr><td colspan="11"><strong>Venta de Productos:</strong> ${ventaProducto.join(", ")}</td></tr>`;
                    }
                    modalBodyProveedorServicios.innerHTML = rows;
                } else {
                    modalBodyProveedorServicios.innerHTML = `<tr><td colspan="11" class="text-center">NO HAY PRODUCTOS DE SERVICIO PARA ESTE PROVEEDOR</td></tr>`;
                }
            });
        });


        document.getElementById("btnAgregarRegistro").addEventListener("click", function() {
            var formNuevoRegistro = document.getElementById("formNuevoRegistro");
            
            // Alterna la visibilidad del formulario
            if (formNuevoRegistro.style.display === "block") {
                formNuevoRegistro.style.display = "none";
            } else {
                formNuevoRegistro.style.display = "block";
            }
        });


    document.getElementById("nuevoRegistroForm").addEventListener("submit", function(event) {
        event.preventDefault();
        
        const formData = new FormData(this);
        fetch("/guardarnuevoproducto", { 
            method: "POST",
            body: formData,
            headers: {
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            }
        })
        .then(response => response.json())
        .then(data => {
            alert("Registro guardado correctamente");
            document.getElementById("formNuevoRegistro").style.display = "none";
            this.reset();
        })
        .catch(error => console.error("Error:", error));
    });

    });
</script>

{{-- RELLENAR SEGUN LO SELECCIONADO --}}
<script>
    const opciones = {
        "ALMACEN": {
            "seccion": ["ESCRITORIO", "COCINA", "USO MEDICO", "PROMOCIONAL", "LIMPIEZA", "CONSTRUCCION Y FERRETERIA", "INSUMOS DECORATIVOS"],
            "unidad_medida": {
                "ESCRITORIO": ["BOLSA", "CAJA", "PAQUETE", "UNIDADES"],
                "COCINA": ["UNIDADES", "BOLSA"],
                "USO MEDICO": ["CAJA", "UNIDADES"],
                "PROMOCIONAL": ["UNIDADES"],
                "LIMPIEZA": ["CAJA", "PAQUETE", "UNIDADES"],
                "CONSTRUCCION Y FERRETERIA": ["BOLSA", "CAJA", "ROLLO", "UNIDADES"],
                "INSUMOS DECORATIVOS": ["UNIDADES"]
            },
            "materia_prima": {
                "ESCRITORIO": ["ACRILICO", "CARBONICO", "CARTON", "GOMA", "LIQUIDO", "MADERA", "METALICO", "PAPEL", "PLASTICO", "TELA", "VENESTA"],
                "COCINA": ["PLASTICO", "MADERA", "PAPEL"],
                "USO MEDICO": ["FIBRA", "GEL", "GOMA", "ISOPROPILICO", "LATEX", "LIQUIDO", "MADERA", "METALICO", "PAPEL", "PLASTICO", "POLIPROPILENO", "ROLLO", "TELA", "TERMICO"],
                "PROMOCIONAL": ["ALGODÓN", "CARTULINA", "CERAMICA", "LONA", "METALICO", "PAPEL", "PLASTICO", "POLIESTER", "PORCELANA", "PVC"],
                "LIMPIEZA": ["GOMA", "INTERFOLIADAS", "LIQUIDO", "MADERA", "MICROFIBRA", "PAPEL", "PLASTICO", "POLVO", "TELA"],
                "CONSTRUCCION Y FERRETERIA": ["ACRILICA", "ALUMINIO", "CARTULINA", "CAUCHO", "CERAMICA", "COBRE", "CONCRETO", "CUERO", "GOMA", "LATA", "LINO", "LIQUIDO", "MADERA", "MALLA", "METALICO", "PLASTICO", "POLIESTER", "PORCELANA", "PVC", "SINTETICO", "TELA", "YESO"],
                "INSUMOS DECORATIVOS": ["MADERA"]
            },
            "marca": {
                "ESCRITORIO": ["ACRICOLOR", "ARTESCO", "BWHITE", "CASIO", "CHRISTMAS HOUSE", "CONDOR", "ENERGIZER", "FIVE STICK", "FRINGE CURTAIN", "ISOFIT", "MADISON", "MAXOFFICE", "MERTETTO", "MILCAR", "MONAMI"],
                "COCINA": ["SCOTT", "COPOBRAS", "BELEN"],
                "USO MEDICO": ["A&E", "BIOHIT", "BIOPLAST", "BRAUN", "BTL", "CAPULLO", "CUREBAND", "DRENACATH", "EARNIZ", "EKOSUR"],
                "PROMOCIONAL": ["S/M"],
                "LIMPIEZA": ["ARCHER", "ARISTECH", "ARMORALL", "BELEN", "BRISTAR", "CLIN", "ELITE", "HIGIA", "LIZ"],
                "CONSTRUCCION Y FERRETERIA": ["ABRO", "ADHEPLAS", "AMERICAN WORKS", "ARATY", "ARCELOR MITTAL"],
                "INSUMOS DECORATIVOS": ["S/M"]
            }
        },
        "ACTIVO FIJO": {
            "seccion": [
                "ALMACEN","GERENCIA GENERAL", "GERENCIA COMERCIAL Y FINANCIERA", "SALA DE REUNIONES", "ZONA DE MONITOREO", "BAÑO GERENCIAL",
                "SALA DE ESPERA PLANTA ALTA", "COCINA", "BAÑO PLANTA ALTA", "OFICINA 1 PLANTA ALTA", "CONSULTORIO 1 PLANTA ALTA",
                "CONSULTORIO 2 PLANTA ALTA", "CONSULTORIO 3 PLANTA ALTA", "CONSULTORIO 4 PLANTA ALTA", "CONSULTORIO 5 PLANTA ALTA",
                "CONSULTORIO 6 PLANTA BAJA", "CONSULTORIO 7 PLANTA BAJA", "CONSULTORIO 8 PLANTA BAJA", "CONSULTORIO 9 PLANTA BAJA",
                "OFICINA 2 PLANTA BAJA", "OFICINA 3 PLANTA BAJA", "SALA DE ESPERA PLANTA BAJA", "SALA DE ATENCION AL CLIENTE",
                "BAÑO PLANTA BAJA", "BAÑO CONSULTORIO 7 PLANTA BAJA", "DEPOSITO PRINCIPAL", "DEPOSITO SECUNDARIO",
                "PASILLO PLANTA ALTA", "PASILLO PLANTA BAJA", "GRADAS", "ENTRADA PRINCIPAL", "VISTA FRONTAL"
            ],
            "unidad_medida": ["UNIDADES"],
            "materia_prima": ["CONCRETO", "GOMA", "MADERA", "METALICO", "PLASTICO", "POLIESTER"],
            "marca": ["3D OPTICAL MOUSE", "AC-DELL", "ARRIX", "BIZLINK", "BREATHALYZER", "CONTEC", "DAHUA", "DIMAX", "DYMO", "ECCOSUR"]
        }
    };

    document.getElementById("tipo_inventario").addEventListener("change", function () {
        const tipo = this.value;
        const seccion = document.getElementById("seccion");
        const materia = document.getElementById("materia_prima");
        const unidad = document.getElementById("unidad_medida");
        const marca = document.getElementById("marca");
        const modelo = document.getElementById("modelo");

        seccion.innerHTML = '<option value="" disabled selected></option>';
        materia.innerHTML = '<option value="" disabled selected></option>';
        unidad.innerHTML = '<option value="" disabled selected></option>';
        marca.innerHTML = '<option value="" disabled selected></option>';

        if (tipo in opciones) {
            opciones[tipo].seccion.forEach(s => {
                seccion.innerHTML += `<option value="${s}">${s}</option>`;
            });

            opciones[tipo].materia_prima.forEach(m => {
                materia.innerHTML += `<option value="${m}">${m}</option>`;
            });

            opciones[tipo].unidad_medida.forEach(u => {
                unidad.innerHTML += `<option value="${u}">${u}</option>`;
            });

            opciones[tipo].marca.forEach(m => {
                marca.innerHTML += `<option value="${m}">${m}</option>`;
            });

            if (tipo === "ACTIVO FIJO") {
                modelo.disabled = false;
                modelo.style.display = "block";
            } else {
                modelo.disabled = true;
                modelo.style.display = "none";
            }
        }
    });

    document.getElementById("seccion").addEventListener("change", function () {
        const tipo = document.getElementById("tipo_inventario").value;
        const seccionSeleccionada = this.value;
        const materia = document.getElementById("materia_prima");
        const unidad = document.getElementById("unidad_medida");
        const marca = document.getElementById("marca");

        materia.innerHTML = '<option value="" disabled selected></option>';
        unidad.innerHTML = '<option value="" disabled selected></option>';
        marca.innerHTML = '<option value="" disabled selected></option>';

        if (tipo === "ALMACEN") {
            const materias = opciones[tipo].materia_prima[seccionSeleccionada] || opciones[tipo].materia_prima["default"];
            const unidades = opciones[tipo].unidad_medida[seccionSeleccionada] || opciones[tipo].unidad_medida["default"];
            const marcas = opciones[tipo].marca[seccionSeleccionada] || opciones[tipo].marca["default"];

            materias.forEach(m => {
                materia.innerHTML += `<option value="${m}">${m}</option>`;
            });

            unidades.forEach(u => {
                unidad.innerHTML += `<option value="${u}">${u}</option>`;
            });

            marcas.forEach(m => {
                marca.innerHTML += `<option value="${m}">${m}</option>`;
            });
        }

        if (tipo === "ACTIVO FIJO" && seccionSeleccionada) {
            const materias = opciones[tipo].materia_prima || [];
            const unidades = opciones[tipo].unidad_medida || [];
            const marcas = opciones[tipo].marca || [];

            materias.forEach(m => {
                materia.innerHTML += `<option value="${m}">${m}</option>`;
            });

            unidades.forEach(u => {
                unidad.innerHTML += `<option value="${u}">${u}</option>`;
            });

            marcas.forEach(m => {
                marca.innerHTML += `<option value="${m}">${m}</option>`;
            });
        }
    });
</script>

{{-- COLORES --}}
<script>
    document.addEventListener("DOMContentLoaded", function() {
        let colorBoxes = document.querySelectorAll(".color-box");
        let colorInput = document.getElementById("colorInput");
        let colorDropdown = document.getElementById("colorDropdown");

        const colorNames = {
            "#000000": "NEGRO",
            "#FFFFFF": "BLANCO",
            "#FF0000": "ROJO",
            "#008000": "VERDE",
            "#0000FF": "AZUL",
            "#FFFF00": "AMARILLO",
            "#FFA500": "NARANJA",
            "#800080": "MORADO",
            "#FFC0CB": "ROSADO",
            "#808080": "GRIS",
            "#A52A2A": "MARRON",
            "#00BFFF": "CELESTE",
            "#FFD700": "DORADO",
            "#008B8B": "TURQUESA",
            "#B22222": "GUINDO"
        };

        colorBoxes.forEach(box => {
            box.style.backgroundColor = box.getAttribute("data-color");
            box.addEventListener("click", function() {
                colorBoxes.forEach(b => b.classList.remove("selected"));
                this.classList.add("selected");
                let selectedColor = this.getAttribute("data-color");
                colorInput.value = colorNames[selectedColor];
                colorDropdown.style.backgroundColor = selectedColor;
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