@extends('adminlte::page')

@section('content_header')
<a class="btn btn-sm float-right btn-regresar" href="{{ route('admin.asociados.listadoclientebanco', $clientebanco->asociadoid) }}">REGRESAR</a>
<a class="btn btn-sm float-right btn-acciones" data-toggle="modal" data-target="#ventanaModal">ACCIONES DEL CLIENTE</a>

<h5>DATOS DE:</h5>
<h3>{{$clientebanco->nombrecompleto}}</h3>
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
            <div class="row">
                <div class="">
                    <!-- Modal -->
                    <div class="modal fade" id="ventanaModal" tabindex="-1" role="dialog" aria-labelledby="ventanaModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <strong style="text-align: center; font-size:20px; margin-top: 20px;">ACCIONES DEL CLIENTE</strong>
                                <style>
                                    .bordeetapa1 {
                                        border-top: 2px solid #26a1c0;
                                        border-bottom: 2px solid #26a1c0;
                                        border-right: 2px solid #26a1c0;
                                        border-left: 2px solid #26a1c0;
                                    }
                                    .bordeetapa2 {
                                        border-top: 2px solid #409c3e;
                                        border-bottom: 2px solid #409c3e;
                                        border-right: 2px solid #409c3e;
                                        border-left: 2px solid #409c3e;
                                    }
                                    .bordeetapa3 {
                                        border-top: 2px solid #a3bc35;
                                        border-bottom: 2px solid #a3bc35;
                                        border-right: 2px solid #a3bc35;
                                        border-left: 2px solid #a3bc35;
                                    }
                                    .otros {
                                        border-top: 2px solid #c47a35;
                                        border-bottom: 2px solid #c47a35;
                                        border-right: 2px solid #c47a35;
                                        border-left: 2px solid #c47a35;
                                    }
                                </style>
                                <div class="modal-body">
                                    <div style="background-color: #e9fbff;  border-radius: 40px;">
                                        <div style="text-align: center;padding: 1.5px;">
                                            <strong style="color: #26a1c0; font-size:20px;">ETAPA 1</strong>
                                        </div>
                                        <div class="row text-center">
                                            @can('admin.asociados.crearbateriaclientebanco')
                                                <div class="col-6 mb-3 d-flex justify-content-center align-items-center">
                                                    <a href="{{ route('admin.asociados.crearbateriaclientebanco', $clientebanco) }}" class="btn btn-etapa1 btn-icono btn-block" data-toggle="tooltip" data-placement="top" title="CREAR BATERÍA">
                                                        <i class="fas fa-charging-station"></i>
                                                        <strong>BATERIA</strong>
                                                    </a>
                                                </div>
                                            @endcan
                                            @can('admin.asociados.crearprogramacionclientebanco')
                                                @if ($tieneBateria)
                                                <div class="col-6 mb-3 d-flex justify-content-center align-items-center">
                                                    <a href="{{ route('admin.asociados.crearprogramacionclientebanco', $clientebanco) }}" class="btn btn-etapa1 btn-icono btn-block" data-toggle="tooltip" data-placement="top" title="PROGRAMAR CLIENTE">
                                                        <i class="fas fa-calendar-alt"></i>
                                                        <strong>PROG.</strong>
                                                    </a>
                                                </div>
                                                @else
                                                <div class="col-6 mb-3 d-flex justify-content-center align-items-center">
                                                    <a href="#" class="btn btn-etapa1 btn-icono btn-block disabled" data-toggle="tooltip" data-placement="top" title="PROGRAMAR CLIENTE" aria-disabled="true">
                                                        <i class="fas fa-calendar-alt"></i>
                                                        <strong>PROG.</strong>
                                                    </a>
                                                </div>
                                                @endif
                                            @endcan
                                        </div>
                                    </div>
                                    <div style="margin-top: 10px; background-color: #e9ffe9;  border-radius: 40px;">
                                        <div style="text-align: center; padding: 1.5px;">
                                            <strong style="color: #409c3e; font-size:20px;">ETAPA 2</strong>
                                        </div> 
                                        <div class="row text-center">
                                            {{-- @can('admin.asociados.aprobacioncotizacionclientebanco')
                                                @if ($tieneBateria)
                                                    <div class="col-6 mb-3 d-flex justify-content-center align-items-center">
                                                        <a href="{{ route('admin.asociados.aprobacioncotizacionclientebanco', $clientebanco) }}" class="btn btn-cotizacion btn-icono btn-block" data-toggle="tooltip" data-placement="top" title="COTIZACIÓN DE PROGRAMACIÓN">
                                                            <i class="fas fa-donate"></i>
                                                            <strong>COTIZACIÓN</strong>
                                                        </a>
                                                    </div>
                                                    @else
                                                    <div class="col-6 mb-3 d-flex justify-content-center align-items-center">
                                                        <a href="#" class="btn btn-cotizacion btn-icono btn-block disabled" data-toggle="tooltip" data-placement="top" title="COTIZACIÓN DE PROGRAMACIÓN" aria-disabled="true">
                                                            <i class="fas fa-donate"></i>
                                                            <strong>COTIZACIÓN</strong>
                                                        </a>
                                                    </div>
                                                @endif
                                            @endcan --}}
                                            <div class="col-4 mb-3 d-flex justify-content-center align-items-center">
                                                <a href="{{ route('admin.asociados.formularios.declaracionesmedico', $clientebanco) }}" class="btn btn-etapa2 btn-icono btn-block" data-toggle="tooltip" data-placement="top" title="DECLARACION MEDICA">
                                                    <i class="fas fa-lungs-virus"></i>
                                                    <strong>DEC. MED.</strong>
                                                </a>
                                            </div>
                                            <div class="col-4 mb-3 d-flex justify-content-center align-items-center">
                                                <a href="{{ route('admin.asociados.crearformularioclientebanco', $clientebanco) }}" class="btn btn-etapa2 btn-icono btn-block" data-toggle="tooltip" data-placement="top" title="FICHA MEDICA">
                                                    <i class="fas fa-file-signature"></i>
                                                    <strong>FICHA MED.</strong>
                                                </a>
                                            </div>
                                            @can('admin.asociados.creardocumentacionclientebanco')
                                            @if ($tieneProgramacionatentido)
                                                <div class="col-4 mb-3 d-flex justify-content-center align-items-center">
                                                    <a href="{{ route('admin.asociados.creardocumentacionclientebanco', $clientebanco) }}" class="btn btn-etapa2 btn-icono btn-block" data-toggle="tooltip" data-placement="top" title="SUBIR INFORMES">
                                                        <i class="fas fa-list-alt"></i>
                                                        <strong>INFORMES</strong>
                                                    </a>
                                                </div>
                                                @else
                                                <div class="col-4 mb-3 d-flex justify-content-center align-items-center">
                                                    <a href="#" class="btn btn-etapa2 btn-icono btn-block disabled" data-toggle="tooltip" data-placement="top" title="PROGRAMAR CLIENTE" aria-disabled="true">
                                                        <i class="fas fa-list-alt"></i>
                                                        <strong>INFORMES</strong>
                                                    </a>
                                                </div>
                                                @endif
                                            @endcan
                                        </div>
                                    </div>
                                    <div style="margin-top: 10px; background-color: #fff0e3;  border-radius: 40px;">
                                        <div style="text-align: center; padding: 1.5px;">
                                            <strong  style="color: #c47a35; font-size:20px;">OTROS</strong>
                                        </div>
                                        <div class="row text-center">
                                            @can('admin.asociados.editarclientebanco')
                                            <div class="col-12 mb-3 d-flex justify-content-center align-items-center">
                                                <a href="{{ route('admin.asociados.editarclientebanco', $clientebanco) }}" class="btn btn-editar btn-icono btn-block" data-toggle="tooltip" data-placement="top" title="EDITAR CLIENTE">
                                                    <i class="fas fa-edit"></i>
                                                    <strong>EDITAR</strong>
                                                </a>
                                            </div>
                                            @endcan
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-cerrar" data-dismiss="modal">Cerrar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-6">
                    <div class="profile-feed">
                        <div class="d-flex align-items-start profile-feed-item">
                            <div class="form-group col-md-12">
                                <div class="row">
                                    <div class="col-md-12">
                                        <table style="width: 100%;">
                                            <tbody>
                                                <tr>
                                                    <th>Sucursal</th>
                                                    <td>{{$clientebanco->sucursal}}</td>
                                                </tr>
                                                <tr>
                                                    <th>ID</th>
                                                    <td>{{$clientebanco->id}}</td>
                                                </tr>
                                                <tr>
                                                    <th>CI</th>
                                                    <td>{{$clientebanco->ci}}</td>
                                                </tr>
                                                
                                                <tr>
                                                    <th>Fecha de nacimiento</th>
                                                    <td>{{$clientebanco->fechanacimiento}}</td>
                                                </tr>
                                                <tr>
                                                    <th>Edad</th>
                                                    <td>{{$clientebanco->edad}}</td>
                                                </tr>
                                                
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="profile-feed">
                        <div class="d-flex align-items-start profile-feed-item">
                            <div class="form-group col-md-12">
                                <div class="row">
                                    <div class="col-md-12">
                                        <table style="width: 100%;">
                                            <tbody>
                                                <tr>
                                                    <th>Género</th>
                                                    <td>{{$clientebanco->genero}}</td>
                                                </tr>
                                                <tr>
                                                    <th>Celular</th>
                                                    <td>{{$clientebanco->celular}}</td>
                                                </tr>
                                                <tr>
                                                    <th>Lugar de nacimiento</th>
                                                    <td>{{$clientebanco->ciudad}}</td>
                                                </tr>
                                                <tr>
                                                    <th>Estado civil</th>
                                                    <td>{{$clientebanco->estadocivil}}</td>
                                                </tr>
                                                
                                                <tr>
                                                    <th>Ocupación / Profesión</th>
                                                    <td>{{$clientebanco->ocupacionprofesion}}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
