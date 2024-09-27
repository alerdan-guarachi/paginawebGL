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
                                <div class="modal-header">
                                    <h5 class="modal-title" id="ventanaModalLabel"><strong>ACCIONES DEL CLIENTE</strong></h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <div class="row text-center">
                                        @can('admin.asociados.vercontactoclienteauditoria')
                                        <div class="col-4 mb-3 d-flex justify-content-center align-items-center">
                                            <a href="{{ route('admin.asociados.vercontactoclientebanco', $clientebanco) }}" class="btn btn-contactos btn-block" data-toggle="tooltip" data-placement="top" title="CONTACTOS">
                                                <i class="fas fa-users fa-2x"></i>
                                            </a>
                                        </div>
                                        @endcan
                                        @can('admin.asociados.editarclienteauditoria')
                                        <div class="col-4 mb-3 d-flex justify-content-center align-items-center">
                                            <a href="{{ route('admin.asociados.editarclientebanco', $clientebanco) }}" class="btn btn-editar btn-block" data-toggle="tooltip" data-placement="top" title="EDITAR CLIENTE">
                                                <i class="fas fa-edit fa-2x"></i>
                                            </a>
                                        </div>
                                        @endcan
                                        @can('admin.asociados.crearbateriaclienteauditoria')
                                        <div class="col-4 mb-3 d-flex justify-content-center align-items-center">
                                            <a href="{{ route('admin.asociados.crearbateriaclientebanco', $clientebanco) }}" class="btn btn-bateria btn-block" data-toggle="tooltip" data-placement="top" title="CREAR BATERÍA">
                                                <i class="fas fa-charging-station fa-2x"></i>
                                            </a>
                                        </div>
                                        @endcan
                                        @can('admin.asociados.aprobacioncotizacionclienteauditoria')
                                        <div class="col-4 mb-3 d-flex justify-content-center align-items-center">
                                            <a href="{{ route('admin.asociados.aprobacioncotizacionclientebanco', $clientebanco) }}" class="btn btn-cotizacion btn-block" data-toggle="tooltip" data-placement="top" title="COTIZACIÓN DE PROGRAMACIÓN">
                                                <i class="fas fa-donate fa-2x"></i>
                                            </a>
                                        </div>
                                        @endcan
                                        @can('admin.asociados.aprobarcotizacionprogramacionclienteauditoria')
                                        <div class="col-4 mb-3 d-flex justify-content-center align-items-center">
                                            <a href="{{ route('admin.asociados.aprobarcotizacionprogramacionclientebanco', $clientebanco) }}" class="btn btn-aprobacion btn-block" data-toggle="tooltip" data-placement="top" title="APROBAR COTIZACIÓN">
                                                <i class="fas fa-hand-holding-usd fa-2x"></i>
                                            </a>
                                        </div>
                                        @endcan
                                        @can('admin.asociados.crearprogramacionclienteauditoria')
                                        <div class="col-4 mb-3 d-flex justify-content-center align-items-center">
                                            <a href="{{ route('admin.asociados.crearprogramacionclientebanco', $clientebanco) }}" class="btn btn-programar btn-block" data-toggle="tooltip" data-placement="top" title="PROGRAMAR CLIENTE">
                                                <i class="fas fa-calendar-alt fa-2x"></i>
                                            </a>
                                        </div>
                                        @endcan
                                        @can('admin.asociados.estadoprogramacionclienteauditoria')
                                        <div class="col-4 mb-3 d-flex justify-content-center align-items-center">
                                            <a href="{{ route('admin.asociados.estadoprogramacionclientebanco', $clientebanco) }}" class="btn btn-estado btn-block" data-toggle="tooltip" data-placement="top" title="ESTADO DE PROGRAMACIÓN">
                                                <i class="fas fa-calendar-check fa-2x"></i>
                                            </a>
                                        </div>
                                        @endcan
                                        @can('admin.asociados.creardocumentacionclienteauditoria')
                                        <div class="col-4 mb-3 d-flex justify-content-center align-items-center">
                                            <a href="{{ route('admin.asociados.creardocumentacionclientebanco', $clientebanco) }}" class="btn btn-subirdocumento btn-block" data-toggle="tooltip" data-placement="top" title="SUBIR DOCUMENTOS">
                                                <i class="fas fa-file-upload fa-2x"></i>
                                            </a>
                                        </div>
                                        @endcan
                                        @can('admin.asociados.listadodocumentacionclienteauditoria')
                                        <div class="col-4 mb-3 d-flex justify-content-center align-items-center">
                                            <a href="{{ route('admin.asociados.listadodocumentacionclientebanco', $clientebanco) }}" class="btn btn-listadodocumentos btn-block" data-toggle="tooltip" data-placement="top" title="LISTA DE DOCUMENTOS">
                                                <i class="fas fa-list-alt fa-2x"></i>
                                            </a>
                                        </div>
                                        @endcan
                                        {{-- @can('admin.asociados.crearformularioclienteauditoria')
                                        <div class="col-4 mb-3 d-flex justify-content-center align-items-center">
                                            <a href="{{ route('admin.asociados.crearformularioclientebanco', $clientebanco) }}" class="btn btn-formulario btn-block" data-toggle="tooltip" data-placement="top" title="FORMULARIO MÉDICO">
                                                <i class="fas fa-file-signature fa-2x"></i>
                                            </a>
                                        </div>
                                        @endcan --}}
                                        {{-- @can('admin.asociados.generaretiquetaclienteauditoria')
                                        <div class="col-4 mb-3 d-flex justify-content-center align-items-center">
                                            <a href="{{ route('admin.asociados.generaretiquetaclientebanco', $clientebanco) }}" class="btn btn-etiqueta btn-block" data-toggle="tooltip" data-placement="top" title="GENERAR ETIQUETA">
                                                <i class="fas fa-tags fa-2x"></i>
                                            </a>
                                        </div>
                                        @endcan
                                        @can('admin.asociados.generarchecklistclienteauditoria')
                                        <div class="col-4 mb-3 d-flex justify-content-center align-items-center">
                                            <a href="{{ route('admin.asociados.generarchecklistclientebanco', $clientebanco) }}" class="btn btn-requisitos btn-block" data-toggle="tooltip" data-placement="top" title="REQUISITOS">
                                                <i class="fas fa-tasks fa-2x"></i>
                                            </a>
                                        </div>
                                        @endcan --}}
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
    .custom-dropdown {
        position: relative;
        display: inline-block;
    }
    
    .custom-select-wrapper {
        border: 1px solid black; /* Agrega un borde alrededor del texto */
        background-color: #fceacf; /* Cambia el color de fondo */
        padding: 1px; /* Ajusta el padding según sea necesario */
        text-align: center;
        border-radius: 5px;
        width: 140px; 
    }

    .custom-dropdown-content {
        display: none;
        position: absolute;
        background-color: #f9f9f9;
        width: 200px; /* Ajusta el ancho del menú desplegable según sea necesario */
        box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
        z-index: 1;
    }

    .custom-dropdown-content a {
        color: black;
        padding: 0px 5px; /* Ajusta el padding según sea necesario */
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
</style>
<style>
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
    .btn-editar {
        width: 100px;
        height: 90px;
        font-size: 24px;
        line-height: 80px;
        text-align: center;
        padding: 0;
        background-color: #ffffff;
        color: #faa625;
        border-color: #faa625;
        border-radius: 5px;
    }
    .btn-editar:hover {
        background-color: #faa625;
        color: #ffffff;
    }
    .btn-editar i {
        display: inline-block;
        vertical-align: middle;
    }
    .btn-bateria {
        width: 100px;
        height: 90px;
        font-size: 24px;
        line-height: 80px;
        text-align: center;
        padding: 0;
        background-color: #ffffff;
        color: #47b4e7;
        border-color: #47b4e7;
        border-radius: 5px;
    }
    .btn-bateria:hover {
        background-color: #47b4e7;
        color: #ffffff;
    }
    .btn-bateria i {
        display: inline-block;
        vertical-align: middle;
    }
    .btn-cotizacion {
        width: 100px;
        height: 90px;
        font-size: 24px;
        line-height: 80px;
        text-align: center;
        padding: 0;
        background-color: #ffffff;
        color: #c8ce22;
        border-color: #c8ce22;
        border-radius: 5px;
    }
    .btn-cotizacion:hover {
        background-color: #c8ce22;
        color: #ffffff;
    }
    .btn-cotizacion i {
        display: inline-block;
        vertical-align: middle;
    }
    .btn-aprobacion {
        width: 100px;
        height: 90px;
        font-size: 24px;
        line-height: 80px;
        text-align: center;
        padding: 0;
        background-color: #ffffff;
        color: #20a425;
        border-color: #20a425;
        border-radius: 5px;
    }
    .btn-aprobacion:hover {
        background-color: #20a425;
        color: #ffffff;
    }
    .btn-aprobacion i {
        display: inline-block;
        vertical-align: middle;
    }
    .btn-programar {
        width: 100px;
        height: 90px;
        font-size: 24px;
        line-height: 80px;
        text-align: center;
        padding: 0;
        background-color: #ffffff;
        color: #1d31e2;
        border-color: #1d31e2;
        border-radius: 5px;
    }
    .btn-programar:hover {
        background-color: #1d31e2;
        color: #ffffff;
    }
    .btn-programar i {
        display: inline-block;
        vertical-align: middle;
    }
    .btn-estado {
        width: 100px;
        height: 90px;
        font-size: 24px;
        line-height: 80px;
        text-align: center;
        padding: 0;
        background-color: #ffffff;
        color: #2f755a;
        border-color: #2f755a;
        border-radius: 5px;
    }
    .btn-estado:hover {
        background-color: #2f755a;
        color: #ffffff;
    }
    .btn-estado i {
        display: inline-block;
        vertical-align: middle;
    }
    .btn-subirdocumento {
        width: 100px;
        height: 90px;
        font-size: 24px;
        line-height: 80px;
        text-align: center;
        padding: 0;
        background-color: #ffffff;
        color: #c547e8;
        border-color: #c547e8;
        border-radius: 5px;
    }
    .btn-subirdocumento:hover {
        background-color: #c547e8;
        color: #ffffff;
    }
    .btn-subirdocumento i {
        display: inline-block;
        vertical-align: middle;
    }
    .btn-listadodocumentos {
        width: 100px;
        height: 90px;
        font-size: 24px;
        line-height: 80px;
        text-align: center;
        padding: 0;
        background-color: #ffffff;
        color: #531f84;
        border-color: #531f84;
        border-radius: 5px;
    }
    .btn-listadodocumentos:hover {
        background-color: #531f84;
        color: #ffffff;
    }
    .btn-listadodocumentos i {
        display: inline-block;
        vertical-align: middle;
    }
    .btn-formulario {
        width: 100px;
        height: 90px;
        font-size: 24px;
        line-height: 80px;
        text-align: center;
        padding: 0;
        background-color: #ffffff;
        color: #8f6542;
        border-color: #8f6542;
        border-radius: 5px;
    }
    .btn-formulario:hover {
        background-color: #8f6542;
        color: #ffffff;
    }
    .btn-formulario i {
        display: inline-block;
        vertical-align: middle;
    }
    .btn-etiqueta {
        width: 100px;
        height: 90px;
        font-size: 24px;
        line-height: 80px;
        text-align: center;
        padding: 0;
        background-color: #ffffff;
        color: #2e5362;
        border-color: #2e5362;
        border-radius: 5px;
    }
    .btn-etiqueta:hover {
        background-color: #2e5362;
        color: #ffffff;
    }
    .btn-etiqueta i {
        display: inline-block;
        vertical-align: middle;
    }
    .btn-requisitos {
        width: 100px;
        height: 90px;
        font-size: 24px;
        line-height: 80px;
        text-align: center;
        padding: 0;
        background-color: #ffffff;
        color: #556f34;
        border-color: #556f34;
        border-radius: 5px;
    }
    .btn-requisitos:hover {
        background-color: #556f34;
        color: #ffffff;
    }
    .btn-requisitos i {
        display: inline-block;
        vertical-align: middle;
    }
    .btn-contactos {
        width: 100px;
        height: 90px;
        font-size: 24px;
        line-height: 80px;
        text-align: center;
        padding: 0;
        background-color: #ffffff;
        color: #94c93b;
        border-color: #94c93b;
        border-radius: 5px;
    }
    .btn-contactos:hover {
        background-color: #94c93b;
        color: #ffffff;
    }
    .btn-contactos i {
        display: inline-block;
        vertical-align: middle;
    }
    
</style>
@stop
