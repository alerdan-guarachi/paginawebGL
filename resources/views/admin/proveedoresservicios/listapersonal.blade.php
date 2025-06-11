@extends('adminlte::page')
    
@section('content_header')
{{-- <a class="btn float-right btn-crear btn-sm" data-toggle="modal" data-target="#crearProductoModal">
    CREAR PROVEEDOR
</a> --}}

<h1>LISTA DE PERSONAL INTERNO</h1>
@stop

@section('css')
<link rel="stylesheet" href="{{ asset('css/proveedoresserviciosgeneral.css') }}">
@stop

@section('js')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script type="text/javascript" src="https://jeremyfagis.github.io/dropify/dist/js/dropify.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://jeremyfagis.github.io/dropify/dist/css/dropify.min.css"> 
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
        {{-- BUSCADOR --}}
        <div class="card-header d-flex justify-content-end"> 
            <form class="form-inline">
                <div class="input-group">
                    <input name="buscarpor" class="form-control" type="search" style="width: 300px;" placeholder="NOMBRE DE PERSONAL">
                    <div class="input-group-append">
                        <button class="btn btn-buscar" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
        
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th style="width: 10%;">ID</th>
                        <th style="width: 30%;">Proveedor</th>
                        <th style="width: 15%;">CI</th>
                        <th style="width: 25%;">Cargo</th>
                        <th style="width: 15%;">Estado</th>
                        <th style="width: 5%;">Ver</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($personales as $personal)
                        @if ($personal->categoria === 'PROVEEDOR INTERNO')
                            <tr>
                                <td>{{$personal->id}}</td>
                                <td>{{$personal->razonsocial}}</td>
                                <td>{{$personal->ci}}</td>
                                <td>{{$personal->cargo}}</td>
                                <td>
                                    @if ($personal->estado == 'ACTIVO')
                                        <span class="badge badge-success">{{ $personal->estado }}</span>
                                    @else
                                        <span class="badge badge-danger">{{ $personal->estado }}</span>
                                    @endif
                                </td>
                                <td width="10px">
                                    @can('admin.proveedoresservicios.show')
                                        <a class="btn btn-sm btn-verperfil fas fa-eye" href="{{route('admin.proveedoresservicios.verpersonal', $personal->id)}}" title="VER PROVEEDOR"></a>
                                    @endcan
                                </td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>      
@stop
