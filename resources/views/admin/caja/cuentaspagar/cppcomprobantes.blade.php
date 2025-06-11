@extends('adminlte::page')

@section('content_header')
<h1>CUENTAS POR PAGAR COMPROBANTES</h1>
@stop

@section('css')
<link rel="stylesheet" href="{{ asset('css/cuentascobrarpagar.css') }}">
<style>
    .table td {
        padding: 5px 10px;
    }
    .btn-botongris {
        background-color: #676767;
        color: #ffffff;
        border-color: #676767;
        border-radius: 5px;
        padding: 1px 3px;
    }
    .btn-botongris:hover {
        background-color: #676767;
        color: #ffffff;
        }
    .btn-botongrisgrande {
        background-color: #ffffff;
        color: #676767;
        border-color: #676767;
        border-radius: 5px;
        padding: 5px 10px;
    }
    .btn-botongrisgrande:hover {
        background-color: #676767;
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
    <div class="card-header">
        <ul class="nav nav-tabs card-header-tabs" id="myTabs">  
            <li class="nav-item">
                <a class="nav-link active" id="tab-2" data-toggle="tab" href="#tab-content-2" role="tab" aria-controls="tab-content-2" aria-selected="true">
                    PENDIENTES
                </a>
            </li> 
            <li class="nav-item">
                <a class="nav-link" id="tab-1" data-toggle="tab" href="#tab-content-1" role="tab" aria-controls="tab-content-1" aria-selected="true">
                    COMPLETOS
                </a>
            </li>         
        </ul>
    </div>

        <div class="card-body">
            <div class="tab-content" id="myTabContent">
            
                {{-- PENDIENTES --}}
                <div class="tab-pane fade show active" id="tab-content-2" role="tabpanel" aria-labelledby="tab-2">
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive" style="max-height: 70vh;">
                                <h4 style="font-weight:900;">CUENTAS POR PAGAR</h4>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Selec.</th>
                                                <th hidden>ID</th>
                                                <th>Orden.ID</th>
                                                <th>Proveedor</th>
                                                <th>Detalle</th>
                                                <th>Fecha_Pago</th>
                                                <th>Tipo_Planilla</th>
                                                <th>Banco_Origen</th>
                                                <th>Banco_Destino</th>
                                                <th>N.Cuenta</th>
                                                <th>Monto_Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($cuentaspagar->where('comprobante', null)->whereIn('estadoaprobacion', ['APROBADO', 'SUBIDO'])->sortByDesc('nrobancoorigen') as $item)
                                                @php
                                                    $tipoplanilla = optional($item->proveedorServicio)->tipoplanilla ?? 'NO DEFINIDO';
                                                @endphp

                                                @if (in_array($tipoplanilla, ['PAGO QR', 'PAGO EN LINEA','PAGO A TERCERO', 'PAGO INTERBANCARIO']))
                                                    @php
                                                        $archivoExistente = \App\Models\PlanillasPagosGeneradas::where('proveedor', $item->proveedornombre)
                                                            ->where('fechapago', $item->fechaasignada)
                                                            ->first();
                                                        $archivoExistentefijo = \App\Models\ProveedoresServicios::where('razonsocial', $item->proveedornombre)->first();
                                                    @endphp
                                                    <tr>
                                                        <td>
                                                            @if ($item->comprobante)
                                                                <a class="btn btn-sm btn-botongris mt-1" 
                                                                href="{{ asset('comprobantescuentaspagar/' . $item->comprobante) }}" 
                                                                target="_blank" title="Ver comprobante">
                                                                    <i class="fas fa-file-alt"></i>
                                                                </a>
                                                            @else
                                                            <input type="checkbox" class="checkbox-cuentas" value="{{ $item->id }}">
                                                            @endif
                                                        </td>
                                                        <td hidden>{{ $item->id }}</td>
                                                        <td>{{ $item->ordenid }}</td>
                                                        <td>{{ $item->proveedornombre }}</td>
                                                        <td>{{ $item->detalleproducto }}</td>
                                                        <td>{{ $item->fechaasignada }}</td>
                                                        <td>
                                                            {{ $tipoplanilla }}
                                                            @if ($tipoplanilla === 'PAGO QR' && $archivoExistente)
                                                                <a class="btn btn-botongris" 
                                                                    onclick="mostrarQR('{{ asset('planillaspagosgeneradas/' . str_replace('-', '', $archivoExistente->fechapago) . '/' . $archivoExistente->documento) }}')">
                                                                    <i class="fas fa-qrcode"></i>
                                                                </a>
                                                            @endif
                                                            
                                                            @if ($tipoplanilla === 'PAGO QR' && ($archivoExistentefijo && $archivoExistentefijo->imagenqr))
                                                                <a class="btn btn-botongris" 
                                                                    onclick="mostrarQR('{{ asset('proveedoresdocumentos/' . $archivoExistentefijo->id . '/' . $archivoExistentefijo->imagenqr) }}')">
                                                                    <i class="fas fa-qrcode"></i>
                                                                </a>
                                                            @endif
                                                        </td>
                                                        <td>{{ $item->nrobancoorigen }}</td>
                                                        <td>{{ $item->proveedorServicio->banco ?? '0' }}</td>
                                                        <td>{{ $item->proveedorServicio->numcuenta ?? '0' }}</td>
                                                        <td class="text-right">{{ number_format($item->montototal, 2) }}</td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <br>
                                <h4 style="font-weight:900;">PROVEEDORES MÉDICOS</h4>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Selec.</th>
                                                <th hidden>ID</th>
                                                <th>Orden.ID</th>
                                                <th>Proveedor</th>
                                                <th>Cliente</th>
                                                <th>Detalle</th>
                                                <th>Fecha_Pago</th>
                                                <th>Tipo_Planilla</th>
                                                <th>Banco_Origen</th>
                                                <th>Banco_Destino</th>
                                                <th>N.Cuenta</th>
                                                <th>Monto_Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($registrosbateria->where('comprobante', null)->whereIn('estadoaprobacion', ['APROBADO', 'SUBIDO'])->sortByDesc('nrobancoorigen') as $item)
                                                @php
                                                    $tipoplanilla = $proveedoresServicios[$item->proveedorasignado] ?? 'NO DEFINIDO';
                                                @endphp

                                                @if (in_array($tipoplanilla, ['PAGO QR', 'PAGO EN LINEA','PAGO A TERCERO', 'PAGO INTERBANCARIO']))
                                                    @php
                                                        $archivoExistente = \App\Models\PlanillasPagosGeneradas::where('proveedor', $item->proveedorasignado)
                                                            ->where('fechapago', $item->fechapago)
                                                            ->first();
                                                            $archivoExistentefijo = \App\Models\Proveedor::where('proveedor', $item->proveedorasignado)->first();
                                                    @endphp
                                                    <tr>
                                                        <td>
                                                            @if ($item->comprobante)
                                                                <a class="btn btn-sm btn-primary mt-1" 
                                                                href="{{ asset('comprobantescuentaspagar/' . $item->comprobante) }}" 
                                                                target="_blank" title="Ver comprobante">
                                                                    <i class="fas fa-file-alt"></i>
                                                                </a>
                                                            @else
                                                                <input type="checkbox" class="checkbox-bateria" value="{{ $item->id }}">
                                                            @endif
                                                        </td>
                                                        <td hidden>{{ $item->id }}</td>
                                                        <td>{{ $item->ordenid }}</td>
                                                        <td>{{ $item->proveedorasignado }}</td>
                                                        <td>{{ $item->clienteitanombre }}{{ $item->clienteauditorianombre }}{{ $item->clientecomunnombre }}</td>
                                                        <td>{{ $item->accionnombre }}</td>
                                                        <td>{{ $item->fechapago }}</td>
                                                        <td>
                                                            {{ $tipoplanilla }}
                                                            @if ($tipoplanilla === 'PAGO QR' && $archivoExistente)
                                                                <a class="btn btn-botongris" 
                                                                    onclick="mostrarQR('{{ asset('planillaspagosgeneradas/' . str_replace('-', '', $archivoExistente->fechapago) . '/' . $archivoExistente->documento) }}')">
                                                                    <i class="fas fa-qrcode"></i>
                                                                </a>
                                                            @endif

                                                            @if ($tipoplanilla === 'PAGO QR' && ($archivoExistentefijo && $archivoExistentefijo->imagenqr))
                                                                <a class="btn btn-botongris" 
                                                                    onclick="mostrarQR('{{ asset('proveedoresdocumentos/' . $archivoExistentefijo->id . '/' . $archivoExistentefijo->imagenqr) }}')">
                                                                    <i class="fas fa-qrcode"></i>
                                                                </a>
                                                            @endif
                                                        </td>
                                                        <td>{{ $item->nrobancoorigen }}</td>
                                                        <td>{{ $item->proveedoresmedicos->banco ?? '0' }}</td>
                                                        <td>{{ $item->proveedoresmedicos->cuenta ?? '0' }}</td>
                                                        <td class="text-right">{{ number_format($item->preciocompra, 2) }}</td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                        </tbody>
                                    </table>
                                    
                                </div>
                            </div>
                            @can('admin.cuentaspagar.guardarcomprobantescxp')
                            <div class="d-flex flex-wrap align-items-end" style="border-radius: 5px; margin-top:10px;">
                                <!-- Campo para subir el comprobante -->
                                <div>
                                    <label for="archivoComprobante" class="form-label"><strong>Comprobante:</strong></label>
                                    <input type="file" id="archivoComprobante" accept=".pdf,.jpg,.png" class="form-control" required>
                                </div>

                                <!-- Campo para seleccionar el usuario -->
                                <div class="ms-2">
                                    <label for="usuarioNotificado" class="form-label"><strong>Notificar a:</strong></label>
                                    <select id="usuarioNotificado" class="form-control" required>
                                        <option value="">-- Seleccione --</option>
                                        <option value="ROLANDO RAFAEL RAMOS TORRICO">ROLANDO RAFAEL RAMOS TORRICO</option>
                                        <option value="CRISTHIAN ALAIN DURAN SULLCA">CRISTHIAN ALAIN DURAN SULLCA</option>
                                        <option value="JHOSELINE EVA VELASQUEZ ESCOBAR">JHOSELINE EVA VELASQUEZ ESCOBAR</option>
                                    </select>
                                </div>


                                <!-- Botón para guardar -->
                                <div class="d-flex align-items-end">
                                    <button class="btn btn-botongrisgrande" onclick="enviarSeleccionados()">GUARDAR</button>
                                </div>
                            </div>
                            @endcan


                            <script>
                                function enviarSeleccionados() {
                                    const archivo = document.getElementById('archivoComprobante').files[0];
                                    const usuarioNotificado = document.getElementById('usuarioNotificado').value; //
                                    if (!archivo || !usuarioNotificado) { //
                                        alert("Debe seleccionar un archivo y un usuario para notificar.");
                                        return;
                                    }
                                    const seleccionadosCuentas = Array.from(document.querySelectorAll('.checkbox-cuentas:checked')).map(cb => cb.value);
                                    const seleccionadosBateria = Array.from(document.querySelectorAll('.checkbox-bateria:checked')).map(cb => cb.value);

                                    const formData = new FormData();
                                    formData.append('archivo', archivo);
                                    formData.append('usuarioNotificado', usuarioNotificado); //
                                    seleccionadosCuentas.forEach(id => formData.append('cuentas[]', id));
                                    seleccionadosBateria.forEach(id => formData.append('bateria[]', id));

                                    fetch("{{ route('actualizar.comprobante') }}", {
                                        method: 'POST',
                                        headers: {
                                            'X-CSRF-TOKEN': "{{ csrf_token() }}"
                                        },
                                        body: formData
                                    })
                                    .then(response => response.json())
                                    .then(data => {
                                        alert(data.message);
                                        location.reload();
                                    })
                                    .catch(error => {
                                        console.error(error);
                                        alert("Error al actualizar.");
                                    });
                                }
                            </script>
                        </div>
                    </div>
                </div>
                {{-- COMPLETOS --}}
                <div class="tab-pane fade" id="tab-content-1" role="tabpanel" aria-labelledby="tab-1">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="card">
                                <div class="card-body">
                                    <h4 style="font-weight:900;">CUENTAS POR PAGAR</h4>
                                    <input type="text" class="form-control mb-3" placeholder="Buscar proveedor..." onkeyup="filtrarTabla(this, 'tabla-cuentas-pagar')">
                                    <div class="table-responsive">
                                        <table id="tabla-cuentas-pagar" class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Edit.</th>
                                                    <th>Ver</th>
                                                    <th>ID</th>
                                                    <th>Proveedor</th>
                                                    <th>Detalle</th>
                                                    <th>Fecha_Pago</th>
                                                    <th>Tipo_Planilla</th>
                                                    <th>Banco_Origen</th>
                                                    <th>Banco_Destino</th>
                                                    <th>N.Cuenta</th>
                                                    <th>Monto_Total</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($cuentaspagar->whereNotNull('comprobante')->whereIn('estadoaprobacion', ['APROBADO', 'CARGADO']) as $item)
                                                    @php
                                                        $tipoplanilla = optional($item->proveedorServicio)->tipoplanilla ?? 'NO DEFINIDO';
                                                    @endphp

                                                    @if (in_array($tipoplanilla, ['PAGO QR', 'PAGO EN LINEA','PAGO A TERCERO', 'PAGO INTERBANCARIO']))
                                                        @php
                                                            $archivoExistente = \App\Models\PlanillasPagosGeneradas::where('proveedor', $item->proveedornombre)
                                                                ->where('fechapago', $item->fechaasignada)
                                                                ->first();
                                                                $archivoExistentefijo = \App\Models\ProveedoresServicios::where('razonsocial', $item->proveedornombre)->first();
                                                        @endphp
                                                        <tr>
                                                            <td>
                                                                <input type="checkbox" class="checkbox-cuentas" value="{{ $item->id }}">
                                                            </td>
                                                            <td>
                                                                @if ($item->comprobante)
                                                                    <a class="btn btn-sm btn-botongris mt-1" 
                                                                    href="{{ asset('comprobantescuentaspagar/' . $item->comprobante) }}" 
                                                                    target="_blank" title="VER COMPROBANTE">
                                                                        <i class="fas fa-file-alt"></i>
                                                                    </a>
                                                                @endif
                                                            </td>
                                                            <td>{{ $item->id }}</td>
                                                            <td>{{ $item->proveedornombre }}</td>
                                                            <td>{{ $item->detalleproducto }}</td>
                                                            <td>{{ $item->fechaasignada }}</td>
                                                            <td>
                                                                {{ $tipoplanilla }}
                                                                @if ($tipoplanilla === 'PAGO QR' && $archivoExistente)
                                                                <a class="btn btn-botongris" 
                                                                    onclick="mostrarQR('{{ asset('planillaspagosgeneradas/' . str_replace('-', '', $archivoExistente->fechapago) . '/' . $archivoExistente->documento) }}')">
                                                                    <i class="fas fa-qrcode"></i>
                                                                </a>
                                                            @endif
                                                            
                                                            @if ($tipoplanilla === 'PAGO QR' && ($archivoExistentefijo && $archivoExistentefijo->imagenqr))
                                                                <a class="btn btn-botongris" 
                                                                    onclick="mostrarQR('{{ asset('proveedoresdocumentos/' . $archivoExistentefijo->id . '/' . $archivoExistentefijo->imagenqr) }}')">
                                                                    <i class="fas fa-qrcode"></i>
                                                                </a>
                                                            @endif
                                                            </td>
                                                            <td>{{ $item->nrobancoorigen }}</td>
                                                            <td>{{ $item->proveedorServicio->banco ?? '0' }}</td>
                                                            <td>{{ $item->proveedorServicio->numcuenta ?? '0' }}</td>
                                                            <td class="text-right">{{ number_format($item->montototal, 2) }}</td>
                                                        </tr>
                                                    @endif
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="card">
                                <div class="card-body">
                                    <h4 style="font-weight:900;">PROVEEDORES MÉDICOS</h4>
                                    <input type="text" class="form-control mb-3" placeholder="Buscar proveedor médico..." onkeyup="filtrarTabla(this, 'tabla-proveedores-medicos')">
                                    <div class="table-responsive">
                                        <table id="tabla-proveedores-medicos" class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Edit.</th>
                                                    <th>Ver</th>
                                                    <th>ID</th>
                                                    <th>Proveedor</th>
                                                    <th>Cliente</th>
                                                    <th>Detalle</th>
                                                    <th>Fecha_Pago</th>
                                                    <th>Tipo_Planilla</th>
                                                    <th>Banco_Origen</th>
                                                    <th>Banco_Destino</th>
                                                    <th>N.Cuenta</th>
                                                    <th>Monto_Total</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($registrosbateria->whereNotNull('comprobante')->whereIn('estadoaprobacion', ['APROBADO', 'CARGADO']) as $item)
                                                    @php
                                                        $tipoplanilla = $proveedoresServicios[$item->proveedorasignado] ?? 'NO DEFINIDO';
                                                    @endphp

                                                    @if (in_array($tipoplanilla, ['PAGO QR', 'PAGO EN LINEA','PAGO A TERCERO', 'PAGO INTERBANCARIO']))
                                                        @php
                                                            $archivoExistente = \App\Models\PlanillasPagosGeneradas::where('proveedor', $item->proveedorasignado)
                                                                ->where('fechapago', $item->fechapago)
                                                                ->first();
                                                                $archivoExistentefijo = \App\Models\Proveedor::where('proveedor', $item->proveedorasignado)->first();
                                                        @endphp
                                                        <tr>
                                                            <td>
                                                                <input type="checkbox" class="checkbox-bateria" value="{{ $item->id }}">
                                                            </td>
                                                            <td>
                                                                @if ($item->comprobante)
                                                                    <a class="btn btn-sm btn-botongris mt-1" 
                                                                    href="{{ asset('comprobantescuentaspagar/' . $item->comprobante) }}" 
                                                                    target="_blank" title="VER COMPROBANTE">
                                                                        <i class="fas fa-file-alt"></i>
                                                                    </a>
                                                                @endif
                                                            </td>
                                                            <td>{{ $item->id }}</td>
                                                            <td>{{ $item->proveedorasignado }}</td>
                                                            <td>{{ $item->clienteitanombre }}{{ $item->clienteauditorianombre }}{{ $item->clientecomunnombre }}</td>
                                                            <td>{{ $item->accionnombre }}</td>
                                                            <td>{{ $item->fechapago }}</td>
                                                            <td>
                                                                {{ $tipoplanilla }}
                                                                @if ($tipoplanilla === 'PAGO QR' && $archivoExistente)
                                                                <a class="btn btn-botongris" 
                                                                    onclick="mostrarQR('{{ asset('planillaspagosgeneradas/' . str_replace('-', '', $archivoExistente->fechapago) . '/' . $archivoExistente->documento) }}')">
                                                                    <i class="fas fa-qrcode"></i>
                                                                </a>
                                                            @endif

                                                            @if ($tipoplanilla === 'PAGO QR' && ($archivoExistentefijo && $archivoExistentefijo->imagenqr))
                                                                <a class="btn btn-botongris" 
                                                                    onclick="mostrarQR('{{ asset('proveedoresdocumentos/' . $archivoExistentefijo->id . '/' . $archivoExistentefijo->imagenqr) }}')">
                                                                    <i class="fas fa-qrcode"></i>
                                                                </a>
                                                            @endif
                                                            </td>
                                                            <td>{{ $item->nrobancoorigen }}</td>
                                                            <td>{{ $item->proveedoresmedicos->banco ?? '0' }}</td>
                                                            <td>{{ $item->proveedoresmedicos->cuenta ?? '0' }}</td>
                                                            <td class="text-right">{{ number_format($item->preciocompra, 2) }}</td>
                                                        </tr>
                                                    @endif
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @can('admin.cuentaspagar.guardarcomprobantescxp')
                        <div class="d-flex flex-wrap align-items-end" style="border-radius: 5px;">
                            <!-- Campo para subir el comprobante -->
                            <div>
                                <label for="archivoComprobante" class="form-label"><strong>Comprobante:</strong></label>
                                <input type="file" id="archivoComprobante2" accept=".pdf,.jpg,.png" class="form-control" required>
                            </div>

                            <!-- Botón para guardar -->
                            <div class="d-flex align-items-end">
                                <button class="btn btn-botongrisgrande" onclick="enviarSeleccionados2()">EDITAR</button>
                            </div>
                        </div>
                        @endcan
                        <script>
                            function enviarSeleccionados2() {
                                const archivo = document.getElementById('archivoComprobante2').files[0];
                                if (!archivo) {
                                    alert("Debe seleccionar un archivo comprobante.");
                                    return;
                                }

                                const seleccionadosCuentas = Array.from(document.querySelectorAll('.checkbox-cuentas:checked')).map(cb => cb.value);
                                const seleccionadosBateria = Array.from(document.querySelectorAll('.checkbox-bateria:checked')).map(cb => cb.value);

                                const formData = new FormData();
                                formData.append('archivo', archivo);
                                seleccionadosCuentas.forEach(id => formData.append('cuentas[]', id));
                                seleccionadosBateria.forEach(id => formData.append('bateria[]', id));

                                fetch("{{ route('actualizar.comprobante') }}", {
                                    method: 'POST',
                                    headers: {
                                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                                    },
                                    body: formData
                                })
                                .then(response => response.json())
                                .then(data => {
                                    alert(data.message);
                                    location.reload();
                                })
                                .catch(error => {
                                    console.error(error);
                                    alert("Error al actualizar.");
                                });
                            }
                        </script>
                    </div>
                </div>
                <script>
                    function filtrarTabla(input, tablaId) {
                        const filtro = input.value.toLowerCase();
                        const tabla = document.getElementById(tablaId);
                        const filas = tabla.getElementsByTagName('tbody')[0].getElementsByTagName('tr');

                        for (let i = 0; i < filas.length; i++) {
                            const celdas = filas[i].getElementsByTagName('td');
                            const proveedor = celdas[3] ? celdas[3].textContent.toLowerCase() : '';
                            if (proveedor.includes(filtro)) {
                                filas[i].style.display = '';
                            } else {
                                filas[i].style.display = 'none';
                            }
                        }
                    }
                </script>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="modalQR" tabindex="-1" aria-labelledby="modalQRLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalQRLabel"><strong>CODIGO QR</strong></h5>
                <button type="button" class="close position-absolute" style="top: 10px; right: 10px;" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <img id="imagenQR" src="" class="img-fluid" alt="QR">
            </div>
            </div>
        </div>
    </div>
    <script>
        function mostrarQR(ruta) {
            document.getElementById('imagenQR').src = ruta;
            const modal = new bootstrap.Modal(document.getElementById('modalQR'));
            modal.show();
        }
    </script>


@stop

@section('js')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script type="text/javascript" src="https://jeremyfagis.github.io/dropify/dist/js/dropify.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://jeremyfagis.github.io/dropify/dist/css/dropify.min.css"> 
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropify/0.2.2/css/dropify.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/dropify/0.2.2/js/dropify.min.js"></script>
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
        $(document).ready(function() {
            $('input[name="buscarporfecha"], input[name="buscarporarea"]').on('keyup change', function() {
                var fechaSeleccionada = $('input[name="buscarporfecha"]').val();
                var areaSeleccionada = $('input[name="buscarporarea"]').val();
                var botonBuscar = $('#btn-buscar');
                
                if (fechaSeleccionada.trim() === '' && areaSeleccionada.trim() === '') {
                    botonBuscar.prop('disabled', true);
                } else {
                    botonBuscar.prop('disabled', false);
                }
            });
        });
    </script>
    <script>
        function cargarVistaPrevia() {
          var document = document.getElementById('document').files[0];
          if (document) {
            var reader = new FileReader();
            reader.onload = function(e) {
              var previewIframe = document.getElementById('document-preview');
              previewIframe.src = e.target.result;
            };
            reader.readAsDataURL(document);
          }
        }
      
        document.getElementById('document').addEventListener('change', function() {
          cargarVistaPrevia();
        });
      </script>
    <script>
        $(document).ready(function() {
            $('.dropify').dropify({
                messages: {
                    'default': 'Arrastre y suelte un archivo o haga clic aquí',
                    'replace': 'Arrastre y suelte o haga clic para reemplazar',
                    'remove': 'Eliminar',
                    'error': 'Ooops, algo salió mal.'
                }
            });
        
            $('.dropify').on('dropify.error.fileSize', function(event, element) {
                var maxSize = element.input.files[0].size / (1024 * 1024);
                var errorMessage = 'El archivo es demasiado grande (' + maxSize.toFixed(2) + ' MB máx.).';
                $(element.input).siblings('.dropify-error').text(errorMessage);
            });
        });

        document.getElementById('document').addEventListener('change', function(event) {
            var file = event.target.files[0];
            if (file) {
                var fileURL = URL.createObjectURL(file);
                var previewCard = document.getElementById('preview-card');
                var documentPreview = document.getElementById('document-preview');
        
                previewCard.style.display = 'block';
                documentPreview.src = fileURL;
            } else {
                var previewCard = document.getElementById('preview-card');
                previewCard.style.display = 'none';
                documentPreview.src = '';
            }
        });

    </script>
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
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('btn-mostrar-todo').addEventListener('click', function() {
                window.location.href = "{{ route('buscarlistacuentaspagar') }}";
            });
        });
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            document.querySelectorAll(".seleccionarTodos").forEach(checkbox => {
                checkbox.addEventListener("change", function () {
                    let modalId = this.getAttribute("data-modal");
                    let checkboxes = document.querySelectorAll(`.seleccionarFila[data-modal="${modalId}"]`);
        
                    checkboxes.forEach(chk => {
                        chk.checked = checkbox.checked;
                    });
                });
            });
        
            document.querySelectorAll(".seleccionarFila").forEach(checkbox => {
                checkbox.addEventListener("change", function () {
                    let modalId = this.getAttribute("data-modal");
                    let allCheckboxes = document.querySelectorAll(`.seleccionarFila[data-modal="${modalId}"]`);
                    let selectAllCheckbox = document.querySelector(`#seleccionarTodos${modalId}`);
        
                    if (Array.from(allCheckboxes).every(chk => chk.checked)) {
                        selectAllCheckbox.checked = true;
                    } else {
                        selectAllCheckbox.checked = false;
                    }
                });
            });
        });
    </script>
    <script>
            document.getElementById('actualizarPrioridadBtn').addEventListener('click', function () {
            let preordenesSeleccionadas = [];

            document.querySelectorAll('.check-prioridad:checked').forEach(cb => {
                preordenesSeleccionadas.push(cb.dataset.bateriaid);
            });

            if (preordenesSeleccionadas.length === 0) {
                alert('Seleccione al menos un registro para actualizar.');
                return;
            }

            fetch("{{ route('actualizar.prioridad.programacion') }}", {
                method: "POST",
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    preordenes: preordenesSeleccionadas
                })
            })
            .then(response => response.json())
            .then(data => {
                alert(data.message);
                location.reload(); // o actualiza la tabla dinámicamente
            })
            .catch(error => {
                console.error("Error al actualizar:", error);
                alert("Error al actualizar prioridad.");
            });
        });
    </script>
    <script>
        function handleQRUpload(inputId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById('icon_' + inputId);

            if (input.files.length > 0) {
                icon.classList.remove('fa-upload');
                icon.classList.add('fa-check');
                icon.style.color = 'green';
            } else {
                icon.classList.remove('fa-check');
                icon.classList.add('fa-upload');
                icon.style.color = '#555';
            }
        }
    </script>
@endsection

