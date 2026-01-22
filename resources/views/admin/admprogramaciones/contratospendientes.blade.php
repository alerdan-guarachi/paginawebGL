@extends('adminlte::page')
    
@section('content_header')
<h1>CONTRATOS PENDIENTES</h1>
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
        <div class="col-lg-4">
            <div class="mb-3">
                <input type="text" id="buscador" class="form-control" placeholder="BUSCAR POR ID CLIENTE, NOMBRE CLIENTE O TRÁMITE...">
            </div>
        </div>
        <script>
            document.getElementById("buscador").addEventListener("keyup", function() {
                let value = this.value.toLowerCase();
                let rows = document.querySelectorAll("#tablaRequisitos tbody tr");

                rows.forEach(function(row) {
                    let clienteId = row.cells[1]?.textContent.toLowerCase();
                    let clienteNombre = row.cells[2]?.textContent.toLowerCase();
                    let tramite = row.cells[3]?.textContent.toLowerCase();

                    if (clienteId.includes(value) || clienteNombre.includes(value) || tramite.includes(value)) {
                        row.style.display = "";
                    } else {
                        row.style.display = "none";
                    }
                });
            });
        </script>
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-sm" id="tablaRequisitos">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Cliente_ID</th>
                        <th>Cliente_Nombre</th>
                        <th>Trámite</th>
                        <th>Pendiente</th>
                        <th class="text-center">Ver</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requisitos as $req)
                        <tr>
                            <td>{{ $req->id }}</td>
                            <td>{{ $req->clienteitaid }}</td>
                            <td>{{ $req->clienteitanombre }}</td>
                            <td>{{ $req->servicio }}</td>
                            <td>
                                @if($req->contrato === 'PENDIENTE' && $req->poder === 'PENDIENTE')
                                    CONTRATO Y PODER
                                @elseif($req->contrato === 'PENDIENTE')
                                    CONTRATO
                                @elseif($req->poder === 'PENDIENTE')
                                    PODER
                                @endif
                            </td>
                            <td class="text-center">
                                @if($req->servicio === 'INVALIDEZ')
                                    <a href="{{ route('admin.asociados.subirdocrequisitos', $req->clienteitaid) }}" 
                                    class="btn btn-crear btn-sm"><i class="fas fa-file"></i></a>
                                @elseif($req->servicio === 'APELACIÓN')
                                    <a href="{{ route('admin.asociados.subirdocrequisitosapelacion', $req->clienteitaid) }}" 
                                    class="btn btn-crear btn-sm"><i class="fas fa-file"></i></a>
                                @elseif($req->servicio === 'SEGUNDA SOLICITUD')
                                    <a href="{{ route('admin.asociados.subirdocrequisitossegsolicitud', $req->clienteitaid) }}" 
                                    class="btn btn-crear btn-sm"><i class="fas fa-file"></i></a>
                                @elseif($req->servicio === 'TERCERA SOLICITUD')
                                    <a href="{{ route('admin.asociados.subirdocrequisitostercerasolicitud', $req->clienteitaid) }}" 
                                    class="btn btn-crear btn-sm"><i class="fas fa-file"></i></a>
                                @elseif($req->servicio === 'JUBILACIÓN')
                                    <a href="{{ route('admin.asociados.subirdocrequisitosjubilacion', $req->clienteitaid) }}" 
                                    class="btn btn-crear btn-sm"><i class="fas fa-file"></i></a>
                                @elseif($req->servicio === 'PENSIÓN POR MUERTE')
                                    <a href="{{ route('admin.asociados.subirdocrequisitospensionmuerte', $req->clienteitaid) }}" 
                                    class="btn btn-crear btn-sm"><i class="fas fa-file"></i></a>
                                @elseif($req->servicio === 'RETIRO DE APORTES TOTAL')
                                    <a href="{{ route('admin.asociados.subirdocrequisitosretiroaportestotal', $req->clienteitaid) }}" 
                                    class="btn btn-crear btn-sm"><i class="fas fa-file"></i></a>
                                @elseif($req->servicio === 'RETIRO DE APORTES PARCIAL')
                                    <a href="{{ route('admin.asociados.subirdocrequisitosretiroaportesparcial', $req->clienteitaid) }}" 
                                    class="btn btn-crear btn-sm"><i class="fas fa-file"></i></a>
                                @elseif($req->servicio === 'MASA HEREDITARIA')
                                    <a href="{{ route('admin.asociados.subirdocrequisitosmasahereditaria', $req->clienteitaid) }}" 
                                    class="btn btn-crear btn-sm"><i class="fas fa-file"></i></a>
                                @elseif($req->servicio === 'COMPENSACIÓN DE COTIZACIONES (SENASIR)')
                                    <a href="{{ route('admin.asociados.subirdocrequisitoscompensacioncotsenasir', $req->clienteitaid) }}" 
                                    class="btn btn-crear btn-sm"><i class="fas fa-file"></i></a>
                                @else

                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center">NO HAY CONTRATOS PENDIENTES</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@stop

@section('css')
<link rel="styleheet" href="/css/admin_custom.css">
<style>
    h1, th {
        color:#94c93b; 
        font-family: "Segoe UI";
        font-weight: 900;
    }
    .btn-crear {
        background-color:  #ffffff;
        color: #94c93b;
        border-color: #94c93b;
        border-radius: 5px;
        padding: 2px 5px;
    }
    .btn-crear:hover {
        background-color: #94c93b;
        color: #ffffff;
    }
</style>
@stop

@section('js')
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script type="text/javascript" src="https://jeremyfagis.github.io/dropify/dist/js/dropify.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://jeremyfagis.github.io/dropify/dist/css/dropify.min.css"> 
@endsection