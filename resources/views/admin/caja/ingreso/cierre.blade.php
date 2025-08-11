@extends('adminlte::page')

<link href="assets/img/logo.png" rel="icon">

@section('content_header')
@php
    $rolUsuario = auth()->user()->getRoleNames()->first();
@endphp
@php
    $tieneRolContable = auth()->user()->getRoleNames()->contains('CONTABLE');
@endphp

{{-- @if (!$mostrarVista && $rolUsuario === 'CONTABLE') --}}
@if (!$mostrarVista && $tieneRolContable)
    <div class="alert alert-danger text-center py-4" style="border-radius: 10px; background-color: #f8d7da; color: #842029; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);">
        <h4 class="font-weight-bold mb-3" style="text-transform: uppercase; letter-spacing: 1px;">Caja Bloqueada</h4>
        <p class="mb-4" style="font-size: 1.1rem;">
            {{ $motivoBloqueo ?? 'TU CAJA ESTA BLOQUEADA, SOLICITA UN CÓDIGO DE DESBLOQUEO A ADMINISTRACIÓN.' }}
        </p>
        <form action="{{ route('verificar.codigo3') }}" method="POST" style="max-width: 500px; margin: 0 auto;">
            @csrf
            <div class="form-group mb-3">
                <label for="codigo" class="font-weight-bold" style="font-size: 1rem;">Ingresa el código para continuar:</label>
                <input type="text" id="codigo" name="codigo" class="form-control" placeholder="Código de autorización" required style="border-radius: 5px;">
            </div>
            <button type="submit" class="btn btn-sm btn-success btn-block" style="padding: 5px 10px; font-size: 1rem; border-radius: 5px;">VALIDAR CÓDIGO</button>
        </form>
    </div>
