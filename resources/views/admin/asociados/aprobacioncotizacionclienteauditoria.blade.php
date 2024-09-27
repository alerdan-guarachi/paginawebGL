@extends('adminlte::page')

@section('content_header')
<a class="btn btn-sm float-right btn-regresar" href="{{ route('admin.asociados.verclienteauditoria', $clienteauditoria) }}">REGRESAR</a>
<a class="btn btn-sm float-right btn-crear" href="{{ route('admin.asociados.generarpdfcotizacionclienteauditoria', ['clienteauditoria' => $clienteauditoria, 'buscarporfecha' => $fechaSeleccionada, 'buscarporarea' => $areaSeleccionada, 'total' => $total]) }}">GENERAR PDF</a>
<h5>COTIZACIÓN DE PROGRAMACIÓN DE:</h5>
<h3>{{$clienteauditoria->nombrecompleto}}</h3>
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
@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div> 
@endif
<div class="card"> 
    <div class="card-body">
        <nav class="navbar navbar-expand-lg float-right">
            <div class="container-fluid">
                <div class="d-flex flex-wrap align-items-center">
                    <form action="{{ route('buscarbateriaclienteauditoria', $clienteauditoria) }}" method="get" class="form-inline">
                        <div class="flex-grow-1">
                            <select name="buscarporfecha" class="form-control mr-sm-2">
                                <option value="" disabled selected>Fecha de Bateria</option>
                                @foreach($fechas as $fecha)
                                    <option value="{{ $fecha }}">{{ $fecha }}</option>
                                @endforeach
                            </select>
                            <select name="buscarporarea" class="form-control mr-sm-2">
                                <option value="" disabled selected>Área</option>
                                @foreach($areas as $area)
                                    <option value="{{ $area }}">{{ $area }}</option>
                                @endforeach
                            </select>
                        </div>
                        <input type="hidden" name="total" id="total" value="{{ $total }}">
                        <button id="btn-buscar" class="btn btn-buscar my-2 my-sm-0" type="submit">Buscar</button>
                    </form>
                </div>
            </div>
        </nav>        

        @if($bateriasubclientes->isEmpty())
        @else
            <div class="table-responsive">
                <table class="table table-striped" id="tablaPrecios">
                    <thead>
                        <tr>
                            <th>Fecha de Bateria</th>
                            <th>Tipo de área</th>
                            <th>Área</th>
                            <th>Acción</th>
                            <th>Precio</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($bateriasubclientes as $bateriasubcliente)
                        <tr>
                            <td>{{$bateriasubcliente->fechabateria}}</td>
                            <td>{{$bateriasubcliente->tipoarea}}</td>
                            <td>{{$bateriasubcliente->areanombre}}</td>
                            <td>{{$bateriasubcliente->accionnombre}}</td>
                            <td class="precio" >{{$bateriasubcliente->precio}}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

@stop
@section('js')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script type="text/javascript" src="https://jeremyfagis.github.io/dropify/dist/js/dropify.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://jeremyfagis.github.io/dropify/dist/css/dropify.min.css"> 
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
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
    var precios = document.querySelectorAll('.precio');
    var total = 0;
    precios.forEach(function(precio) {
        total += parseFloat(precio.textContent);
    });
    var totalFormateado = total.toLocaleString('es-ES', { minimumFractionDigits: 2 });
    var filaTotal = '<tr style="background-color: #fcecd4;"><td colspan="4">TOTAL</td><td>' + totalFormateado + '</td></tr>';
    document.querySelector('#tablaPrecios tbody').insertAdjacentHTML('beforeend', filaTotal);
</script>
@endsection

@section('css')
<link rel="styleheet" href="/css/admin_custom.css">
<style>
    .btn-buscar { 
        background-color:  #ffffff;
        color: #faa625;
        border-color: #faa625;
        border-radius: 5px;
    }
    .btn-buscar:hover {
        background-color: #faa625;
        color: #ffffff;
    } 
    .total-row {
        background-color: #94c93b;
    }
    .hidden-field {
        display: none;
    }
    th {color:#94c93b; 
        font-family: "Segoe UI";
        font-weight: 900;
        }
    h5 {
        color:#94c93b; 
        font-family: "Segoe UI";
        font-weight: 500;
        margin-bottom: 0%;
        }
    h3 {
        color:#94c93b; 
        font-family: "Segoe UI";
        font-weight: 1000;
        }
    .btn-crear {
        background-color:  #ffffff;
        color: #94c93b;
        border-color: #94c93b;
        border-radius: 5px;
        padding: 10px 20px;
        margin-left: 10px;
        margin-right: 10px;
        }
    .btn-crear:hover {
        background-color: #94c93b;
        color: #ffffff;
        }
    .mensaje-error {
        color: #e1172b;
        font-family: "Times New Roman";
        padding: 10px;
        margin-top: 5px;
        border-radius: 5px;
        font-size: 12.5px;
        font-weight: bold;
        display: inline-block;
        margin-left: -10px;
    }
    .custom-button {
        background-color: #ffffff;
        color: #faa625;
        border-color: #faa625;
        border-radius: 5px;
        padding: 5px 50px;

    }
    .custom-button:hover {
        background-color: #faa625;
        color: #ffffff;
    }
    .custom2-button {
        background-color: #ffffff;
        color: #faa625;
        border-color: #faa625;
        border-radius: 5px;
        padding: 5px 20px;
        margin-top: 33px;
    }
    .custom2-button:hover {
        background-color: #faa625;
        color: #ffffff;
    }
    .btn-cerrar {
        background-color: #ffffff;
        color: #94c93b;
        border-color: #94c93b;
        border-radius: 5px;
        padding: 5px 10px;
    }
    .btn-cerrar:hover {
        background-color: #94c93b;
        color: #ffffff;
    }
    .btn-regresar {
        background-color: #ffffff;
        color: #2926e2;
        border-color: #2926e2;
        border-radius: 5px;
        padding: 10px 10px;
    }
    .btn-regresar:hover {
        background-color: #2926e2;
        color: #ffffff;
    }
</style>
@stop