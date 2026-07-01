@extends('adminlte::page')
    
@section('content_header')
<h1>COBRAR HOY ({{ $fechaActual }})</h1>
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
    <nav class="navbar navbar-expand-lg float-right">
        <div class="container-fluid">
            <div class="d-flex flex-wrap align-items-center ml-auto">
                <form action="{{ route('admin.caja.cuentascobrar.cobrarhoy') }}" method="get" class="form-inline">
                    <div class="form-group mr-2">
                        <input 
                            name="criterio" 
                            class="form-control" 
                            type="text" 
                            placeholder="ID, Nombre del Cliente" 
                            value="{{ old('criterio') }}" 
                            aria-label="Criterio de búsqueda">
                    </div>
                    <div class="form-group mr-2">
                        <input 
                            name="fecha" 
                            class="form-control" 
                            type="date" 
                            value="{{ old('fecha') }}" 
                            aria-label="Fecha">
                    </div>
                    <button id="btn-buscar" class="btn btn-outline-secondary my-2 my-sm-0" type="submit">BUSCAR</button>
                </form>
            </div>
        </div>
    </nav>
    
    {{-- PESTAÑAS SUPERIORES --}}
    <div class="card-header">
        <ul class="nav nav-tabs card-header-tabs" id="myTabs">
            <li class="nav-item">
                <a class="nav-link active" id="tab-1" data-toggle="tab" href="#tab-content-1" role="tab" aria-controls="tab-content-1" aria-selected="true">
                    PAGOS PENDIENTES INT.
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="tab-5" data-toggle="tab" href="#tab-content-5" role="tab" aria-controls="tab-content-5" aria-selected="true">
                    PAGOS PENDIENTES EXT.
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="tab-3" data-toggle="tab" href="#tab-content-3" role="tab" aria-controls="tab-content-3" aria-selected="true">
                    PAGOS PROCESADOS
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="tab-6" data-toggle="tab" href="#tab-content-6" role="tab" aria-controls="tab-content-6" aria-selected="true">
                    PAGOS PEND. INFO. FINAL
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="tab-7" data-toggle="tab" href="#tab-content-7" role="tab" aria-controls="tab-content-7" aria-selected="true">
                    PAGOS PROC. INFO. FINAL
                </a>
            </li>
        </ul>
    </div>

    <div class="card-body">
        <div class="tab-content" id="myTabContent">

            {{-- PAGOS PENDIENTES INTERNOS --}}
            <div class="tab-pane fade show active" id="tab-content-1" role="tabpanel" aria-labelledby="tab-1"> 
                <form method="POST" action="{{ route('confirmar-pagos') }}">
                    @csrf
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID_Prog</th>
                                <th>Tipo_Cliente</th>
                                <th>ID_Cli.</th>
                                <th>Nombre_Cliente</th>
                                <th>Proveedor</th>
                                <th>Estudio/Especialidad</th>
                                <th>Fecha_Batería</th>
                                <th>Fecha_Prog.</th>
                                <th>Hora_Prog.</th>
                                <th>Servicio</th>
                                <th>Precio</th>
                                <th hidden>Bateria</th>
                                <th>Crédito</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pagosproginterno as $programacion)
                                <tr>
                                    <td>{{ $programacion->id }}</td>
                                    <td>{{ $programacion->tipocliente }}</td>
                                    <td>{{ $programacion->clienteid }}</td>
                                    <td>{{ $programacion->clientenombre }}</td>
                                    <td>{{ $programacion->proveedornombre }}</td>
                                    <td>{{ $programacion->accionnombre }}</td>
                                    <td>{{ $programacion->fechabateria }}</td>
                                    <td>{{ $programacion->fechaasignada }}</td>
                                    <td>{{ $programacion->horadesde }} - {{ $programacion->horahasta }}</td>
                                    <td>{{ $programacion->servicio }}</td>
                                    <td>{{ $programacion->precio }}</td>
                                    <td hidden>{{ $programacion->bateriaid }}</td>
                                    <td>{{ $programacion->tiene_credito }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </form>
            </div>
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    const checkboxes = document.querySelectorAll('.programacion-checkbox');
                    const confirmButton = document.getElementById('confirmar-pago-btn');
                    function toggleButtonState() {
                        const anyChecked = Array.from(checkboxes).some(checkbox => checkbox.checked);
                        confirmButton.disabled = !anyChecked;
                    }
            
                    checkboxes.forEach(checkbox => {
                        checkbox.addEventListener('change', toggleButtonState);
                    });
                    toggleButtonState();
                });
            </script>
            
            {{-- PAGOS PENDIENTES EXTERNOS --}}
            <div class="tab-pane fade" id="tab-content-5" role="tabpanel" aria-labelledby="tab-5"> 
                <form method="POST" action="{{ route('confirmar-pagos') }}">
                    @csrf
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID_Prog</th>
                                <th>Tipo_Cliente</th>
                                <th>ID_Cli.</th>
                                <th>Nombre_Cliente</th>
                                <th>Proveedor</th>
                                <th>Estudio/Especialidad</th>
                                <th>Fecha_Bateria</th>
                                <th>Fecha_Prog.</th>
                                <th>Hora_Prog.</th>
                                <th>Servicio</th>
                                <th>Precio</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pagosprogexterno as $programacion)
                                <tr>
                                    <td>{{ $programacion->id }}</td>
                                    <td>{{ $programacion->tipocliente }}</td>
                                    <td>{{ $programacion->clienteid }}</td>
                                    <td>{{ $programacion->clientenombre }}</td>
                                    <td>{{ $programacion->proveedornombre }}</td>
                                    <td>{{ $programacion->accionnombre }}</td>
                                    <td>{{ $programacion->fechabateria }}</td>
                                    <td>{{ $programacion->fechaasignada }}</td>
                                    <td>{{ $programacion->horadesde }} - {{ $programacion->horahasta }}</td>
                                    <td>{{ $programacion->servicio }}</td>
                                    <td>{{ $programacion->precio }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </form>
            </div>
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    const checkboxes = document.querySelectorAll('.programacion-checkbox2');
                    const confirmButton = document.getElementById('confirmar-pago-btn2');
                    function toggleButtonState() {
                        const anyChecked = Array.from(checkboxes).some(checkbox => checkbox.checked);
                        confirmButton.disabled = !anyChecked;
                    }
            
                    checkboxes.forEach(checkbox => {
                        checkbox.addEventListener('change', toggleButtonState);
                    });
                    toggleButtonState();
                });
            </script>

            {{-- PAGOS PROCESADOS --}}
            <div class="tab-pane fade" id="tab-content-3" role="tabpanel" aria-labelledby="tab-3">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID_Prog</th>
                            <th>Tipo_Cliente</th>
                            <th>ID_Cli.</th>
                            <th>Nombre_Cliente</th>
                            <th>Proveedor</th>
                            <th>Estudio/Especialidad</th>
                            <th>Fecha_Bateria</th>
                            <th>Fecha_Prog.</th>
                            <th>Hora_Prog.</th>
                            <th>Servicio</th>
                            <th>Precio</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pagadosprogramaciones as $programacion)
                            <tr>
                                <td>{{ $programacion->id }}</td>
                                <td>{{ $programacion->tipocliente }}</td>
                                <td>{{ $programacion->clienteid }}</td>
                                <td>{{ $programacion->clientenombre }}</td>
                                <td>{{$programacion->proveedornombre}}</td>
                                <td>{{$programacion->accionnombre}}</td>
                                <td>{{$programacion->fechabateria}}</td>
                                <td>{{$programacion->fechaasignada}}</td>
                                <td>{{$programacion->horadesde }} - {{ $programacion->horahasta }}</td>
                                <td>{{$programacion->servicio}}</td>
                                <td>{{$programacion->precio}}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- PAGOS PENDIENTES INFORMES FINALES --}}
            <div class="tab-pane fade" id="tab-content-6" role="tabpanel" aria-labelledby="tab-6"> 
                <form method="POST" action="{{ route('confirmar-pagos-informefinal') }}">
                    @csrf
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID_Prog</th>
                                <th>Tipo_Cliente</th>
                                <th>ID_Cli.</th>
                                <th>Nombre_Cliente</th>
                                <th>Proveedor</th>
                                <th>Fecha_Bateria</th>
                                <th>Servicio</th>
                                <th>Precio</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pagosinformefinal as $infofinal)
                                <tr>
                                    <td>{{ $infofinal->id }}</td>
                                    <td>{{ $infofinal->tipocliente }}</td>
                                    <td>{{ $infofinal->clienteid }}</td>
                                    <td>{{ $infofinal->clientenombre }}</td>
                                    <td>{{ $infofinal->proveedorasignado }}</td>
                                    <td>{{ $infofinal->fechabateria }}</td>
                                    <td>{{ $infofinal->servicio }}</td>
                                    <td>{{ $infofinal->precio }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </form>
            </div>
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    const checkboxes = document.querySelectorAll('.programacion-checkbox3');
                    const confirmButton = document.getElementById('confirmar-pago-btn3');
                    function toggleButtonState() {
                        const anyChecked = Array.from(checkboxes).some(checkbox => checkbox.checked);
                        confirmButton.disabled = !anyChecked;
                    }
            
                    checkboxes.forEach(checkbox => {
                        checkbox.addEventListener('change', toggleButtonState);
                    });
                    toggleButtonState();
                });
            </script>

            {{-- PAGOS PROCESADOS INFORMES FINALES--}}
            <div class="tab-pane fade" id="tab-content-7" role="tabpanel" aria-labelledby="tab-7">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID_Prog</th>
                            <th>Tipo_Cliente</th>
                            <th>ID_Cli.</th>
                            <th>Nombre_Cliente</th>
                            <th>Proveedor</th>
                            <th>Fecha_Bateria</th>
                            <th>Servicio</th>
                            <th>Precio</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pagadosinformefinal as $infofinal)
                            <tr>
                                <td>{{$infofinal->id }}</td>
                                <td>{{$infofinal->tipocliente }}</td>
                                <td>{{$infofinal->clienteid}}</td>
                                <td>{{$infofinal->clientenombre}}</td>
                                <td>{{$infofinal->proveedorasignado}}</td>
                                <td>{{$infofinal->fechabateria}}</td>
                                <td>{{$infofinal->servicio}}</td>
                                <td>{{$infofinal->precio}}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@stop

