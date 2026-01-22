@extends('adminlte::page')

@section('content_header')
<a class="btn btn-regresar btn-sm float-right" style="margin-left: 10px;" href="{{route('admin.asociados.index')}}">REGRESAR</a>
<a class="btn btn-crear btn-sm float-right" href="{{route('admin.cartaspolizas.create')}}">CREAR CARTA</a>
<h1>CARTAS DE SOLICITUD DE POLIZAS</h1>
@stop


@section('css')
<link rel="stylesheet" href="{{ asset('css/estilogl.css') }}">
<style>
    .btn-regresar {
        background-color: #ffffff;
        color: #2926e2;
        border-color: #2926e2;
        border-radius: 5px;
        padding: 5px 10px;
    }
    .btn-regresar:hover {
        background-color: #2926e2;
        color: #ffffff;
    }
    .btn-crear {
        background-color:  #ffffff;
        color: #94c93b;
        border-color: #94c93b;
        border-radius: 5px;
        padding: 5px 10px;
    }
    .btn-crear:hover {
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


<div class="card">
    <div class="card-body">
        <nav class="navbar float-right">
            <form class="form-inline">
                <input name="buscarpor" class="form-control mr-sm-2" type="search" placeholder="Nombre del cliente" aria-label="Search">
                <button class="btn btn-buscar my-2 my-sm-0" type="submit">Buscar</button>
            </form>
        </nav>
        <div class="table-responsive">
            <table class="table table-striped table-bordered table-sm">
                <thead class="table-secondary">
                    <tr>
                        <th>ID</th>
                        <th>Nombre_CI_Cliente</th>
                        <th>Nombre_CI_Esp/Cony</th>
                        <th>Tipo_Carta</th>
                        <th>Banco_Carta</th>
                        <th>Fecha_Carta</th>
                        <th>Ciudad_Carta</th>
                        <th>Fecha_Reg.</th>
                        <th>Ver</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($cartaspolizas as $cartaspoliza)
                        <tr>
                            <td>{{$cartaspoliza->id}}</td>
                            <td>{{$cartaspoliza->nombreclienteuno}} - {{$cartaspoliza->ciclienteuno}}</td>
                            <td>
                                {{ $cartaspoliza->nombreclientedos }} 
                                @if(!empty($cartaspoliza->ciclientedos))
                                    - {{ $cartaspoliza->ciclientedos }}
                                @else
                                    VACIO
                                @endif
                            </td>

                            <td>{{$cartaspoliza->nombrecarta}}</td>
                            <td>{{$cartaspoliza->banco}}</td>
                            <td>{{ \Carbon\Carbon::parse($cartaspoliza->fecha)->format('d-m-Y') }}</td>
                            <td>{{$cartaspoliza->ciudad}}</td>
                            <td>{{ $cartaspoliza->created_at->format('d-m-Y') }}</td>
                            <td>
                                <a href="{{ asset('/cartaspolizas/' . $cartaspoliza->documentocarta) }}" class="btn btn-sm btn-buscar" target="_blank" title="VER CARTA">
                                    <i class="fas fa-file"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@stop