@else
    <div class="d-flex align-items-center justify-content-between mb-0">
        <div class="flex-grow-1">
            <h1 class="font-weight-bold"
                style="font-size: 1.8rem; color: #343a40; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 0;">
                CIERRE DE CAJAS
            </h1>
        </div>
        <a class="btn float-right btn-outline-secondary btn-sm" data-toggle="modal" data-target="#cierresModal">
            CIERRES DE CAJA
        </a>
        {{-- <a class="btn btn-outline-secondary btn-sm" data-toggle="modal" data-target="#consolidadosModal">
            CONSOLIDADOS
        </a> --}}
    </div>

    <div class="modal fade" id="consolidadosModal" tabindex="-1" aria-labelledby="consolidadosModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content border-0 shadow-sm">
                <div class="modal-header w-100 text-center">
                    <h5 class="modal-title w-100 fw-bold" id="consolidadosModalLabel">{{ auth()->user()->name }}</h5>
                    <p class="text-muted mt-2 mb-0">{{ now()->format('d/m/Y') }}</p>

                        {{-- @if ($todosFinalizados)
                            <span class="text-success">CAJA CERRADA</span>
                        @else
                            <span class="text-danger">CIERRE PENDIENTE</span>
                        @endif --}}

                </div>
                <style>
                    .modal-header {
                    display: block;
                    text-align: center;
                }
                </style>                
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle">
                            <thead>
                                <tr class="border-bottom">
                                    <th class="text-center">Tipo de Transacción</th>
                                    <th class="text-center">Consolidado</th>
                                    <th class="text-center">Caja Ingreso</th>
                                    <th class="text-center">Caja Egreso</th>
                                    {{-- <th class="text-center">Total Caja</th> --}}
                                </tr>
                            </thead>
                            <tbody>
                                @php 
                                // Asumimos que los valores consolidados vienen del registro de 'consolidados' que es el primer (y único) registro del usuario
                                $consolidadoEfectivo = $consolidados ? $consolidados->consolidadoefectivo : 0;
                                $consolidadoCheque = $consolidados ? $consolidados->consolidadocheque : 0;
                                $consolidadoATC = $consolidados ? $consolidados->consolidadoatc : 0;
                                $consolidadoDeposito = $consolidados ? $consolidados->consolidadodeposito : 0;
                                $consolidadoTransferencia = $consolidados ? $consolidados->consolidadotransferencia : 0;
                            
                                // Calcular el total consolidado
                                $granTotalConsolidado = $consolidadoEfectivo + $consolidadoCheque + $consolidadoATC + $consolidadoDeposito + $consolidadoTransferencia;
                            
                                // Inicializar el total de la caja central
                                $granTotalCajaCentral = 0;
                                $granTotalCajaCentralegreso = 0;
                            @endphp
                            
                            @foreach($tiposTransaccion as $tipo)
                                @php
                                    // Sumar los montos totales de la tabla cajacentral para este tipo de transacción y usuario (solo con fecha actual)
                                    $cajaCentralTotal = DB::table('cajacentral')
                                        ->where('usuarioregistronombre', auth()->user()->name)
                                        ->where('tipotransaccion', $tipo)
                                        ->where('tipomovimiento', 'INGRESO')
                                        ->whereDate('updated_at', today())  // Solo registros actualizados hoy
                                        ->sum('montototal');
                            
                                    // Acumulamos el total de la caja central
                                    $granTotalCajaCentral += $cajaCentralTotal;

                                    $cajaCentralTotalegreso = DB::table('cajacentral')
                                        ->where('usuarioregistronombre', auth()->user()->name)
                                        ->where('tipotransaccion', $tipo)
                                        ->where('tipomovimiento', 'EGRESO')
                                        ->whereDate('updated_at', today())  // Solo registros actualizados hoy
                                        ->sum('montototal');
                            
                                    // Acumulamos el total de la caja central
                                    $granTotalCajaCentralegreso += $cajaCentralTotalegreso;
                                @endphp
                                <tr>
                                    <td class="text-center">{{ $tipo }}</td>
                                    <td class="text-center">
                                        @switch(strtolower($tipo))
                                            @case('efectivo')
                                                {{ number_format($consolidadoEfectivo, 2) }}
                                                @break
                                            @case('cheque')
                                                {{ number_format($consolidadoCheque, 2) }}
                                                @break
                                            @case('atc')
                                                {{ number_format($consolidadoATC, 2) }}
                                                @break
                                            @case('deposito bancario')
                                                {{ number_format($consolidadoDeposito, 2) }}
                                                @break
                                            @case('transferencia bancaria')
                                                {{ number_format($consolidadoTransferencia, 2) }}
                                                @break
                                        @endswitch
                                    </td>
                                    <td class="text-center">{{ number_format($cajaCentralTotal, 2) }}</td>
                                    <td class="text-center">{{ number_format($cajaCentralTotalegreso, 2) }}</td>
                                </tr>
                            @endforeach
                            
                            <!-- Mostrar los totales al final, asegurando que cada total esté en su columna correspondiente -->
                            <tr>
                                <th class="text-end" colspan="1"></th>
                                <td class="fw-bold text-center">{{ number_format($granTotalConsolidado, 2) }}</td>
                                <td class="fw-bold text-center">{{ number_format($granTotalCajaCentral, 2) }}</td>
                                <td class="fw-bold text-center">{{ number_format($granTotalCajaCentralegreso, 2) }}</td>
                                {{-- <td class="fw-bold text-center">{{ number_format($granTotalCajaCentral - $granTotalCajaCentralegreso, 2) }}</td> <!-- Resultado de la resta --> --}}
                            </tr>
                            
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
    
    <style>
        .modal-title {
            font-size: 1.6rem;
            text-transform: uppercase;
            margin-bottom: 0;
            letter-spacing: 0.5px;
        }
    
        .table th, .table td {
            vertical-align: middle;
        }
    
        .modal-footer {
            justify-content: right;
        }
    
        .fw-bold {
            font-weight: bold !important;
        }
    </style>

    {{-- <div class="modal fade" id="cierresModal" tabindex="-1" aria-labelledby="cierresModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content border-0 shadow-sm">
                <div class="modal-header w-100 text-center">
                    <h4 class="modal-title w-100 fw-bold" id="cierresModalLabel" style="font-weight: 900">CIERRES DE CAJA</h4>
                </div>
                    
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Usuario</th>
                                    <th>Efectivo</th>
                                    <th>ATC</th>
                                    <th>Deposito</th>
                                    <th>Transferencia</th>
                                    <th>Cheque</th>
                                    <th>Fecha Cierre</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($cierrecajas as $cierrecaja)
                                    <tr>
                                        <td>{{ $cierrecaja->usuariocierrenombre }}</td>
                                        <td>{{ $cierrecaja->cierreefectivo }}</td>
                                        <td>{{ $cierrecaja->cierreatc }}</td>
                                        <td>{{ $cierrecaja->cierredeposito }}</td>
                                        <td>{{ $cierrecaja->cierretransferencia }}</td>
                                        <td>{{ $cierrecaja->cierrecheque }}</td>
                                        <td>{{ $cierrecaja->created_at->format('Y-m-d') }}</td>
                                        </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div> --}}
    <div class="modal fade" id="cierresModal" tabindex="-1" aria-labelledby="cierresModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content border-0 shadow-sm">
            <div class="modal-header w-100 text-center">
                <h4 class="modal-title w-100 fw-bold" id="cierresModalLabel" style="font-weight: 900">CIERRES DE CAJA</h4>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    @php
                        $authUser = auth()->user();
                        $esContable = $authUser->hasRole('CONTABLE');
                        $fechaFiltro = \Carbon\Carbon::create(2025, 7, 8);
                    @endphp
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th rowspan="2" style="background-color: #ececec; text-align: center; vertical-align: middle; padding: 4px;">Usuario_Cierre</th>
                                <th rowspan="2" style="background-color: #ececec; text-align: center; vertical-align: middle; padding: 4px;">Fecha_Cierre</th>
                                <th colspan="6" style="background-color: #eaffde; text-align: center; vertical-align: middle; padding: 4px;">Ingresos</th>
                                <th colspan="3" style="background-color: #feebdb; text-align: center; vertical-align: middle; padding: 4px;">Egresos</th>
                                <th rowspan="2" style="background-color: #e3f1fd; text-align: center; vertical-align: middle; padding: 4px;">Neto</th>
                            </tr>
                            <tr>
                                <th style="background-color: #eaffde; text-align: center; padding: 4px;">Efectivo</th>
                                <th style="background-color: #eaffde; text-align: center; padding: 4px;">ATC</th>
                                <th style="background-color: #eaffde; text-align: center; padding: 4px;">Depósito</th>
                                <th style="background-color: #eaffde; text-align: center; padding: 4px;">Transf.</th>
                                <th style="background-color: #eaffde; text-align: center; padding: 4px;">Cheque</th>
                                <th style="background-color: #eaffde; text-align: center; padding: 4px;">Total</th>
                                <th style="background-color: #feebdb; text-align: center; padding: 4px;">Transf.</th>
                                <th style="background-color: #feebdb; text-align: center; padding: 4px;">Cheque</th>
                                <th style="background-color: #feebdb; text-align: center; padding: 4px;">Total</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($cierrecajas as $cierrecaja)
                                @php
                                    $fechaCierre = \Carbon\Carbon::parse($cierrecaja->fechacierre ?? $cierrecaja->created_at);
                                @endphp

                                @if($fechaCierre >= $fechaFiltro && (!$esContable || ($esContable && $cierrecaja->usuariocierrenombre == $authUser->name)))
                                    <tr>
                                        <td style="background-color: #ffffff">{{ $cierrecaja->usuariocierrenombre }}</td>
                                        <td style="background-color: #ffffff; text-align: center;">{{\Carbon\Carbon::parse($cierrecaja->fechacierre ?? $cierrecaja->created_at)->format('Y-m-d')}}</td>
                                        <td style="background-color: #fcfff5; text-align: right;">{{ $cierrecaja->cierreefectivo }}</td>
                                        <td style="background-color: #fcfff5; text-align: right;">{{ $cierrecaja->cierreatc }}</td>
                                        <td style="background-color: #fcfff5; text-align: right;">{{ $cierrecaja->cierredeposito }}</td>
                                        <td style="background-color: #fcfff5; text-align: right;">{{ $cierrecaja->cierretransferencia }}</td>
                                        <td style="background-color: #fcfff5; text-align: right;">{{ $cierrecaja->cierrecheque }}</td>
                                        <td style="background-color: #fafafa; text-align: right;">
                                            {{
                                                number_format(
                                                    ($cierrecaja->cierreefectivo ?? 0)
                                                    + ($cierrecaja->cierreatc ?? 0)
                                                    + ($cierrecaja->cierredeposito ?? 0)
                                                    + ($cierrecaja->cierretransferencia ?? 0)
                                                    + ($cierrecaja->cierrecheque ?? 0),
                                                    2, '.', ','
                                                )
                                            }}
                                        </td>
                                        <td style="background-color: #fff9f5; text-align: right;">{{ $cierrecaja->egresotransferencia }}</td>
                                        <td style="background-color: #fff9f5; text-align: right;">{{ $cierrecaja->egresocheque }}</td>
                                        <td style="background-color: #fafafa; text-align: right;">
                                            {{
                                                number_format(
                                                    ($cierrecaja->egresotransferencia ?? 0)
                                                    + ($cierrecaja->egresocheque ?? 0),
                                                    2, '.', ','
                                                )
                                            }}
                                        </td>
                                        <td style="background-color: #f3f9ff; text-align: right;">
                                            {{
                                                number_format(
                                                    ($cierrecaja->cierreefectivo ?? 0)
                                                    + ($cierrecaja->cierreatc ?? 0)
                                                    + ($cierrecaja->cierredeposito ?? 0)
                                                    + ($cierrecaja->cierretransferencia ?? 0)
                                                    + ($cierrecaja->cierrecheque ?? 0)
                                                    - ($cierrecaja->egresotransferencia ?? 0)
                                                    - ($cierrecaja->egresocheque ?? 0),
                                                    2, '.', ','
                                                )
                                            }}
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-outline-danger" data-dismiss="modal">CERRAR</button>
            </div>
        </div>
    </div>
