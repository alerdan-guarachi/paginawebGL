@extends('adminlte::page')

@section('content_header')
<a class="btn btn-regresar btn-sm float-right" style="margin-left: 10px;" href="{{route('admin.asociados.index')}}">REGRESAR</a>
<a class="btn btn-crear btn-sm float-right" href="{{route('admin.cartaspolizas.create')}}">CREAR CARTA</a>
<h1>CARTAS DE SOLICITUD DE POLIZAS</h1>
@stop


@section('css')
<link rel="stylesheet" href="{{ asset('css/estilogl.css') }}">
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
        <nav class="navbar float-right">
            <form class="form-inline">
                <input name="buscarpor" class="form-control mr-sm-2" type="search" placeholder="Nombre del cliente" aria-label="Search">
                <button class="btn btn-buscar my-2 my-sm-0" type="submit">Buscar</button>
            </form>
        </nav>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Cliente 1</th>
                        <th>Cliente 2</th>
                        <th>Carta</th>
                        <th>Fecha Carta</th>
                        <th>Ciudad Carta</th>
                        <th>Banco Carta</th>
                        <th>Fecha Registro</th>
                        <th>Ver</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($cartaspolizas as $cartaspoliza)
                        <tr>
                            <td>{{$cartaspoliza->id}}</td>
                            <td>{{$cartaspoliza->nombreclienteuno}} - {{$cartaspoliza->ciclienteuno}}</td>
                            <td>{{$cartaspoliza->nombreclientedos}} - {{$cartaspoliza->ciclientedos}}</td>
                            <td>{{$cartaspoliza->nombrecarta}}</td>
                            <td>{{$cartaspoliza->fecha}}</td>
                            <td>{{$cartaspoliza->ciudad}}</td>
                            <td>{{$cartaspoliza->banco}}</td>
                            <td>{{$cartaspoliza->created_at}}</td>
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
