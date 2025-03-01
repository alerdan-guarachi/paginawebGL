@extends('adminlte::page')
    
@section('content_header')
<a class="btn float-right btn-outline-secondary" href="{{route('admin.caja.cuentaspagar.registrarcpp')}}">CREAR CUENTA POR PAGAR</a>
<h1>CUENTAS POR PAGAR</h1>
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
    {{-- <nav class="navbar navbar-expand-lg float-right">
        <div class="container-fluid">
            <div class="d-flex flex-wrap align-items-center ml-auto">
                <form action="{{ route('buscarccporfecha') }}" method="get" class="form-inline">
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
    </nav> --}}
    
    {{-- PESTAÑAS SUPERIORES --}}
    <div class="card-header">
        <ul class="nav nav-tabs card-header-tabs" id="myTabs">
            <li class="nav-item">
                <a class="nav-link active" id="tab-1" data-toggle="tab" href="#tab-content-1" role="tab" aria-controls="tab-content-1" aria-selected="true">
                    PAGOS PENDIENTES
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="tab-5" data-toggle="tab" href="#tab-content-5" role="tab" aria-controls="tab-content-5" aria-selected="true">
                    PAGOS PROCESADOS
                </a>
            </li>
        </ul>
    </div>

    <div class="card-body">
        <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade show active" id="tab-content-1" role="tabpanel" aria-labelledby="tab-1"> 
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Tipo Proveedor</th>
                                <th>Proveedor</th>
                                <th>Detalle</th>
                                <th>Fecha asignada</th>
                                <th>Subtotal</th>
                                <th>Descuento</th>
                                <th>Monto Total</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($cuentaspagar as $cuentaspaga)
                                @if ($cuentaspaga->estado === 'PENDIENTE')
                                    <tr>
                                        <td>{{$cuentaspaga->id}}</td>
                                        <td>{{$cuentaspaga->tipoproveedor}}</td>
                                        <td>{{$cuentaspaga->proveedornombre}}</td>
                                        <td>{{$cuentaspaga->detalle}}</td>
                                        <td>{{$cuentaspaga->fechaasignada}}</td>
                                        <td>{{$cuentaspaga->subtotal}}</td>
                                        <td>{{$cuentaspaga->descuentosancion}}</td>
                                        <td>{{$cuentaspaga->montototal}}</td>
                                        <td>{{$cuentaspaga->estado}}</td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- PAGOS PENDIENTES EXTERNOS --}}
            <div class="tab-pane fade" id="tab-content-5" role="tabpanel" aria-labelledby="tab-5"> 
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Tipo Proveedor</th>
                                <th>Proveedor</th>
                                <th>Detalle</th>
                                <th>Fecha asignada</th>
                                <th>Subtotal</th>
                                <th>Descuento</th>
                                <th>Monto Total</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($cuentaspagar as $cuentaspaga)
                                @if ($cuentaspaga->estado === 'PROCESADO')
                                    <tr>
                                        <td>{{$cuentaspaga->id}}</td>
                                        <td>{{$cuentaspaga->tipoproveedor}}</td>
                                        <td>{{$cuentaspaga->proveedornombre}}</td>
                                        <td>{{$cuentaspaga->detalle}}</td>
                                        <td>{{$cuentaspaga->fechaasignada}}</td>
                                        <td>{{$cuentaspaga->subtotal}}</td>
                                        <td>{{$cuentaspaga->descuentosancion}}</td>
                                        <td>{{$cuentaspaga->montototal}}</td>
                                        <td>{{$cuentaspaga->estado}}</td>
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

@stop

@section('css')
<link rel="styleheet" href="/css/admin_custom.css">
<style>
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