@section('js')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script type="text/javascript" src="https://jeremyfagis.github.io/dropify/dist/js/dropify.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://jeremyfagis.github.io/dropify/dist/css/dropify.min.css"> 
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha512-..." crossorigin="anonymous" />

<script>
$('.dropify').dropify();
</script>
@stop

@section('css')
<style>
    /* Estilo para el botón deshabilitado */
    .btn.disabled, .btn.disabled:hover, .btn.disabled:focus, .btn.disabled:active {
        pointer-events: none; /* Evita la interacción con el botón */
        opacity: 0.6; /* Da un aspecto visual de deshabilitado */
        cursor: not-allowed; /* Cambia el cursor para mostrar que está deshabilitado */
    }
    .btn-etapa1 {
        width: 100px;
        height: 90px;
        font-size: 13px;
        flex-direction: column;
        text-align: center;
        padding: 10px;
        background-color: #ffffff;
        color: #26a1c0;
        border-color: #26a1c0;
        border-radius: 5px;
    }
    .btn-etapa1:hover {
        background-color: #26a1c0;
        color: #ffffff;
    }
    .btn-etapa1 i {
        display: inline-block;
        vertical-align: middle;
    }
    .btn-etapa2 {
        width: 100px;
        height: 90px;
        font-size: 13px;
        flex-direction: column;
        text-align: center;
        padding: 10px;
        background-color: #ffffff;
        color: #148734;
        border-color: #148734;
        border-radius: 5px;
    }
    .btn-etapa2:hover {
        background-color: #148734;
        color: #ffffff;
    }
    .btn-etapa2 i {
        display: inline-block;
        vertical-align: middle;
    }
    .btn-editar {
        width: 100px;
        height: 90px;
        font-size: 13px;
        flex-direction: column;
        text-align: center;
        padding: 10px;
        background-color: #ffffff;
        color: #e05f28;
        border-color: #e05f28;
        border-radius: 5px;
    }
    .btn-editar:hover {
        background-color: #e05f28;
        color: #ffffff;
    }
    .btn-editar i {
        display: inline-block;
        vertical-align: middle;
    }
    .btn-icono i {
        font-size: 4em;
    }
