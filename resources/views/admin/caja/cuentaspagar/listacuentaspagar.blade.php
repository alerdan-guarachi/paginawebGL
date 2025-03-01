@extends('adminlte::page')

@section('content_header')
{{-- <a href="{{ route('reporte.cuentaspagar') }}" class="btn btn-danger">
    <i class="fas fa-file-pdf"></i> Generar PDF
</a> --}}

<a href="{{ route('reporte.cuentaspagar') }}" class="btn float-right btn-outline-secondary" style="margin-right: 10px;">GENERAR REPORTE</a>

<h1>CUENTAS POR PAGAR</h1>
@stop

@section('css')
<link rel="stylesheet" href="{{ asset('css/cuentascobrarpagar.css') }}">
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
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid justify-content-end">
            <div class="d-flex flex-wrap align-items-center">
                <form id="search-form" action="{{ route('buscarlistacuentaspagar') }}" method="get" class="form-inline">
                    <div class="flex-grow-1">
                        <input type="text" name="buscarporcliente" class="form-control mr-sm-2" placeholder="Nombre del Proveedor">
                    </div>
                    <button id="btn-buscar" class="btn btn-outline-secondary my-2 my-sm-0" type="submit">Buscar</button>
                    <button id="btn-mostrar-todo" class="btn btn-outline-secondary my-2 my-sm-0 ml-2" type="button">Mostrar Todo</button>
                </form>
            </div>
        </div>
    </nav>
    <div class="card-body">
        <div class="table-responsive" style="max-height: 70vh;">
            <table class="table table-striped">
                <thead style="position: sticky; top: 0; z-index: 1010; background-color: #ffffff;">
                    <tr>
                        <th style="width: 50%;">Proveedor</th>
                        <th style="width: 10%;">Informes</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($result as $item)
                        <tr>
                            <td>{{ $item['proveedorasignado'] }}</td>
                            <td>
                                <abbr title="VER REGISTROS">
                                    <a class="btn btn-sm btn-outline-secondary" data-toggle="modal" data-target="#modal{{ $loop->index }}"><i class="fas fa-file-medical-alt"></i></a>
                                </abbr>
                            </td>
                        </tr>
                    @endforeach
                </tbody> 
            </table>
        </div>
    </div>
</div>

