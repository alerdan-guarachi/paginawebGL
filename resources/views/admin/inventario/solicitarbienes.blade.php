@extends('adminlte::page')

@section('content_header')
<a class="btn float-right btn-botongris" style="margin-left: 10px;" href="{{ route('admin.inventario.index') }}">REGRESAR</a>
<a class="btn float-right btn-botongris" data-toggle="modal" data-target="#modalSolicitarBienes">NUEVA SOLICITUD</a>
<h1>SOLICITUDES DE INVENTARIO</h1>
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
                    <input type="hidden" name="usuarioregistroid" value="{{ auth()->user()->id }}">
                    <input type="hidden" name="usuariosolicitante" value="{{ auth()->user()->name }}">
                    <input type="hidden" name="estado" value="SOLICITADO">
                    <input type="hidden" name="sucursal" value="{{ auth()->user()->sucursal }}">

                    <div class="form-group">
                        <label for="productosolicitado">Producto solicitado:</label>
                        <input type="text" name="productosolicitado" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="cantidad">Cantidad:</label>
                        <input type="text" name="cantidad" class="form-control" required>
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
    <div class="card-body">
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
                                            'VANESSA MAMANI HUANACO',
                                            'MARLENE ANDREA MONTELLANO ORTIZ',
                                            'CRISTHIAN ALAIN DURAN SULLCA',
                                            'JHOSELINE EVA VELASQUEZ ESCOBAR'
                                        ];
                                    @endphp

                                    @if(in_array(auth()->user()->name, $usuariosAutorizados) && in_array($solicitudinventario->estado, ['SOLICITADO', 'RECHAZADO']))
                                    <a class="btn btn-botongris btn-sm" data-toggle="modal" data-target="#productoModal{{ $solicitudinventario->id }}" title="VER INVENTARIO">
                                        <i class="fas fa-tag"></i>
                                    </a>
                                    @endif

                                    <div class="modal fade" id="productoModal{{ $solicitudinventario->id }}" tabindex="-1" role="dialog" aria-labelledby="productoModalLabel{{ $solicitudinventario->id }}" aria-hidden="true"> 
                                        <div class="modal-dialog modal-lg" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="productoModalLabel{{ $solicitudinventario->id }}" style="font-weight: 900">COINCIDENCIAS EN INVENTARIO</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <h5>Producto: {{ $solicitudinventario->productosolicitado }} - Cantidad: {{ $solicitudinventario->cantidad }}</h5>

                                                    COINCIDENCIAS:
                                                    <ul>
                                                        @if(isset($coincidencias[$solicitudinventario->id]) && $coincidencias[$solicitudinventario->id]->isNotEmpty()) 
                                                            @foreach($coincidencias[$solicitudinventario->id] as $coincidencia)
                                                                <li>
                                                                    {{ $coincidencia->nombreproducto }} - {{ $coincidencia->marca }} - {{ $coincidencia->stockactual }} {{ $coincidencia->unidadmedida }} EN STOCK
                                                                    <form action="{{ route('procesarsolicitud', $solicitudinventario->id) }}" method="POST" class="d-inline">
                                                                        @csrf
                                                                        <input type="hidden" name="producto_id" value="{{ $coincidencia->id }}">
                                                                        <button type="submit" class="btn btn-primary btn-sm" id="procesarBtn{{ $coincidencia->id }}">Procesar</button>
                                                                    </form>
                                                                    <form action="{{ route('ofertarProducto', $solicitudinventario->id) }}" method="POST" class="d-inline">
                                                                        @csrf
                                                                        <input type="hidden" name="producto_id" value="{{ $coincidencia->id }}">
                                                                        <input type="text" name="cantidadofertado" id="cantidadOferta{{ $coincidencia->id }}" oninput="toggleButtons({{ $coincidencia->id }})">
                                                                        <button type="submit" class="btn btn-primary btn-sm" id="ofertarBtn{{ $coincidencia->id }}" style="display:none;">Ofertar</button>
                                                                    </form>
                                                                </li>
                                                            @endforeach
                                                        @else
                                                            <li>No hay coincidencias en inventario.</li>
                                                        @endif
                                                        <script>
                                                            function toggleButtons(coincidenciaId) {
                                                            var ofertaInput = document.getElementById('cantidadOferta' + coincidenciaId);
                                                            var procesarBtn = document.getElementById('procesarBtn' + coincidenciaId);
                                                            var ofertarBtn = document.getElementById('ofertarBtn' + coincidenciaId);

                                                            // Si el campo de cantidad ofertada tiene valor, ocultamos el botón de "Procesar" y mostramos el de "Ofertar"
                                                            if (ofertaInput.value.trim() !== "") {
                                                                procesarBtn.style.display = 'none';  // Ocultar el botón de "Procesar"
                                                                ofertarBtn.style.display = 'inline-block';  // Mostrar el botón de "Ofertar"
                                                            } else {
                                                                procesarBtn.style.display = 'inline-block';  // Mostrar el botón de "Procesar"
                                                                ofertarBtn.style.display = 'none';  // Ocultar el botón de "Ofertar"
                                                            }
                                                        }

                                                        </script>
                                                    </ul>

                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- @if($solicitudinventario->estado === 'OFERTADO' && auth()->user()->id == $solicitudinventario->usuarioregistroid)
                                        <form action="{{ route('aceptarOferta', $solicitudinventario->id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="btn btn-botongris btn-sm" title="ACEPTAR OFERTA">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                    @endif --}}
                                    @if($solicitudinventario->estado === 'OFERTADO' && auth()->user()->id == $solicitudinventario->usuarioregistroid)
                                        <button type="button" class="btn btn-botonofertado btn-sm" title="ACEPTAR O RECHAZAR OFERTA" onclick="showConfirmationModal({{ $solicitudinventario->id }})">
                                            <i class="fas fa-exchange-alt"></i>
                                        </button>
                                    @endif

                                    @if($solicitudinventario->estado === 'OFERTADO' && auth()->user()->id == $solicitudinventario->usuarioregistroid)
                                        <form action="{{ route('aceptarOferta', $solicitudinventario->id) }}" method="POST" style="display:none;" id="accept-form-{{ $solicitudinventario->id }}">
                                            @csrf
                                            @method('PUT')
                                        </form>
                                    @endif
                                    @if($solicitudinventario->estado === 'OFERTADO' && auth()->user()->id == $solicitudinventario->usuarioregistroid)
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
                                        <button type="button" class="btn btn-botonaceptado btn-sm" data-toggle="modal" data-target="#productoModal2{{ $solicitudinventario->id }}" title="GENERAR COMPROBANTE">
                                            <i class="fas fa-archive"></i>
                                        </button>
                                    @endif
                                    @if($solicitudinventario->estado === 'PROCESADO')
                                        <a href="{{ asset('comprobanteinventario/' . $solicitudinventario->usuarioregistroid . '/' . $solicitudinventario->documento) }}" 
                                        target="_blank" class="btn btn-botonprocesado btn-sm" title="VER COMPROBANTE">
                                            <i class="fas fa-file"></i>
                                        </a>
                                    @endif

                                    <!-- Modal -->
                                    <div class="modal fade" id="productoModal2{{ $solicitudinventario->id }}" tabindex="-1" role="dialog" aria-labelledby="productoModalLabel" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="productoModalLabel">Detalles de la Oferta</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <p><strong>Código de Producto:</strong> {{ $solicitudinventario->codigoproducto }}</p>
                                                    <p><strong>Producto Ofertado:</strong> {{ $solicitudinventario->productoofertado }}</p>
                                                    
                                                    @if($productoDetalles)
                                                        <p><strong>Stock Actual:</strong> {{ $productoDetalles->stockactual }}</p>
                                                        <p><strong>Unidad de Medida:</strong> {{ $productoDetalles->unidadmedida }}</p>
                                                    @else
                                                        <p>No se encontró información de inventario para este producto.</p>
                                                    @endif


                                                    <form action="{{ route('actualizarStock', $solicitudinventario->id) }}" method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class="form-group">
                                                            <label for="cantidad">Cantidad a Entregar:</label>
                                                            <input type="number" class="form-control" id="cantidad" name="cantidad" value="{{ $solicitudinventario->cantidadofertado }}" required>
                                                        </div>
                                                        <button type="submit" class="btn btn-primary">Actualizar Stock</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </td>
                            </tr>
                        @endforeach
                    </tbody> 
                </table>
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