</style>
<style>
    .btn-no {
    color: #fd1d1d;
    border-color: #fd1d1d;
    }
    .btn-no:hover {
    background-color: #fd1d1d;
    color: #ffffff;
    }
    .btn-si {
    color: #94c93b;
    border-color: #94c93b;
    }
    .btn-si:hover {
    background-color: #94c93b;
    color: #ffffff;
    }
    .custom-dropdown {
        position: relative;
        display: inline-block;
    }
    .custom-select-wrapper {
        border: 1px solid black;
        background-color: #fceacf;
        padding: 1px;
        text-align: center;
        border-radius: 5px;
        width: 140px; 
    }
    .custom-dropdown-content {
        display: none;
        position: absolute;
        background-color: #f9f9f9;
        width: 200px;
        box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
        z-index: 1;
    }
    .custom-dropdown-content a {
        color: black;
        padding: 0px 5px;
        text-decoration: none;
        display: block;
    }
    .custom-dropdown-content a:hover {
        background-color: #eefed3;
    }
    .custom-dropdown:hover .custom-dropdown-content {
        display: block;
    }
    th, td {
        border-bottom: 1px solid #94c93b;
    }
    h1, th {
        color:#94c93b; 
        font-family: "Segoe UI";
        font-weight: 700;
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
    #vista-previa {
        display: block;
        height: auto;
        border: 1px solid #ccc;
        padding: 5px;
        box-shadow: 0px 0px 5px 0px rgba(0, 0, 0, 0.2);
        }
    table {
        border-collapse: separate;
        border-spacing: 2px;
    }
    th, td {
        padding: 15px;
    }
    td{
        text-align: right;
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
    .btn-acciones {
        background-color: #ffffff;
        color: #94c93b;
        border-color: #94c93b;
        border-radius: 5px;
        padding: 10px 10px;
        margin-left: 10px;
        margin-right: 10px;
    }
    .btn-acciones:hover {
        background-color: #94c93b;
        color: #ffffff;
    }
    .btn-auditoriamedica {
        background-color: #ffffff;
        color: #e0752e;
        border-color: #e0752e;
        border-radius: 5px;
        padding: 10px 10px;

    }
    .btn-auditoriamedica:hover {
        background-color: #e0752e;
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
</style>
@stop
