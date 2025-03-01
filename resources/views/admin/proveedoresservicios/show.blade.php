@extends('adminlte::page')

@section('content_header')
<a class="btn btn-sm float-right btn-regresar" href="{{ route('admin.proveedoresservicios.index') }}">REGRESAR</a>

@can('admin.proveedoresservicios.edit')
<a class="btn btn-sm btn-editarregistro float-right" style="margin-right: 10px;" href="{{route('admin.proveedoresservicios.edit', $proveedoresservicios)}}">EDITAR PERFIL</a>
@endcan

<h1>INFORMACIÓN DEL PROVEEDOR</h1>
@stop

@section('css')
<link rel="stylesheet" href="{{ asset('css/estilogl.css') }}">
<style>
    .btn-editarregistro {
    background-color:  #ffffff;
    color: #faa625;
    border-color: #faa625;
    border-radius: 5px;
    padding: 10px 20px;
    }
    .btn-editarregistro:hover {
    background-color: #faa625;
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
        }, 5000);
    </script>
@endif
<div class="card col-lg-12">
    <div class="card-body">
        <div class="row">
            <div class="col-lg-4 pl-lg-4">
                <div class="border-bottom text-center pb-4">
                    <div style="display: flex; flex-direction: column;">
                        <div class="image-container" style="width: 100%; height: 320px; overflow: auto;">
                            <img src="{{asset('proveedoresserviciosfotos/'.$proveedoresservicios->image)}}" alt="Foto de perfil" class="col-md-12 mb-12" id="vista-previa" style="width: 100%; height: auto; object-fit: cover; object-position: center; " />
                        </div><br>
                    </div><br>
                    <p class="clearfix text-center" style="font-weight: 900; font-size: 22px;">
                        <span class="text-muted">
                            ID: {{$proveedoresservicios->id}}
                        </span>
                    </p>
                    <p class="clearfix text-center" style="font-size: 18px; margin-top:-10px;">
                        <span class="text-muted">
                            {{$proveedoresservicios->nombrecompleto}}
                        </span>
                    </p>
                    {{-- <p class="clearfix text-center" style="font-weight: 900; margin-top:-10px; font-size: 18px;">
                        <span class="text-muted">
                            {{$proveedoresservicios->cargo}}
                        </span>
                    </p> --}}
                </div>
            </div>
            <div class="col-lg-4 pl-lg-4">
                <div class="profile-feed">
                    <div class="d-flex align-items-start profile-feed-item">
                        <div class="form-group col-md-9">
                            <strong><i class="fas fa-map-marker-alt mr-1"></i> Sucursal</strong>
                            <p class="text-muted">
                                {{$proveedoresservicios->sucursal}}
                            </p>
                            <hr>
                            <strong><i class="fas fa-address-card mr-1"></i> CI</strong>
                            <p class="text-muted">
                                {{$proveedoresservicios->ci}} {{$proveedoresservicios->ciexp}}
                            </p>
                            <hr>
                            @if($proveedoresservicios->nit)
                                <strong><i class="fas fa-folder mr-1"></i> NIT</strong>
                                <p class="text-muted">{{ $proveedoresservicios->nit }}</p>
                            @else
                                <strong><i class="fas fa-folder mr-1"></i> NIT</strong>
                                <p class="text-muted">NO TIENE</p>
                            @endif

                            <hr>
                            <strong><i class="fas fa-at mr-1"></i> Email</strong>
                            <p class="text-muted">
                                {{$proveedoresservicios->email}}
                            </p>
                            <hr>
                            <strong><i class="fas fa-business-time mr-1"></i> Tipo de proveedor</strong>
                            <p class="text-muted">
                                {{$proveedoresservicios->tipoproveedor}}
                            </p>
                            <hr>
                            <strong><i class="fas fa-map-marked-alt mr-1"></i> Dirección</strong>
                            <p class="text-muted">
                                {{$proveedoresservicios->direccion}}
                            </p>
                            <hr>
                            <strong><i class="fas fa-phone-square-alt mr-1"></i> Teléfono</strong>
                            <p class="text-muted">
                                {{$proveedoresservicios->celular}}
                            </p>
                            <hr>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 pl-lg-4">
                <div class="profile-feed">
                    <div class="d-flex align-items-start profile-feed-item">
                        <div class="form-group col-md-9">
                            <strong><i class="fas fa-shield-alt mr-1"></i> Estado</strong>
                            <p class="text-muted">
                                {{$proveedoresservicios->estado}}
                            </p>
                            <hr>
                            <strong><i class="fas fa-hotel mr-1"></i> Nombre de Banco</strong>
                            <p class="text-muted">
                                {{$proveedoresservicios->banco}}
                            </p>
                            <hr>
                            <strong><i class="fas fa-file mr-1"></i> Emisión</strong>
                            <p class="text-muted">
                                {{$proveedoresservicios->emision}}
                            </p>
                            <hr>
                            <strong><i class="far fa-credit-card mr-1"></i> Nro. de Cuenta</strong>
                            <p class="text-muted">
                                {{$proveedoresservicios->numcuenta}}
                            </p>
                            <hr>
                            @if($proveedoresservicios->fechasalida)
                                <strong><i class="fas fa-calendar-check mr-1"></i> Fecha de ingreso y salida</strong>
                                <p class="text-muted">{{$proveedoresservicios->fechaingreso}} - {{$proveedoresservicios->fechasalida}}</p>
                            @else
                                <strong><i class="fas fa-calendar-check mr-1"></i> Fecha de ingreso y salida</strong>
                                <p class="text-muted">{{$proveedoresservicios->fechaingreso}}</p>
                            @endif
                            <hr>
                            <strong><i class="fas fa-user-shield mr-1"></i> Nombre de contacto</strong>
                            <p class="text-muted">
                                {{$proveedoresservicios->contacto}}
                            </p>
                            <hr>
                            <strong><i class="fas fa-phone-volume mr-1"></i> Celular de contacto</strong>
                            <p class="text-muted">
                                {{$proveedoresservicios->celcontacto}}
                            </p>
                            <hr>
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
<script>
$('.dropify').dropify();
</script>
@stop