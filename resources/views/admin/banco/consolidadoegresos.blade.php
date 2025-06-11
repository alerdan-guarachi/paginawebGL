@extends('adminlte::page')

@section('content_header')
<h1>CONSOLIDADOS DE ÓRDENES</h1>
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
    .bg-light-purple {
        background-color: #fceaff !important;
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
        {{-- <div class="table responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th class="text-center">Egresos_Prog.</th>
                        <th class="text-center">Egresos_Info_Final</th>
                        <th class="text-center">Total_Egreso</th>

                        <th>Ver</th>
                    </tr>
                </thead>
                <tbody>
                @php 
                    $totales = [];
                    foreach ($consolidacion as $prog) {
                        $fecha_asignada = $prog->fechaasignada;
                        if (!isset($totales[$fecha_asignada])) {
                            $totales[$fecha_asignada] = [
                                'total_programado'         => 0,
                                'total_recibido'           => 0,
                                'total_recibido_otras'     => 0,
                                'total_recibido_infofinal' => 0
                            ];
                        }
                        $totales[$fecha_asignada]['total_programado'] += $prog->total_programado;
                    }
                    foreach ($recibos as $pago) {
                        $pago_fecha = $pago->fecha;
                        $programacion_id = $pago->programacionid;
                        $fecha_asignada = null;
                        foreach ($consolidacion as $prog) {
                            if ($prog->id == $programacion_id) {
                                $fecha_asignada = $prog->fechaasignada;
                                break;
                            }
                        }
                        if (!$fecha_asignada) {
                            $fecha_asignada = $pago_fecha;
                        }
                        if (!isset($totales[$pago_fecha])) {
                            $totales[$pago_fecha] = [
                                'total_programado'         => 0,
                                'total_recibido'           => 0,
                                'total_recibido_otras'     => 0,
                                'total_recibido_infofinal' => 0
                            ];
                        }
                        if ($pago_fecha == $fecha_asignada) {
                            if (($pago->area ?? null) === 'MEDICA') {
                                $totales[$pago_fecha]['total_recibido'] += $pago->total_recibido;
                            }
                        } else {
                            $totales[$pago_fecha]['total_recibido_otras'] += $pago->total_recibido;
                        }
                    }
                    foreach ($totales as $fecha => &$data) {
                        $data['total_recibido_infofinal'] = DB::table('detallerecibos')
                            ->where('area', 'INFORME FINAL')
                            ->where('tipomovimiento', 'INGRESO')
                            ->whereDate('created_at', $fecha)
                            ->sum('montototal');
                    }
                    unset($data);
                    ksort($totales);
                    $fechas = array_reverse(array_keys($totales));
                @endphp
                
                @foreach($fechas as $index => $fecha)
                    @php
                        $data = $totales[$fecha];
                    @endphp
                        <tr class="{{ \Carbon\Carbon::parse($fecha)->isToday() ? 'bg-light-green' : '' }}">
                            <td>{{ $fecha }}</td>
                            <td class="text-center">
                                {{ number_format($data['total_recibido_otras'], 2, '.', '') }}
                            </td>
                            <td class="text-center">
                                {{ number_format($data['total_recibido_infofinal'], 2, '.', '') }}
                            </td>
                            <td class="text-center">
                                <strong>{{ number_format($data['total_recibido'] + $data['total_recibido_otras'] + $data['total_recibido_infofinal'], 2, '.', '') }}</strong>
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-veringresos" data-toggle="modal" data-target="#modalProgramacion_{{ $fecha }}_{{ $index }}" data-fecha="{{ $fecha }}">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <div class="modal fade" id="modalProgramacion_{{ $fecha }}_{{ $index }}" tabindex="-1" role="dialog" aria-labelledby="modalProgramacionLabel_{{ $fecha }}_{{ $index }}" aria-hidden="true">
                                    <div class="modal-dialog modal-xl" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h4 class="modal-title" style="font-weight: 900" id="modalProgramacionLabel_{{ $fecha }}_{{ $index }}">
                                                    EGRESOS DEL: {{ $fecha }}
                                                </h4>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="table-responsive">
                                                    <strong>EGRESOS</strong>
                                                    <table class="table table-striped">
                                                        <thead>
                                                            <tr>
                                                                <th>ID_Ing.</th>
                                                                <th>ID_Reg.</th>
                                                                <th>ID_Cli.</th>
                                                                <th>Cliente</th>
                                                                <th>Estudio/Especialidad</th>
                                                                <th>Proveedor</th>
                                                                <th>Fecha_Aten.</th>
                                                                <th>Fecha_Pago</th>
                                                                <th>Subtotal</th>
                                                                <th>Desc.</th>
                                                                <th>Monto_Total</th>
                                                                <th>ID_Rec.</th>
                                                                <th>Usu_Reg.</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($recibos->where('fechaatencion', $fecha)->merge($recibos->where('fecha', $fecha))->unique('id') as $recibo)
                                                            <tr>
                                                                <td>{{ $recibo->id }}</td>
                                                                <td>{{ $recibo->programacionid }}{{ $recibo->provinfofinalid }}</td>
                                                                <td>{{ $recibo->clienteid }}</td>
                                                                <td title="{{ $recibo->clientenombre }}" class="truncar">{{ $recibo->clientenombre }}</td>
                                                                <td title="{{ $recibo->detalle }}" class="truncar">{{ $recibo->detalle }}</td>
                                                                <td title="{{ $recibo->proveedoratencion }}" class="truncar">{{ $recibo->proveedoratencion }}</td>
                                                                <td>{{ $recibo->fechaatencion ?? 'IF' }}</td>
                                                                <td>{{ $recibo->fecha }}</td>
                                                                <td>{{ $recibo->subtotal }}</td>
                                                                <td>{{ $recibo->descuento }}</td>
                                                                <td>{{ $recibo->montototal }}</td>
                                                                <td>{{ $recibo->reciboid }}</td>
                                                                <td title="{{ $recibo->usuarioregistronombre }}" class="truncar">{{ $recibo->usuarioregistronombre }}</td>
                                                            </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div> --}}
        <div class="table responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th class="text-center">Egreso_Programado</th>
                        <th class="text-center">Egreso_Fecha_Actual</th>
                        <th class="text-center">Egreso_Otras_Fechas</th>
                        <th class="text-center">Total_Egreso</th>
                        <th class="text-center">Ver</th>
                    </tr>
                </thead>
                <tbody>
                @php 
                    $totales = [];
                    foreach ($consolidacion as $prog) {
                        $fecha_asignada = $prog->fechaasignada;
                        if (!isset($totales[$fecha_asignada])) {
                            $totales[$fecha_asignada] = [
                                'total_programado'         => 0,
                                'total_recibido'           => 0,
                                'total_recibido_otras'     => 0,
                                'total_recibido_infofinal' => 0
                            ];
                        }
                        $totales[$fecha_asignada]['total_programado'] += $prog->total_programado;
                    }
                    foreach ($recibos as $pago) {
                        $pago_fecha = $pago->fecha;
                        $cuentapagarid = $pago->cuentapagarid;
                        $fecha_asignada = null;
                        foreach ($consolidacion as $prog) {
                            if ($prog->id == $cuentapagarid) {
                                $fecha_asignada = $prog->fechaasignada;
                                break;
                            }
                        }
                        if (!$fecha_asignada) {
                            $fecha_asignada = $pago_fecha;
                        }
                        if (!isset($totales[$pago_fecha])) {
                            $totales[$pago_fecha] = [
                                'total_programado'         => 0,
                                'total_recibido'           => 0,
                                'total_recibido_otras'     => 0,
                                'total_recibido_infofinal' => 0
                            ];
                        }
                        if ($pago_fecha == $fecha_asignada) {
                            if (($pago->area ?? null) === 'CUENTAS POR PAGAR') {
                                $totales[$pago_fecha]['total_recibido'] += $pago->total_recibido;
                            }
                        } else {
                            $totales[$pago_fecha]['total_recibido_otras'] += $pago->total_recibido;
                        }
                    }
                    foreach ($totales as $fecha => &$data) {
                        $data['total_recibido_infofinal'] = DB::table('detallerecibos')
                            ->where('area', 'INFORME FINAL')
                            ->where('tipomovimiento', 'INGRESO')
                            ->whereDate('created_at', $fecha)
                            ->sum('montototal');
                    }
                    unset($data);
                    ksort($totales);
                    $fechas = array_reverse(array_keys($totales));
                @endphp
                
                @foreach($fechas as $index => $fecha)
                    @php
                        $data = $totales[$fecha];
                    @endphp
                        <tr class="{{ \Carbon\Carbon::parse($fecha)->isToday() ? 'bg-light-green' : '' }}">
                            <td>{{ $fecha }}</td>
                            <td class="text-center">
                                <strong>{{ number_format($data['total_programado'], 2, '.', '') }}</strong>
                            </td>
                            <td class="text-center">
                                {{ number_format($data['total_recibido'], 2, '.', '') }}
                            </td>
                            <td class="text-center">
                                {{ number_format($data['total_recibido_otras'], 2, '.', '') }}
                            </td>
                            <td class="text-center">
                                <strong>{{ number_format($data['total_recibido'] + $data['total_recibido_otras'], 2, '.', '') }}</strong>
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-veringresos" data-toggle="modal" data-target="#modalProgramacion_{{ $fecha }}_{{ $index }}" data-fecha="{{ $fecha }}">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <div class="modal fade" id="modalProgramacion_{{ $fecha }}_{{ $index }}" tabindex="-1" role="dialog" aria-labelledby="modalProgramacionLabel_{{ $fecha }}_{{ $index }}" aria-hidden="true">
                                    <div class="modal-dialog modal-xl" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h4 class="modal-title" style="font-weight: 900" id="modalProgramacionLabel_{{ $fecha }}_{{ $index }}">
                                                    EGRESOS DEL: {{ $fecha }}
                                                </h4>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="table-responsive">
                                                    <strong>CUENTAS POR PAGAR</strong>
                                                    <table class="table table-striped">
                                                        <thead>
                                                            <tr>
                                                                <th>ID_CxP.</th>
                                                                <th>ID_Prov.</th>
                                                                <th>Proveedor</th>
                                                                <th>Detalle</th>
                                                                <th>Fecha_Asig.</th>
                                                                <th>Fecha_Pago</th>
                                                                <th>Subtot.</th>
                                                                <th>Desc.</th>
                                                                <th>Total</th>
                                                                <th>Usu_Reg.</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($programaciones->where('fechaasignada', $fecha) as $prog)
                                                            <tr>
                                                                <td>{{ $prog->id }}</td>
                                                                <td>{{ $prog->proveedorid }}</td>
                                                                <td title="{{ $prog->proveedornombre }}" class="truncar">
                                                                    {{ $prog->proveedornombre }}
                                                                </td>
                                                                <td title="{{ $prog->cantidad }} {{ $prog->detalleproducto }}" class="truncar">{{ $prog->cantidad }} {{ $prog->detalleproducto }}</td>
                                                                <td>{{ $prog->fechaasignada }}</td>
                                                                <td>{{ $prog->fechapago  ?? '0' }}</td>
                                                                <td>{{ $prog->subtotal }}</td>
                                                                <td>{{ $prog->descuento }}</td>
                                                                <td>{{ $prog->montototal }}</td>
                                                                <td title="{{ $prog->usuarioregistronombre }}" class="truncar">{{ $prog->usuarioregistronombre }}</td>
                                                            </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div class="table-responsive">
                                                    <strong>EGRESOS</strong>
                                                    <table class="table table-striped">
                                                        <thead>
                                                            <tr>
                                                                <th>ID_Ing.</th>
                                                                <th>ID_Reg.</th>
                                                                <th>Proveedor</th>
                                                                <th>Detalle</th>
                                                                
                                                                <th>Fecha_Aten.</th>
                                                                <th>Fecha_Pago</th>
                                                                <th>Subto.</th>
                                                                <th>Desc.</th>
                                                                <th>Total</th>
                                                                <th>ID_Rec.</th>
                                                                <th>Usu_Reg.</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($recibos->where('fechaatencion', $fecha)->merge($recibos->where('fecha', $fecha))->unique('id') as $recibo)
                                                            <tr class="{{ 
                                                                ($recibo->fechaatencion === null || $recibo->fechaatencion == 'IF') ? 'bg-light-purple' : 
                                                                ($recibo->fechaatencion != $recibo->fecha ? 'bg-light-yellow' : '') 
                                                            }}">
                                                                <td>{{ $recibo->id }}</td>
                                                                <td>{{ $recibo->cuentapagarid }}</td>
                                                                <td title="{{ $recibo->proveedoratencion }}" class="truncar">{{ $recibo->proveedoratencion }}</td>
                                                                <td title="{{ $recibo->detalle }}" class="truncar">{{ $recibo->detalle }}</td>
                                                                
                                                                <td>{{ $recibo->fechaatencion ?? 'IF' }}</td>
                                                                <td>{{ $recibo->fecha }}</td>
                                                                <td>{{ $recibo->subtotal }}</td>
                                                                <td>{{ $recibo->descuento }}</td>
                                                                <td>{{ $recibo->montototal }}</td>
                                                                <td>{{ $recibo->reciboid }}</td>
                                                                <td title="{{ $recibo->usuarioregistronombre }}" class="truncar">{{ $recibo->usuarioregistronombre }}</td>
                                                            </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
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