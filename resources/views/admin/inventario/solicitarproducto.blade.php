@extends('adminlte::page')

@section('content_header')
{{-- <a class="btn float-right btn-botongris" style="margin-left: 10px;" href="{{ route('admin.inventarioW.index') }}">REGRESAR</a> --}}
<a class="btn float-right btn-botongris" data-toggle="modal" data-target="#modalSolicitarBienes">NUEVA SOLICITUD</a>
<h1>SOLICITUDES DE PRODUCTOS</h1>
@stop

@section('css')
<link rel="stylesheet" href="{{ asset('css/opcionesmultiples.css') }}">
<style>
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
        padding: 5px 10px;
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
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalLabel" style="font-weight: 900">REGISTRAR NUEVA SOLICITUD</h5>
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
                                    <option value="{{ $prov->id }}" data-nombre="{{ $prov->razonsocial }}">{{ $prov->razonsocial }}</option>
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
    

                    <div class="form-group">
                        <label for="productosolicitado">Producto solicitado:</label>
                        <input type="text" name="productosolicitado" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="cantidad">Cantidad (UNIDADES):</label>
                        <input type="number" name="cantidadsolicitud" class="form-control" required min="1" step="1" onkeydown="if(['e','E','+','-','.'].includes(event.key)) event.preventDefault();">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-outline-secondary">SOLICITAR</button>
                    <button type="button" class="btn btn-outline-danger" data-dismiss="modal">CERRAR</button>
                </div>
            </form>
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
    <div class="card-body">
        <div class="tab-content" id="myTabContent">
            {{-- SOLICITUDES PENDIENTES --}}
            <div class="tab-pane fade show active" id="tab-content-1" role="tabpanel" aria-labelledby="tab-1">
                <div class="row ">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Solicitante</th>
                                    <th>Producto y cantidad solicitado</th>
                                    <th>Solicitado</th>
                                    <th hidden>Sucursal</th>
                                    <th>Producto y cantidad ofertado</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($solicitudinventarios as $solicitudinventario)
                                    @if ($solicitudinventario->estado != 'PROCESADO')
                                        @if (array_intersect($rolesUsuario, ['CONTABLE', 'ADMINISTRADOR', 'MAESTRO']) || $solicitudinventario->usuariosolicitante == $nombreUsuario)
                                            <tr>
                                                <td>{{$solicitudinventario->id}}</td>
                                                <td>{{$solicitudinventario->usuariosolicitante}}</td>
                                                <td>{{$solicitudinventario->productosolicitado}} - {{$solicitudinventario->cantidad}}</td>
                                                <td>{{ \Carbon\Carbon::parse($solicitudinventario->created_at)->format('Y-m-d') }}</td>
                                                <td hidden>{{$solicitudinventario->sucursal}}</td>
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
                                                    @php
                                                        $usuariosAutorizados = [
                                                            'CARLOS ALEJANDRO GUARACHI SANDOVAL',
                                                            'DENISSE MAUREN LOPEZ FLORES',
                                                            'ROLANDO RAFAEL RAMOS TORRICO',
                                                            'MARLENE ANDREA MONTELLANO ORTIZ',
                                                            'CRISTHIAN ALAIN DURAN SULLCA',
                                                            'JHOSELINE EVA VELASQUEZ ESCOBAR'
                                                        ];
                                                    @endphp

                                                    @if(in_array(auth()->user()->name, $usuariosAutorizados) && in_array($solicitudinventario->estado, ['SOLICITADO', 'RECHAZADO', 'EN ESPERA']))
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
                                                                                    Producto Solicitado: <span class="font-weight-bold">{{ $solicitudinventario->productosolicitado }}</span><br>
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
                                                                                                <strong>{{ $coincidencia->nombreproducto }}</strong> - {{ $coincidencia->especificacionmedida }} - {{ $coincidencia->color }} - {{ $coincidencia->marca }}<br>
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
                                                    
                                                    <!-- Script para alternar botones según se ingrese cantidad -->
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
                                                    
                                                    @if(in_array(auth()->user()->name, $usuariosAutorizados) && $solicitudinventario->estado === 'ACEPTADO')
                                                        {{-- <button type="button" class="btn btn-botonaceptado btn-sm" data-toggle="modal" data-target="#productoModal2{{ $solicitudinventario->id }}" title="GENERAR COMPROBANTE">
                                                            <i class="fas fa-archive"></i>
                                                        </button> --}}
                                                        <button type="button" class="btn btn-botonaceptado btn-sm" data-toggle="modal" data-target="#modalSolicitante{{ Str::slug($solicitudinventario->usuariosolicitante) }}">
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
                                            </tr>
                                        @endif
                                    @endif
                                @endforeach
                            </tbody> 
                        </table>
                    </div>
                </div>
            </div>

            {{-- SOLICITUDES PROCESADAS --}}
            <div class="tab-pane fade" id="tab-content-2" role="tabpanel" aria-labelledby="tab-2">
                <div class="row ">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Solicitante</th>
                                    <th>Producto y cantidad solicitado</th>
                                    <th>Solicitado</th>
                                    <th hidden>Sucursal</th>
                                    <th>Producto y cantidad ofertado</th>
                                    <th>Entregado</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($solicitudinventarios as $solicitudinventario)
                                @if ($solicitudinventario->estado == 'PROCESADO')
                                @if (array_intersect($rolesUsuario, ['CONTABLE', 'ADMINISTRADOR', 'MAESTRO']) || $solicitudinventario->usuariosolicitante == $nombreUsuario)
                                    <tr>
                                        <td>{{$solicitudinventario->id}}</td>
                                        <td>{{$solicitudinventario->usuariosolicitante}}</td>
                                        <td>{{$solicitudinventario->productosolicitado}} - {{$solicitudinventario->cantidad}}</td>
                                        <td>{{ \Carbon\Carbon::parse($solicitudinventario->created_at)->format('Y-m-d') }}</td>
                                        <td hidden>{{$solicitudinventario->sucursal}}</td>
                                        <td>{{$solicitudinventario->productoofertado}} - {{$solicitudinventario->cantidadofertado}}</td>
                                        <td>{{ \Carbon\Carbon::parse($solicitudinventario->updated_at)->format('Y-m-d') }}</td>
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
                                            <button type="button" class="btn btn-outline-primary btn-sm" data-toggle="modal" data-target="#subirDocumentoModal{{ $solicitudinventario->id }}">
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
                        </table>
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