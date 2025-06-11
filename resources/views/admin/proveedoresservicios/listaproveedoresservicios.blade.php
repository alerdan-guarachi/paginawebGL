@extends('adminlte::page')
    
@section('content_header')
@can('admin.proveedoresservicios.create')
<a class="btn float-right btn-crear btn-sm" data-toggle="modal" data-target="#crearProductoModal">
    CREAR PROVEEDOR
</a>
@endcan
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
            <div class="modal-body text-center" style="margin-top: 20px; margin-bottom: 50px;">
                <div class="d-flex justify-content-center">
                    <button type="button" class="btn btn-custom mx-2" style="width: 200px;" onclick="redirectTo('medico')">
                        <div class="d-flex flex-column align-items-center justify-content-center">
                            <i class="fas fa-user-md fa-4x mb-2"></i>
                            <span class="h6 mb-0">MÉDICO</span>
                        </div>
                    </button>
                    <button type="button" class="btn btn-custom mx-2" style="width: 200px;" onclick="redirectTo('personalexterno')">
                        <div class="d-flex flex-column align-items-center justify-content-center">
                            <i class="fas fa-users fa-4x mb-2"></i>
                            <span class="h6 mb-0">PERSONAL EXTERNO</span>
                        </div>
                    </button>
                    <button type="button" class="btn btn-custom mx-2" style="width: 200px;" onclick="redirectTo('general')">
                        <div class="d-flex flex-column align-items-center justify-content-center">
                            <i class="fas fa-id-card fa-4x mb-2"></i> 
                            <span class="h6 mb-0">GENERALES</span>
                        </div>
                    </button>
                    <button type="button" class="btn btn-custom mx-2" style="width: 200px;" onclick="redirectTo('serviciobasico')">
                        <div class="d-flex flex-column align-items-center justify-content-center">
                            <i class="fas fa-plug fa-4x mb-2"></i>
                            <span class="h6 mb-0">SERVICIO BASICO</span>
                        </div>
                    </button>
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
        } else if (type === 'personalexterno') {
            window.location.href = "{{ route('admin.proveedoresservicios.crearprovpersonalexterno') }}";
        } else if (type === 'medico') {
            window.location.href = "{{ route('admin.proveedores.create') }}";
        } 
    }
</script>
<h1>LISTA DE PROVEEDORES</h1>
@stop