@foreach ($result as $item)
    <div class="modal fade" id="modal{{ $loop->index }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel{{ $loop->index }}" aria-hidden="true"> 
        <div class="modal-dialog modal-xxl" role="document"> 
            <div class="modal-content">
                @php 
                    $sumaPrecios = collect($item['acciones'])->sum(function ($accion) use ($item) {
                        return (!is_null($accion['informedocumentacion']) || !is_null($accion['informedocumentacionfinal'])) 
                            && is_numeric($accion['precio']) 
                            ? $accion['precio'] : 0;
                    });
                @endphp
                <div class="modal-header d-block text-center py-4" style="background: #efefef">
                    <div class="mb-3">
                        <h4 class="modal-title font-weight-bold" id="modalLabel{{ $loop->index }}">
                            <strong>{{ $item['proveedorasignado'] }}</strong>
                        </h4>
                        <button type="button" class="close position-absolute" style="top: 10px; right: 10px;" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>
                <div class="card-header">
                    <ul class="nav nav-tabs card-header-tabs" id="tabs-{{ $loop->index }}">
                        <li class="nav-item">
                            <a class="nav-link active" id="tab-completos-{{ $loop->index }}" data-toggle="tab" href="#tab-content-completos-{{ $loop->index }}" role="tab" aria-controls="tab-content-completos-{{ $loop->index }}" aria-selected="true">
                                INFORMES COMPLETOS Y PAGOS PENDIENTES
                            </a>
                        </li> 
                        <li class="nav-item">
                            <a class="nav-link" id="tab-pendientes-{{ $loop->index }}" data-toggle="tab" href="#tab-content-pendientes-{{ $loop->index }}" role="tab" aria-controls="tab-content-pendientes-{{ $loop->index }}" aria-selected="false">
                                INFORMES PENDIENTES
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="tab-completosprocesados-{{ $loop->index }}" data-toggle="tab" href="#tab-content-completosprocesados-{{ $loop->index }}" role="tab" aria-controls="tab-content-completosprocesados-{{ $loop->index }}" aria-selected="false">
                                INFORMES COMPLETOS Y PAGOS PROCESADOS
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="tabsContent-{{ $loop->index }}">
                        {{-- INFORMES COMPLETOS Y PAGOS PENDIENTES --}}
                        <div class="tab-pane fade show active" id="tab-content-completos-{{ $loop->index }}" role="tabpanel" aria-labelledby="tab-completos-{{ $loop->index }}">
                            <form action="{{ route('actualizarFactura') }}" method="POST">
                                @csrf
                                <div class="row" style="margin-bottom: 10px;">
                                    <div class="col-lg-2">
                                        <input type="text" class="form-control" id="nroFactura" name="nroFactura" placeholder="Nro. Factura" required>
                                    </div>
                                    <div class="col-lg-2">
                                        <button type="submit" class="btn btn-outline-secondary">Actualizar</button>
                                    </div>
                                </div>
                                <div class="table-responsive" style="max-height: 65vh;">
                                    <table class="table table-striped">
                                        <thead style="position: sticky; top: 0; z-index: 1010; background-color: #f8f9fa;">
                                            <tr>
                                                <th>ID</th>
                                                <th>Est./Esp.</th>
                                                <th>Tipo Cli.</th>
                                                <th>Cliente_ID</th>
                                                <th>Cliente_Nombre</th>
                                                <th>Fecha_Bateria</th>
                                                <th>Pago</th>
                                                <th>Prog.</th>
                                                <th>Informe</th>
                                                <th>Fecha_Pago</th>
                                                <th hidden>ID Prog</th>
                                                <th>N.Factura</th>
                                                <th>
                                                    Selec. <input type="checkbox" id="seleccionarTodos{{ $loop->index }}" class="seleccionarTodos" data-modal="{{ $loop->index }}">
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($item['acciones'] as $accion) 
                                                @php
                                                    $hoy = \Carbon\Carbon::now();
                                                    $fechabateria = \Carbon\Carbon::parse($item['fechabateria']);
                                                    $diasDesdeBateria = $fechabateria->diffInDays($hoy);
                                                    $fechaprogramacion = $accion['fechaprogramacion'] ? \Carbon\Carbon::parse($accion['fechaprogramacion']) : null;
                                                    $fechaatencionprogramacion = $accion['fechaatencionprogramacion'] ? \Carbon\Carbon::parse($accion['fechaatencionprogramacion']) : null;
                                                @endphp
                                                
                                                @if ($accion['accion'] !== 'INFORME FINAL')
                                                    @if (!is_null($accion['informedocumentacion']) && is_null($accion['pagoservicioinforme']))
                                                    <tr>
                                                        <td>{{ $accion['id'] }}</td>
                                                        <td title="{{ $accion['accion'] }}" class="truncar">{{ $accion['accion'] }}</td>
                                                        <td>
                                                            @if($accion['clienteitaid'] !== null)
                                                                ITA
                                                            @elseif($accion['clienteauditoriaid'] !== null)
                                                                AUDITORIA
                                                            @elseif($accion['clientecomunid'] !== null)
                                                                COMUN
                                                            @else
                                                                NINGUNO
                                                            @endif
                                                        </td>
                                                        <td>{{ $accion['clienteitaid'] }}{{ $accion['clienteauditoriaid'] }}{{ $accion['clientecomunid'] }}</td>
                                                        <td title="{{ $accion['clienteitanombre'] }}{{ $accion['clienteauditorianombre'] }}{{ $accion['clientecomunnombre'] }}" class="truncar">{{ $accion['clienteitanombre'] }}{{ $accion['clienteauditorianombre'] }}{{ $accion['clientecomunnombre'] }}</td>
                                                        <td>{{ $accion['fechabateria'] }}</td>
                                                        <td>{{ $accion['preciocompra'] }}</td>
                                                        <td>
                                                            @if ($accion['fechaprogramacion'])
                                                                {{ $accion['fechaprogramacion'] }}
                                                            @else
                                                                <div class="badge 
                                                                    {{ $diasDesdeBateria >= 14 ? 'badge-danger' : 'badge-warning' }}">
                                                                    PENDIENTE
                                                                </div>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if ($accion['informedocumentacion'])
                                                                {{ $accion['informedocumentacion'] }}
                                                            @else
                                                                <div class="badge 
                                                                    {{ $fechaatencionprogramacion ? 'badge-danger' : 'badge-warning' }}">
                                                                    PENDIENTE
                                                                </div>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if ($accion['pagoservicioinforme'])
                                                            <div class="badge badge-success" style="font-size: 0.8rem; padding: 6px;">
                                                                {{ $accion['pagoservicioinforme'] }}
                                                            </div>
                                                            @else
                                                                <div class="badge badge-danger" style="font-size: 0.8rem; padding: 6px;">
                                                                    PENDIENTE
                                                                </div>
                                                            @endif
                                                        </td>
                                                        <td hidden>{{ $accion['idprogramacion'] }}</td>
                                                        <td>
                                                            {{ $accion['nrofacturaprog'] ?? 'PENDIENTE' }}
                                                        </td>
                                                        <td>
                                                            <input type="checkbox" name="seleccionados[]" value="{{ $accion['idprogramacion'] ?? $accion['provinfofinalid'] }}" class="seleccionarFila" data-modal="{{ $loop->parent->index }}">
                                                        </td>
                                                    </tr>
                                                    @endif
                                                @endif

                                                @if ($accion['accion'] === 'INFORME FINAL')
                                                    @if (!is_null($accion['informedocumentacionfinal']) && is_null($accion['pagoservicioinformefinal']))
                                                    <tr>
                                                        <td>{{ $accion['id'] }}</td>
                                                        <td title="{{ $accion['accion'] }}" class="truncar">{{ $accion['accion'] }}</td>
                                                        <td>
                                                            @if($accion['clienteitaid'] !== null)
                                                                ITA
                                                            @elseif($accion['clienteauditoriaid'] !== null)
                                                                AUDITORIA
                                                            @elseif($accion['clientecomunid'] !== null)
                                                                COMUN
                                                            @else
                                                                NINGUNO
                                                            @endif
                                                        </td>
                                                        <td>{{ $accion['clienteitaid'] }}{{ $accion['clienteauditoriaid'] }}{{ $accion['clientecomunid'] }}</td>
                                                        <td title="{{ $accion['clienteitanombre'] }}{{ $accion['clienteauditorianombre'] }}{{ $accion['clientecomunnombre'] }}" class="truncar">{{ $accion['clienteitanombre'] }}{{ $accion['clienteauditorianombre'] }}{{ $accion['clientecomunnombre'] }}</td>
                                                        <td>{{ $accion['fechabateria'] }}</td>
                                                        <td>{{ $accion['preciocompra'] }}</td>
                                                        
                                                        @if ($accion['accion'] === 'INFORME FINAL')
                                                            <td>-------------</td>
                                                            <td>
                                                                {{ $accion['informedocumentacionfinal'] ?? 'PENDIENTE' }}
                                                            </td>
                                                            <td>
                                                                @if ($accion['pagoservicioinformefinal'])
                                                                <div class="badge badge-success" style="font-size: 0.8rem; padding: 6px;">
                                                                    {{ $accion['pagoservicioinformefinal'] }}
                                                                </div>
                                                                @else
                                                                    <div class="badge badge-danger" style="font-size: 0.8rem; padding: 6px;">
                                                                        PENDIENTE
                                                                    </div>
                                                                @endif
                                                            </td>
                                                            <td hidden>{{ $accion['provinfofinalid'] }}</td>
                                                            <td>
                                                                {{ $accion['nrofacturainformefinal'] ?? 'PENDIENTE' }}
                                                            </td>
                                                            <td>
                                                                <input type="checkbox" name="seleccionados[]" value="{{ $accion['idprogramacion'] ?? $accion['provinfofinalid'] }}" class="seleccionarFila" data-modal="{{ $loop->parent->index }}">
                                                            </td>
                                                            
                                                        @endif
                                                    </tr>
                                                    @endif
                                                @endif
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </form>
                        </div>

                        {{-- INFORMES PENDIENTES --}}
                        <div class="tab-pane fade" id="tab-content-pendientes-{{ $loop->index }}" role="tabpanel" aria-labelledby="tab-pendientes-{{ $loop->index }}">
                            <div class="table-responsive" style="max-height: 65vh;">
                                <table class="table table-striped">
                                    <thead style="position: sticky; top: 0; z-index: 1010; background-color: #f8f9fa;">
                                        <tr>
                                            <th>ID</th>
                                            <th>Est./Esp.</th>
                                            <th>Tipo Cli.</th>
                                            <th>Cliente_ID</th>
                                            <th>Cliente_Nombre</th>
                                            <th>Fecha_Bateria</th>
                                            <th>Pago</th>
                                            <th>Prog.</th>
                                            <th>Informe</th>
                                            <th>Fecha_Pago</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($item['acciones'] as $accion) 
                                            @php
                                                $hoy = \Carbon\Carbon::now();
                                                $fechabateria = \Carbon\Carbon::parse($item['fechabateria']);
                                                $diasDesdeBateria = $fechabateria->diffInDays($hoy);
                                                $fechaprogramacion = $accion['fechaprogramacion'] ? \Carbon\Carbon::parse($accion['fechaprogramacion']) : null;
                                                $fechaatencionprogramacion = $accion['fechaatencionprogramacion'] ? \Carbon\Carbon::parse($accion['fechaatencionprogramacion']) : null;
                                            @endphp
                                            @if (is_null($accion['informedocumentacion']) && is_null($accion['informedocumentacionfinal'])) 
                                            <tr>
                                                <td>{{ $accion['id'] }}</td>
                                                <td title="{{ $accion['accion'] }}" class="truncar">{{ $accion['accion'] }}</td>
                                                <td>
                                                    @if($accion['clienteitaid'] !== null)
                                                        ITA
                                                    @elseif($accion['clienteauditoriaid'] !== null)
                                                        AUDITORIA
                                                    @elseif($accion['clientecomunid'] !== null)
                                                        COMUN
                                                    @else
                                                        NINGUNO
                                                    @endif
                                                </td>
                                                <td>{{ $accion['clienteitaid'] }}{{ $accion['clienteauditoriaid'] }}{{ $accion['clientecomunid'] }}</td>
                                                <td title="{{ $accion['clienteitanombre'] }}{{ $accion['clienteauditorianombre'] }}{{ $accion['clientecomunnombre'] }}" class="truncar">{{ $accion['clienteitanombre'] }}{{ $accion['clienteauditorianombre'] }}{{ $accion['clientecomunnombre'] }}</td>
                                                <td>{{ $accion['fechabateria'] }}</td>
                                                <td>{{ $accion['preciocompra'] }}</td>
                                                
                                                @if ($accion['accion'] === 'INFORME FINAL')
                                                    <td>-------------</td>
                                                    <td>
                                                        <span class="badge badge-warning">
                                                            {{ $accion['informedocumentacionfinal'] ?? 'PENDIENTE' }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        @if ($accion['pagoservicioinformefinal'])
                                                        <div class="badge badge-success" style="font-size: 0.8rem; padding: 6px;">
                                                            {{ $accion['pagoservicioinformefinal'] }}
                                                        </div>
                                                        @else
                                                            <div class="badge badge-danger" style="font-size: 0.8rem; padding: 6px;">
                                                                PENDIENTE
                                                            </div>
                                                        @endif
                                                    </td>
                                                @else
                                                    <td>
                                                        @if ($accion['fechaprogramacion'])
                                                            {{ $accion['fechaprogramacion'] }}
                                                        @else
                                                            <div class="badge 
                                                                {{ $diasDesdeBateria >= 14 ? 'badge-danger' : 'badge-warning' }}">
                                                                PENDIENTE
                                                            </div>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($accion['informedocumentacion'])
                                                            {{ $accion['informedocumentacion'] }}
                                                        @else
                                                            <div class="badge 
                                                                {{ $fechaatencionprogramacion ? 'badge-danger' : 'badge-warning' }}">
                                                                PENDIENTE
                                                            </div>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($accion['pagoservicioinforme'])
                                                        <div class="badge badge-success" style="font-size: 0.8rem; padding: 6px;">
                                                            {{ $accion['pagoservicioinforme'] }}
                                                        </div>
                                                        @else
                                                            <div class="badge badge-danger" style="font-size: 0.8rem; padding: 6px;">
                                                                PENDIENTE
                                                            </div>
                                                        @endif
                                                    </td>
                                                @endif
                                            </tr>
                                            @endif
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- INFORMES COMPLETOS Y PAGOS PROCESADOS --}}
                        <div class="tab-pane fade" id="tab-content-completosprocesados-{{ $loop->index }}" role="tabpanel" aria-labelledby="tab-completosprocesados-{{ $loop->index }}">
                            <div class="table-responsive" style="max-height: 65vh;">
                                <table class="table table-striped">
                                    <thead style="position: sticky; top: 0; z-index: 1010; background-color: #f8f9fa;">
                                        <tr>
                                            <th>ID</th>
                                            <th>Est./Esp.</th>
                                            <th>Tipo Cli.</th>
                                            <th>Cliente_ID</th>
                                            <th>Cliente_Nombre</th>
                                            <th>Fecha_Bateria</th>
                                            <th>Pago</th>
                                            <th>Prog.</th>
                                            <th>Informe</th>
                                            <th>Fecha_Pago</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($item['acciones'] as $accion) 
                                            @php
                                                $hoy = \Carbon\Carbon::now();
                                                $fechabateria = \Carbon\Carbon::parse($item['fechabateria']);
                                                $diasDesdeBateria = $fechabateria->diffInDays($hoy);
                                                $fechaprogramacion = $accion['fechaprogramacion'] ? \Carbon\Carbon::parse($accion['fechaprogramacion']) : null;
                                                $fechaatencionprogramacion = $accion['fechaatencionprogramacion'] ? \Carbon\Carbon::parse($accion['fechaatencionprogramacion']) : null;
                                            @endphp
                                            @if ((!is_null($accion['informedocumentacion']) && !is_null($accion['pagoservicioinforme'])) || (!is_null($accion['informedocumentacionfinal']) && !is_null($accion['pagoservicioinformefinal'])))
                                            <tr>
                                                <td>{{ $accion['id'] }}</td>
                                                <td title="{{ $accion['accion'] }}" class="truncar">{{ $accion['accion'] }}</td>
                                                <td>
                                                    @if($accion['clienteitaid'] !== null)
                                                        ITA
                                                    @elseif($accion['clienteauditoriaid'] !== null)
                                                        AUDITORIA
                                                    @elseif($accion['clientecomunid'] !== null)
                                                        COMUN
                                                    @else
                                                        NINGUNO
                                                    @endif
                                                </td>
                                                <td>{{ $accion['clienteitaid'] }}{{ $accion['clienteauditoriaid'] }}{{ $accion['clientecomunid'] }}</td>
                                                <td title="{{ $accion['clienteitanombre'] }}{{ $accion['clienteauditorianombre'] }}{{ $accion['clientecomunnombre'] }}" class="truncar">{{ $accion['clienteitanombre'] }}{{ $accion['clienteauditorianombre'] }}{{ $accion['clientecomunnombre'] }}</td>
                                                <td>{{ $accion['fechabateria'] }}</td>
                                                <td>{{ $accion['preciocompra'] }}</td>
                                                
                                                @if ($accion['accion'] === 'INFORME FINAL')
                                                    <td>-------------</td>
                                                    <td>
                                                        {{ $accion['informedocumentacionfinal'] ?? 'PENDIENTE' }}
                                                    </td>
                                                    <td>
                                                        @if ($accion['pagoservicioinformefinal'])
                                                        <div class="badge badge-success" style="font-size: 0.8rem; padding: 6px;">
                                                            {{ $accion['pagoservicioinformefinal'] }}
                                                        </div>
                                                        @else
                                                            <div class="badge badge-danger" style="font-size: 0.8rem; padding: 6px;">
                                                                PENDIENTE
                                                            </div>
                                                        @endif
                                                    </td>
                                                @else
                                                    <td>
                                                        @if ($accion['fechaprogramacion'])
                                                            {{ $accion['fechaprogramacion'] }}
                                                        @else
                                                            <div class="badge 
                                                                {{ $diasDesdeBateria >= 14 ? 'badge-danger' : 'badge-warning' }}">
                                                                PENDIENTE
                                                            </div>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($accion['informedocumentacion'])
                                                            {{ $accion['informedocumentacion'] }}
                                                        @else
                                                            <div class="badge 
                                                                {{ $fechaatencionprogramacion ? 'badge-danger' : 'badge-warning' }}">
                                                                PENDIENTE
                                                            </div>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($accion['pagoservicioinforme'])
                                                        <div class="badge badge-success" style="font-size: 0.8rem; padding: 6px;">
                                                            {{ $accion['pagoservicioinforme'] }}
                                                        </div>
                                                        @else
                                                            <div class="badge badge-danger" style="font-size: 0.8rem; padding: 6px;">
                                                                PENDIENTE
                                                            </div>
                                                        @endif
                                                    </td>
                                                @endif
                                            </tr>
                                            @endif
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <a type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cerrar</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endforeach

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
        
@endsection

