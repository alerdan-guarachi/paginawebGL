@extends('adminlte::page')

@section('content_header')
<h1>CUENTAS BANCARIAS</h1>
@stop

@section('css')
<link rel="stylesheet" href="{{ asset('css/opcionesmultiples.css') }}">
<style>
    .table td {
        padding: 5px 10px;
    }
    .btn-veringresos {
        background-color:  #ffffff;
        color: #94c93b;
        border-color: #94c93b;
        border-radius: 5px;
        padding: 2px 6px;
        }
    .btn-veringresos:hover {
        background-color: #94c93b;
        color: #ffffff;
        }
    .btn-vermov {
        background-color:  #ffffff;
        color: #5f5f5f;
        border-color: #5f5f5f;
        border-radius: 5px;
        padding: 2px 6px;
        }
    .btn-vermov:hover {
        background-color: #5f5f5f;
        color: #ffffff;
        }
</style>
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
<style>
    .bg-light-green {
        background-color: #ebfff0 !important;
    }
    .bg-light-yellow {
        background-color: #fafadb !important;
    }
    h1, th, h5 {
        color:#000000; 
        font-family: "Segoe UI";
        font-weight: 700;
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
<div class="card"><div class="card-body">
<div class="row justify-content-center"> 
    <!-- Tarjeta Cuenta 1 -->
    <div class="col-md-6 mb-4">
        <div class="card shadow-lg border-0" style="color: #495057; border-radius: 15px; overflow: hidden; position: relative;">
            <div class="card-header text-center border-0 position-relative" style="background: rgba(0, 0, 0, 0.03); padding: 1.2rem;">
                <img src="{{ asset('img/logobanco.png') }}" alt="Logo Banco" class="img-fluid" style="max-height: 50px; filter: drop-shadow(0px 2px 4px rgba(0, 0, 0, 0.2));">
                <h6 class="mt-2 text-uppercase font-weight-bold">N° Cuenta: 3000189269</h6>
            </div>
            <div class="card-body" style="text-align: left;">
                <div class="d-flex justify-content-between">
                    <h6 class="font-weight-bold">Saldo Anterior</h6>
                    <p class="h6 fw-bold text-dark">{{ number_format($saldoanteriorcuenta1, 2, '.', '') }} <span class="text-muted">Bs.</span></p>
                </div>
                <hr class="bg-secondary">
                <div class="d-flex justify-content-between">
                    <h6 class="font-weight-bold">Total Ingresos</h6>
                    <p class="h6 fw-bold text-dark">{{ number_format($totalCuenta1Ingreso, 2, '.', '') }} <span class="text-muted">Bs.</span></p>
                </div>
                <hr class="bg-secondary">
                <div class="d-flex justify-content-between">
                    <h6 class="font-weight-bold">Total Egresos</h6>
                    <p class="h6 fw-bold text-dark">{{ number_format($totalCuenta1Egreso, 2, '.', '') }} <span class="text-muted">Bs.</span></p>
                </div>
                <hr class="bg-secondary">
                <div class="d-flex justify-content-between">
                    <h6 class="font-weight-bold">Saldo</h6>
                    <p class="h5 fw-bold text-success">{{ number_format($saldoanteriorcuenta1 + $totalCuenta1Ingreso - $totalCuenta1Egreso, 2, '.', '') }} <span class="text-muted">Bs.</span></p>
                </div>
            </div>
            <div class="card-footer text-center border-0" style="background: rgba(0, 0, 0, 0.05); padding: 0.8rem;">
                <a class="btn btn btn-vermov" data-toggle="modal" data-target="#ingresoscuenta1">VER INGRESOS</a>
                <a class="btn btn btn-vermov" data-toggle="modal" data-target="#egresoscuenta1">VER EGRESOS</a>
            </div>
        </div>
    </div>

    <!-- Tarjeta Cuenta 2 -->
    <div class="col-md-6 mb-4">
        <div class="card shadow-lg border-0" style="color: #495057; border-radius: 15px; overflow: hidden; position: relative;">
            <div class="card-header text-center border-0 position-relative" style="background: rgba(0, 0, 0, 0.03); padding: 1.2rem;">
                <img src="{{ asset('img/logobanco.png') }}" alt="Logo Banco" class="img-fluid" style="max-height: 50px; filter: drop-shadow(0px 2px 4px rgba(0, 0, 0, 0.2));">
                <h6 class="mt-2 text-uppercase font-weight-bold">N° Cuenta: 2505314878</h6>
            </div>
            <div class="card-body" style="text-align: left;">
                <div class="d-flex justify-content-between">
                    <h6 class="font-weight-bold">Saldo Anterior</h6>
                    <p class="h6 fw-bold text-dark">{{ number_format($saldoanteriorcuenta2, 2, '.', '') }} <span class="text-muted">Bs.</span></p>
                </div>
                <hr class="bg-secondary">
                <div class="d-flex justify-content-between">
                    <h6 class="font-weight-bold">Total Ingresos</h6>
                    <p class="h6 fw-bold text-dark">{{ number_format($totalCuenta2Ingreso, 2, '.', '') }} <span class="text-muted">Bs.</span></p>
                </div>
                <hr class="bg-secondary">
                <div class="d-flex justify-content-between">
                    <h6 class="font-weight-bold">Total Egresos</h6>
                    <p class="h6 fw-bold text-dark">{{ number_format($totalCuenta2Egreso, 2, '.', '') }} <span class="text-muted">Bs.</span></p>
                </div>
                <hr class="bg-secondary">
                <div class="d-flex justify-content-between">
                    <h6 class="font-weight-bold">Saldo</h6>
                    <p class="h5 fw-bold text-success">{{ number_format($saldoanteriorcuenta2 + $totalCuenta2Ingreso - $totalCuenta2Egreso, 2, '.', '') }} <span class="text-muted">Bs.</span></p>
                </div>
            </div>
            <div class="card-footer text-center border-0" style="background: rgba(0, 0, 0, 0.05); padding: 0.8rem;">
                <a class="btn btn btn-vermov" data-toggle="modal" data-target="#ingresoscuenta2">VER INGRESOS</a>
                <a class="btn btn btn-vermov" data-toggle="modal" data-target="#egresoscuenta2">VER EGRESOS</a>
            </div>
        </div>
    </div>

    <!-- Tarjeta Cuenta 3 -->
    <div class="col-md-4 mb-4" hidden>
        <div class="card shadow-lg border-0" style="color: #495057; border-radius: 15px; overflow: hidden; position: relative;">
            <div class="card-header text-center border-0 position-relative" style="background: rgba(0, 0, 0, 0.03); padding: 1.2rem;">
                <img src="{{ asset('img/bancomercantil.png') }}" alt="Logo Banco" class="img-fluid" style="max-height: 50px; filter: drop-shadow(0px 2px 4px rgba(0, 0, 0, 0.2));">
                <h6 class="mt-2 text-uppercase font-weight-bold">N° Cuenta: 4011113557</h6>
            </div>
            <div class="card-body" style="text-align: left;">
                <div class="d-flex justify-content-between">
                    <h6 class="font-weight-bold">Saldo Anterior</h6>
                    <p class="h6 fw-bold text-dark">{{ number_format($saldoanteriorcuenta3, 2, '.', '') }} <span class="text-muted">Bs.</span></p>
                </div>
                <hr class="bg-secondary">
                <div class="d-flex justify-content-between">
                    <h6 class="font-weight-bold">Total Ingresos</h6>
                    <p class="h6 fw-bold text-dark">{{ number_format($totalCuenta3Ingreso, 2, '.', '') }} <span class="text-muted">Bs.</span></p>
                </div>
                <hr class="bg-secondary">
                <div class="d-flex justify-content-between">
                    <h6 class="font-weight-bold">Total Egresos</h6>
                    <p class="h6 fw-bold text-dark">{{ number_format($totalCuenta3Egreso, 2, '.', '') }} <span class="text-muted">Bs.</span></p>
                </div>
                <hr class="bg-secondary">
                <div class="d-flex justify-content-between">
                    <h6 class="font-weight-bold">Saldo</h6>
                    <p class="h5 fw-bold text-success">{{ number_format($saldoanteriorcuenta3 + $totalCuenta3Ingreso - $totalCuenta3Egreso, 2, '.', '') }} <span class="text-muted">Bs.</span></p>
                </div>
            </div>
            <div class="card-footer text-center border-0" style="background: rgba(0, 0, 0, 0.05); padding: 0.8rem;">
                <a class="btn btn btn-vermov" data-toggle="modal" data-target="#ingresoscuenta3">VER INGRESOS</a>
                <a class="btn btn btn-vermov" data-toggle="modal" data-target="#egresoscuenta3">VER EGRESOS</a>
            </div>
        </div>
    </div>
</div>
{{-- INGRESOS Y EGRESOS CUENTA 3000189269 --}}
<div class="modal fade" id="ingresoscuenta1" tabindex="-1" aria-labelledby="ingresoscuenta1Label" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ingresoscuenta1Label">INGRESOS DE LA CUENTA: 3000189269</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Cliente</th>
                            <th>Proveedor</th>
                            <th>Detalle</th>
                            <th>Tipo_Transac.</th>
                            <th>Subto.</th>
                            <th>Desc.</th>
                            <th>Total</th>
                            <th>Saldo</th>
                            <th>N.Rec.</th>
                            <th>Usuario_Reg.</th>
                            <th>Fecha_Dep.</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $idsMostrados = [];
                        @endphp

                        @foreach($ingresoscuenta1 as $movimiento)
                            @if(!in_array($movimiento->id, $idsMostrados))
                                @php
                                    $idsMostrados[] = $movimiento->id;
                                @endphp
                                <tr>
                                    <td>{{ $movimiento->id }}</td>
                                    <td title="{{ $movimiento->clientenombre }}" class="truncar">{{ $movimiento->clientenombre ?? 0 }}</td>
                                    <td title="{{ $movimiento->proveedoratencion }}" class="truncar">{{ $movimiento->proveedoratencion  ?? 0 }}</td>
                                    <td title="{{ $movimiento->detalle }}" class="truncar">{{ $movimiento->detalle }}</td>
                                    <td title="{{ $movimiento->tipotransaccion }}" class="truncar">{{ $movimiento->tipotransaccion }}</td>
                                    <td>{{ $movimiento->subtotal }}</td>
                                    <td>{{ $movimiento->descuento }}</td>
                                    <td>
                                        {{ $movimiento->descuentoatc 
                                            ? number_format($movimiento->montototal - $movimiento->descuentoatc, 2) 
                                            : number_format($movimiento->montototal, 2) }}
                                    </td>                                    
                                    <td>{{ $movimiento->saldo }}</td>
                                    <td>{{ $movimiento->reciboid }}</td>
                                    <td title="{{ $movimiento->usuarioregistronombre }}" class="truncar">{{ $movimiento->usuarioregistronombre }}</td>
                                    @if($movimiento->tipotransaccion === 'EFECTIVO')
                                    <td>{{ \Carbon\Carbon::parse($movimiento->depositosbancarios_created_at)->format('Y-m-d') }}</td>
                                    @elseif($movimiento->tipotransaccion === 'TRANSFERENCIA BANCARIA' || $movimiento->tipotransaccion === 'DEPOSITO BANCARIO')
                                        <td>{{ \Carbon\Carbon::parse($movimiento->created_at)->format('Y-m-d') }}</td>
                                    @elseif($movimiento->tipotransaccion === 'ATC')
                                        <td>{{ \Carbon\Carbon::parse($movimiento->cajacentral_fechabancarizacionatc)->format('Y-m-d') }}</td>
                                    @elseif($movimiento->tipotransaccion === 'CHEQUE')
                                        <td>{{ \Carbon\Carbon::parse($movimiento->cajacentral_updated_at)->format('Y-m-d') }}</td>
                                    @else
                                        <td>---------------</td>
                                    @endif
                                    <td>
                                        @if ($movimiento->estado === 'PAGO PROCESADO')
                                            <span class="badge bg-success">{{ $movimiento->estado }}</span>
                                        @elseif ($movimiento->estado === 'SALDO PENDIENTE')
                                            <span class="badge bg-warning text-dark">{{ $movimiento->estado }}</span>
                                        @else
                                            <span class="badge bg-secondary">{{ $movimiento->estado }}</span>
                                        @endif
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
<div class="modal fade" id="egresoscuenta1" tabindex="-1" aria-labelledby="egresoscuenta1Label" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="egresoscuenta1Label">EGRESOS DE LA CUENTA: 2505314878</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Proveedor</th>
                            <th>Cliente</th>
                            <th>Detalle</th>
                            <th>Tipo_Transac.</th>
                            <th>Subto.</th>
                            <th>Desc.</th>
                            <th>Total</th>
                            <th>Saldo</th>
                            <th>N.Rec.</th>
                            <th>Usuario_Reg.</th>
                            <th>Fecha_Reg.</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($egresoscuenta1 as $movimiento)
                        <tr>
                            <td>{{ $movimiento->id }}</td>
                            <td title="{{ $movimiento->proveedoratencion }}" class="truncar">{{ $movimiento->proveedoratencion  ?? 0 }}</td>
                            <td title="{{ $movimiento->clientenombre }}" class="truncar">{{ $movimiento->clientenombre  ?? 0 }}</td>
                            <td title="{{ $movimiento->detalle }}" class="truncar">{{ $movimiento->detalle }}</td>
                            <td title="{{ $movimiento->tipotransaccion }}" class="truncar">{{ $movimiento->tipotransaccion }}</td>
                            <td>{{ $movimiento->subtotal }}</td>
                            <td>{{ $movimiento->descuento }}</td>
                            <td>{{ $movimiento->montototal }}</td>
                            <td>{{ $movimiento->saldo }}</td>
                            <td>{{ $movimiento->reciboid }}</td>
                            <td title="{{ $movimiento->usuarioregistronombre }}" class="truncar">{{ $movimiento->usuarioregistronombre }}</td>
                            <td>{{ $movimiento->updated_at }}</td>
                            <td>
                                @if ($movimiento->estado === 'PAGO PROCESADO')
                                    <span class="badge bg-success">{{ $movimiento->estado }}</span>
                                @elseif ($movimiento->estado === 'SALDO PENDIENTE')
                                    <span class="badge bg-warning text-dark">{{ $movimiento->estado }}</span>
                                @else
                                    <span class="badge bg-secondary">{{ $movimiento->estado }}</span>
                                @endif
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

{{-- INGRESOS Y EGRESOS CUENTA 2505314878 --}}
<div class="modal fade" id="ingresoscuenta2" tabindex="-1" aria-labelledby="ingresoscuenta2Label" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ingresoscuenta2Label">INGRESOS DE LA CUENTA: 3000189269</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Cliente</th>
                            <th>Proveedor</th>
                            <th>Detalle</th>
                            <th>Tipo_Transac.</th>
                            <th>Subto.</th>
                            <th>Desc.</th>
                            <th>Total</th>
                            <th>Saldo</th>
                            <th>N.Rec.</th>
                            <th>Usuario_Reg.</th>
                            <th>Fecha_Reg.</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $idsMostrados2 = [];
                        @endphp

                        @foreach($ingresoscuenta2 as $movimiento)
                            @if(!in_array($movimiento->id, $idsMostrados2))
                                @php
                                    $idsMostrados2[] = $movimiento->id;
                                @endphp
                                <tr>
                                    <td>{{ $movimiento->id }}</td>
                                    <td title="{{ $movimiento->clientenombre }}" class="truncar">{{ $movimiento->clientenombre ?? 0 }}</td>
                                    <td title="{{ $movimiento->proveedoratencion }}" class="truncar">{{ $movimiento->proveedoratencion ?? 0 }}</td>
                                    <td title="{{ $movimiento->detalle }}" class="truncar">{{ $movimiento->detalle }}</td>
                                    <td title="{{ $movimiento->tipotransaccion }}" class="truncar">{{ $movimiento->tipotransaccion }}</td>
                                    <td>{{ $movimiento->subtotal }}</td>
                                    <td>{{ $movimiento->descuento }}</td>
                                    <td>
                                        {{ $movimiento->descuentoatc 
                                            ? number_format($movimiento->montototal - $movimiento->descuentoatc, 2) 
                                            : number_format($movimiento->montototal, 2) }}
                                    </td> 
                                    <td>{{ $movimiento->saldo }}</td>
                                    <td>{{ $movimiento->reciboid }}</td>
                                    <td title="{{ $movimiento->usuarioregistronombre }}" class="truncar">{{ $movimiento->usuarioregistronombre }}</td>
                                    @if($movimiento->tipotransaccion === 'EFECTIVO')
                                    <td>{{ \Carbon\Carbon::parse($movimiento->depositosbancarios_created_at)->format('Y-m-d') }}</td>
                                    @elseif($movimiento->tipotransaccion === 'TRANSFERENCIA BANCARIA' || $movimiento->tipotransaccion === 'DEPOSITO BANCARIO')
                                        <td>{{ \Carbon\Carbon::parse($movimiento->created_at)->format('Y-m-d') }}</td>
                                    @elseif($movimiento->tipotransaccion === 'ATC')
                                        <td>{{ \Carbon\Carbon::parse($movimiento->cajacentral_fechabancarizacionatc)->format('Y-m-d') }}</td>
                                    @elseif($movimiento->tipotransaccion === 'CHEQUE')
                                        <td>{{ \Carbon\Carbon::parse($movimiento->cajacentral_updated_at)->format('Y-m-d') }}</td>
                                    @else
                                        <td>---------------</td>
                                    @endif
                                    <td>
                                        @if ($movimiento->estado === 'PAGO PROCESADO')
                                            <span class="badge bg-success">{{ $movimiento->estado }}</span>
                                        @elseif ($movimiento->estado === 'SALDO PENDIENTE')
                                            <span class="badge bg-warning text-dark">{{ $movimiento->estado }}</span>
                                        @else
                                            <span class="badge bg-secondary">{{ $movimiento->estado }}</span>
                                        @endif
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
<div class="modal fade" id="egresoscuenta2" tabindex="-1" aria-labelledby="egresoscuenta2Label" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="egresoscuenta2Label">EGRESOS DE LA CUENTA: 2505314878</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Proveedor</th>
                            <th>Cliente</th>
                            <th>Detalle</th>
                            <th>Tipo_Transac.</th>
                            <th>Subto.</th>
                            <th>Desc.</th>
                            <th>Total</th>
                            <th>Saldo</th>
                            <th>N.Rec.</th>
                            <th>Usuario_Reg.</th>
                            <th>Fecha_Reg.</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($egresoscuenta2 as $movimiento)
                        <tr>
                            <td>{{ $movimiento->id }}</td>
                            <td title="{{ $movimiento->proveedoratencion }}" class="truncar">{{ $movimiento->proveedoratencion ?? 0 }}</td>
                            <td title="{{ $movimiento->clientenombre }}" class="truncar">{{ $movimiento->clientenombre ?? 0 }}</td>
                            <td title="{{ $movimiento->detalle }}" class="truncar">{{ $movimiento->detalle }}</td>
                            <td title="{{ $movimiento->tipotransaccion }}" class="truncar">{{ $movimiento->tipotransaccion }}</td>
                            <td>{{ $movimiento->subtotal }}</td>
                            <td>{{ $movimiento->descuento }}</td>
                            <td>{{ $movimiento->montototal }}</td>
                            <td>{{ $movimiento->saldo }}</td>                    
                            <td>{{ $movimiento->reciboid }}</td>
                            <td title="{{ $movimiento->usuarioregistronombre }}" class="truncar">{{ $movimiento->usuarioregistronombre }}</td>
                            <td>{{ $movimiento->updated_at }}</td>
                            <td>
                                @if ($movimiento->estado === 'PAGO PROCESADO')
                                    <span class="badge bg-success">{{ $movimiento->estado }}</span>
                                @elseif ($movimiento->estado === 'SALDO PENDIENTE')
                                    <span class="badge bg-warning text-dark">{{ $movimiento->estado }}</span>
                                @else
                                    <span class="badge bg-secondary">{{ $movimiento->estado }}</span>
                                @endif
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

{{-- INGRESOS Y EGRESOS CUENTA 4011113557 --}}
<div class="modal fade" id="ingresoscuenta3" tabindex="-1" aria-labelledby="ingresoscuenta3Label" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ingresoscuenta3Label">INGRESOS DE LA CUENTA: 4011113557</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Cliente</th>
                            <th>Proveedor</th>
                            <th>Detalle</th>
                            <th>Tipo_Transac.</th>
                            <th>Subto.</th>
                            <th>Desc.</th>
                            <th>Total</th>
                            <th>Saldo</th>
                            <th>N.Rec.</th>
                            <th>Usuario_Reg.</th>
                            <th>Fecha_Dep.</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $idsMostrados = [];
                        @endphp

                        @foreach($ingresoscuenta3 as $movimiento)
                            @if(!in_array($movimiento->id, $idsMostrados))
                                @php
                                    $idsMostrados[] = $movimiento->id;
                                @endphp
                                <tr>
                                    <td>{{ $movimiento->id }}</td>
                                    <td title="{{ $movimiento->clientenombre }}" class="truncar">{{ $movimiento->clientenombre ?? 0 }}</td>
                                    <td title="{{ $movimiento->proveedoratencion }}" class="truncar">{{ $movimiento->proveedoratencion ?? 0 }}</td>
                                    <td title="{{ $movimiento->detalle }}" class="truncar">{{ $movimiento->detalle }}</td>
                                    <td title="{{ $movimiento->tipotransaccion }}" class="truncar">{{ $movimiento->tipotransaccion }}</td>
                                    <td>{{ $movimiento->subtotal }}</td>
                                    <td>{{ $movimiento->descuento }}</td>
                                    <td>
                                        {{ $movimiento->descuentoatc 
                                            ? number_format($movimiento->montototal - $movimiento->descuentoatc, 2) 
                                            : number_format($movimiento->montototal, 2) }}
                                    </td>                                    
                                    <td>{{ $movimiento->saldo }}</td>
                                    <td>{{ $movimiento->reciboid }}</td>
                                    <td title="{{ $movimiento->usuarioregistronombre }}" class="truncar">{{ $movimiento->usuarioregistronombre }}</td>
                                    @if($movimiento->tipotransaccion === 'EFECTIVO')
                                    <td>{{ \Carbon\Carbon::parse($movimiento->depositosbancarios_created_at)->format('Y-m-d') }}</td>
                                    @elseif($movimiento->tipotransaccion === 'TRANSFERENCIA BANCARIA' || $movimiento->tipotransaccion === 'DEPOSITO BANCARIO')
                                        <td>{{ \Carbon\Carbon::parse($movimiento->created_at)->format('Y-m-d') }}</td>
                                    @elseif($movimiento->tipotransaccion === 'ATC')
                                        <td>{{ \Carbon\Carbon::parse($movimiento->cajacentral_fechabancarizacionatc)->format('Y-m-d') }}</td>
                                    @elseif($movimiento->tipotransaccion === 'CHEQUE')
                                        <td>{{ \Carbon\Carbon::parse($movimiento->cajacentral_updated_at)->format('Y-m-d') }}</td>
                                    @else
                                        <td>---------------</td>
                                    @endif
                                    <td>
                                        @if ($movimiento->estado === 'PAGO PROCESADO')
                                            <span class="badge bg-success">{{ $movimiento->estado }}</span>
                                        @elseif ($movimiento->estado === 'SALDO PENDIENTE')
                                            <span class="badge bg-warning text-dark">{{ $movimiento->estado }}</span>
                                        @else
                                            <span class="badge bg-secondary">{{ $movimiento->estado }}</span>
                                        @endif
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
<div class="modal fade" id="egresoscuenta3" tabindex="-1" aria-labelledby="egresoscuenta3Label" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="egresoscuenta3Label">EGRESOS DE LA CUENTA: 4011113557</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Proveedor</th>
                            <th>Cliente</th>
                            <th>Detalle</th>
                            <th>Tipo_Transac.</th>
                            <th>Subto.</th>
                            <th>Desc.</th>
                            <th>Total</th>
                            <th>Saldo</th>
                            <th>N.Rec.</th>
                            <th>Usuario_Reg.</th>
                            <th>Fecha_Reg.</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($egresoscuenta3 as $movimiento)
                        <tr>
                            <td>{{ $movimiento->id }}</td>
                            <td title="{{ $movimiento->proveedoratencion }}" class="truncar">{{ $movimiento->proveedoratencion ?? 0 }}</td>
                            <td title="{{ $movimiento->clientenombre }}" class="truncar">{{ $movimiento->clientenombre ?? 0 }}</td>
                            <td title="{{ $movimiento->detalle }}" class="truncar">{{ $movimiento->detalle }}</td>
                            <td title="{{ $movimiento->tipotransaccion }}" class="truncar">{{ $movimiento->tipotransaccion }}</td>
                            <td>{{ $movimiento->subtotal }}</td>
                            <td>{{ $movimiento->descuento }}</td>
                            <td>{{ $movimiento->montototal }}</td>
                            <td>{{ $movimiento->saldo }}</td>
                            <td>{{ $movimiento->reciboid }}</td>
                            <td title="{{ $movimiento->usuarioregistronombre }}" class="truncar">{{ $movimiento->usuarioregistronombre }}</td>
                            <td>{{ $movimiento->updated_at }}</td>
                            <td>
                                @if ($movimiento->estado === 'PAGO PROCESADO')
                                    <span class="badge bg-success">{{ $movimiento->estado }}</span>
                                @elseif ($movimiento->estado === 'SALDO PENDIENTE')
                                    <span class="badge bg-warning text-dark">{{ $movimiento->estado }}</span>
                                @else
                                    <span class="badge bg-secondary">{{ $movimiento->estado }}</span>
                                @endif
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
@stop

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