@extends('adminlte::page')

@section('content_header')
{{-- <a class="btn float-right btn-botongris" style="margin-left: 10px;" href="{{ route('admin.inventarioW.index') }}">REGRESAR</a> --}}
<a class="btn btn-sm float-right btn-botongris" data-toggle="modal" data-target="#modalSolicitarBienes">NUEVA SOLICITUD</a>
<a class="btn btn-sm float-right btn-botonrojo" data-toggle="modal" data-target="#modalsolicitudesanulaciones">SOLIC. ANULADAS</a>
<h1>SOLICITUDES DE PRODUCTOS</h1>
@stop

@section('css')
<link rel="stylesheet" href="{{ asset('css/opcionesmultiples.css') }}">
<style>
    .truncar {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 150px;
        }
    .btn-botongris {
        background-color: #ffffff;
        color: #676767;
        border-color: #676767;
        border-radius: 5px;
        }
    .btn-botongris:hover {
        background-color: #676767;
        color: #ffffff;
        }
    .btn-botonrojo {
        background-color: #ffffff;
        color: #d72626;
        border-color: #d72626;
        border-radius: 5px;
        margin-right: 10px;
        }
    .btn-botonrojo:hover {
        background-color: #d72626;
        color: #ffffff;
        }
    .btn-botonaceptado {
        background-color: #ffffff;
        color: #ff7b00;
        border-color: #ff7b00;
        border-radius: 5px;
        }
    .btn-botonaceptado:hover {
        background-color: #ff7b00;
        color: #ffffff;
        }
    .btn-botonprocesado {
        background-color: #ffffff;
        color: #199442;
        border-color: #199442;
        border-radius: 5px;
        }
    .btn-botonprocesado:hover {
        background-color: #199442;
        color: #ffffff;
        }
    .btn-botonsubir {
        background-color: #ffffff;
        color: #3e57e5;
        border-color: #3e57e5;
        border-radius: 5px;
        }
    .btn-botonsubir:hover {
        background-color: #3e57e5;
        color: #ffffff;
        }
    .btn-botonofertado {
        background-color: #ffffff;
        color: #2661d7;
        border-color: #2661d7;
        border-radius: 5px;
        }
    .btn-botonofertado:hover {
        background-color: #2661d7;
        color: #ffffff;
        }
    .table td {
        padding: 4px 10px;
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
@if(session('success'))
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            alert("{{ session('success') }}");

            // Descargar el archivo automáticamente
            var downloadUrl = "{{ session('download_url') }}";
            if (downloadUrl) {
                var link = document.createElement("a");
                link.href = downloadUrl;
                link.download = "";
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);

                // Recargar la página después de la descarga
                setTimeout(function() {
                    location.reload();
                }, 2000);
            }
        });
    </script>
@endif

