@extends('adminlte::page')
<link href="assets/img/logo.png" rel="icon">
@section('content_header')

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
@can('admin.asociados.listadoclienteita')
<div class="card">
    <div class="card-body">
        @endcan
        @can('admin.asociados.listadoclienteita')
        <div class="titulo">CLIENTES GOOD LIFE</div>
        <div class="card-body">
            <div class="row">
                {{-- <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                    <div class="card card-stats bg-color-2">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-5 col-md-4">
                                    <div class="icon-big text-center icon-warning">
                                        <i class="fas fa-user fa-5x text-white"></i>
                                    </div>
                                </div>
                                <div class="col-7 col-md-8">
                                    <div class="numbers">
                                        <h3>CLIENTES<br>BANCOS</h3>
                                    </div>
                                </div>
                            </div>
                            <h4 class="total-bottom-right">Total: {{ $clientesBancosCount }}</h4>
                        </div>
                    </div>
                </div> --}}
                <div class="col-lg-4 col-md-6 col-sm-6 col-12">
                    <div class="card card-stats bg-color-3">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-5 col-md-4">
                                    <div class="icon-big text-center icon-warning">
                                        <i class="fas fa-user fa-5x text-white"></i>
                                    </div>
                                </div>
                                <div class="col-7 col-md-8">
                                    <div class="numbers">
                                        <h3>CLIENTES<br>ITA</h3>
                                    </div>
                                </div>
                            </div>
                            <h4 class="total-bottom-right">Total: {{ $clientesITACount }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 col-sm-6 col-12">
                    <div class="card card-stats bg-color-4">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-5 col-md-4">
                                    <div class="icon-big text-center icon-warning">
                                        <i class="fas fa-user fa-5x text-white"></i>
                                    </div>
                                </div>
                                <div class="col-7 col-md-8">
                                    <div class="numbers">
                                        <h3>CLIENTES<br>AUDITORIAS</h3>
                                    </div>
                                </div>
                            </div>
                            <h4 class="total-bottom-right">Total: {{ $clientesAuditoriasCount }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 col-sm-6 col-12">
                    <div class="card card-stats bg-color-1">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-5 col-md-4">
                                    <div class="icon-big text-center icon-warning">
                                        <i class="fas fa-user fa-5x text-white"></i>
                                    </div>
                                </div>
                                <div class="col-7 col-md-8">
                                    <div class="numbers">
                                        <h3>CLIENTES<br>COMUNES</h3>
                                    </div>
                                </div>
                            </div>
                            <h4 class="total-bottom-right">Total: {{ $clientesComunesCount }}</h4>
                        </div>
                    </div>
                </div>
                <style>
                    .card {
                        min-height: 150px;
                        position: relative;
                    }
                    .total-bottom-right {
                        position: absolute;
                        bottom: 10px;
                        right: 10px;
                        margin: 0;
                    }
                    .icon-big {
                        font-size: 1rem;
                    }
                    .bg-color-1 {
                        background-color: #c0dffc;
                    }
                    .bg-color-2 {
                        background-color: #f9e2c4;
                    }
                    .bg-color-3 {
                        background-color: #f9d1f4;
                    }
                    .bg-color-4 {
                        background-color: #c0fbcb;
                    }
                    .bg-color-5 {
                        background-color: #f8f5f1;
                    }
                </style>
            </div>
        </div>
        @endcan
        @can('admin.mensajes.index')
        @if($mensajes->isNotEmpty())
        <div class="col-lg-12 col-md-12 col-sm-12 col-12">
            <div class="card card-stats bg-color-5">
                <div class="card-body">
                    <div class="novedadtitulo">MENSAJES</div>
                    <table class="table">
                        <thead>
                            <tr>
                                <th class="novedad">Encargado</th>
                                <th class="novedad">Destino</th>
                                <th class="novedad">Asunto</th>
                                {{-- <th class="novedad">Fecha y Hora</th> --}}
                                <th class="novedad">Ver</th>
                                <th class="novedad">ESTADO</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($mensajesPrincipales as $titulo => $data)
                                <tr>
                                    <td>{{ $data['mensaje']->usuarioregistro }}</td>
                                    <td>{{ $data['mensaje']->usuariodestino }}</td>
                                    <td>{{ $data['mensaje']->titulo }}</td>
                                    {{-- <td>{{ $data['mensaje']->created_at->format('Y-m-d H:i:s') }}</td> --}}
                                    <td>
                                        <button type="button" class="btn btn-ver btn-sm" data-toggle="modal" data-target="#modalMensaje{{ $titulo }}">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </td>
                                    <td>
                                        @if ($data['esUltimoMensajeParaUsuario'])
                                            <span class="badge badge-success">NUEVO</span>
                                        @else
                                            <span class="badge badge-warning">ENVIADO</span>
                                        @endif
                                    </td>
                                </tr>
        
                                <!-- Modal para mostrar todos los mensajes del título -->
                                <div class="modal fade" id="modalMensaje{{ $titulo }}" tabindex="-1" role="dialog" aria-labelledby="modalMensajeLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-lg" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="titulo" id="modalMensajeLabel">{{ $titulo }}</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <!-- Mostrar toda la conversación -->
                                                <div class="message-container" id="messageContainer{{ $titulo }}">
                                                    @foreach ($data['mensajes'] as $mensajeConversacion)
                                                        <div class="message {{ $mensajeConversacion->usuarioregistro == Auth::user()->name ? 'message-sent' : 'message-received' }}">
                                                            <strong style="font-size: 12px; margin-bottom: -4px;">{{ $mensajeConversacion->usuarioregistro }}</strong>
                                                            <p style="font-size: 17px; margin-bottom: -4px; line-height: 1.2;">{{ $mensajeConversacion->mensaje }}</p>
                                                            <small class="text-muted" style="font-size: 12px;">{{ $mensajeConversacion->created_at->format('H:i:s') }}</small>
                                                        </div>
                                                        <hr>
                                                    @endforeach
                                                </div>
                                            
                                                <!-- Campo de respuesta y botón de enviar -->
                                                <form action="{{ route('admin.home.store') }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="asunto" value="{{ $titulo }}">
                                                    <input type="hidden" name="usuariodestino" value="{{ $data['mensaje']->usuarioregistro }}">
                                                    <input type="hidden" name="usuarioregistro" value="{{ Auth::user()->name }}">
                                                    <input type="hidden" name="usuarioid" value="{{ Auth::user()->id }}">
                                                    <div class="form-group">
                                                        <textarea name="respuesta" class="form-control" rows="3" placeholder="Escribe tu respuesta aquí..." required></textarea>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-cerrar" data-dismiss="modal">Cerrar</button>
                                                        <button type="submit" class="btn btn-whatsapp">Enviar Respuesta</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
        
                                <script>
                                    // Asegúrate de que el script se ejecute después de que el modal se haya mostrado
                                    $('#modalMensaje{{ $titulo }}').on('shown.bs.modal', function () {
                                        // Selecciona el contenedor de mensajes para el modal actual
                                        var messageContainer = document.getElementById('messageContainer{{ $titulo }}');
                                        // Desplaza el contenedor de mensajes hacia el final
                                        messageContainer.scrollTop = messageContainer.scrollHeight;
                                    });
                                </script>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        @else
        @endif
        @endcan

        @can('admin.admprogramaciones.index')
        <div class="titulo">PROGRAMACIONES PRÓXIMAS</div>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Cliente</th>
                        <th>Acciones</th>
                        <th>Proveedor</th>
                        <th>Fecha asignada</th>
                        <th>Hora asignada</th>
                        <th>Celular</th>
                        {{-- <th>Estado</th> --}}
                    </tr>
                </thead>
                <tbody>
                    @foreach($programacionclienteauditorias as $programacionclienteauditoria)
                        <?php
                        $clienteAuditoria = $programacionclienteauditoria->clienteAuditoria;
                        $celular = $clienteAuditoria ? $clienteAuditoria->celular : '';

                        $mensaje = "Hola, le hablo de la empresa GOOD LIFE, le recordamos que tiene una cita con: " .
                                $programacionclienteauditoria->proveedornombre . ", para realizarse: " .
                                $programacionclienteauditoria->accionnombre . ", para la fecha: " .
                                $programacionclienteauditoria->fechaasignada . ", a la hora: " . 
                                $programacionclienteauditoria->horadesde . ". Que tenga un excelente dia.";

                        $mensajeCodificado = urlencode($mensaje);
                        ?>
                        <tr>
                            <td>{{ $programacionclienteauditoria->clienteauditorianombre }}</td>
                            <td>{{ $programacionclienteauditoria->accionnombre }}</td>
                            <td>{{ $programacionclienteauditoria->proveedornombre }}</td>
                            <td>{{ $programacionclienteauditoria->fechaasignada }}</td>
                            <td>{{ $programacionclienteauditoria->horadesde }} - {{ $programacionclienteauditoria->horahasta }}</td>
                            <td>{{ $celular }}</td>
                            {{-- <td width="10px">
                                @if(in_array($programacionclienteauditoria->accionnombre, $estadoRegistradosAuditoria))
                                    <i class="fas fa-check-circle fa-2x text-success"></i>
                                @else
                                    <i class="fas fa-times-circle fa-2x text-danger"></i>
                                @endif
                            </td> --}}
                            <td width="10px">
                                <abbr title="Recordar">
                                    <a class="btn btn-sm btn-whatsapp @if(in_array($programacionclienteauditoria->accionnombre, $estadoRegistradosAuditoria)) disabled @endif" 
                                    @if(in_array($programacionclienteauditoria->accionnombre, $estadoRegistradosAuditoria)) 
                                        onclick="return false;" 
                                    @else 
                                        href="https://wa.me/{{ $celular }}?text={{ $mensajeCodificado }}" 
                                    @endif>
                                        <i class="fas fa-sms"></i>
                                    </a>
                                </abbr>
                            </td>
                        </tr>
                    @endforeach

                    @foreach($programacionclientecomunes as $programacionclientecomun)
                        <?php
                        $clienteComun = $programacionclientecomun->clienteComun;
                        $celular = $clienteComun ? $clienteComun->celular : '';

                        $mensaje = "Hola, le hablo de la empresa GOOD LIFE, le recordamos que tiene una cita con: " .
                                $programacionclientecomun->proveedornombre . ", para realizarse: " .
                                $programacionclientecomun->accionnombre . ", para la fecha: " .
                                $programacionclientecomun->fechaasignada . ", a la hora: " . 
                                $programacionclientecomun->horadesde . ". Que tenga un excelente dia.";

                        $mensajeCodificado = urlencode($mensaje);
                        ?>
                        <tr>
                            <td>{{ $programacionclientecomun->clientecomunnombre}}</td>
                            <td>{{ $programacionclientecomun->accionnombre }}</td>
                            <td>{{ $programacionclientecomun->proveedornombre }}</td>
                            <td>{{ $programacionclientecomun->fechaasignada }}</td>
                            <td>{{ $programacionclientecomun->horadesde }} - {{ $programacionclientecomun->horahasta }}</td>
                            <td>{{ $celular }}</td>
                            {{-- <td width="10px">
                                @if(in_array($programacionclientecomun->accionnombre, $estadoRegistradosComun))
                                    <i class="fas fa-check-circle fa-2x text-success"></i>
                                @else
                                    <i class="fas fa-times-circle fa-2x text-danger"></i>
                                @endif
                            </td> --}}
                            <td width="10px">
                                <abbr title="Recordar">
                                    <a class="btn btn-sm btn-whatsapp @if(in_array($programacionclientecomun->accionnombre, $estadoRegistradosComun)) disabled @endif" 
                                    @if(in_array($programacionclientecomun->accionnombre, $estadoRegistradosComun)) 
                                        onclick="return false;" 
                                    @else 
                                        href="https://wa.me/{{ $celular }}?text={{ $mensajeCodificado }}" 
                                    @endif>
                                        <i class="fas fa-sms"></i>
                                    </a>
                                </abbr>
                            </td>
                        </tr>
                    @endforeach
                    @foreach($programacionclienteitas as $programacionclienteita)
                        <?php
                        $clienteIta = $programacionclienteita->clienteIta;
                        $celular = $clienteIta ? $clienteIta->celular : '';

                        $mensaje = "Hola, le hablo de la empresa GOOD LIFE, le recordamos que tiene una cita con: " .
                                $programacionclienteita->proveedornombre . ", para realizarse: " .
                                $programacionclienteita->accionnombre . ", para la fecha: " .
                                $programacionclienteita->fechaasignada . ", a la hora: " . 
                                $programacionclienteita->horadesde . ". Que tenga un excelente dia.";

                        $mensajeCodificado = urlencode($mensaje);
                        ?>
                        <tr>
                            <td>{{ $programacionclienteita->clienteitanombre}}</td>
                            <td>{{ $programacionclienteita->accionnombre }}</td>
                            <td>{{ $programacionclienteita->proveedornombre }}</td>
                            <td>{{ $programacionclienteita->fechaasignada }}</td>
                            <td>{{ $programacionclienteita->horadesde }} - {{ $programacionclienteita->horahasta }}</td>
                            <td>{{ $celular }}</td>
                            {{-- <td width="10px">
                                @if(in_array($programacionclienteita->accionnombre, $estadoRegistradosIta))
                                    <i class="fas fa-check-circle fa-2x text-success"></i>
                                @else
                                    <i class="fas fa-times-circle fa-2x text-danger"></i>
                                @endif
                            </td> --}}
                            <td width="10px">
                                <abbr title="Recordar">
                                    <a class="btn btn-sm btn-whatsapp @if(in_array($programacionclienteita->accionnombre, $estadoRegistradosIta)) disabled @endif" 
                                    @if(in_array($programacionclienteita->accionnombre, $estadoRegistradosIta)) 
                                        onclick="return false;" 
                                    @else 
                                        href="https://wa.me/{{ $celular }}?text={{ $mensajeCodificado }}" 
                                    @endif>
                                        <i class="fas fa-sms"></i>
                                    </a>
                                </abbr>
                            </td>
                        </tr>
                    @endforeach 
                    @foreach($programacionclientebancos as $programacionclientebanco)
                        <?php
                        $clienteBanco = $programacionclientebanco->clienteBanco;
                        $celular = $clienteBanco ? $clienteBanco->celular : '';

                        $mensaje = "Hola, le hablo de la empresa GOOD LIFE, le recordamos que tiene una cita con: " .
                                $programacionclientebanco->proveedornombre . ", para realizarse: " .
                                $programacionclientebanco->accionnombre . ", para la fecha: " .
                                $programacionclientebanco->fechaasignada . ", a la hora: " . 
                                $programacionclientebanco->horadesde . ". Que tenga un excelente dia.";

                        $mensajeCodificado = urlencode($mensaje);
                        ?>
                        <tr>
                            <td>{{ $programacionclientebanco->clientenombre}}</td>
                            <td>{{ $programacionclientebanco->accionnombre }}</td>
                            <td>{{ $programacionclientebanco->proveedornombre }}</td>
                            <td>{{ $programacionclientebanco->fechaasignada }}</td>
                            <td>{{ $programacionclientebanco->horadesde }} - {{ $programacionclientebanco->horahasta }}</td>
                            <td>{{ $celular }}</td>
                            {{-- <td width="10px">
                                @if(in_array($programacionclientebanco->accionnombre, $estadoRegistradosBanco))
                                    <i class="fas fa-check-circle fa-2x text-success"></i>
                                @else
                                    <i class="fas fa-times-circle fa-2x text-danger"></i>
                                @endif
                            </td> --}}
                            <td width="10px">
                                <abbr title="Recordar">
                                    <a class="btn btn-sm btn-whatsapp @if(in_array($programacionclientebanco->accionnombre, $estadoRegistradosBanco)) disabled @endif" 
                                    @if(in_array($programacionclientebanco->accionnombre, $estadoRegistradosBanco)) 
                                        onclick="return false;" 
                                    @else 
                                        href="https://wa.me/{{ $celular }}?text={{ $mensajeCodificado }}" 
                                    @endif>
                                        <i class="fas fa-sms"></i>
                                    </a>
                                </abbr>
                            </td>
                        </tr>
                    @endforeach
                    
                </tbody>
            </table>
            @error('accion')
                <small class="text-danger fas fa-exclamation-circle">
                    {{$message}}
                </small>
            @enderror
        </div>
        @endcan

        @if($userRole === 'ASOCIADO')

        <div class="titulo">{{-- BIENVENIDO ALIANZA SEGUROS --}}</div>

        @endif

        @can('admin.asociados.listadoclienteita')
    </div>
</div>
@endcan

@if($userRole === 'PROVEEDOR')   
    <div class="row justify-content-center" style="">
        <div class="col-md-8">
            <div class="custom-card shadow-lg">
                <div class="card-header text-white text-center">
                    <h3 class="mb-0">¡BIENVENID@ AL SISTEMA WEB DE GOOD LIFE S.R.L.!</h3>
                </div>
                <div class="card-body text-center">
                    <p class="lead">
                        En nuestro sistema web, los médicos podrán visualizar fácilmente sus programaciones médicas 
                        y citas programadas para los próximos días, permitiéndoles organizar mejor su tiempo y atención a los pacientes.  
                    </p>
                    <p class="lead">
                        Además, tendrán la posibilidad de subir informes detallados sobre los estudios o especialidades realizadas, 
                        garantizando un registro completo y actualizado de cada atención médica brindada.  
                    </p>
                    <p class="lead">
                        Accede a todas estas funciones desde cualquier dispositivo y mantén tu información médica siempre disponible.  
                    </p>
                </div>
            </div>
        </div>
    </div>
@endif

<style>
    /* Diseño limpio y elegante con tonos más suaves */
    .custom-card {
        background: linear-gradient(to bottom, #faf9f4, #f5f1e8);
        border-radius: 15px;
        border-left: 8px solid rgba(250, 166, 37, 0.8);
        padding: 20px;
        transition: all 0.3s ease-in-out;
        position: relative;
        overflow: hidden;
        margin-top:20px;
    }

    .custom-card:hover {
        transform: scale(1.02);
        box-shadow: 0px 8px 20px rgba(0, 0, 0, 0.12);
    }

    .card-header {

        font-size: 1.5rem;
        font-weight: bold;
        padding: 15px;
        border-radius: 12px 12px 0 0;
    }

    .lead {
        font-size: 1.2rem;
        color: #555;
        font-weight: 500;
        margin-bottom: 15px;
    }

    .welcome-animation {
        font-size: 2rem;
        margin-top: 10px;
        animation: shine 1.5s infinite alternate;
    }

    @keyframes shine {
        from { opacity: 0.3; transform: scale(1); }
        to { opacity: 1; transform: scale(1.1); }
    }
</style>

@endsection

@section('css')
<link rel="styleheet" href="/css/admin_custom.css">
<style>
    .modal-body {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .message-container {
        max-height: 350px; /* Ajusta el valor según tus necesidades */
        overflow-y: auto; /* Permite el desplazamiento vertical */
        margin-bottom: 10px; /* Espacio entre los mensajes y el formulario */
    }

    .message {
        max-width: 60%;
        padding: 10px;
        border-radius: 10px;
        margin-bottom: 10px;
        position: relative;
    }

    .message-received {
        background-color: #e2ffde;
        color: #000000;
        align-self: flex-start;
        margin-left: 0;
        margin-right: auto;
        text-align: left;
    }

    .message-sent {
        background-color: #f1f1f1;
        color: #000000;
        align-self: flex-end;
        margin-left: auto;
        margin-right: 0;
        text-align: right;
    }

    .message strong {
        display: block;
        margin-bottom: 5px;
    }

    .message p {
        margin: 0;
    }

    .modal-footer {
        margin-top: 10px; /* Espacio entre el formulario y el pie del modal */
    }
</style>



<style>
    h5{
        font-size: 25px;
        font-weight: 900;
        }
    .btn-ver {
        background-color:  #ffffff;
        color: #94c93b;
        border-color: #94c93b;
        border-radius: 5px;
        }
    .btn-ver:hover {
        background-color: #94c93b;
        color: #ffffff;
        }
    .titulo {
        color:#94c93b; 
        font-family: "Segoe UI";
        font-weight: 900;
        font-size: 23px;
        }
    .novedadtitulo {
        color:#faa625; 
        font-family: "Segoe UI";
        font-weight: 900;
        font-size: 23px;
        }
    th {
        color:#94c93b; 
        font-family: "Segoe UI";
        font-weight: 900;
    }
    .novedad {
        color:#faa625; 
        
    }
    h2 {color:green; 
        font-family: "Segoe UI";
        font-weight: 900;
        }
    h3{
        color:black; 
        font-family: "Segoe UI";
        font-weight: 700;
        font-size: 110%;
        text-align: right;
    }
    h4{
        color:black; 
        font-family: "Segoe UI";
        font-weight: 500;
        font-size: 110%;
        text-align: right;
    }
    h6 {color:black; 
        font-family: "Segoe UI";
        font-weight: 900;
        font-size: 100%;
        text-align: left;
        }
    h1 {color:black; 
        font-family: "Segoe UI";
        font-weight: 900;
        font-size: 130%;
        text-align: center;
        }
    .icono:hover{
        transition:all .5s ease-in-out;
        -webkit-transform:scale(1.1);transform:scale(1.1);
        }
    .icono {
        transition:all .5s ease-in-out;
        overflow:hidden;
        }
    .icono2:hover{
        transition:all .5s ease-in-out;
        -webkit-transform:scale(1.3);transform:scale(1.3);
        }
        .btn-cerrar {
                background-color:  #ffffff;
                color: #ff0000;
                border-color: #ff0000;
                border-radius: 5px;
                padding: 4px 5px;
            }
        .btn-cerrar:hover {
                background-color: #ff0000;
                color: #ffffff;
            }
    .icono2 {
        transition:all .5s ease-in-out;
        overflow:hidden;
        }
        .btn-whatsapp {
        background-color: #ffffff;
        color: #94c93b;
        border-color: #94c93b;
        border-radius: 5px;
        padding: 5px 10px;
    }
    .btn-whatsapp:hover {
        background-color: #94c93b;
        color: #ffffff;
    }
</style>
@stop
@push('scripts')
    <script>
        $(document).ready(function() {
            // Javascript method's body can be found in assets/assets-for-demo/js/demo.js
            demo.initChartsPages();
        });
    </script>
    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/aos.js"></script>
    <script src="js/smoothscroll.js"></script>
    <script src="js/custom.js"></script>
@endpush