</div>
@endif
@stop

@section('css')
    <style>
        .form-group label {
            font-weight: bold;
            color: #000000;
        }

        .btn-primary {
            background-color: #6c757d;
            border-color: #6c757d;

        }

        .btn-primary:hover {
            background-color: #5a6268;
            border-color: #545b62;
        }

        .btn-success {
            background-color: #28a745;
            border-color: #28a745;
        }

        .actions-container {
            margin-top: 15px;
            display: flex;
            justify-content: center;
            gap: 15px;
        }

        .listar-datos {
            float: right;
            width: auto;
        }
        .table td {
            padding: 7px 10px;
        }
        .btn-verregistros {
            background-color:  #ffffff;
            color: #94c93b;
            border-color: #94c93b;
            border-radius: 5px;
            padding: 2px 5px;
            }
        .btn-verregistros:hover {
            background-color: #94c93b;
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
        }, 3000);
    </script>
@endif
@if (!$mostrarVista && $tieneRolContable)
@else

<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-md-12">
                <form id="cierreCajaForm" method="POST" action="{{ route('cierre.caja') }}">
                    @csrf
                    <div class="form-row">
                        @if ($rolusuario !== 'CONTABLE')
                        <div class="form-group col-md-3">
                            <label for="usuario_busqueda">Seleccionar Usuario</label>
                            <select id="usuario_busqueda" name="usuario_busqueda" class="form-control">
                                <option value=""></option>
                                @foreach ($usuariosConsolidados as $usuario)
                                    <option value="{{ $usuario->usuarioconsolidadonombre }}"
                                        {{ old('usuario_busqueda', $usuarioBusqueda) == $usuario->usuarioconsolidadonombre ? 'selected' : '' }}>
                                        {{ $usuario->usuarioconsolidadonombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @endif
                        <div class="form-group col-md-3">
                            <label for="tipotransaccion">Tipo de Transacción</label>
                            <select id="tipotransaccion" name="tipotransaccion" class="form-control">
                                <option value=""></option>
                                <option value="ATC" {{ request('tipotransaccion') == 'ATC' ? 'selected' : '' }}>ATC</option>
                                <option value="Cheque" {{ request('tipotransaccion') == 'Cheque' ? 'selected' : '' }}>CHEQUE</option>
                                <option value="Deposito Bancario" {{ request('tipotransaccion') == 'Deposito Bancario' ? 'selected' : '' }}>DEPOSITO BANCARIO</option>
                                <option value="Efectivo" {{ request('tipotransaccion') == 'Efectivo' ? 'selected' : '' }}>EFECTIVO</option>
                                <option value="Transferencia Bancaria" {{ request('tipotransaccion') == 'Transferencia Bancaria' ? 'selected' : '' }}>TRANSFERENCIA BANCARIA</option>
                            </select>
                            
                        </div>
                        <div class="form-group col-md-3">
                            <label for="estadorevisioncierre">Estado de Revisión</label>
                            <select id="estadorevisioncierre" name="estadorevisioncierre" class="form-control">
                                <option value=""></option>
                                <option value="DOCUMENTACION PENDIENTE" {{ request('estadorevisioncierre') == 'DOCUMENTACION PENDIENTE' ? 'selected' : '' }}>DOCUMENTACION PENDIENTE</option>
                                <option value="CIERRE APROBADO" {{ request('estadorevisioncierre') == 'CIERRE APROBADO' ? 'selected' : '' }}>CIERRE APROBADO</option>
                                <option value="FINALIZADO" {{ request('estadorevisioncierre') == 'FINALIZADO' ? 'selected' : '' }}>FINALIZADO</option>
                            </select>
                            
                        </div>
                        <div class="form-group col-md-2">
                            <label for="fechacierre">Fecha de Registro</label>
                            <input type="date" id="fechacierre" name="fechacierre" class="form-control" value="{{ request('fechacierre') }}">
                        </div>
                        <div class="form-group col-md-1">
                            <label for="buscar">Buscar</label>
                            <button type="submit" class="btn btn-outline-secondary">Filtrar <i class="fas fa-search"></i></button>
                        </div>
                    </div>
                    <table class="table table-bordered table-striped" style="background-color:#ffffff">
                        <thead class="table-secondary">
                            <tr>
                                <th>ID</th>
                                <th>Ciudad_Reg.</th>
                                <th>Cliente/Prov.</th>
                                <th>Mov.</th>
                                <th>Detalle</th>
                                <th>Transac.</th>
                                <th>Subtotal</th>
                                <th>Desc.</th>
                                <th>Total</th>
                                <th>Saldo</th>
                                <th>Recibo</th>
                                <th>Fact.</th>
                                <th>Comp.</th>
                                <th>
                                    @can('admin.caja.ingresos.aprobarcierrecaja')
                                    <input type="checkbox" id="selectAllAprobarCierre">
                                    @endcan
                                    Aprob. Cierre
                                </th>
                                <th>
                                    <input type="checkbox" id="selectAllCerrarCaja"> Cerrar Caja
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($registros as $registro)
                                @if ($registro->usuarioanulacion === null)
                                <tr>
                                    <td class="truncar">{{ $registro->id }}</td>
                                    <td title="{{ $registro->ciudadregistro }}" class="truncar">{{ $registro->ciudadregistro }}</td>
                                    <td title="{{ $registro->clientenombre }} {{ $registro->proveedornombre }}" class="truncar">{{ $registro->clientenombre }} {{ $registro->proveedornombre }}</td>
                                    <td class="truncar">{{ $registro->tipomovimiento }}</td>
                                    {{-- <td class="truncar">{{ $registro->area }}</td> --}}
                                    <td title="{{ $registro->detalle }}" class="truncar2">{{ $registro->detalle ?? 'SIN DETALLE' }}</td>
                                    <td title="{{ $registro->tipotransaccion }}" class="truncar2">{{ $registro->tipotransaccion }}</td>
                                    <td class="truncar">{{ number_format($registro->subtotal, 2) }}</td>
                                    <td class="truncar">{{ number_format($registro->descuento, 2) }}</td>
                                    <td title="{{ number_format($registro->montototal, 2) }} - {{ number_format($registro->descuentoatc, 2) }} = {{ number_format($registro->montototal - $registro->descuentoatc, 2) }}" class="truncar2">
                                        {{ number_format($registro->montototal, 2) }}
                                        @if ($registro->descuentoatc !== null && $registro->descuentoatc != 0.00)
                                            - {{ number_format($registro->descuentoatc, 2) }} =
                                            {{ number_format($registro->montototal - $registro->descuentoatc, 2) }}
                                        @endif
                                    </td>
                                    <td class="truncar">{{ number_format($registro->saldo, 2) }}</td>
                                    <td>
                                        {{ $registro->nrorecibo }}
                                        @if ($registro->documentorespaldo)
                                            <a href="{{ asset('documentacioncaja/ingresos/' . $registro->usuarioregistroid . '/' . $registro->documentorespaldo) }}" class="btn btn-sm btn-verregistros" target="_blank" title="VER RECIBO"><i class="fas fa-eye"></i></a>
                                            
                                        @else
                                            <span class="badge badge-danger">VACIO</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($registro->docfactura)
                                            <a href="{{ asset('documentacioncaja/ingresos/' . $registro->usuarioregistroid . '/' . $registro->docfactura) }}" class="btn btn-sm btn-verregistros" target="_blank" title="VER FACTURA"><i class="fas fa-eye"></i></a>
                                        @else
                                            <span class="badge badge-danger">VACIO</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($registro->doccomprobante)
                                        <a href="{{ asset('documentacioncaja/ingresos/' . $registro->usuarioregistroid . '/' . $registro->doccomprobante) }}" class="btn btn-sm btn-verregistros" target="_blank" title="VER COMPROBANTE"><i class="fas fa-eye"></i></a>
                                        @else
                                            <span class="badge badge-danger">VACIO</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if ($registro->estadorevisioncierre == 'CIERRE APROBADO' || $registro->estadorevisioncierre == 'FINALIZADO')
                                            <span class="badge badge-success">CIERRE APROB.</span>
                                        @else
                                            @can('admin.caja.ingresos.aprobarcierrecaja')
                                                <input type="checkbox" name="registro_ids[]" value="{{ $registro->id }}" class="registro-checkbox" data-type="aprobar-cierre">
                                            @endcan
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if ($registro->estadorevisioncierre == 'CIERRE APROBADO')
                                            <input type="checkbox" name="registro_ids[]" value="{{ $registro->id }}" class="registro-checkbox" data-type="cerrar-caja">
                                        @elseif ($registro->estadorevisioncierre == 'FINALIZADO')
                                            <span class="badge badge-success">FINALIZADO</span>
                                        @else
                                            <span class="badge badge-danger">PENDIENTE</span>
                                        @endif
                                    </td>                            
                                </tr>
                                @endif
                            @endforeach
                            @foreach ($registrosegreso as $registro)
                                @if ($registro->usuarioanulacion === null)
                                <tr>
                                    <td class="truncar">{{ $registro->id }}</td>
                                    <td title="{{ $registro->ciudadregistro }}" class="truncar">{{ $registro->ciudadregistro }}</td> 
                                    <td title="{{ $registro->proveedornombre }}" class="truncar">{{ $registro->proveedornombre }}</td>
                                    <td class="truncar2">{{ $registro->tipomovimiento }}</td>
                                    {{-- <td class="truncar">{{ $registro->area }}</td> --}}
                                    <td title="{{ $registro->detalle }}" class="truncar2">{{ $registro->detalle ?? 'SIN DETALLES' }}</td>
                                    <td title="{{ $registro->tipotransaccion }}" class="truncar2">{{ $registro->tipotransaccion }}</td>
                                    <td class="truncar">{{ number_format($registro->subtotal, 2) }}</td>
                                    <td class="truncar">{{ number_format($registro->descuento, 2) }}</td>
                                    <td class="truncar2">
                                        {{ number_format($registro->montototal, 2) }}
                                        @if ($registro->descuentoatc !== null && $registro->descuentoatc != 0.00)
                                            - {{ number_format($registro->descuentoatc, 2) }} =
                                            {{ number_format($registro->montototal - $registro->descuentoatc, 2) }}
                                        @endif
                                    </td>
                                    <td class="truncar">{{ number_format($registro->saldo, 2) }}</td>
                                    
                                    <td>
                                        {{ $registro->nrorecibo }}
                                        @if ($registro->docrespaldoegreso)
                                            <a href="{{ asset('documentacioncaja/egresos/' . $registro->usuarioregistroid . '/' . $registro->docrespaldoegreso) }}" class="btn btn-sm btn-verregistros" target="_blank" title="VER RESPALDO"><i class="fas fa-eye"></i></a>
                                        @else
                                            <span class="badge badge-danger">VACIO</span>
                                        @endif
                                    </td>

                                    @php
                                        $ruta1 = public_path('documentacioncaja/egresos/' . $registro->usuarioregistroid . '/' . $registro->docfactura);
                                        $ruta2 = public_path('comprobantescuentaspagar/' . $registro->docfactura);

                                        /* $ruta3 = public_path('documentacioncaja/egresos/' . $registro->usuarioregistroid . '/' . $registro->docfactura2);
                                        $ruta4 = public_path('comprobantescuentaspagar/' . $registro->docfactura2);

                                        $ruta5 = public_path('documentacioncaja/egresos/' . $registro->usuarioregistroid . '/' . $registro->docfactura3);
                                        $ruta6 = public_path('comprobantescuentaspagar/' . $registro->docfactura3); */
                                    @endphp

                                    <td>
                                        @if (!empty($registro->docfactura) && file_exists($ruta1))
                                            <a href="{{ asset('documentacioncaja/egresos/' . $registro->usuarioregistroid . '/' . $registro->docfactura) }}" class="btn btn-sm btn-verregistros" target="_blank" title="VER FACTURA">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        @elseif (!empty($registro->docfactura) && file_exists($ruta2))
                                            <a href="{{ asset('comprobantescuentaspagar/' . $registro->docfactura) }}" class="btn btn-sm btn-verregistros" target="_blank" title="VER FACTURA">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        @else
                                            <span class="badge badge-danger">VACÍO</span>
                                        @endif

                                        {{-- @if (!empty($registro->docfactura2) && file_exists($ruta4))
                                            <a href="{{ asset('comprobantescuentaspagar/' . $registro->docfactura2) }}" class="btn btn-sm btn-verregistros" target="_blank" title="VER FACTURA 2">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        @elseif (!empty($registro->docfactura2) && file_exists($ruta3))
                                            <a href="{{ asset('documentacioncaja/egresos/' . $registro->usuarioregistroid . '/' . $registro->docfactura2) }}" class="btn btn-sm btn-verregistros" target="_blank" title="VER FACTURA 2">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        @endif

                                        @if (!empty($registro->docfactura3) && file_exists($ruta6))
                                            <a href="{{ asset('comprobantescuentaspagar/' . $registro->docfactura3) }}" class="btn btn-sm btn-verregistros" target="_blank" title="VER FACTURA 3">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        @elseif (!empty($registro->docfactura3) && file_exists($ruta5))
                                            <a href="{{ asset('documentacioncaja/egresos/' . $registro->usuarioregistroid . '/' . $registro->docfactura3) }}" class="btn btn-sm btn-verregistros" target="_blank" title="VER FACTURA 3">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        @endif --}}
                                    </td>

                                    @php
                                        $ruta1 = public_path('documentacioncaja/egresos/' . $registro->usuarioregistroid . '/' . $registro->doccomprobante);
                                        $ruta2 = public_path('comprobantescuentaspagar/' . $registro->doccomprobante);
                                    @endphp

                                    <td>
                                        @if (!empty($registro->doccomprobante) && file_exists($ruta1))
                                            <a href="{{ asset('documentacioncaja/egresos/' . $registro->usuarioregistroid . '/' . $registro->doccomprobante) }}" class="btn btn-sm btn-verregistros" target="_blank" title="VER COMPROBANTE">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        @elseif (!empty($registro->doccomprobante) && file_exists($ruta2))
                                            <a href="{{ asset('comprobantescuentaspagar/' . $registro->doccomprobante) }}" class="btn btn-sm btn-verregistros" target="_blank" title="VER COMPROBANTE">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        @else
                                            <span class="badge badge-danger">VACÍO</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if ($registro->estadorevisioncierre == 'CIERRE APROBADO' || $registro->estadorevisioncierre == 'FINALIZADO')
                                            <span class="badge badge-success">CIERRE APROB.</span>
                                        @else
                                            <input type="checkbox" name="registro_ids[]" value="{{ $registro->id }}" class="registro-checkbox" data-type="aprobar-cierre">
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if ($registro->estadorevisioncierre == 'CIERRE APROBADO')
                                            <input type="checkbox" name="registro_ids[]" value="{{ $registro->id }}" class="registro-checkbox" data-type="cerrar-caja">
                                        @elseif ($registro->estadorevisioncierre == 'FINALIZADO')
                                            <span class="badge badge-success">FINALIZADO</span>
                                        @else
                                            <span class="badge badge-danger">PENDIENTE</span>
                                        @endif
                                    </td>                            
                                </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                    
                    <style>
                        td {
                            white-space: nowrap;
                            overflow: hidden;
                            text-overflow: ellipsis;
                        }
                        .truncar {
                            white-space: nowrap;
                            overflow: hidden;
                            text-overflow: ellipsis;
                            max-width: 150px;
                        }
                        .truncar2 {
                            white-space: nowrap;
                            overflow: hidden;
                            text-overflow: ellipsis;
                            max-width: 100px;
                        }
                    </style>
                    <div class="mt-3 d-flex justify-content-between">
                        @can('admin.caja.ingresos.aprobarcierrecaja')
                        <button type="submit" class="btn btn-sm btn-outline-success aprobar-cierre-btn" name="accion" value="aprobar" id="aprobarCierreBtn">APROBAR CIERRE</button>
                        @endcan
                        @can('admin.caja.ingresos.cerrarcaja')
                        <button type="submit" class="btn btn-sm btn-outline-primary cerrar-caja-btn" name="accion" value="cerrar" id="cerrarCajaBtn">CERRAR CAJA</button>
                        @endcan
                    </div>
                    
                    <script>
                        document.getElementById('selectAllAprobarCierre').addEventListener('change', function() {
                            const checkboxes = document.querySelectorAll('input[data-type="aprobar-cierre"]');
                            checkboxes.forEach(checkbox => {
                                checkbox.checked = document.getElementById('selectAllAprobarCierre').checked;
                            });
                            document.getElementById('aprobarCierreBtn').disabled = !document.getElementById('selectAllAprobarCierre').checked;
                        });
                        document.getElementById('selectAllCerrarCaja').addEventListener('change', function() {
                            const checkboxes = document.querySelectorAll('input[data-type="cerrar-caja"]');
                            checkboxes.forEach(checkbox => {
                                checkbox.checked = document.getElementById('selectAllCerrarCaja').checked;
                            });
                            document.getElementById('cerrarCajaBtn').disabled = !document.getElementById('selectAllCerrarCaja').checked;
                        });
                        document.querySelectorAll('.registro-checkbox').forEach((checkbox) => {
                            checkbox.addEventListener('change', () => {
                                const anyCheckedAprobar = document.querySelectorAll('input[data-type="aprobar-cierre"]:checked').length > 0;
                                const anyCheckedCerrar = document.querySelectorAll('input[data-type="cerrar-caja"]:checked').length > 0;
                                document.getElementById('aprobarCierreBtn').disabled = !anyCheckedAprobar;
                                document.getElementById('cerrarCajaBtn').disabled = !anyCheckedCerrar;
                            });
                        });
                    </script>
                    <script>
                        document.addEventListener('DOMContentLoaded', function () {
                            const body = document.body;
                            const todosUsuariosCheckbox = document.getElementById('todos_usuarios');
                            const usuarioSelect = document.getElementById('usuario');
                
                            todosUsuariosCheckbox.addEventListener('change', function () {
                                if (this.checked) {
                                    usuarioSelect.value = 'TODOS LOS USUARIOS';
                                }
                            });
                
                            usuarioSelect.addEventListener('change', function () {
                                const options = Array.from(usuarioSelect.options);
                                const allSelected = options.every(option => option.selected);
                                todosUsuariosCheckbox.checked = allSelected;
                            });
                
                            const todosPagosCheckbox = document.getElementById('todos_pagos');
                            const formaPagoSelect = document.getElementById('forma_pago');
                
                            todosPagosCheckbox.addEventListener('change', function () {
                                if (this.checked) {
                                    formaPagoSelect.value = 'TODOS LOS PAGOS';
                                }
                            });
                
                            formaPagoSelect.addEventListener('change', function () {
                                const options = Array.from(formaPagoSelect.options);
                                const allSelected = options.every(option => option.selected);
                                todosPagosCheckbox.checked = allSelected;
                            });
                
                            document.querySelectorAll('.cambiar-estado').forEach(button => {
                                button.addEventListener('click', function () {
                                    const row = this.closest('tr');
                                    const estadoCell = row.querySelector('td:nth-child(3)');
                
                                    const estadoSelect = document.createElement('select');
                                    estadoSelect.classList.add('form-control');
                
                                    const estados = ['DOCUMENTACION PENDIENTE', 'CANCELADO', 'PENDIENTE', 'ANULADO', 'FINALIZADO'];
                                    estados.forEach(estado => {
                                        const option = document.createElement('option');
                                        option.value = estado;
                                        option.textContent = estado;
                                        if (estado === estadoCell.textContent) {
                                            option.selected = true;
                                        }
                                        estadoSelect.appendChild(option);
                                    });
                
                                    estadoCell.innerHTML = '';
                                    estadoCell.appendChild(estadoSelect);
                
                                    estadoSelect.addEventListener('change', function () {
                                        estadoCell.textContent = this.value;
                                    });
                                });
                            });
                        });
                    </script>
                </form>
            </div>
        </div>
    </div>
</div>
@endif
@stop

@section('js')
    
@stop