<div class="modal fade" id="modalSolicitarBienes" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modalLabel" style="font-weight: 900">NUEVA SOLICITUD</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('admin.inventario.guardarsolicitudproducto') }}" method="POST">
                @csrf
                <div class="modal-body">
                    
                    <input type="hidden" name="estado" value="SOLICITADO">
                    <input type="hidden" name="sucursal" value="{{ auth()->user()->sucursal }}">

                    <div class="form-group" hidden>
                        <label for="productosolicitado">Usuario Registro:</label>
                        <input type="text" name="usuarioregistro" class="form-control" value="{{ auth()->user()->name }}" readonly required>
                    </div>

                    @if (array_intersect($rolesUsuario, ['CONTABLE', 'ADMINISTRADOR']))
                        <div class="form-group">
                            <label for="tiposolicitante">Tipo de solicitante:</label>
                            <select id="tiposolicitante" class="form-control" onchange="mostrarSelect(this.value)">
                                <option value="">Seleccione una opción</option>
                                <option value="cliente_ita">CLIENTE ITA</option>
                                <option value="cliente_auditoria">CLIENTE AUDITORIA</option>
                                <option value="cliente_comun">CLIENTE COMUN</option>
                                <option value="proveedor_medico">PROVEEDOR MEDICO</option>
                                <option value="personal">PERSONAL</option>
                                <option value="otros">OTROS</option>
                            </select>
                        </div>

                        <div class="form-group" id="select_cliente_ita" style="display: none;">
                            <label>Usuario Solicitante - CLIENTE ITA:</label>
                            <select class="form-control" onchange="setSolicitante(this)">
                                <option value="">Seleccione</option>
                                @foreach($clientesITA as $cli)
                                    <option value="{{ $cli->id }}" data-nombre="{{ $cli->nombrecompleto }}">{{ $cli->nombrecompleto }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group" id="select_cliente_auditoria" style="display: none;">
                            <label>Usuario Solicitante - CLIENTE AUDITORIA:</label>
                            <select class="form-control" onchange="setSolicitante(this)">
                                <option value="">Seleccione</option>
                                @foreach($clientesAuditoria as $cli)
                                    <option value="{{ $cli->id }}" data-nombre="{{ $cli->nombrecompleto }}">{{ $cli->nombrecompleto }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group" id="select_cliente_comun" style="display: none;">
                            <label>Usuario Solicitante - CLIENTE COMUN:</label>
                            <select class="form-control" onchange="setSolicitante(this)">
                                <option value="">Seleccione</option>
                                @foreach($clientesComun as $cli)
                                    <option value="{{ $cli->id }}" data-nombre="{{ $cli->nombrecompleto }}">{{ $cli->nombrecompleto }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group" id="select_proveedor_medico" style="display: none;">
                            <label>Usuario Solicitante - PROVEEDOR MEDICO:</label>
                            <select class="form-control" onchange="setSolicitante(this)">
                                <option value="">Seleccione</option>
                                @foreach($proveedoresMedicos as $prov)
                                    <option value="{{ $prov->id }}" data-nombre="{{ $prov->proveedor }}">{{ $prov->proveedor }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group" id="select_personal" style="display: none;">
                            <label>Usuario Solicitante - PERSONAL:</label>
                            <select class="form-control" onchange="setSolicitante(this)">
                                <option value="">Seleccione</option>
                                @foreach($personal as $prov)
                                    <option value="{{ $prov->id }}" data-nombre="{{ $prov->name }}">{{ $prov->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group" id="select_otros" style="display: none;">
                            <label>Usuario Solicitante - OTROS:</label>
                            <select class="form-control" onchange="setSolicitante(this)">
                                <option value="">Seleccione</option>
                                <option value="CADECRUZ" data-nombre="CADECRUZ">CADECRUZ</option>
                                <option value="CLAUDIO SAUL LLANOS MENDOZA" data-nombre="CLAUDIO SAUL LLANOS MENDOZA">CLAUDIO SAUL LLANOS MENDOZA</option>
                                <option value="BRAYAN HURTADO ALVARADO" data-nombre="BRAYAN HURTADO ALVARADO">BRAYAN HURTADO ALVARADO</option>
                            </select>
                        </div>

                        <input type="hidden" name="usuariosolicitante_id" id="usuariosolicitante_id">
                        <input type="hidden" name="usuariosolicitante" id="usuariosolicitante">

                    @else
                        <div class="form-group">
                            <label for="usuariosolicitante">Usuario Solicitante:</label>
                            <input type="text" name="usuariosolicitante" class="form-control" value="{{ auth()->user()->name }}" readonly>
                        </div>
                    @endif
                    <script>
                        function mostrarSelect(valor) {
                            const selects = ['cliente_ita', 'cliente_auditoria', 'cliente_comun', 'proveedor_medico', 'personal', 'otros'];
                        
                            selects.forEach(tipo => {
                                document.getElementById('select_' + tipo).style.display = (valor === tipo) ? 'block' : 'none';
                            });
                        
                            document.getElementById('usuariosolicitante_id').value = '';
                        }
                        function setSolicitante(select) {
                            const id = select.value;
                            const nombre = select.options[select.selectedIndex].getAttribute('data-nombre');
                            document.getElementById('usuariosolicitante_id').value = id;
                            document.getElementById('usuariosolicitante').value = nombre;
                        }
                    </script>
    
                    <div class="row">
                        <div class="form-group col-lg-6">
                            <label for="buscar_producto">Buscar producto:</label>
                            <input list="listaProductos" id="buscar_producto" name="productosolicitadousuario" class="form-control" placeholder="Buscar producto..." oninput="actualizarProducto()">
                            {{-- <datalist id="listaProductos">
                                @foreach($productos as $producto)
                                    <option value="{{ $producto->nombreproducto }} - {{ $producto->marca }} - {{ $producto->especificacionmedida }} - {{ $producto->color }}"
                                            data-nombre="{{ $producto->nombreproducto }}"
                                            data-stock="{{ $producto->stockactual }}">
                                    </option>
                                @endforeach
                            </datalist> --}}
                            <datalist id="listaProductos">
                                @foreach($productos as $producto)
                                    @php
                                        $texto = "{$producto->nombreproducto} - {$producto->marca} - {$producto->especificacionmedida} - {$producto->color}";
                                    @endphp
                                    <option 
                                        value="{{ $texto }}" 
                                        title="{{ $texto }}"
                                        data-nombre="{{ $producto->nombreproducto }}"
                                        data-stock="{{ $producto->stockactual }}">
                                    </option>
                                @endforeach
                            </datalist>

                        </div>

                        <div class="form-group col-lg-6">
                            <label for="productosolicitado">Producto a solicitar:</label>
                            <input type="text" name="productosolicitado" id="productosolicitado" class="form-control" placeholder="Producto seleccionado" readonly required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group col-lg-6">
                            <label for="stock_disponible">Stock Disponible:</label>
                            <input type="number" id="stock_disponible" class="form-control" readonly>
                        </div>
                        <div class="form-group col-lg-6">
                            <label for="cantidad">Cantidad a solicitar (UNIDADES):</label>
                            <input type="number" name="cantidadsolicitud" id="cantidadsolicitud" class="form-control"
                                required min="1" step="1"
                                onkeydown="if(['e','E','+','-','.'].includes(event.key)) event.preventDefault();"
                                oninput="validarCantidad()">
                        </div>
                    </div>

                    <script>
                        function actualizarProducto() {
                            const input = document.getElementById("buscar_producto");
                            const datalist = document.getElementById("listaProductos");
                            const options = datalist.querySelectorAll("option");

                            const campoProducto = document.getElementById("productosolicitado");
                            const campoStock = document.getElementById("stock_disponible");
                            const cantidad = document.getElementById("cantidadsolicitud");

                            let encontrado = false;

                            options.forEach(option => {
                                if (option.value === input.value) {
                                    campoProducto.value = option.dataset.nombre;
                                    campoStock.value = option.dataset.stock;
                                    cantidad.max = option.dataset.stock;
                                    encontrado = true;
                                }
                            });

                            if (!encontrado) {
                                campoProducto.value = "";
                                campoStock.value = "";
                                cantidad.value = "";
                            }
                        }

                        function validarCantidad() {
                            const cantidad = parseInt(document.getElementById("cantidadsolicitud").value);
                            const stock = parseInt(document.getElementById("stock_disponible").value);

                            if (cantidad > stock) {
                                alert("No puedes solicitar más de lo disponible en stock.");
                                document.getElementById("cantidadsolicitud").value = "";
                            }
                        }
                    </script>

                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-sm btn-outline-secondary">SOLICITAR</button>
                    <button type="button" class="btn btn-sm btn-outline-danger" data-dismiss="modal">CERRAR</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalsolicitudesanulaciones" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modalLabel" style="font-weight: 900">SOLICITUDES ANULADAS</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead class="table-secondary">
                                <tr>
                                    <th>ID</th>
                                    <th>Solicitante</th>
                                    <th>Sucursal</th>
                                    <th>Producto_Cant_Solic.</th>
                                    <th>Solicitado</th>
                                    <th>Motivo_Anul.</th>
                                    <th>Usuario_Anul.</th>
                                    <th>Fecha_Anul.</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($solicitudesanuladas as $solicitudanulada)
                                    <tr>
                                        <td>{{$solicitudanulada->id}}</td>
                                        <td title="{{ $solicitudanulada->usuariosolicitante }}" class="truncar">{{$solicitudanulada->usuariosolicitante}}</td>
                                        <td>{{$solicitudanulada->sucursal}}</td>
                                        <td>{{$solicitudanulada->productosolicitado}} - {{$solicitudanulada->cantidad}}</td>
                                        <td>{{ \Carbon\Carbon::parse($solicitudanulada->created_at)->format('Y-m-d') }}</td>
                                        <td>{{$solicitudanulada->motivoanulacion}}</td>
                                        <td title="{{ $solicitudanulada->usuarioanulacion }}" class="truncar">{{$solicitudanulada->usuarioanulacion}}</td>
                                        <td>{{ \Carbon\Carbon::parse($solicitudanulada->deleted_at)->format('Y-m-d') }}</td>
                                        <td>
                                            <span class="badge badge-danger">{{ $solicitudanulada->estado }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody> 
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-outline-danger" data-dismiss="modal">CERRAR</button>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <ul class="nav nav-tabs card-header-tabs" id="myTabs">  
            <li class="nav-item">
                <a class="nav-link active" id="tab-1" data-toggle="tab" href="#tab-content-1" role="tab" aria-controls="tab-content-1" aria-selected="true">
                    SOLICITUDES PENDIENTES
                </a>
            </li>     
            <li class="nav-item">
                <a class="nav-link" id="tab-2" data-toggle="tab" href="#tab-content-2" role="tab" aria-controls="tab-content-2" aria-selected="true">
                    SOLICITUDES PROCESADAS
                </a>
            </li>     
        </ul>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            document.querySelectorAll('a[data-toggle="tab"]').forEach(tab => {
                tab.addEventListener('click', function () {
                    localStorage.setItem('pestana_activa', this.getAttribute('href'));
                });
            });
            let pestana = localStorage.getItem('pestana_activa');
            if (pestana) {
                const tabElement = document.querySelector(`a[href="${pestana}"]`);
                if (tabElement) {
                    new bootstrap.Tab(tabElement).show();
                }
            }
        });
    </script>
    <div class="card-body">
        <div class="tab-content" id="myTabContent">
            {{-- SOLICITUDES PENDIENTES --}}
            <div class="tab-pane fade show active" id="tab-content-1" role="tabpanel" aria-labelledby="tab-1">
                <div class="row ">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead class="table-secondary">
                                <tr>
                                    <th>ID</th>
                                    <th>Solicitante</th>
                                    <th>Sucursal</th>
                                    <th>Producto_Cantidad_Solicitado</th>
                                    <th>Solicitado</th>
                                    <th>Producto_Cantidad_Ofertado</th>
                                    <th>Estado</th>
                                    <th>Ver</th>
                                    @can('admin.inventario.anularsolicitudinventario')
                                    <th>{{-- <input type="checkbox" id="selectAll"> --}}Sel.</th>
                                    @endcan
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($solicitudinventarios as $solicitudinventario)
                                    @if ($solicitudinventario->estado != 'PROCESADO')
                                        @if (array_intersect($rolesUsuario, ['CONTABLE', 'ADMINISTRADOR', 'MAESTRO']) || $solicitudinventario->usuariosolicitante == $nombreUsuario)
                                            <tr>
                                                <td>{{$solicitudinventario->id}}</td>
                                                <td>{{$solicitudinventario->usuariosolicitante}}</td>
                                                <td>{{$solicitudinventario->sucursal}}</td>
                                                <td>{{$solicitudinventario->productosolicitadousuario}} - {{$solicitudinventario->cantidad}}</td>
                                                <td>{{ \Carbon\Carbon::parse($solicitudinventario->created_at)->format('Y-m-d') }}</td>
                                                <td>{{$solicitudinventario->productoofertado}} - {{$solicitudinventario->cantidadofertado}}</td>
                                                
                                                <td>
                                                    <span class="badge 
                                                        @if($solicitudinventario->estado == 'SOLICITADO') badge-warning  
                                                        @elseif($solicitudinventario->estado == 'OFERTADO') badge-primary 
                                                        @elseif($solicitudinventario->estado == 'ACEPTADO') badge-orange 
                                                        @elseif($solicitudinventario->estado == 'PROCESADO') badge-success 
                                                        @elseif($solicitudinventario->estado == 'FINALIZADO') badge-success 
                                                        @elseif($solicitudinventario->estado == 'EN ESPERA') badge-secondary 
                                                        @else badge-danger 
                                                        @endif">
                                                        {{ $solicitudinventario->estado }}
                                                    </span>
                                                </td>
                                                <td>
                                                    {{-- @php
                                                        $usuariosAutorizados = [
                                                            'CARLOS ALEJANDRO GUARACHI SANDOVAL',
                                                            'DENISSE MAUREN LOPEZ FLORES',
                                                            'SERGIO ARMANDO MICHEL MAITA',
                                                            'JHOSELINE EVA VELASQUEZ ESCOBAR',
                                                            'ROGER CANDIA JUSTINIANO'
                                                        ];
                                                    @endphp --}}

                                                    {{-- @if(
                                                        in_array(auth()->user()->name, $usuariosAutorizados) && 
                                                        in_array($solicitudinventario->estado, ['SOLICITADO', 'RECHAZADO', 'EN ESPERA'])
                                                    ) --}}

                                                    @if (
                                                        auth()->user()->hasAnyRole(['MAESTRO', 'ADMINISTRADOR', 'CONTABLE']) &&
                                                        in_array($solicitudinventario->estado, ['SOLICITADO', 'RECHAZADO', 'EN ESPERA'])
                                                    )
                                                    <a class="btn btn-botongris btn-sm" data-toggle="modal" data-target="#productoModal{{ $solicitudinventario->id }}" title="VER INVENTARIO">
                                                        <i class="fas fa-tag"></i>
                                                    </a>
                                                    @endif

                                                    <div class="modal fade" id="productoModal{{ $solicitudinventario->id }}" tabindex="-1" role="dialog" aria-labelledby="productoModalLabel{{ $solicitudinventario->id }}" aria-hidden="true">
                                                        <div class="modal-dialog modal-lg" role="document">
                                                            <div class="modal-content shadow-sm">
                                                                <div class="modal-header">
                                                                    <h4 class="modal-title font-weight-bold" id="productoModalLabel{{ $solicitudinventario->id }}">
                                                                        OFERTAR PRODUCTO
                                                                    </h4>
                                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                                                                        <span aria-hidden="true">&times;</span>
                                                                    </button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <div class="card">
                                                                        <div class="card-body" style="background-color: #f5f5f5; margin-bottom: -10px; margin-top: -10px;"> 
                                                                            <div class="mb-3">
                                                                                <h5>
                                                                                    Producto Solicitado: <span class="font-weight-bold">{{ $solicitudinventario->productosolicitadousuario }}</span><br>
                                                                                    Cantidad Solicitado: <span class="font-weight-bold">{{ $solicitudinventario->cantidad }}</span>
                                                                                </h5>
                                                                            </div>
                                                                        </div>
                                                                    </div><br>
                                                                    <div>
                                                                        <p class="font-weight-bold">COINCIDENCIAS EN INVENTARIO:</p>
                                                                        <ul class="list-group">
                                                                            @if(isset($coincidencias[$solicitudinventario->id]) && $coincidencias[$solicitudinventario->id]->isNotEmpty()) 
                                                                                @foreach($coincidencias[$solicitudinventario->id] as $coincidencia)
                                                                                    <li class="list-group-item">
                                                                                        <div class="d-flex justify-content-between align-items-center">
                                                                                            <div>
                                                                                                <strong>{{ $coincidencia->nombreproducto }}</strong> - {{ $coincidencia->marca }} - {{ $coincidencia->especificacionmedida }} - {{ $coincidencia->color }}<br>
                                                                                                <small>{{ $coincidencia->stockactual }}{{--  {{ $coincidencia->unidadmedida }} --}} EN STOCK</small>
                                                                                            </div>
                                                                                            <div>
                                                                                                <form action="{{ route('procesarsolicitud', $solicitudinventario->id) }}" method="POST" class="d-inline">
                                                                                                    @csrf
                                                                                                    <input type="hidden" name="producto_id" value="{{ $coincidencia->id }}">
                                                                                                    <button type="submit" class="btn btn-sm btn-outline-success" id="procesarBtn{{ $coincidencia->id }}">PROCESAR</button>
                                                                                                </form>
                                                                                                <form action="{{ route('ofertarProducto', $solicitudinventario->id) }}" method="POST" class="d-inline ml-2">
                                                                                                    @csrf
                                                                                                    <input type="hidden" name="producto_id" value="{{ $coincidencia->id }}">
                                                                                                    <input type="text" name="cantidadofertado" id="cantidadOferta{{ $coincidencia->id }}" class="form-control form-control-sm d-inline-block" style="width:80px;" oninput="toggleButtons({{ $coincidencia->id }})" placeholder="Cantidad" required>
                                                                                                    <button type="submit" class="btn btn-sm btn-outline-success" id="ofertarBtn{{ $coincidencia->id }}" style="display:none; margin-left:12px; width:80px;">OFERTAR</button>
                                                                                                </form>
                                                                                            </div>
                                                                                        </div>
                                                                                    </li>
                                                                                @endforeach
                                                                            @else
                                                                                <li class="list-group-item">No hay coincidencias en inventario.</li>
                                                                            @endif
                                                                        </ul>
                                                                    </div>
                                                                    @if($solicitudinventario->estado !== 'EN ESPERA')
                                                                        <form action="{{ route('solicitudinventario.espera', $solicitudinventario->id) }}" method="POST" class="mt-3">
                                                                            @csrf
                                                                            <button type="submit" class="btn btn-sm btn-outline-danger">PASAR A ESPERA</button>
                                                                        </form>
                                                                    @endif

                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    <script>
                                                        function toggleButtons(coincidenciaId) {
                                                            var ofertaInput = document.getElementById('cantidadOferta' + coincidenciaId);
                                                            var procesarBtn = document.getElementById('procesarBtn' + coincidenciaId);
                                                            var ofertarBtn = document.getElementById('ofertarBtn' + coincidenciaId);
                                                    
                                                            if (ofertaInput.value.trim() !== "") {
                                                                procesarBtn.style.display = 'none';
                                                                ofertarBtn.style.display = 'inline-block';
                                                            } else {
                                                                procesarBtn.style.display = 'inline-block';
                                                                ofertarBtn.style.display = 'none';
                                                            }
                                                        }
                                                    </script>
                                                    
                                                    @if($solicitudinventario->estado === 'OFERTADO' && auth()->user()->name == $solicitudinventario->usuariosolicitante)
                                                        <button type="button" class="btn btn-botonofertado btn-sm" title="ACEPTAR O RECHAZAR OFERTA" onclick="showConfirmationModal({{ $solicitudinventario->id }})">
                                                            <i class="fas fa-exchange-alt"></i>
                                                        </button>
                                                    @endif
                                                    @if($solicitudinventario->estado === 'OFERTADO' && auth()->user()->name == $solicitudinventario->usuariosolicitante)
                                                        <form action="{{ route('aceptarOferta', $solicitudinventario->id) }}" method="POST" style="display:none;" id="accept-form-{{ $solicitudinventario->id }}">
                                                            @csrf
                                                            @method('PUT')
                                                        </form>
                                                    @endif
                                                    @if($solicitudinventario->estado === 'OFERTADO' && auth()->user()->name == $solicitudinventario->usuariosolicitante)
                                                        <form action="{{ route('rechazarOferta', $solicitudinventario->id) }}" method="POST" style="display:none;" id="reject-form-{{ $solicitudinventario->id }}">
                                                            @csrf
                                                            @method('PUT')
                                                        </form>
                                                    @endif

                                                    <script> 
                                                        function showConfirmationModal(solicitudId) {
                                                            Swal.fire({
                                                                title: '¿Deseas aceptar o rechazar esta oferta?',
                                                                text: "",
                                                                icon: 'warning',
                                                                showCancelButton: true,
                                                                confirmButtonText: 'Aceptar',
                                                                cancelButtonText: 'Rechazar',
                                                                customClass: {
                                                                    confirmButton: 'btn btn-success',
                                                                    cancelButton: 'btn btn-danger'
                                                                },
                                                            }).then((result) => {
                                                                if (result.isConfirmed) {
                                                                    document.getElementById('accept-form-' + solicitudId).submit();
                                                                } else if (result.dismiss === Swal.DismissReason.cancel) {
                                                                    document.getElementById('reject-form-' + solicitudId).submit();
                                                                }
                                                            });
                                                        }
                                                    </script>
                                                    
                                                    {{-- @if(
                                                        in_array(auth()->user()->name, $usuariosAutorizados) && 
                                                        $solicitudinventario->estado === 'ACEPTADO'
                                                    ) --}}

                                                    @if (
                                                        auth()->user()->hasAnyRole(['MAESTRO', 'ADMINISTRADOR', 'CONTABLE']) &&
                                                        $solicitudinventario->estado === 'ACEPTADO'
                                                    )
                                                        <button type="button" class="btn btn-botonaceptado btn-sm" data-toggle="modal" data-target="#modalSolicitante{{ Str::slug($solicitudinventario->usuariosolicitante) }}" title="GENERAR COMPROBANTE">
                                                            <i class="fas fa-archive"></i>
                                                        </button>
                                                    @endif

                                                    @if(!is_null($solicitudinventario->documento))
                                                        <a href="{{ asset('comprobanteinventario/' . $solicitudinventario->usuarioactualizacionid . '/' . $solicitudinventario->documento) }}" 
                                                            target="_blank" class="btn btn-botonprocesado btn-sm" title="VER COMPROBANTE">
                                                            <i class="fas fa-file"></i>
                                                        </a>
                                                    @endif

                                                    <!-- Modal -->
                                                    <div class="modal fade" id="productoModal2{{ $solicitudinventario->id }}" tabindex="-1" role="dialog" aria-labelledby="productoModalLabel{{ $solicitudinventario->id }}" aria-hidden="true">
                                                        <div class="modal-dialog" role="document">
                                                            <div class="modal-content shadow-sm rounded">
                                                                <div class="modal-header">
                                                                    <h4 class="modal-title" style="font-weight: 900" id="productoModalLabel{{ $solicitudinventario->id }}">
                                                                        DETALLES DE SALIDA
                                                                    </h4>
                                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                                                                        <span aria-hidden="true">&times;</span>
                                                                    </button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <div class="mb-3">
                                                                        <p class="mb-3"><strong>Solicitante:</strong> {{ $solicitudinventario->usuariosolicitante }}</p>
                                                                        <p class="mb-3"><strong>Código de Producto:</strong> {{ $solicitudinventario->codigoproducto }}</p>
                                                                        <p class="mb-3"><strong>Producto Ofertado:</strong> {{ $solicitudinventario->productoofertado }}</p>
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        @php
                                                                            $productoDetalles = \App\Models\Inventario::whereRaw('TRIM(codigo) = ?', [trim($solicitudinventario->codigoproducto)])->first();
                                                                        @endphp

                                                                        @if($productoDetalles)
                                                                            <p class="mb-3"><strong>Especificaciones:</strong> {{ $productoDetalles->especificacionmedida }}</p>
                                                                            <p class="mb-3"><strong>Marca:</strong> {{ $productoDetalles->marca }}</p>
                                                                            <p class="mb-3"><strong>Color:</strong> {{ $productoDetalles->color }}</p>
                                                                            <p class="mb-3"><strong>Stock Actual en Inventario:</strong> {{ $productoDetalles->stockactual }}</p>
                                                                        @else
                                                                            <p class="text-muted">No se encontró información de inventario para este producto.</p>
                                                                        @endif
                                                                    </div>
                                                                    <hr>
                                                                    <form action="{{ route('actualizarStock', $solicitudinventario->id) }}" method="POST">
                                                                        @csrf
                                                                        @method('PUT')
                                                                        <div class="form-group">
                                                                            <label for="cantidad" class="font-weight-bold">Cantidad a Entregar:</label>
                                                                            <input type="number" class="form-control" id="cantidad" name="cantidad" value="{{ $solicitudinventario->cantidadofertado }}" required readonly>
                                                                        </div>
                                                                        <div class="text-right">
                                                                            <button type="submit" class="btn btn-outline-success">
                                                                                GENERAR COMPROBANTE
                                                                            </button>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    @php
                                                        $registrosAgrupados = $solicitudinventarios
                                                            ->where('estado', 'ACEPTADO')
                                                            ->groupBy('usuariosolicitante');
                                                    @endphp

                                                    @foreach ($registrosAgrupados as $usuario => $registros)
                                                    <div class="modal fade" id="modalSolicitante{{ Str::slug($usuario) }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel{{ Str::slug($usuario) }}" aria-hidden="true">
                                                        <div class="modal-dialog modal-lg" role="document">
                                                            <div class="modal-content border-0" style="background-color: #f8f9fa;">
                                                                <div class="modal-header border-bottom-0">
                                                                    <h5 class="modal-title font-weight-bold text-dark">SALIDA DE INVENTARIO PARA: {{ $usuario }}</h5>
                                                                    <button type="button" class="close text-dark" data-dismiss="modal" aria-label="Cerrar">
                                                                        <span aria-hidden="true">&times;</span>
                                                                    </button>
                                                                </div>
                                                                <form action="{{ route('generarComprobanteMasivo') }}" method="POST">
                                                                    @csrf
                                                                    <div class="modal-body">
                                                                        <div class="row justify-content-center">
                                                                            @foreach ($registros as $registro)
                                                                                @php
                                                                                    $producto = \App\Models\Inventario::whereRaw('TRIM(codigo) = ?', [trim($registro->codigoproducto)])->first();
                                                                                @endphp
                                                                                <input type="hidden" name="ids[]" value="{{ $registro->id }}">
                                                                                <div class="col-md-4 mb-3 d-flex align-items-stretch">
                                                                                    <div class="border p-3 w-100" style="background-color: #ffffff; border-radius: 8px;">
                                                                                        <p class="mb-2 text-dark"><strong>Producto:</strong> {{ $registro->productoofertado }}</p>
                                                                                        <p class="mb-2 text-dark"><strong>Código:</strong> {{ $registro->codigoproducto }}</p>
                                                                                        @if($producto)
                                                                                            <p class="mb-2 text-dark"><strong>Especif.:</strong> {{ $producto->especificacionmedida }}</p>
                                                                                            <p class="mb-2 text-dark"><strong>Color:</strong> {{ $producto->color }}</p>
                                                                                            <p class="mb-2 text-dark"><strong>Stock Actual:</strong> {{ $producto->stockactual }}</p>
                                                                                        @else
                                                                                            <p class="text-muted mb-0">Producto no encontrado en inventario.</p>
                                                                                        @endif
                                                                                        <p class="mb-0 text-dark"><strong>Cantidad a Entregar:</strong> {{ $registro->cantidadofertado }}</p>
                                                                                    </div>
                                                                                </div>
                                                                            @endforeach
                                                                        </div>
                                                                    </div>
                                                                    <div class="modal-footer border-top-0 d-flex justify-content-center">
                                                                        <button type="submit" class="btn btn-sm btn-outline-success">GENERAR COMPROBANTE</button>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @endforeach
                                                </td>
                                                @can('admin.inventario.anularsolicitudinventario')
                                                <td>
                                                    <input type="checkbox" class="check-solicitud" name="solicitudes[]" value="{{ $solicitudinventario->id }}">
                                                </td>
                                                @endcan
                                            </tr>
                                        @endif
                                    @endif
                                @endforeach
                            </tbody> 
                        </table>
                    </div>
                </div>
                @can('admin.inventario.anularsolicitudinventario')
                    {{-- <form action="{{ route('anular.solicitudes.inventario') }}" method="POST">
                    @csrf
                        <div class="form-group mt-3">
                            <div class="row justify-content-end align-items-center">
                                <div class="col-auto">
                                    <label for="motivo_anulacion" class="mb-0">Motivo de Anulación:</label>
                                </div>
                                <div class="col-auto">
                                    <input type="text" name="motivo_anulacion" id="motivo_anulacion" class="form-control form-control-sm" style="width: 250px;" required>
                                </div>
                                <div class="col-auto">
                                    <button type="submit" class="btn btn-outline-danger btn-sm">ANULAR</button>
                                </div>
                            </div>
                        </div>
                    </form> --}}
                    <form id="formAnulacion" action="{{ route('anular.solicitudes.inventario') }}" method="POST">
                        @csrf
                        <input type="hidden" name="solicitudes_json" id="solicitudes_json">
                        <div class="form-group mt-3">
                            <div class="row justify-content-end align-items-center">
                                <div class="col-auto">
                                    <label for="motivo_anulacion" class="mb-0">Motivo de Anulación:</label>
                                </div>
                                <div class="col-auto">
                                    <input type="text" name="motivo_anulacion" id="motivo_anulacion" class="form-control form-control-sm" style="width: 250px;" required>
                                </div>
                                <div class="col-auto">
                                    <button type="submit" class="btn btn-outline-danger btn-sm">ANULAR</button>
                                </div>
                            </div>
                        </div>
                    </form>
                    <script>
                        document.getElementById('formAnulacion').addEventListener('submit', function (e) {
                            const checkboxes = document.querySelectorAll('.check-solicitud:checked');
                            const ids = Array.from(checkboxes).map(cb => cb.value);
                            document.getElementById('solicitudes_json').value = JSON.stringify(ids);
                        });

                        // Checkbox select all
                        document.getElementById('selectAll')?.addEventListener('change', function () {
                            document.querySelectorAll('.check-solicitud').forEach(chk => {
                                chk.checked = this.checked;
                            });
                        });
                    </script>
                @endcan
                <script>
                    document.getElementById('selectAll').addEventListener('change', function() {
                        document.querySelectorAll('.check-solicitud').forEach(chk => {
                            chk.checked = this.checked;
                        });
                    });
                </script>
            </div>

            {{-- SOLICITUDES PROCESADAS --}}
            <div class="tab-pane fade" id="tab-content-2" role="tabpanel" aria-labelledby="tab-2">
                <div class="card">
                    <div class="card-body" style="background-color: #f7f7f7">
                        <div class="row">
                            <div class="col-lg-5">
                                <label for="">Cliente/Proveedor:</label>
                                <input type="text" id="buscarUsuario" class="form-control" placeholder="Buscar por solicitante...">
                            </div>
                            <div class="col-lg-3">
                                <label for="">Sucursal:</label>
                                <select id="filtroSucursal" class="form-control">
                                    <option value="">Buscar por sucursal...</option>
                                    <option value="SANTA CRUZ">SANTA CRUZ</option>
                                    <option value="COCHABAMBA">COCHABAMBA</option>
                                </select>
                            </div>
                            <div class="col-lg-3">
                                <label for="">Fecha de Entrega:</label>
                                <input type="date" id="filtroFecha" class="form-control" placeholder="Filtrar por fecha de entrega">
                            </div>
                            <div class="col-lg-1">
                                <label class="d-block" style="visibility: hidden;">.</label>
                                <button type="button" class="btn btn-secondary btn-block" onclick="limpiarFiltros()">Limpiar</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead class="table-secondary">
                                <tr>
                                    <th>ID</th>
                                    <th>Solicitante</th>
                                    <th>Sucursal</th>
                                    <th>Producto_Cantidad_Solicitado</th>
                                    <th>Solicitado</th>
                                    <th>Producto_Cantidad_Ofertado</th>
                                    <th>Encargado_Entrega</th>
                                    <th>Fecha_Entrega</th>
                                    <th>Estado</th>
                                    <th>Ver</th>
                                </tr>
                            </thead>
                            <tbody id="tablaSolicitudes">
                                @foreach ($solicitudinventarios as $solicitudinventario)
                                @if ($solicitudinventario->estado == 'PROCESADO')
                                @if (array_intersect($rolesUsuario, ['CONTABLE', 'ADMINISTRADOR', 'MAESTRO']) || $solicitudinventario->usuariosolicitante == $nombreUsuario)
                                    <tr>
                                        <td>{{$solicitudinventario->id}}</td>
                                        <td class="col-usuario truncar" title="{{ $solicitudinventario->usuariosolicitante }}">{{$solicitudinventario->usuariosolicitante}}</td>
                                        <td class="col-sucursal">{{$solicitudinventario->sucursal}}</td>
                                        <td title="{{ $solicitudinventario->productosolicitado }} - {{ $solicitudinventario->cantidad }}" class="truncar">{{$solicitudinventario->productosolicitado}} - {{$solicitudinventario->cantidad}}</td>
                                        <td>{{ \Carbon\Carbon::parse($solicitudinventario->created_at)->format('Y-m-d') }}</td>
                                        <td title="{{ $solicitudinventario->productoofertado }} - {{ $solicitudinventario->cantidadofertado }}" class="truncar">{{$solicitudinventario->productoofertado}} - {{$solicitudinventario->cantidadofertado}}</td>
                                        <td title="{{ $solicitudinventario->usuarioactualizacion }}" class="truncar">{{$solicitudinventario->usuarioactualizacion}}</td>
                                        <td class="col-fecha">{{ \Carbon\Carbon::parse($solicitudinventario->updated_at)->format('Y-m-d') }}</td>
                                        <td>
                                            <span class="badge 
                                                @if($solicitudinventario->estado == 'SOLICITADO') badge-warning  
                                                @elseif($solicitudinventario->estado == 'OFERTADO') badge-primary 
                                                @elseif($solicitudinventario->estado == 'ACEPTADO') badge-orange 
                                                @elseif($solicitudinventario->estado == 'PROCESADO') badge-success 
                                                @elseif($solicitudinventario->estado == 'FINALIZADO') badge-success 
                                                @elseif($solicitudinventario->estado == 'EN ESPERA') badge-secondary 
                                                @else badge-danger 
                                                @endif">
                                                {{ $solicitudinventario->estado }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($solicitudinventario->estado === 'PROCESADO' && is_null($solicitudinventario->documento))
                                            <button type="button" class="btn btn-botonsubir btn-sm" data-toggle="modal" data-target="#subirDocumentoModal{{ $solicitudinventario->id }}">
                                                <i class="fas fa-upload"></i>
                                            </button>
                                            @endif
                                            <div class="modal fade" id="subirDocumentoModal{{ $solicitudinventario->id }}" tabindex="-1" role="dialog" aria-labelledby="subirDocumentoLabel" aria-hidden="true">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" style="font-weight: 900;">SUBIR RESPALDO DE ENTREGA</h5>
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <form action="{{ route('subirDocumento', $solicitudinventario->id) }}" method="POST" enctype="multipart/form-data">
                                                                @csrf
                                                                @method('PUT')
                                                                <div class="form-group">
                                                                    <label for="documento">Seleccionar archivo</label>
                                                                    <input type="file" class="form-control" name="documento" required accept=".pdf,.jpg,.png">
                                                                </div>
                                                                <button type="submit" class="btn btn-sm btn-outline-success">GUARDAR RESPALDO</button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            @if(!is_null($solicitudinventario->documento))
                                            <a href="{{ asset('comprobanteinventario/' . $solicitudinventario->usuarioactualizacionid . '/' . $solicitudinventario->documento) }}" 
                                                target="_blank" class="btn btn-botonprocesado btn-sm" title="VER COMPROBANTE">
                                                    <i class="fas fa-file"></i>
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                    @endif
                                    @endif
                                @endforeach
                            </tbody> 
                            <script>
                                document.addEventListener('DOMContentLoaded', function () {
                                    const inputUsuario = document.getElementById('buscarUsuario');
                                    const inputSucursal = document.getElementById('filtroSucursal');
                                    const inputFecha = document.getElementById('filtroFecha');
                                    const tabla = document.getElementById('tablaSolicitudes');
                                    const filas = tabla.querySelectorAll('tr');

                                    function filtrarTabla() {
                                        const textoUsuario = inputUsuario.value.toLowerCase();
                                        const sucursalSeleccionada = inputSucursal.value.toLowerCase();
                                        const fechaSeleccionada = inputFecha.value;

                                        filas.forEach(fila => {
                                            const usuario = fila.querySelector('.col-usuario')?.textContent.toLowerCase() || '';
                                            const sucursal = fila.querySelector('.col-sucursal')?.textContent.toLowerCase() || '';
                                            const fecha = fila.querySelector('.col-fecha')?.textContent.trim() || '';

                                            const coincideUsuario = usuario.includes(textoUsuario);
                                            const coincideSucursal = !sucursalSeleccionada || sucursal === sucursalSeleccionada;
                                            const coincideFecha = !fechaSeleccionada || fecha === fechaSeleccionada;

                                            if (coincideUsuario && coincideSucursal && coincideFecha) {
                                                fila.style.display = '';
                                            } else {
                                                fila.style.display = 'none';
                                            }
                                        });
                                    }

                                    inputUsuario.addEventListener('input', filtrarTabla);
                                    inputSucursal.addEventListener('change', filtrarTabla);
                                    inputFecha.addEventListener('change', filtrarTabla);
                                });

                                function limpiarFiltros() {
                                    document.getElementById('buscarUsuario').value = '';
                                    document.getElementById('filtroSucursal').value = '';
                                    document.getElementById('filtroFecha').value = '';

                                    const filas = document.querySelectorAll('#tablaSolicitudes tr');
                                    filas.forEach(fila => fila.style.display = '');
                                }
                            </script>
                        </table>
                        <div class="d-flex justify-content-center mt-3">
                        {{ $solicitudinventarios->withQueryString()->fragment('tab-content-2')->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<style>
    .badge-orange {
        background-color: #fd7e14;
        color: white;
    }
    .badge-warning {
        color: white;
    }
    .badge-primary {
        color: white;
    }
    .badge-success {
        color: white;
    }

</style>
@endsection

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