@extends('adminlte::page')

@section('content_header')
<div class="modal fade" id="crearProductoModal" tabindex="-1" aria-labelledby="crearProductoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="crearProductoModalLabel" style="font-weight: 900;">NUEVA FACTURA</h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                {!! Form::open(['route' => 'admin.facturasegreso.store', 'method'=>'POST']) !!}
                    {!! Form::hidden('usuarioregistroid', auth()->user()->id) !!}
                    {!! Form::hidden('usuarioregistronombre', auth()->user()->name) !!}
                    <div class="row">
                        <div class="col-lg-3 form-group">
                            <label for="tipo">Tipo</label>
                            <select name="tipo" id="tipo" class="form-control" required>
                                <option value=""></option>
                                <option value="1">COMPRA</option>
                                <option value="2">VENTA</option>
                            </select>
                        </div>
                        <div class="col-lg-3 form-group">
                            <label for="ciudad">Ciudad</label>
                            <select name="ciudad" id="ciudad" class="form-control" required>
                                <option value=""></option>
                                <option value="SANTA CRUZ">SANTA CRUZ</option>
                                <option value="COCHABAMBA">COCHABAMBA</option>
                            </select>
                        </div>
                        <div class="col-lg-3 form-group">
                            <label>Nro. Factura</label>
                            <input type="text" id="nrofactura" name="nrofactura" class="form-control" required>
                        </div>
                        <div class="col-lg-3 form-group">
                            <label>Nro. NIT/CI</label>
                            <input type="text" id="nitci" name="nitci" class="form-control" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-4 form-group">
                            <label>Razon Social</label>
                            <input type="text" id="razonsocial" name="razonsocial" class="form-control" required>
                        </div>
                        <div class="col-lg-1 form-group">
                            <label>Comp.</label>
                            <input type="text" id="complemento" name="complemento" class="form-control">
                        </div>
                        <script>
                            document.addEventListener('DOMContentLoaded', function () {
                                const tipo = document.getElementById('tipo');
                                const complemento = document.getElementById('complemento');
                                function toggleComplemento() {
                                    if (tipo.value === '2') {
                                        complemento.removeAttribute('disabled');
                                    } else {
                                        complemento.setAttribute('disabled', true);
                                        complemento.value = '';
                                    }
                                }
                                toggleComplemento();
                                tipo.addEventListener('change', toggleComplemento);
                            });
                        </script>
                        <div class="col-lg-4 form-group">
                            <label>Cod. Autorización</label>
                            <input type="text" id="codigoautorizacion" name="codigoautorizacion" class="form-control" required>
                        </div>
                        <div class="col-lg-3 form-group">
                            <label>Fecha Factura</label>
                            <input type="date" id="fechafacturaduidim" name="fechafacturaduidim" class="form-control" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-3 form-group">
                            <label>Total</label>
                            <input type="number" id="total" name="total" class="form-control" required>
                        </div>
                        <div class="col-lg-3 form-group">
                            <label>Descuento</label>
                            <input type="number" id="descuento" name="descuento" class="form-control" required>
                        </div>
                        <div class="col-lg-3 form-group">
                            <label>Subtotal</label>
                            <input type="number" id="subtotal" name="subtotal" class="form-control" required>
                        </div>
                        <div class="col-lg-3 form-group">
                            <label>Importe base CF/DF</label>
                            <input type="number" id="cfdf2" name="cfdf" class="form-control" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-4 form-group">
                            <label>Imp. Suj. Créd./Déb. Fiscal</label>
                            <input type="text" id="importesujetocfdf" name="importesujetocfdf" class="form-control" disabled>
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="habilitarImporte">
                                <label class="form-check-label" for="habilitarImporte">Habilitar Imp. Créd./Déb. Fiscal</label>
                            </div>
                        </div>
                        <div class="col-lg-4 form-group">
                            <label>Créd./Déb. Fiscal</label>
                            <input type="text" id="creditodebitofiscal" name="creditodebitofiscal" class="form-control" readonly>
                        </div>
                        <script>
                            document.addEventListener("DOMContentLoaded", function () {
                                const checkbox = document.getElementById('habilitarImporte');
                                const importesujetocfdfInput = document.getElementById('importesujetocfdf');
                                const cfdfInput = document.getElementById('cfdf2');
                                const creditoInput = document.getElementById('creditodebitofiscal');
                                checkbox.addEventListener('change', function () {
                                    if (this.checked) {
                                        importesujetocfdfInput.disabled = false;
                                        calcularCredito();
                                    } else {
                                        importesujetocfdfInput.disabled = true;
                                        importesujetocfdfInput.value = "";
                                    }
                                });
                                cfdf2.addEventListener('input', calcularCredito);
                                function calcularCredito() {
                                    const valor = parseFloat(cfdf2.value);
                                    if (!isNaN(valor)) {
                                        const resultado = valor * 0.13;
                                        creditoInput.value = resultado.toFixed(2);
                                    } else {
                                    }
                                }
                            });
                        </script>
                        <div class="col-lg-4 form-group">
                            <label>Cod. Control</label>
                            <input type="text" id="codigocontrol" name="codigocontrol" class="form-control" required>
                        </div>
                    </div>

                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const habilitarImporteCheckbox = document.getElementById('habilitarImporte');
                            const cfdfInput = document.getElementById('cfdf');

                            habilitarImporteCheckbox.addEventListener('change', function() {
                                cfdfInput.disabled = !this.checked;
                            });
                        });
                    </script>

                    <div class="row" hidden>
                        <div class="col-lg-3 form-group">
                            <label>Especificación</label>
                            <input type="text" id="especificacion" name="especificacion" value="1" class="form-control">
                        </div>
                        <div class="col-lg-3 form-group">
                            <label>Nro. DUI/DIM</label>
                            <input type="text" id="nroduidim" name="nroduidim" class="form-control" value="0.00">
                        </div>
                        <div class="col-lg-3 form-group">
                            <label>Importe ICE</label>
                            <input type="text" id="ice" name="ice" class="form-control" value="0.00">
                        </div>
                        <div class="col-lg-3 form-group">
                            <label>Importe IEHD</label>
                            <input type="text" id="iehd" name="iehd" class="form-control" value="0.00">
                        </div>
                        <div class="col-lg-3 form-group">
                            <label>Estado</label>
                            <input type="text" id="estado" name="estado" class="form-control" value="ACTIVO">
                        </div>
                    </div>
                    <div class="row" hidden>
                        <div class="col-lg-3 form-group">
                            <label>Tasas</label>
                            <input type="text" id="tasas" name="tasas" class="form-control" value="0.00">
                        </div>
                        <div class="col-lg-3 form-group">
                            <label>Otro No Suj.Cred.Fiscal o IVA</label>
                            <input type="text" id="otronosujcredfiscaloiva" name="otronosujcredfiscaloiva" class="form-control" value="0.00">
                        </div>
                        <div class="col-lg-3 form-group">
                            <label>Importe/Exporte Externo</label>
                            <input type="text" id="importeyexporteexterno" name="importeyexporteexterno" class="form-control" value="0.00">
                        </div>
                        <div class="col-lg-3 form-group">
                            <label>Importe Gift Card</label>
                            <input type="text" id="giftcard" name="giftcard" class="form-control" value="0.00">
                        </div>
                    </div>
                    <div class="row" hidden>
                        <div class="col-lg-3 form-group">
                            <label>Importe IPJ</label>
                            <input type="text" id="ipj" name="ipj" class="form-control" value="0.00">
                        </div>
                        <div class="col-lg-3 form-group">
                            <label>Importe Externo</label>
                            <input type="text" id="importeexterno" name="importeexterno" class="form-control" value="0.00">
                        </div>
                        <div class="col-lg-3 form-group">
                            <label>Importe Grav. Tasa Cero</label>
                            <input type="text" id="tasacero" name="tasacero" class="form-control" value="0.00">
                        </div>
                    </div>
                    {!! Form::submit('GUARDAR FACTURA', ['class' => 'btn btn-sm btn-outline-secondary']) !!}
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>
<a type="button" class="btn float-right btn-outline-secondary btn-sm" data-toggle="modal" data-target="#crearProductoModal">
    AGREGAR FACTURA
