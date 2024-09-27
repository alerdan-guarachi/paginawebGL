@extends('adminlte::page')

@section('content_header')
<a class="btn btn-sm float-right btn-regresar" href="{{ route('admin.personal.index') }}">REGRESAR</a>
@if ($personal->usuarioid == Auth::id())
{{-- @can('admin.personal.edit') --}}
<a class="btn btn-sm btn-editar float-right" href="{{route('admin.personal.edit', $personal->id)}}">EDITAR PERFIL</a>
{{-- @endcan --}}
@endif

<h4>INFORMACIÓN DEL PERFIL</h4>
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
            <div class="col-lg-4">
                
                <div class="border-bottom text-center pb-4">
                    <div style="display: flex; flex-direction: column;">
                        <div class="image-container" style="width: 100%; height: 320px; overflow: auto;">
                            <img src="{{asset('personalfotos/'.$personal->image)}}" alt="Foto de perfil" class="col-md-12 mb-12" id="vista-previa" style="width: 100%; height: auto; object-fit: cover; object-position: center; " />
                        </div><br>
                        <h5>ID: {{$personal->id}}</h5>
                    </div><br>
                    <p class="clearfix text-center">
                        <span class="text-muted">
                            {{$personal->nombrecompleto}}
                        </span>
                    </p>
                </div>
            </div>
            <div class="col-lg-4 pl-lg-4">
                <div class="profile-feed">
                    <div class="d-flex align-items-start profile-feed-item">
                        <div class="form-group col-md-9">
                            <strong><i class="fas fa-address-card mr-1"></i> CI</strong>
                            <p class="text-muted">
                                {{$personal->ci}} {{$personal->ciexp}}
                            </p>
                            <hr>
                            @if($personal->nit)
                                <strong><i class="fas fa-folder mr-1"></i> NIT</strong>
                                <p class="text-muted">{{ $personal->nit }}</p>
                            @else
                                <strong><i class="fas fa-folder mr-1"></i> NIT</strong>
                                <p class="text-muted">NO TIENE</p>
                            @endif

                            <hr>
                            <strong><i class="fas fa-at mr-1"></i> Email</strong>
                            <p class="text-muted">
                                {{$personal->email}}
                            </p>
                            <hr>
                            <strong><i class="fas fa-business-time mr-1"></i> Cargo</strong>
                            <p class="text-muted">
                                {{$personal->cargo}}
                            </p>
                            <hr>
                            <strong><i class="fas fa-map-marked-alt mr-1"></i> Dirección</strong>
                            <p class="text-muted">
                                {{$personal->direccion}}
                            </p>
                            <hr>
                            <strong><i class="fas fa-phone-square-alt mr-1"></i> Teléfono</strong>
                            <p class="text-muted">
                                {{$personal->celular}}
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
                                {{$personal->estado}}
                            </p>
                            <hr>
                            <strong><i class="fas fa-hotel mr-1"></i> Nombre de Banco</strong>
                            <p class="text-muted">
                                {{$personal->banco}}
                            </p>
                            <hr>
                            <strong><i class="far fa-credit-card mr-1"></i> Nro. de Cuenta</strong>
                            <p class="text-muted">
                                {{$personal->numcuenta}}
                            </p>
                            <hr>
                            @if($personal->fechasalida)
                                <strong><i class="fas fa-calendar-check mr-1"></i> Fecha de ingreso y salida</strong>
                                <p class="text-muted">{{$personal->fechaingreso}} - {{$personal->fechasalida}}</p>
                            @else
                                <strong><i class="fas fa-calendar-check mr-1"></i> Fecha de ingreso y salida</strong>
                                <p class="text-muted">{{$personal->fechaingreso}}</p>
                            @endif
                            <hr>
                            <strong><i class="fas fa-user-shield mr-1"></i> Nombre de contacto</strong>
                            <p class="text-muted">
                                {{$personal->contacto}}
                            </p>
                            <hr>
                            <strong><i class="fas fa-phone-volume mr-1"></i> Celular de contacto</strong>
                            <p class="text-muted">
                                {{$personal->celcontacto}}
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

@section('css')
<style>
    h4 {color:#94c93b; 
        font-family: "Segoe UI";
        font-weight: 900;
        font-size: 170%;
        }

    h3 {color:black; 
        font-family: "Segoe UI";
        font-weight: 700;
        font-size: 170%;
        }
    #vista-previa {
        display: block;
      
        height: auto;

        border: 1px solid #ccc;
        padding: 5px;
        box-shadow: 0px 0px 5px 0px rgba(0, 0, 0, 0.2);
        }
        .btn-editar {
            background-color: #ffffff;
        color: #94c93b;
        border-color: #94c93b;
        border-radius: 5px;
        padding: 10px 10px;
        margin-left: 10px;
        margin-right: 10px;
            }
        .btn-editar:hover {
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