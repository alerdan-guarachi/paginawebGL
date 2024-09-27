@extends('adminlte::page')

@section('content_header')
<h1>PROGRAMACIONES DE HOY</h1>
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
    </div>
</div>
@stop

@section('css')
<link rel="styleheet" href="/css/admin_custom.css">
<style>
    h1, th {color:#94c93b; 
        font-family: "Segoe UI";
        font-weight: 900;
        }
        .btn-editar {
                background-color:  #ffffff;
                color: #0400ff;
                border-color: #0400ff;
                border-radius: 5px;
            }
        .btn-editar:hover {
                background-color: #0400ff;
                color: #ffffff;
            }
        .btn-eliminar {
                background-color:  #ffffff;
                color: #ff0000;
                border-color: #ff0000;
                border-radius: 5px;
            }
        .btn-eliminar:hover {
                background-color: #ff0000;
                color: #ffffff;
            }
        .btn-crear {
                background-color:  #ffffff;
                color: #94c93b;
                border-color: #94c93b;
                border-radius: 5px;
                padding: 10px 20px;
            }
        .btn-crear:hover {
                background-color: #94c93b;
                color: #ffffff;
            }
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

@section('js')
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @if (session('eliminar')=='ok')
    <script>
        Swal.fire(
      '¡Eliminado!',
      'El rol se eliminó con éxito',
      'success')
    </script>
    @endif

<script>

    $('.formulario-eliminar').submit(function(e){
        e.preventDefault();

        Swal.fire({
        title: '¿Estás seguro?',
        text: "El rol se eliminará definitivamente",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: '¡Si, eliminar!',
        cancelButtonText: 'Cancelar'
        }).then((result) => {
        if (result.isConfirmed) {
            this.submit();
        }
        }) 
    });
</script>
@endsection