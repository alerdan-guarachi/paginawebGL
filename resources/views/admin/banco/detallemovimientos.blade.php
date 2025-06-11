@extends('adminlte::page')

@section('content_header')
<h1>DETALLE MOVIMIENTOS</h1>
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
    .chart-responsive {
        position: relative;
        width: 100%;
        height: auto;
    }
    .chart-responsive canvas {
        width: 100% !important;
        height: auto !important;
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
                <a class="nav-link active" id="tab-4" data-toggle="tab" href="#tab-content-4" role="tab" aria-controls="tab-content-4" aria-selected="true">
                    POR TIPO DE TRANSACCIÓN
                </a>
            </li>     
            <li class="nav-item">
                <a class="nav-link" id="tab-5" data-toggle="tab" href="#tab-content-5" role="tab" aria-controls="tab-content-5" aria-selected="true">
                    POR PROVEEDOR
                </a>
            </li>       
        </ul>
    </div>

    <div class="card-body">
        <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade show active" id="tab-content-4" role="tabpanel" aria-labelledby="tab-4">
                <form method="GET" action="{{ route('admin.banco.detallemovimientos') }}">
                    <div class="row">
                        <div class="col-lg-4">
                            <select name="tipotransaccion" id="selectTransaccion" class="form-control">
                                <option value="">-- Todos --</option>
                                @foreach($listaTransacciones as $transaccion)
                                    <option value="{{ $transaccion }}"
                                        {{ (isset($filtroTransaccion) && $filtroTransaccion == $transaccion) ? 'selected' : '' }}>
                                        {{ $transaccion }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-6">
                            <button type="submit" class="btn btn-outline-secondary"><i class="fas fa-search"></i></button>
                        </div>
                    </div>
                </form>
                <div class="chart-responsive">
                    <canvas id="chartTransaccion"></canvas>  
                </div>   
            </div>

            <div class="tab-pane fade" id="tab-content-5" role="tabpanel" aria-labelledby="tab-5">
                <form method="GET" action="{{ route('admin.banco.detallemovimientos') }}">
                    <div class="row">
                        <div class="col-lg-4">
                            <select name="proveedoratencion" id="selectProveedor" class="form-control">
                                <option value="">-- Todos --</option>
                                @foreach($listaProveedores as $proveedor)
                                    <option value="{{ $proveedor }}"
                                        {{ (isset($filtroProveedor) && $filtroProveedor == $proveedor) ? 'selected' : '' }}>
                                        {{ $proveedor }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <button type="submit" class="btn btn-outline-secondary"><i class="fas fa-search"></i></button>
                        </div>
                    </div>
                </form>
                <div class="chart-responsive">
                    <canvas id="chartProveedor"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function(){
        // Al cargar la página, verifica si hay una pestaña activa almacenada
        var activeTab = localStorage.getItem('activeTab');
        if(activeTab){
            $('#myTabs a[href="' + activeTab + '"]').tab('show');
        }
        
        // Al cambiar de pestaña, almacena el identificador de la pestaña activa
        $('#myTabs a').on('shown.bs.tab', function(e) {
            localStorage.setItem('activeTab', $(e.target).attr('href'));
        });
    });
</script>

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
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Datos para la gráfica por Tipotransacción
    var transaccionLabels = {!! json_encode($transacciones->pluck('tipotransaccion')) !!};
    var totalIngresoTransaccion = {!! json_encode($transacciones->pluck('total_ingreso')) !!};
    var totalEgresoTransaccion = {!! json_encode($transacciones->pluck('total_egreso')) !!};

    var ctxTransaccion = document.getElementById('chartTransaccion').getContext('2d');
    var chartTransaccion = new Chart(ctxTransaccion, {
        type: 'bar',
        data: {
            labels: transaccionLabels,
            datasets: [
                {
                    label: 'Ingreso',
                    backgroundColor: '#94c93b',
                    data: totalIngresoTransaccion
                },
                {
                    label: 'Egreso',
                    backgroundColor: '#faa625',
                    data: totalEgresoTransaccion
                }
            ]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'Bs.' + value;
                        }
                    }
                }
            }
        }
    });

    // Datos para la gráfica por Proveedor
    var proveedorLabels = {!! json_encode($proveedores->pluck('proveedoratencion')) !!};
    var totalIngresoProveedor = {!! json_encode($proveedores->pluck('total_ingreso')) !!};
    var totalEgresoProveedor = {!! json_encode($proveedores->pluck('total_egreso')) !!};

    var ctxProveedor = document.getElementById('chartProveedor').getContext('2d');
    var chartProveedor = new Chart(ctxProveedor, {
        type: 'bar',
        data: {
            labels: proveedorLabels,
            datasets: [
                {
                    label: 'Ingreso',
                    backgroundColor: '#94c93b',
                    data: totalIngresoProveedor
                },
                {
                    label: 'Egreso',
                    backgroundColor: '#faa625',
                    data: totalEgresoProveedor
                }
            ]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'Bs.' + value;
                        }
                    }
                }
            }
        }
    });
});
</script>
@endsection