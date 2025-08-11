@extends('adminlte::page')

@section('content_header')
<h1>FORMULARIOS DE FACTURAS</h1>
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
    .btn-guardarformulario {
        background-color:  #ffffff;
        color: #000000;
        border-color: #000000;
        border-radius: 5px;
        padding: 2px 20px;
        }
    .btn-guardarformulario:hover {
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
        max-width: 150px;
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
<style>
    .alerta-pulso {
        animation: pulso 1s infinite;
    }

    @keyframes pulso {
        0% { transform: scale(1); opacity: 1; }
        50% { transform: scale(1.2); opacity: 0.7; }
        100% { transform: scale(1); opacity: 1; }
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
<div class="card">
    <div class="card-body">
        <div class="tab-content" id="myTabContent">
            @php
            use Carbon\Carbon;
            $mesAnterior = Carbon::now()->subMonth();
            $periodo = $mesAnterior->format('Y-m');
            $periodoLiteral = strtoupper($mesAnterior->formatLocalized('%B'));

            // Verificar si hay registros del mes anterior
            $registrosDelPeriodo = \App\Models\FormularioImpuestos::where('periodo', $periodo)->get();

            // Verificar si existen formularios incompletos (registrocontadornombre nulo)
            $ivaIncompleto = $registrosDelPeriodo->where('nombre', 'IMPUESTO AL VALOR AGREGADO')->whereNull('registrocontadornombre')->isNotEmpty();
            $itIncompleto = $registrosDelPeriodo->where('nombre', 'IMPUESTO A LAS TRANSACCIONES')->whereNull('registrocontadornombre')->isNotEmpty();

            $mostrarFormulario = $ivaIncompleto || $itIncompleto;
            $hayRegistros = $registrosDelPeriodo->isNotEmpty();
            $todoCompletado = $hayRegistros && !$mostrarFormulario;
            @endphp
            @if (!$hayRegistros)
                <div class="alert alert-warning">
                    NO HAY UN REGISTRO DE FORMULARIOS DE IMPUESTOS DEL {{ strtoupper(\Carbon\Carbon::parse($periodo)->translatedFormat('F Y')) }} DE GOOD LIFE
                </div>
            @elseif ($mostrarFormulario)
                {{-- FORMULARIO PARA LLENAR --}}
                <form method="POST" action="{{ route('admin.facturasegreso.guardarfacturacontaext') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="card mb-4" style="background-color:#f8f8f8">
                        <div class="card-header bg-secondary text-white">
                            <div class="d-flex justify-content-between align-items-center w-100" style="margin-bottom: -7px; margin-top: -7px;">
                                <strong>REGISTRO DE FORMULARIOS DE IMPUESTOS - PERIODO: {{ strtoupper(Carbon::parse($periodo)->translatedFormat('F Y')) }}</strong>
                                <button type="submit" class="btn btn-guardarformulario">GUARDAR FORMULARIOS</button>
                            </div>
                        </div>

                        <div class="card-body row" style="margin-bottom: -30px;">
                            {{-- IVA --}}
                            @if ($ivaIncompleto)
                                <div class="col-md-6 mb-4">
                                    <label class="fw-bold">IMPUESTO AL VALOR AGREGADO</label>
                                    <input type="hidden" name="nombre_iva" value="IMPUESTO AL VALOR AGREGADO">
                                    <input type="hidden" name="periodo_iva" value="{{ $periodo }}">

                                    <div class="row">
                                        <div class="mb-2 col-lg-4">
                                            <label>Monto IVA</label>
                                            <input type="text" class="form-control" name="monto_iva" required>
                                        </div>
                                        <div class="col-lg-8">
                                            <label>Archivo PDF IVA</label>
                                            <input type="file" name="archivo_iva" class="form-control" accept="application/pdf" required>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            {{-- IT --}}
                            @if ($itIncompleto)
                                <div class="col-md-6 mb-4">
                                    <label class="fw-bold">IMPUESTO A LAS TRANSACCIONES</label>
                                    <input type="hidden" name="nombre_it" value="IMPUESTO A LAS TRANSACCIONES">
                                    <input type="hidden" name="periodo_it" value="{{ $periodo }}">

                                    <div class="row">
                                        <div class="mb-2 col-lg-4">
                                            <label>Monto IT</label>
                                            <input type="text" class="form-control" name="monto_it" required>
                                        </div>
                                        <div class="col-lg-8">
                                            <label>Archivo PDF IT</label>
                                            <input type="file" name="archivo_it" class="form-control" accept="application/pdf" required>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </form>
            @elseif ($todoCompletado)
                <div class="alert alert-secondary">
                    <strong>¡Ya registrados!</strong> Ambos formularios para el periodo {{ strtoupper(\Carbon\Carbon::parse($periodo)->translatedFormat('F Y')) }} ya han sido registrados.
                </div>
            @endif


            {{-- LISTADO DE REGISTROS --}}
            <h5 class="mb-3" style="font-weight: 900">LISTA DE FORMULARIOS</h5>
            <table class="table table-bordered table-sm">
                <thead class="bg-light">
                    <tr>
                        <th>Tipo Formulario</th>
                        <th>Periodo</th>
                        <th>Usuario_Reg_GL</th>
                        <th>Fecha_Reg_GL</th>
                        <th style="text-align: right;">Monto_GL</th>
                        <th>Usuario_Reg_CE</th>
                        <th>Fecha_Reg_CE</th>
                        <th style="text-align: right;">Monto_CE</th>
                        <th>Form_CE</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($registrosImpuestos as $registro)
                        <tr>
                            <td>{{ $registro->nombre }}</td>
                            <td>{{ strtoupper(Carbon::parse($registro->periodo)->translatedFormat('F Y')) }}</td>
                            <td title="{{ $registro->usuarioregistronombre }}" class="truncar" style="background-color: #f9ffef">{{ $registro->usuarioregistronombre }}</td>
                            <td style="background-color: #f9ffef">{{ $registro->created_at->format('Y-m-d') }}</td>
                            <td style="text-align: right; background-color: #f9ffef">
                                @if ($registro->monto != $registro->montocontador && $registro->montocontador)
                                    <i class="fas fa-exclamation-circle text-warning alerta-pulso me-1" title="LOS MONTOS PARA ESTE MES NO COINCIDEN"></i>
                                @endif
                                {{ number_format($registro->monto, 2) }}
                            </td>
                            <td title="{{ $registro->registrocontadornombre }}" class="truncar" style="background-color: #fff8ef">
                                @if ($registro->registrocontadornombre)
                                    {{ $registro->registrocontadornombre }}
                                @else
                                    <span class="badge badge-danger">PENDIENTE</span>
                                @endif
                            </td>
                            <td style="background-color: #fff8ef">
                                @if ($registro->fecharegistrocontador)
                                    {{ $registro->fecharegistrocontador }}
                                @else
                                    <span class="badge badge-danger">PENDIENTE</span>
                                @endif
                            </td>
                            <td style="text-align: right; background-color: #fff8ef">
                                @if ($registro->montocontador)
                                    @if ($registro->monto != $registro->montocontador)
                                        <i class="fas fa-exclamation-circle text-warning alerta-pulso me-1" title="LOS MONTOS PARA ESTE MES NO COINCIDEN"></i>
                                    @endif
                                    {{ number_format($registro->montocontador, 2) }}
                                @else
                                    <span class="badge badge-danger">PENDIENTE</span>
                                @endif
                            </td>
                            <td style="background-color: #fff8ef">
                                @if ($registro->archivo)
                                    <a class="btn btn-verregistros btn-sm" data-toggle="modal" data-target="#modalArchivo{{ $registro->id }}"><i class="fas fa-eye"></i></a>

                                    <div class="modal fade" id="modalArchivo{{ $registro->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" style="font-weight: 900">FORMULARIO</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <iframe src="{{ asset('formulariosimpuestos/' . $registro->archivo) }}" width="100%" height="700px" style="border: none;"></iframe>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <span class="badge badge-danger">PENDIENTE</span>
                                @endif
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