</a>
<h1>FACTURAS EGRESO</h1>
@stop

@section('css')
<link rel="stylesheet" href="{{ asset('css/opcionesmultiples.css') }}">
<style>
    .table td {
        padding: 2px 10px;
    }
    .table th {
        padding: 6px 10px;
    }
    .btn-registrar {
        background-color:  #ffffff;
        color: #94c93b;
        border-color: #94c93b;
        border-radius: 5px;
        padding: 2px 20px;
        }
    .btn-registrar:hover {
        background-color: #94c93b;
        color: #ffffff;
        }
    .btn-actualizar {
        background-color:  #ffffff;
        color: #e8932b;
        border-color: #e8932b;
        border-radius: 5px;
        padding: 2px 6px;
        }
    .btn-actualizar:hover {
        background-color: #e8932b;
        color: #ffffff;
        }
    .truncar {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 180px;
    }
    .truncar2 {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 150px;
    }
    .btn-verregistros {
        background-color:  #ffffff;
        color: #787878;
        border-color: #787878;
        border-radius: 5px;
        padding: 2px 6px;
        }
    .btn-verregistros:hover {
        background-color: #787878;
        color: #ffffff;
        }
    .btn-custom2 {
        background-color:  #ffffff;
        color: #9d9d9d;
        border-color: #9d9d9d;
        border-radius: 20px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        transition: background-color 0.3s, transform 0.3s, box-shadow 0.3s;
        height: 200px;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0;
    }
    .btn-custom2:hover {
        background-color: #9d9d9d;
        color: #ffffff;
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        transform: scale(1.05);
    }
    .btn-custom2:disabled {
        background-color: #d6d6d6;
        color: #a0a0a0;
        cursor: not-allowed;
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
        <form method="GET" action="{{ route('admin.facturasegreso.index') }}" class="row g-3">
            <div class="col-md-2">
                <label>Fecha Desde</label>
                <input type="date" name="fechaDesde" value="{{ $fechaDesde }}" class="form-control">
            </div>
            <div class="col-md-2">
                <label>Fecha Hasta</label>
                <input type="date" name="fechaHasta" value="{{ $fechaHasta }}" class="form-control">
            </div>
            <div class="col-md-2 align-self-end">
                <button class="btn btn-outline-secondary" type="submit">Filtrar</button>
            </div>
        </form>
        <br>
        <ul class="nav nav-tabs card-header-tabs" id="myTabs">
            <li class="nav-item">
                <a class="nav-link active" id="tab-1" data-toggle="tab" href="#tab-content-1" role="tab" aria-controls="tab-content-1" aria-selected="true">
                    IMPUESTO DETERMINADO
                </a>
            </li>   
            <li class="nav-item">
                <a class="nav-link" id="tab-2" data-toggle="tab" href="#tab-content-2" role="tab" aria-controls="tab-content-2" aria-selected="true">
                    COMPRAS SCZ
                </a>
            </li>     
            <li class="nav-item">
                <a class="nav-link" id="tab-3" data-toggle="tab" href="#tab-content-3" role="tab" aria-controls="tab-content-3" aria-selected="true">
                    VENTAS SCZ
                </a>
            </li>    
            <li class="nav-item">
                <a class="nav-link" id="tab-4" data-toggle="tab" href="#tab-content-4" role="tab" aria-controls="tab-content-4" aria-selected="true">
                    COMPRAS CBBA
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="tab-5" data-toggle="tab" href="#tab-content-5" role="tab" aria-controls="tab-content-5" aria-selected="true">
                    VENTAS CBBA
                </a>
            </li> 
        </ul>
    </div>
    <div class="card-body">
        <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade show active" id="tab-content-1" role="tabpanel" aria-labelledby="tab-1">
                <div class="row">
                    <div class="col-lg-7">
                        <div class="card">
                            <div class="card-body">
                                <h5 style="font-weight: 900;">VENTAS</h5>
                                <div class="table-responsive" style="margin-bottom: -10px;">
                                    <table class="table table-bordered">
                                        <thead style="background-color: #f3f3f3">
                                            <tr>
                                                <th>Sucursal</th>
                                                <th style="text-align: right;">Ventas</th>
                                                <th style="text-align: right;">IVA-Débito Fiscal</th>
                                                <th style="text-align: right;">IT</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($ventas as $ciudad => $registros)
                                                @php
                                                    $total = $registros->sum('total');
                                                    $iva = $total * 0.13;
                                                    $it = $total * 0.03;
                                                @endphp
                                                <tr>
                                                    <td>{{ $ciudad }}</td>
                                                    <td style="text-align: right;">{{ number_format($total, 2) }}</td>
                                                    <td style="text-align: right;">{{ number_format($iva, 2) }}</td>
                                                    <td style="text-align: right;">{{ number_format($it, 2) }}</td>
                                                </tr>
                                            @endforeach
                                            <tr style="background-color: #f3f9e5;">
                                                <th>Total</th>
                                                <th style="text-align: right;">{{ number_format($totales['totalVentas'], 2) }}</th>
                                                <th style="text-align: right;">{{ number_format($totales['ivaDebito'], 2) }}</th>
                                                <th style="text-align: right;">{{ number_format($totales['itVentas'], 2) }}</th>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-body">
                                <h5 style="font-weight: 900;">COMPRAS</h5>
                                <div class="table-responsive" style="margin-bottom: -10px;">
                                    <table class="table table-bordered">
                                        <thead style="background-color: #f3f3f3">
                                            <tr>
                                                <th>Sucursal</Canvas></th>
                                                <th style="text-align: right;">Compras</th>
                                                <th style="text-align: right;">IVA-Crédito Fiscal</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($compras as $ciudad => $registros)
                                                @php
                                                    $total = $registros->sum('total');
                                                    $iva = $total * 0.13;
                                                @endphp
                                                <tr>
                                                    <td>{{ $ciudad }}</td>
                                                    <td style="text-align: right;">{{ number_format($total, 2) }}</td>
                                                    <td style="text-align: right;">{{ number_format($iva, 2) }}</td>
                                                </tr>
                                            @endforeach
                                            <tr style="background-color: #f3f9e5">
                                                <th>Total</th>
                                                <th style="text-align: right;">{{ number_format($totales['totalCompras'], 2) }}</th>
                                                <th style="text-align: right;">{{ number_format($totales['ivaCredito'], 2) }}</th>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="mt-4">
                            <div class="d-flex justify-content-between align-items-center p-2" style="background-color: #fff2dc; font-weight: 900;">
                                <h5 class="mb-0" style="font-weight: 900;">DIFERENCIA:</h5>
                                <h5 class="mb-0" style="font-weight: 900;">{{ number_format($totales['diferencia'], 2) }}</h5>
                            </div>
                        </div>

                    </div>
                    <div class="col-lg-5">
                        <div class="card">
                            <div class="card-body">
                                <div class="mt-4" style="margin-bottom: -10px;">
                                    <div class="p-2 d-flex justify-content-between align-items-center" style="background-color: #f3f3f3">
                                        <h6 class="mb-0">SALDO CRÉDITO FISCAL A FAVOR DEL ANTERIOR PERIODO:</h6>
                                        <input type="number" name="saldo_credito_anterior" class="form-control form-control-sm" style="width: 100px;" value="0" step="0.01">
                                    </div>
                                </div>

                                <div class="mt-4">
                                    <div class="p-2 d-flex justify-content-between align-items-center" style="background-color: #f3f3f3">
                                        <h6 class="mb-0">ACTUALIZACIÓN DEL VALOR SOBRE EL SALDO DE CRÉDITO FISCAL DEL PERIODO ANTERIOR:</h6>
                                        <input type="number" name="actualizacion_saldo_anterior" class="form-control form-control-sm" style="width: 100px;" value="0" step="0.01">
                                    </div>
                                </div>

                                <h5 class="mt-4" style="font-weight: 900;">RESUMEN GENERAL</h5>
                                <div class="table-responsive">
                                    <table class="table table-bordered" style="width: 100%;">
                                        <tbody>
                                            <tr style="background-color: #fff2dc">
                                                <th>Total Ventas</th>
                                                <td style="text-align: right;">{{ number_format($totales['totalVentas'], 2) }}</td>
                                            </tr>
                                            <tr style="background-color: #fff2dc">
                                                <th>Total Compras</th>
                                                <td style="text-align: right;">{{ number_format($totales['totalCompras'], 2) }}</td>
                                            </tr>
                                            <tr style="background-color: #fff2dc">
                                                <th>Total Diferencia</th>
                                                <td style="text-align: right;">{{ number_format($totales['totalVentas'] - $totales['totalCompras'], 2) }}</td>
                                            </tr>
                                            <tr style="background-color: #fff2dc">
                                                <th>Facturación / Pendiente</th>
                                                <td style="text-align: right;">0.00</td>
                                            </tr>
                                            <tr style="background-color: #f3f9e5">
                                                <th>Impuesto Pagar IVA</th>
                                                <td style="text-align: right;">
                                                    {{ number_format(($totales['totalVentas'] * 0.13) - ($totales['totalCompras'] * 0.13), 2) }}
                                                </td>
                                            </tr>
                                            <tr style="background-color: #f3f9e5">
                                                <th>Impuesto Pagar IT</th>
                                                <td style="text-align: right;">{{ number_format($totales['totalVentas'] * 0.03, 2) }}</td>
                                            </tr>
                                            <tr style="background-color: #fff2dc">
                                                <th>Total Pago</th>
                                                <td style="text-align: right;">
                                                    {{ number_format((($totales['totalVentas'] * 0.13) - ($totales['totalCompras'] * 0.13)) + ($totales['totalVentas'] * 0.03), 2) }}
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade" id="tab-content-2" role="tabpanel" aria-labelledby="tab-2">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead style="background-color: #f3f3f3">
                            <tr>
                                <th>Nro.</th>
                                <th>Especif.</th>
                                <th>Nit_Proveedor</th>
                                <th>Razon_Social_Proveedor</th>
                                <th>Codigo_de_Autorización</th>
                                <th>Nro.Factura</th>
                                <th>Nro.DUI/DIM</th>
                                <th>Fecha de Factura_DUI/DIM</th>
                                <th>Importe Total Compra</th>
                                <th>Importe ICE</th>
                                <th>Importe IEHD</th>
                                <th>Importe IPJ</th>
                                <th>Tasas</th>
                                <th>Otro no Sujeto a Credito_Fiscal</th>
                                <th>Importes Externos</th>
                                <th>Importe_Compras Gravadas a Tasa_Cero</th>
                                <th>Subtotal</th>
                                <th>Desc._Bonif._Reb. Sujetas a IVA</th>
                                <th>Importe GIFT CARD</th>
                                <th>Importe Base CF</th>
                                <th>Credito Fiscal</th>
                                <th>Tipo Compra</th>
                                <th>Importe Sujeto_CF</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($comprasscz as $compra)
                                <tr>
                                    <td>{{ $compra->id }}</td>
                                    <td>{{ $compra->especificacion }}</td>
                                    <td>{{ $compra->nitci }}</td>
                                    <td>{{ $compra->razonsocial }}</td>
                                    <td>{{ $compra->codigoautorizacion }}</td>
                                    <td>{{ $compra->nrofactura }}</td>
                                    <td>{{ $compra->nroduidim }}</td>
                                    <td>{{ $compra->fechafacturaduidim }}</td>
                                    <td>{{ $compra->total }}</td>
                                    <td>{{ $compra->ice }}</td>
                                    <td>{{ $compra->iehd }}</td>
                                    <td>{{ $compra->ipj }}</td>
                                    <td>{{ $compra->tasas }}</td>
                                    <td>{{ $compra->otronosujcredfiscaloiva }}</td>
                                    <td>{{ $compra->importeyexporteexterno }}</td>
                                    <td>{{ $compra->tasacero }}</td>
                                    <td>{{ $compra->subtotal }}</td>
                                    <td>{{ $compra->descuento }}</td>
                                    <td>{{ $compra->giftcard }}</td>
                                    <td>{{ $compra->cfdf }}</td>
                                    <td>{{ $compra->creditodebitofiscal }}</td>
                                    <td>{{ $compra->tipo }}</td>
                                    <td>{{ $compra->importesujetocfdf }}</td>
                                    <td>{{ $compra->estado }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="tab-pane fade" id="tab-content-3" role="tabpanel" aria-labelledby="tab-3">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead style="background-color: #f3f3f3">
                            <tr>
                                <th>Nro.</th>
                                <th>Especif.</th>
                                <th>Fecha_de_Factura</th>
                                <th>Nro.Factura</th>
                                <th>Codigo_de_Autorización</th>
                                <th>Nit/CI_Cliente</th>
                                <th>Complemento</th>
                                <th>Nombre_o_Razon_Social</th>
                                <th>Importe Total Venta</th>
                                <th>Importe ICE</th>
                                <th>Importe IEHD</th>
                                <th>Importe IPJ</th>
                                <th>Tasas</th>
                                <th>Otro no Sujeto_al_IVA</th>
                                <th>Importes Externos</th>
                                <th>Importe_Compras Gravadas a Tasa_Cero</th>
                                <th>Subtotal</th>
                                <th>Desc._Bonif._Reb. Sujetas a IVA</th>
                                <th>Importe GIFT CARD</th>
                                <th>Importe Base DF</th>
                                <th>Débito Fiscal</th>
                                <th>Tipo Compra</th>
                                <th>Importe Sujeto_DF</th>
                                <th>Codigo Control</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($ventasscz as $venta)
                                <tr>
                                    <td>{{ $venta->id }}</td>
                                    <td>{{ $venta->especificacion }}</td>
                                    <td>{{ $venta->fechafacturaduidim }}</td>
                                    <td>{{ $venta->nrofactura }}</td>
                                    <td>{{ $venta->codigoautorizacion }}</td>
                                    <td>{{ $venta->nitci }}</td>
                                    <td>{{ $venta->complemento }}</td>
                                    <td>{{ $venta->razonsocial }}</td>
                                    <td>{{ $venta->total }}</td>
                                    <td>{{ $venta->ice }}</td>
                                    <td>{{ $venta->iehd }}</td>
                                    <td>{{ $venta->ipj }}</td>
                                    <td>{{ $venta->tasas }}</td>
                                    <td>{{ $venta->otronosujcredfiscaloiva }}</td>
                                    <td>{{ $venta->importeyexporteexterno }}</td>
                                    <td>{{ $venta->tasacero }}</td>
                                    <td>{{ $venta->subtotal }}</td>
                                    <td>{{ $venta->descuento }}</td>
                                    <td>{{ $venta->giftcard }}</td>
                                    <td>{{ $venta->cfdf }}</td>
                                    <td>{{ $venta->creditodebitofiscal }}</td>
                                    <td>{{ $venta->tipo }}</td>
                                    <td>{{ $compra->importesujetocfdf }}</td>
                                    <td>{{ $compra->codigocontrol }}</td>
                                    <td>{{ $compra->estado }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div class="tab-pane fade" id="tab-content-4" role="tabpanel" aria-labelledby="tab-4">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead style="background-color: #f3f3f3">
                            <tr>
                                <th>Nro.</th>
                                <th>Especif.</th>
                                <th>Nit_Proveedor</th>
                                <th>Razon_Social_Proveedor</th>
                                <th>Codigo_de_Autorización</th>
                                <th>Nro.Factura</th>
                                <th>Nro.DUI/DIM</th>
                                <th>Fecha de Factura_DUI/DIM</th>
                                <th>Importe Total Compra</th>
                                <th>Importe ICE</th>
                                <th>Importe IEHD</th>
                                <th>Importe IPJ</th>
                                <th>Tasas</th>
                                <th>Otro no Sujeto a Credito_Fiscal</th>
                                <th>Importes Externos</th>
                                <th>Importe_Compras Gravadas a Tasa_Cero</th>
                                <th>Subtotal</th>
                                <th>Desc._Bonif._Reb. Sujetas a IVA</th>
                                <th>Importe GIFT CARD</th>
                                <th>Importe Base CF</th>
                                <th>Credito Fiscal</th>
                                <th>Tipo Compra</th>
                                <th>Importe Sujeto_CF</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($comprascbba as $compra)
                                <tr>
                                    <td>{{ $compra->id }}</td>
                                    <td>{{ $compra->especificacion }}</td>
                                    <td>{{ $compra->nitci }}</td>
                                    <td>{{ $compra->razonsocial }}</td>
                                    <td>{{ $compra->codigoautorizacion }}</td>
                                    <td>{{ $compra->nrofactura }}</td>
                                    <td>{{ $compra->nroduidim }}</td>
                                    <td>{{ $compra->fechafacturaduidim }}</td>
                                    <td>{{ $compra->total }}</td>
                                    <td>{{ $compra->ice }}</td>
                                    <td>{{ $compra->iehd }}</td>
                                    <td>{{ $compra->ipj }}</td>
                                    <td>{{ $compra->tasas }}</td>
                                    <td>{{ $compra->otronosujcredfiscaloiva }}</td>
                                    <td>{{ $compra->importeyexporteexterno }}</td>
                                    <td>{{ $compra->tasacero }}</td>
                                    <td>{{ $compra->subtotal }}</td>
                                    <td>{{ $compra->descuento }}</td>
                                    <td>{{ $compra->giftcard }}</td>
                                    <td>{{ $compra->cfdf }}</td>
                                    <td>{{ $compra->creditodebitofiscal }}</td>
                                    <td>{{ $compra->tipo }}</td>
                                    <td>{{ $compra->importesujetocfdf }}</td>
                                    <td>{{ $compra->estado }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="tab-pane fade" id="tab-content-5" role="tabpanel" aria-labelledby="tab-5">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead style="background-color: #f3f3f3">
                            <tr>
                                <th>Nro.</th>
                                <th>Especif.</th>
                                <th>Fecha_de_Factura</th>
                                <th>Nro.Factura</th>
                                <th>Codigo_de_Autorización</th>
                                <th>Nit/CI_Cliente</th>
                                <th>Complemento</th>
                                <th>Nombre_o_Razon_Social</th>
                                <th>Importe Total Venta</th>
                                <th>Importe ICE</th>
                                <th>Importe IEHD</th>
                                <th>Importe IPJ</th>
                                <th>Tasas</th>
                                <th>Otro no Sujeto_al_IVA</th>
                                <th>Importes Externos</th>
                                <th>Importe_Compras Gravadas a Tasa_Cero</th>
                                <th>Subtotal</th>
                                <th>Desc._Bonif._Reb. Sujetas a IVA</th>
                                <th>Importe GIFT CARD</th>
                                <th>Importe Base DF</th>
                                <th>Débito Fiscal</th>
                                <th>Tipo Compra</th>
                                <th>Importe Sujeto_DF</th>
                                <th>Codigo Control</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($ventascbba as $venta)
                                <tr>
                                    <td>{{ $venta->id }}</td>
                                    <td>{{ $venta->especificacion }}</td>
                                    <td>{{ $venta->fechafacturaduidim }}</td>
                                    <td>{{ $venta->nrofactura }}</td>
                                    <td>{{ $venta->codigoautorizacion }}</td>
                                    <td>{{ $venta->nitci }}</td>
                                    <td>{{ $venta->complemento }}</td>
                                    <td>{{ $venta->razonsocial }}</td>
                                    <td>{{ $venta->total }}</td>
                                    <td>{{ $venta->ice }}</td>
                                    <td>{{ $venta->iehd }}</td>
                                    <td>{{ $venta->ipj }}</td>
                                    <td>{{ $venta->tasas }}</td>
                                    <td>{{ $venta->otronosujcredfiscaloiva }}</td>
                                    <td>{{ $venta->importeyexporteexterno }}</td>
                                    <td>{{ $venta->tasacero }}</td>
                                    <td>{{ $venta->subtotal }}</td>
                                    <td>{{ $venta->descuento }}</td>
                                    <td>{{ $venta->giftcard }}</td>
                                    <td>{{ $venta->cfdf }}</td>
                                    <td>{{ $venta->creditodebitofiscal }}</td>
                                    <td>{{ $venta->tipo }}</td>
                                    <td>{{ $compra->importesujetocfdf }}</td>
                                    <td>{{ $compra->codigocontrol }}</td>
                                    <td>{{ $compra->estado }}</td>
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