@section('css')
<link rel="styleheet" href="/css/admin_custom.css">
<style>
    .table td {
        padding: 7px 10px;
    }
    .nav-tabs {
        display: flex;
        justify-content: space-between;
    }
    .nav-tabs .nav-item {
        flex: 1;
    }
    .nav-tabs .nav-link {
        display: block;
        text-align: center;
        width: 100%;
        font-weight: bold;
        font-size: 15px;
        color: #909090;
        background-color: #eaeaea;
    }
    .nav-tabs .nav-link.active {
        font-weight: bold;
        font-size: 15px;
        color: #0a0a0a;
        background-color: #ffffff;
    }
    .circle {
        display: inline-block;
        width: 30px;
        height: 20px;
        line-height: 20px;
        border-radius: 50%;
        text-align: center;
        font-size: 14px;
        font-weight: bold;
        margin-left: 8px;
    }
    .nav-link.active .circle {
        background-color: #94c93b;
        color: #fff;
    }
    .nav-link .circle {
        background-color: #faa625;
        color: #fff;
    }
    .custom-select-wrapper {
        position: relative;
        display: inline-block;
        width: 150px;
    }
    .custom-select-wrapper select {
        width: 100%;
        padding: 6px 26px 6px 10px;
        font-size: 14px;
        border: none;
        border-radius: 3px;
        background-color: #f8f9fa;
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
        cursor: pointer;
    }
    .custom-select-wrapper select:focus {
        outline: none;
    }
    .custom-select-icon {
        position: absolute;
        top: 50%;
        right: 10px;
        transform: translateY(-50%);
        pointer-events: none;
        color: #000000;
    }
    .custom-select-wrapper select {
        background-color:  #eff9df;
        color: #000000;
        border-color: #000000;
        border-radius: 5px;
        padding: 10px 20px;
    }
    .custom-select-wrapper select:hover {
        background-color: #f4e1c6;
        color: #000000;
    }
    .custom-select-wrapper select option {
        background-color: #ffffff;
    }
    h1, th {
        color:#000000; 
        font-family: "Segoe UI";
        font-weight: 700;
    }
</style>
@stop

@section('js')
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script type="text/javascript" src="https://jeremyfagis.github.io/dropify/dist/js/dropify.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://jeremyfagis.github.io/dropify/dist/css/dropify.min.css"> 
<script>
$('.dropify').dropify();
</script>
    @if (session('eliminar')=='ok')
    <script>
        Swal.fire(
      '¡Eliminado!',
      'El perfil se eliminó con éxito',
      'success')
    </script>
    @endif
<script>
    $('.formulario-eliminar').submit(function(e){
        e.preventDefault();

        Swal.fire({
        title: '¿Estás seguro?',
        text: "Este perfil se eliminará definitivamente",
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
    $(document).ready(function() {
        $('input[name="buscarpor"]').on('keyup', function() {
            var query = $(this).val();
            var botonBuscar = $('#btn-buscar');
            if (query.trim() === '') {
                botonBuscar.prop('disabled', true);
            } else {
                botonBuscar.prop('disabled', false);
            }
        });
    });
</script>
@endsection