@section('css')
<link rel="stylesheet" href="{{ asset('css/proveedoresserviciosgeneral.css') }}">
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
        {{-- <p style="margin-bottom: -7px; margin-left: 10px; font-weight: bold;">TU PERFIL</p>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th style="width: 10%;">ID</th>
                    <th style="width: 30%;">Proveedor</th>
                    <th style="width: 20%;">CI</th>
                    <th style="width: 20%;">Cargo</th>
                    <th style="width: 10%;">Estado</th>
                    <th style="width: 10%;">Ver</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($soloproveedorservicio as $proveedoresservicios)
                    <tr>
                        <td>{{$proveedoresservicios->id}}</td>
                        <td>{{$proveedoresservicios->razonsocial}}</td>
                        <td>{{$proveedoresservicios->ci}}</td>
                        <td>{{$proveedoresservicios->cargo}}</td>
                        <td>
                            @if ($proveedoresservicios->estado == 'ACTIVO')
                                <span class="badge badge-success">{{ $proveedoresservicios->estado }}</span>
                            @else
                                <span class="badge badge-danger">{{ $proveedoresservicios->estado }}</span>
                            @endif
                        </td>
                        <td width="10px">
                            @can('admin.proveedoresservicios.index')
                                <a class="btn btn-sm btn-verperfil fas fa-eye" href="{{route('admin.proveedoresservicios.verproveedorexterno', $proveedoresservicios)}}" title="VER PROVEEDOR"></a>
                            @endcan
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table> --}}

        {{-- BUSCADOR --}}
        <div class="card-header d-flex">
            <form class="form-inline" style="margin-left: auto;">
                <div class="input-group" style="margin-left: 20px;">
                    <input name="buscarpor" class="form-control" type="search" style="width: 300px;" placeholder="NOMBRE DEL PROVEEDOR" aria-label="Buscar proveedor">
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
                    <a class="nav-link active" id="tab-4" data-toggle="tab" href="#tab-content-4" role="tab" aria-controls="tab-content-4" aria-selected="true">
                        MÉDICOS
                    </a>
                </li>     
                <li class="nav-item">
                    <a class="nav-link" id="tab-5" data-toggle="tab" href="#tab-content-5" role="tab" aria-controls="tab-content-5" aria-selected="true">
                        PERSONAL EXTERNO
                    </a>
                </li>     
                <li class="nav-item">
                    <a class="nav-link" id="tab-1" data-toggle="tab" href="#tab-content-1" role="tab" aria-controls="tab-content-1" aria-selected="true">
                        GENERALES
                    </a>
                </li>     
                {{-- <li class="nav-item">
                    <a class="nav-link" id="tab-2" data-toggle="tab" href="#tab-content-2" role="tab" aria-controls="tab-content-2" aria-selected="true">
                        SIN FACTURA
                    </a>
                </li>  --}}
                <li class="nav-item">
                    <a class="nav-link" id="tab-3" data-toggle="tab" href="#tab-content-3" role="tab" aria-controls="tab-content-3" aria-selected="true">
                        SERVICIO BASICO
                    </a>
                </li>     
            </ul>
        </div>

        <div class="tab-content" id="myTabContent">
            {{-- PROVEEDORES MÉDICOS --}}
            <div class="tab-pane fade show active" id="tab-content-4" role="tabpanel" aria-labelledby="tab-4">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th style="width: 10%;">ID</th>
                                <th style="width: 30%;">Proveedor</th>
                                <th style="width: 20%;">NIT</th>
                                <th style="width: 20%;">Dirección</th>
                                <th style="width: 10%;">Estado</th>
                                <th style="width: 10%;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($proveedores as $proveedor)
                                <tr>
                                    <td>{{$proveedor->id}}</td>
                                    <td>{{$proveedor->proveedor}}</td>
                                    <td>{{$proveedor->nit}}</td>
                                    <td>{{$proveedor->direccion}}</td>
                                    <td>
                                        @if ($proveedor->estadoproveedor == 'ACTIVO')
                                            <span class="badge badge-success">{{ $proveedor->estadoproveedor }}</span>
                                        @else
                                            <span class="badge badge-danger">{{ $proveedor->estadoproveedor }}</span>
                                        @endif
                                    </td>
                                    <td width="10px">
                                        @can('admin.proveedores.index')
                                        <a class="btn btn-sm btn-verperfil fas fa-eye" href="{{ route('admin.proveedores.show', $proveedor)}}" title="VER PROVEEDOR MÉDICO"></a>
                                        @endcan
                                        @can('admin.proveedores.crearbateriaproveedor')
                                        <a class="btn btn-sm btn-verinventario" href="{{ route('admin.proveedores.crearbateriaproveedor', $proveedor)}}" title="VER Y CREAR BATERIA"><i class="fas fa-clipboard-list"></i></a>
                                        @endcan
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- PROVEEDORES EXTERNOS --}}
            <div class="tab-pane fade" id="tab-content-5" role="tabpanel" aria-labelledby="tab-5">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th style="width: 10%;">ID</th>
                                <th style="width: 30%;">Proveedor</th>
                                <th style="width: 20%;">CI</th>
                                <th style="width: 20%;">Cargo</th>
                                <th style="width: 10%;">Estado</th>
                                <th style="width: 10%;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($todosproveedoresservicios as $proveedoresservicios)
                                @if ($proveedoresservicios->categoria === 'PROVEEDOR EXTERNO')
                                    <tr>
                                        <td>{{$proveedoresservicios->id}}</td>
                                        <td>{{$proveedoresservicios->razonsocial}}</td>
                                        <td>{{$proveedoresservicios->ci}}</td>
                                        <td>{{$proveedoresservicios->cargo}}</td>
                                        <td>
                                            @if ($proveedoresservicios->estado == 'ACTIVO')
                                                <span class="badge badge-success">{{ $proveedoresservicios->estado }}</span>
                                            @else
                                                <span class="badge badge-danger">{{ $proveedoresservicios->estado }}</span>
                                            @endif
                                        </td>
                                        <td width="10px">
                                            @can('admin.proveedoresservicios.show')
                                                <a class="btn btn-sm btn-verperfil fas fa-eye" href="{{route('admin.proveedoresservicios.verproveedorexterno', $proveedoresservicios)}}" title="VER PROVEEDOR"></a>
                                            @endcan

                                            {{-- <a class="btn btn-sm btn-verinventario" 
                                               data-toggle="modal" 
                                               data-target="#modalInventarios" 
                                               data-id="{{ $proveedoresservicios->id }}"
                                               data-nombre="{{ $proveedoresservicios->razonsocial }}"
                                               title="VER INVENTARIOS">
                                               <i class="fas fa-clipboard-list"></i>
                                            </a> --}}

                                            @can('admin.proveedoresservicios.pasarainterno')
                                            <a class="btn btn-sm btn-pasarinterno" 
                                                data-id="{{ $proveedoresservicios->id }}" 
                                                data-nombre="{{ $proveedoresservicios->razonsocial }}" title="PASAR A PERSONAL INTERNO">
                                                <i class="fas fa-star"></i> 
                                            </a>
                                            @endcan
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- PROVEEDORES GENERALES --}}
            <div class="tab-pane fade" id="tab-content-1" role="tabpanel" aria-labelledby="tab-1">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th style="width: 10%;">ID</th>
                                <th style="width: 30%;">Proveedor</th>
                                <th style="width: 20%;">CI</th>
                                <th style="width: 20%;">NIT</th>
                                <th style="width: 10%;">Estado</th>
                                <th style="width: 10%;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($todosproveedoresservicios as $proveedoresservicios)
                                @if ($proveedoresservicios->categoria === 'PROVEEDOR GENERAL')
                                    <tr>
                                        <td>{{$proveedoresservicios->id}}</td>
                                        <td>{{$proveedoresservicios->razonsocial}}</td>
                                        <td>{{$proveedoresservicios->ci}}</td>
                                        <td>{{$proveedoresservicios->nit}}</td>
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
                                                title="VER PORTAFOLIO">
                                                <i class="fas fa-clipboard-list"></i>
                                                </a>

                                            <a class="btn btn-sm btn-vermotivos" 
                                                data-toggle="modal" 
                                                data-target="#modalmotivos" 
                                                data-id="{{ $proveedoresservicios->id }}"
                                                data-nombre="{{ $proveedoresservicios->razonsocial }}"
                                                data-planes="{{ json_encode($proveedoresservicios->planesServicios) }}"
                                                title="VER MOTIVOS">
                                                <i class="fas fa-thumbtack"></i>
                                            </a>
                                            <style>
                                                .btn-vermotivos {
                                                    background-color:  #ffffff;
                                                    color: #be22d2;
                                                    border-color: #be22d2;
                                                    border-radius: 5px;
                                                    padding: 2px 9px;
                                                    }
                                                .btn-vermotivos:hover {
                                                    background-color: #be22d2;
                                                    color: #ffffff;
                                                    }
                                            </style>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- PROVEEDORES SERVICIOS BASICOS --}}
            <div class="tab-pane fade" id="tab-content-3" role="tabpanel" aria-labelledby="tab-3">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th style="width: 10%;">ID</th>
                                <th style="width: 30%;">Proveedor</th>
                                <th style="width: 20%;">NIT</th>
                                <th style="width: 20%;">Ciudad</th>
                                <th style="width: 10%;">Estado</th>
                                <th style="width: 10%;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($todosproveedoresservicios as $proveedoresservicios)
                                @if ($proveedoresservicios->categoria === 'PROVEEDOR SERVICIO BASICO')
                                    <tr>
                                        <td>{{$proveedoresservicios->id}}</td>
                                        <td>{{$proveedoresservicios->razonsocial}}</td>
                                        <td>{{$proveedoresservicios->nit}}</td>
                                        <td>
                                            {{ $proveedoresservicios->ciudad }}
                                            @if (!empty($proveedoresservicios->ciudad2))
                                                - {{ $proveedoresservicios->ciudad2 }}
                                            @endif
                                        </td>
                                        
                                        <td>
                                            @if ($proveedoresservicios->estado == 'ACTIVO')
                                                <span class="badge badge-success">{{ $proveedoresservicios->estado }}</span>
                                            @else
                                                <span class="badge badge-danger">{{ $proveedoresservicios->estado }}</span>
                                            @endif
                                        </td>
                                        <td width="10px">
                                            @can('admin.proveedoresservicios.show')
                                                <a class="btn btn-sm btn-verperfil fas fa-eye" href="{{route('admin.proveedoresservicios.verproveedorserviciobasico', $proveedoresservicios)}}" title="VER PROVEEDOR"></a>
                                            @endcan

                                            <a class="btn btn-sm btn-verplanes" 
                                                data-toggle="modal" 
                                                data-target="#modalplanes" 
                                                data-id="{{ $proveedoresservicios->id }}"
                                                data-nombre="{{ $proveedoresservicios->razonsocial }}"
                                                data-planes="{{ json_encode($proveedoresservicios->planesServicios) }}"
                                                title="VER PLANES">
                                                <i class="fas fa-clipboard-list"></i>
                                            </a>
                                        </td>
                                        <style>
                                            .btn-verplanes {
                                                background-color:  #ffffff;
                                                color: #faa625;
                                                border-color: #faa625;
                                                border-radius: 5px;
                                                padding: 2px 9px;
                                                }
                                            .btn-verplanes:hover {
                                                background-color: #faa625;
                                                color: #ffffff;
                                                }
                                        </style>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- PLANES --}}
            <div class="modal fade" id="modalplanes" tabindex="-1" aria-labelledby="modalplanesLabel" aria-hidden="true">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <div class="modal-header" style="background-color: #f6fdea">
                            <h5 class="modal-title" id="modalplanesLabel" style="font-weight: 900">PLANES DEL PROVEEDOR</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                                <span aria-hidden="true">&times;</span>
                            </button>         
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="form-group" style="margin-top: -10px; margin-bottom: 0px;">
                                    <a class="btn btn-agregar btn-sm" id="btnAgregarPlan">
                                        AGREGAR PLAN
                                    </a>
                                </div>
                                
                                <div id="formNuevoPlan" style="display: none;">
                                    <div class="card">
                                        <div class="card-body" style="background-color: #f5f5f5">
                                            {!! Form::open(['route' => 'admin.proveedoresservicios.guardarnuevoplan', 'method'=>'POST']) !!}
                                                <div class="row">
                                                    <input type="hidden" name="proveedor_id" id="proveedor_id">
                                                    <input type="hidden" name="razon_social" id="razon_social">

                                                    <div class="col-md-2 form-group">
                                                        <label style="margin-bottom: -15px;">Ciudad</label>
                                                        <select class="form-control" id="ciudad" name="ciudad">
                                                            <option value=""></option>
                                                            <option value="SANTA CRUZ">SANTA CRUZ</option>
                                                            <option value="COCHABAMBA">COCHABAMBA</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-2 form-group">
                                                        <label style="margin-bottom: -15px;">Sigla</label>
                                                        <input type="text" class="form-control" id="sigla" name="sigla" required>
                                                    </div>
                                                    <div class="col-md-2 form-group">
                                                        <label style="margin-bottom: -15px;">Código</label>
                                                        <input type="text" class="form-control" id="codigo" name="codigo">
                                                    </div>
                                                    <div class="col-md-3 form-group">
                                                        <label style="margin-bottom: -15px;">Contrato</label>
                                                        <input type="text" class="form-control" id="contrato" name="contrato">
                                                    </div>
                                                    <div class="col-md-3 form-group">
                                                        <label style="margin-bottom: -15px;">Linea</label>
                                                        <input type="text" class="form-control" id="linea" name="linea">
                                                    </div>
                                                    <div class="col-md-3 form-group">
                                                        <label style="margin-bottom: -15px;">Cuenta</label>
                                                        <input type="text" class="form-control" id="cuenta" name="cuenta">
                                                    </div>
                                                    <div class="col-md-3 form-group">
                                                        <label style="margin-bottom: -15px;">Servicio</label>
                                                        <input type="text" class="form-control" id="servicio" name="servicio">
                                                    </div>
                                                    <div class="col-md-3 form-group">
                                                        <label style="margin-bottom: -15px;">Monto Fijo</label>
                                                        <input type="text" class="form-control" id="montofijo" name="montofijo" required>
                                                    </div>
                                                    <div class="col-md-3 form-group">
                                                        <label style="margin-bottom: -15px;">Estado</label>
                                                        <input type="text" class="form-control" id="estado" name="estado" value="ACTIVO" readonly>
                                                    </div>
                                                </div>
                                                {!! Form::submit('REGISTRAR PLAN', ['class' => ' float-right btn btn-sm btn-crear']) !!}
                                            {!! Form::close() !!}
                                        </div>
                                    </div>
                                </div>

                                <div class="card" style="width: 100%;">
                                    <div class="card-body">
                                        <div class="table-responsive w-100">
                                            <table class="table w-100">
                                                <thead>
                                                    <tr>
                                                        <th hidden>ID</th>
                                                        <th>Plan</th>
                                                        <th>Sigla</th>
                                                        <th>Código</th>
                                                        <th>Contrato</th>
                                                        <th>Línea</th>
                                                        <th>Cuenta</th>
                                                        <th>Servicio</th>
                                                        <th>Ciudad</th>
                                                        <th>Monto Fijo</th>
                                                        <th>Estado</th>
                                                        @can('admin.proveedoresservicios.inactivarproductoplan')
                                                        <th>Inac</th>
                                                        @endcan
                                                    </tr>
                                                </thead>
                                                <tbody id="modalplanesBody">
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
            <script>
                $(document).on('click', '.btn-verplanes', function () {
                    var proveedorId = $(this).data('id');
                    var nombreProveedor = $(this).data('nombre');
                    var planes = $(this).data('planes');

                    $('#modalplanesLabel').text('PLANES DEL PROVEEDOR: ' + nombreProveedor);
                    $('#proveedor_id').val(proveedorId);
                    $('#razon_social').val(nombreProveedor);
                    let html = '';
                    if (planes.length > 0) {
                        planes.forEach(plan => {
                            html += `
                                <tr>
                                    <td hidden>${plan.id}</td>
                                    <td>${plan.plan}</td>
                                    <td>${plan.sigla}</td>
                                    <td>${plan.codigo ?? ''}</td>
                                    <td>${plan.contrato ?? ''}</td>
                                    <td>${plan.linea ?? ''}</td>
                                    <td>${plan.cuenta ?? ''}</td>
                                    <td>${plan.servicio ?? ''}</td>
                                    <td>${plan.ciudad ?? ''}</td>
                                    <td>${plan.montofijo}</td>
                                    <td><span class="badge ${plan.estado === 'ACTIVO' ? 'bg-success' : 'bg-danger'}">${plan.estado}</span></td>
                                    @can('admin.proveedoresservicios.inactivarproductoplan')
                                    <td>
                                        ${plan.estado === 'ACTIVO' 
                                            ? `<button class="btn btn-outline-danger btn-sm btn-inactivarplan" data-id="${plan.id}">
                                                <i class="fas fa-times"></i>
                                            </button>` 
                                            : ''
                                        }
                                    </td>
                                    @endcan
                                </tr>
                            `;
                        });
                    } else {
                        html = `<tr><td colspan="11" class="text-center">NO HAY PLANES PARA ESTE PROVEEDOR</td></tr>`;
                    }
                    $('#modalplanesBody').html(html);
                });

                document.getElementById("btnAgregarPlan").addEventListener("click", function() {
                    var formNuevoRegistro = document.getElementById("formNuevoPlan");
                    
                    // Alterna la visibilidad del formulario
                    if (formNuevoRegistro.style.display === "block") {
                        formNuevoRegistro.style.display = "none";
                    } else {
                        formNuevoRegistro.style.display = "block";
                    }
                });
            </script>
            <script>
                document.addEventListener("click", function(event) {
                    if (event.target.classList.contains("btn-inactivarplan")) {
                        let id = event.target.getAttribute("data-id");

                        // SweetAlert para confirmar la acción
                        Swal.fire({
                            title: "¿Estás seguro?",
                            text: "Este plan será inactivado",
                            icon: "warning",
                            showCancelButton: true,
                            confirmButtonColor: "#94c93b",
                            cancelButtonColor: "#faa625",
                            confirmButtonText: "SI, CONTINUAR",
                            cancelButtonText: "CANCELAR"
                        }).then((result) => {
                            if (result.isConfirmed) {
                                fetch("{{ route('admin.proveedoresservicios.planesinactivar', '') }}/" + id, {
                                    method: "POST",
                                    headers: {
                                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
                                        "Content-Type": "application/json"
                                    },
                                    body: JSON.stringify({ estado: "INACTIVO" })
                                })
                                .then(response => {
                                    if (!response.ok) {
                                        throw new Error("Error en la petición");
                                    }
                                    return response.json();
                                })
                                .then(data => {
                                    // Guardar mensaje en localStorage antes de recargar la página
                                    localStorage.setItem("planInactivado", "El plan ha sido inactivado correctamente");

                                    // Recargar la página
                                    location.reload();
                                })
                                .catch(error => {
                                    Swal.fire({
                                        title: "Error",
                                        text: "Ocurrió un error al inactivar el plan",
                                        icon: "error"
                                    });
                                    console.error("Error:", error);
                                });
                            }
                        });
                    }
                });

                // Verificar si hay un mensaje almacenado al cargar la página
                document.addEventListener("DOMContentLoaded", function() {
                    let mensaje = localStorage.getItem("planInactivado");
                    if (mensaje) {
                        Swal.fire({
                            title: "Inactivado",
                            text: mensaje,
                            icon: "success",
                            timer: 2000,
                            showConfirmButton: false
                        });

                        // Eliminar el mensaje para que no se vuelva a mostrar en la próxima carga
                        localStorage.removeItem("planInactivado");
                    }
                });
            </script>


            {{-- MOTIVOS --}}
            <div class="modal fade" id="modalmotivos" tabindex="-1" aria-labelledby="modalmotivosLabel" aria-hidden="true">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <div class="modal-header" style="background-color: #f6fdea">
                            <h5 class="modal-title" id="modalmotivosLabel" style="font-weight: 900">MOTIVOS DEL PROVEEDOR</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                                <span aria-hidden="true">&times;</span>
                            </button>         
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="form-group" style="margin-top: -10px; margin-bottom: 0px;">
                                    <a class="btn btn-agregar btn-sm" id="btnAgregarMotivo">
                                        AGREGAR MOTIVO
                                    </a>
                                </div>
                                
                                <div id="formNuevoMotivo" style="display: none;">
                                    <div class="card">
                                        <div class="card-body" style="background-color: #f5f5f5">
                                            {!! Form::open(['route' => 'admin.proveedoresservicios.guardarnuevoplan', 'method'=>'POST']) !!}
                                                <div class="row">
                                                    <div class="col-md-2 form-group">
                                                        <label style="margin-bottom: -15px;">ID Prov.</label>
                                                        <input type="text" class="form-control" name="proveedor_id" id="proveedor_id2" readonly>
                                                    </div>
                                                    <div class="col-md-6 form-group">
                                                        <label style="margin-bottom: -15px;">Proveedor</label>
                                                        <input type="text" class="form-control" name="razon_social" id="razon_social2" readonly>
                                                    </div>
                                                    <div class="col-md-4 form-group">
                                                        <label style="margin-bottom: -15px;">Estado</label>
                                                        <input type="text" class="form-control" id="estado" name="estado" value="ACTIVO" readonly>
                                                    </div>
                                                    <div class="col-md-6 form-group">
                                                        <label style="margin-bottom: -15px;">Motivo</label>
                                                        <input type="text" class="form-control" id="motivo" name="motivo">
                                                    </div>
                                                    <div class="col-md-6 form-group">
                                                        <label style="margin-bottom: -15px;">Monto Fijo</label>
                                                        <input type="text" class="form-control" id="montofijo" name="montofijo" required>
                                                    </div>
                                                    
                                                </div>
                                                {!! Form::submit('REGISTRAR MOTIVO', ['class' => ' float-right btn btn-sm btn-crear']) !!}
                                            {!! Form::close() !!}
                                        </div>
                                    </div>
                                </div>

                                <div class="card" style="width: 100%;">
                                    <div class="card-body">
                                        <div class="table-responsive w-100">
                                            <table class="table w-100">
                                                <thead>
                                                    <tr>
                                                        <th hidden>ID</th>
                                                        <th>Nro.</th>
                                                        <th>Motivo</th>
                                                        <th>Monto Fijo</th>
                                                        <th>Estado</th>
                                                        @can('admin.proveedoresservicios.inactivarproductoplan')
                                                        @endcan
                                                    </tr>
                                                </thead>
                                                <tbody id="modalmotivosBody">
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
            <script>
                $(document).on('click', '.btn-vermotivos', function () {
                    var proveedorId = $(this).data('id');
                    var nombreProveedor = $(this).data('nombre');
                    var planes = $(this).data('planes');

                    $('#modalmotivosLabel').text('MOTIVOS DEL PROVEEDOR: ' + nombreProveedor);
                    $('#proveedor_id2').val(proveedorId);
                    $('#razon_social2').val(nombreProveedor);
                    let html = '';
                    if (planes.length > 0) {
                        planes.forEach(plan => {
                            html += `
                                <tr>
                                    <td hidden>${plan.id}</td>
                                    <td>${plan.plan}</td>
                                    <td>${plan.motivo ?? ''}</td>
                                    <td>${plan.montofijo}</td>
                                    <td><span class="badge ${plan.estado === 'ACTIVO' ? 'bg-success' : 'bg-danger'}">${plan.estado}</span></td>
                                </tr>
                            `;
                        });
                    } else {
                        html = `<tr><td colspan="11" class="text-center">NO HAY MOTIVOS PARA ESTE PROVEEDOR</td></tr>`;
                    }
                    $('#modalmotivosBody').html(html);
                });

                document.getElementById("btnAgregarMotivo").addEventListener("click", function() {
                    var formNuevoRegistro = document.getElementById("formNuevoMotivo");
                    
                    // Alterna la visibilidad del formulario
                    if (formNuevoRegistro.style.display === "block") {
                        formNuevoRegistro.style.display = "none";
                    } else {
                        formNuevoRegistro.style.display = "block";
                    }
                });
            </script>
        </div>
    </div>
</div>      

{{-- INVENTARIO --}}
<div class="modal fade" id="modalInventarios" tabindex="-1" aria-labelledby="modalInventariosLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #f6fdea">
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

                    <div class="form-group" style="margin-top: -10px; margin-bottom: 0px;">
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
                                        <div class="col-md-3 form-group">
                                            <label for="ciudad" style="margin-bottom: -15px;">Ciudad</label>
                                            <select id="ciudad" name="ciudad" class="form-control">
                                                <option value="" disabled selected></option>
                                                <option value="SANTA CRUZ">SANTA CRUZ</option>
                                                <option value="COCHABAMBA">COCHABAMBA</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3 form-group">
                                            <label for="tipoinventario" style="margin-bottom: -15px;">Tipo Inventario</label>
                                            <select name="tipo_inventario" id="tipo_inventario"  class="form-control">
                                                <option value="" disabled selected></option>
                                                <option value="ALMACEN">ALMACEN</option>
                                                <option value="ACTIVO FIJO">ACTIVO FIJO</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3 form-group">
                                            <label for="seccion" style="margin-bottom: -15px;">Sección</label>
                                            <select name="seccion" id="seccion" class="form-control">
                                                <option value="" disabled selected></option>
                                            </select>
                                        </div>
                                        <div class="col-md-3 form-group">
                                            <label style="margin-bottom: -15px;">Producto</label>
                                            <input type="text" class="form-control" id="nombreproducto" name="nombreproducto" required>
                                        </div>
                                        <div class="col-md-2 form-group">
                                            <label for="materiaprima" style="margin-bottom: -15px;">Mat. Prima</label>
                                            <select name="materia_prima" id="materia_prima" class="form-control">
                                                <option value="" disabled selected></option>
                                            </select>
                                        </div>
                                        <div class="col-md-2 form-group">
                                            <label style="margin-bottom: -15px;">Especif. Medida</label>
                                            <input type="text" class="form-control" id="especificacionmedida" name="especificacionmedida">
                                        </div>
                                        <div class="col-md-2 form-group">
                                            <label for="unidadmedida" style="margin-bottom: -15px;">Unidad Medida</label>
                                            <select name="unidad_medida" id="unidad_medida" class="form-control">
                                                <option value="" disabled selected></option>
                                            </select>
                                        </div>
                                        <div class="col-md-2 form-group">
                                            <label for="marca" style="margin-bottom: -15px;">Marca</label>
                                            <select name="marca" id="marca" class="form-control">
                                                <option value="" disabled selected></option>
                                            </select>
                                        </div>
                                        <div class="form-group col-md-2">
                                            <label for="marca" style="margin-bottom: -15px;">Color</label>
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
                                            <label style="margin-bottom: -15px;">Modelo</label>
                                            <input type="text" class="form-control" name="modelo" id="modelo" placeholder="" disabled>
                                        </div>

                                        <div class="col-md-2 form-group">
                                            <label style="margin-bottom: -15px;">Presentación</label>
                                            <input type="text" class="form-control" id="presentacion" name="presentacion">
                                        </div>
                                        <div class="col-md-2 form-group">
                                            <label style="margin-bottom: -15px;">Unidades</label>
                                            <input type="number" class="form-control" id="unidades" name="unidades" required>
                                        </div>
                                        <div class="col-md-2 form-group"> 
                                            <label style="margin-bottom: -15px;">Cantidad</label>
                                            <input type="number" class="form-control" id="cantidad" name="cantidad" required>
                                        </div>
                                        <div class="col-md-3 form-group">
                                            <label style="margin-bottom: -15px;">Precio Total</label>
                                            <input type="number" step="0.01" class="form-control" id="precio" name="precio" required>
                                        </div>
                                        <div class="col-md-3 form-group">
                                            <label style="margin-bottom: -15px;">Precio Unitario</label>
                                            <input type="number" step="0.01" class="form-control" id="preciounitario" name="preciounitario" required readonly>
                                        </div>
                                        
                                        <script>
                                            document.addEventListener("DOMContentLoaded", function() {
                                                const cantidadInput = document.getElementById("cantidad");
                                                const precioTotalInput = document.getElementById("precio");
                                                const precioUnitarioInput = document.getElementById("preciounitario");
                                            
                                                function calcularPrecioUnitario() {
                                                    const cantidad = parseFloat(cantidadInput.value) || 0;
                                                    const precioTotal = parseFloat(precioTotalInput.value) || 0;
                                                    
                                                    if (cantidad > 0) {
                                                        precioUnitarioInput.value = (precioTotal / cantidad).toFixed(2);
                                                    } else {
                                                        precioUnitarioInput.value = "";
                                                    }
                                                }
                                            
                                                cantidadInput.addEventListener("input", calcularPrecioUnitario);
                                                precioTotalInput.addEventListener("input", calcularPrecioUnitario);
                                            });
                                        </script>
                                        
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
                                                <th>Unid._Medida</th>
                                                <th>Marca</th>
                                                <th>Color</th>
                                                <th>Modelo</th>
                                                <th>Present.</th>
                                                <th>Unid.</th>
                                                <th>Cant.</th>
                                                <th>P.Total.</th>
                                                <th>P.Unitario</th>
                                                <th>Ciudad</th>
                                                <th>Estado</th>
                                                @can('admin.proveedoresservicios.inactivarproductoplan')
                                                <th>Inac.</th>
                                                @endcan
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
                            <td>${item.unidadmedida}</td>
                            <td>${item.marca}</td>
                            <td>${item.color}</td>
                            <td>${item.modelo}</td>
                            <td>${item.presentacion}</td>
                            <td>${item.unidades}</td>
                            <td>${item.cantidad}</td>
                            <td>${item.precio}</td>
                            <td>${item.preciounitario}</td>
                            <td>${item.ciudad}</td>
                            <td>
                                <span class="badge ${item.estado === 'ACTIVO' ? 'bg-success' : 'bg-danger'}">
                                    ${item.estado}
                                </span>
                            </td>
                            @can('admin.proveedoresservicios.inactivarproductoplan')
                            <td>
                                ${item.estado === 'ACTIVO' 
                                    ? `<button class="btn btn-outline-danger btn-sm btn-inactivar" data-id="${item.id}">
                                        <i class="fas fa-times"></i>
                                    </button>` 
                                    : ''
                                }
                            </td>
                            @endcan


                        </tr>`;
                        modalBodyInventarios.innerHTML += row;
                    });
                } else {
                    modalBodyInventarios.innerHTML = `<tr><td colspan="18" class="text-center">NO HAY PORTAFOLIO PARA ESTE PROVEEDOR</td></tr>`;
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
<script>
    document.addEventListener("click", function(event) {
        if (event.target.classList.contains("btn-inactivar")) {
            let id = event.target.getAttribute("data-id");

            // SweetAlert para confirmar la acción
            Swal.fire({
                title: "¿Estás seguro?",
                text: "Este producto será inactivado",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#94c93b",
                cancelButtonColor: "#faa625",
                confirmButtonText: "SI, CONTINUAR",
                cancelButtonText: "CANCELAR"
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch("{{ route('admin.proveedoresservicios.inactivarproducto', '') }}/" + id, {
                        method: "POST",
                        headers: {
                            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
                            "Content-Type": "application/json"
                        },
                        body: JSON.stringify({ estado: "INACTIVO" })
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error("Error en la petición");
                        }
                        return response.json();
                    })
                    .then(data => {
                        // Guardar mensaje en localStorage antes de recargar la página
                        localStorage.setItem("productoInactivado", "El producto ha sido inactivado correctamente");

                        // Recargar la página
                        location.reload();
                    })
                    .catch(error => {
                        Swal.fire({
                            title: "Error",
                            text: "Ocurrió un error al inactivar el producto",
                            icon: "error"
                        });
                        console.error("Error:", error);
                    });
                }
            });
        }
    });

    // Verificar si hay un mensaje almacenado al cargar la página
    document.addEventListener("DOMContentLoaded", function() {
        let mensaje = localStorage.getItem("productoInactivado");
        if (mensaje) {
            Swal.fire({
                title: "Inactivado",
                text: mensaje,
                icon: "success",
                timer: 2000,
                showConfirmButton: false
            });

            // Eliminar el mensaje para que no se vuelva a mostrar en la próxima carga
            localStorage.removeItem("productoInactivado");
        }
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
                "ESCRITORIO": ["ACRICOLOR", "ARTESCO", "BWHITE", "CASIO", "CHRISTMAS HOUSE", "CONDOR", "ENERGIZER", "FIVE STICK", "FRINGE CURTAIN", "ISOFIT", "MADISON", "MAXOFFICE", "MERTETTO", "MILCAR", "MONAMI", "PAPER ONE","RICOH", "TECNO"],
                "COCINA": ["SCOTT", "COPOBRAS", "BELEN"],
                "USO MEDICO": ["A&E", "BIOHIT", "BIOPLAST", "BRAUN", "BTL", "CAPULLO", "CUREBAND", "DRENACATH", "EARNIZ", "EKOSUR"],
                "PROMOCIONAL": ["S/M"],
                "LIMPIEZA": ["ARCHER", "ARISTECH", "ARMORALL", "BELEN", "BRISTAR", "CLIN", "ELITE", "HIGIA", "LIZ"],
                "CONSTRUCCION Y FERRETERIA": ["ABRO", "ADHEPLAS", "AMERICAN WORKS", "ARATY", "ARCELOR MITTAL", "NXL"],
                "INSUMOS DECORATIVOS": ["S/M"]
            }
        },
        "ACTIVO FIJO": {
            "seccion": [
                "ALMACEN","GERENCIA GENERAL","GERENCIA FINANCIERA", "GERENCIA COMERCIAL Y FINANCIERA", "SALA DE REUNIONES", "ZONA DE MONITOREO", "BAÑO GERENCIAL",
                "SALA DE ESPERA PLANTA ALTA", "COCINA", "BAÑO PLANTA ALTA", "OFICINA 1 PLANTA ALTA", "CONSULTORIO 1 PLANTA ALTA",
                "CONSULTORIO 2 PLANTA ALTA", "CONSULTORIO 3 PLANTA ALTA", "CONSULTORIO 4 PLANTA ALTA", "CONSULTORIO 5 PLANTA ALTA",
                "CONSULTORIO 6 PLANTA BAJA", "CONSULTORIO 7 PLANTA BAJA", "CONSULTORIO 8 PLANTA BAJA", "CONSULTORIO 9 PLANTA BAJA",
                "OFICINA 2 PLANTA BAJA", "OFICINA 3 PLANTA BAJA", "SALA DE ESPERA PLANTA BAJA", "SALA DE ATENCION AL CLIENTE", "SALA DE ESPERA", "SALA DE REUNIONES",
                "BAÑO PLANTA BAJA", "BAÑO CONSULTORIO 7 PLANTA BAJA", "DEPOSITO PRINCIPAL", "DEPOSITO SECUNDARIO",
                "PASILLO PLANTA ALTA", "PASILLO PLANTA BAJA", "GRADAS", "ENTRADA PRINCIPAL", "VISTA FRONTAL",
                "ADMINISTRACION","AUDIOMETRIA","CAJA","ELECTROCARDIOGRAMA","ERGOMETRIA","ESPIROMETRIA","FISIOTERAPIA-MEDICINA LABORAL","LABORATORIO",
                "OFICINA ADMINISTRATIVA","OFTALMOLOGIA","PRESTACIONES 1","PRESTACIONES 2","PROGRAMACION","PSICOLOGIA","SISTEMAS"
            ],
            "unidad_medida": ["UNIDADES"],
            "materia_prima": ["CONCRETO", "GOMA", "MADERA", "METALICO", "PLASTICO", "POLIESTER"],
            "marca": ["3D OPTICAL MOUSE", "AC-DELL", "ARRIX", "BIZLINK", "BREATHALYZER", "CONTEC", "DAHUA", "DIMAX", "DYMO", "ECCOSUR", "RICOH", "TECNO"]
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

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        $('.btn-pasarinterno').on('click', function () {
            var proveedorId = $(this).data('id');
            var proveedorNombre = $(this).data('nombre');
    
            Swal.fire({
                title: "¿Estás seguro?",
                text: "¿DESEAS PASAR A " + proveedorNombre + " A PERSONAL INTERNO?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#94c93b",
                cancelButtonColor: "#faa625",
                confirmButtonText: "SI, CONTINUAR",
                cancelButtonText: "CANCELAR"
            }).then((result) => {
                if (result.isConfirmed) {
                    console.log("Enviando petición AJAX...");
    
                    $.ajax({
                        url: "{{ route('admin.proveedoresservicios.pasarapersonal', ':id') }}".replace(':id', proveedorId),
                        type: "PUT",
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        success: function (response) {
                            console.log("Respuesta recibida:", response);
                            if (response.success) {
                                Swal.fire({
                                    title: "¡Éxito!",
                                    text: "El proveedor ha sido pasado a personal interno.",
                                    icon: "success",
                                    timer: 500,
                                    showConfirmButton: false
                                }).then(() => {
                                    // Redirigir a verproveedor/{id}
                                    window.location.href = "{{ route('admin.proveedoresservicios.verpersonal', ':id') }}".replace(':id', proveedorId);
                                });
                            } else {
                                Swal.fire("Error", "No se pudo actualizar el proveedor.", "error");
                            }
                        },
                        error: function (xhr, status, error) {
                            console.log("Error en la petición:", xhr.responseText);
                            Swal.fire("Error", "Hubo un problema al actualizar el proveedor.", "error");
                        }
                    });
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