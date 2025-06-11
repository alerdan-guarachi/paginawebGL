@extends('adminlte::page')

@section('content_header')
<a class="btn btn-sm float-right btn-regresar" href="{{ route('admin.asociados.verclienteita', $cliente) }}">REGRESAR</a>
<a class="btn btn-actualizarestado btn-sm float-right" data-toggle="modal" data-target="#accionModal">ACTUALIZAR ESTADO</a>
<a class="btn btn-sm float-right btn-generarpdf" href="{{ route('admin.asociados.generarpdfprogramacionclienteita', ['cliente' => $cliente, 'buscarpor' => $fechaSeleccionada]) }}">GENERAR PDF</a>
<h5>ESTADO DE PROGRAMACIÓN DE:</h5>
<h3>{{$cliente->nombrecompleto}}</h3>
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
        <nav class="navbar navbar-expand-lg float-right" style="margin-right: -10px;">
            <div class="container-fluid">
                <div class="d-flex flex-wrap align-items-center">
                    <form action="{{ route('buscarprogramacionclientesita', $cliente) }}" method="get" class="form-inline">
                        <div class="flex-grow-1">
                            <select name="buscarpor" class="form-control mr-sm-2">
                                <option value="" disabled selected>Fecha de Bateria</option>
                                @foreach($fechas as $fecha)
                                    <option value="{{ $fecha }}">{{ $fecha }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button id="btn-buscar" class="btn btn-buscar my-2 my-sm-0" type="submit"><i class="fas fa-search"></i></button>
                    </form>
                </div>
            </div>
        </nav>     
        {!! Form::hidden('usuarioid', auth()->user()->id) !!}
        {!! Form::hidden('usuarioregistro', auth()->user()->name) !!}
        {!! Form::hidden('clienteitaid', $id) !!}
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Estudio/Especialidad</th>
                        <th>Proveedor</th>
                        <th>Fecha_Bateria</th>
                        <th>Fecha_Asignada</th>
                        <th>Hora_Asignada</th>
                        <th>Estado</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($accionesDisponibles as $accion)
                        <?php 
                            $mensaje = "Hola, le hablo de la empresa GOOD LIFE, le recordamos que tiene una cita con: " .
                                        $accion->proveedornombre . ", para realizarse: " .
                                        $accion->areanombre . ", para la fecha: " .
                                        $accion->fechaasignada . ", a la hora: " . 
                                        $accion->horadesde . ". Que tenga un excelente día.";

                            $direcciones = "";
                            if (!empty($accion->direccion)) {
                                $direcciones .= "\n\nDirección: " . $accion->direccion;
                                if (!empty($accion->linkubicacion)) {
                                    $direcciones .= " (Ver Ubicación: " . $accion->linkubicacion . ")";
                                }
                            }
                            if (!empty($accion->direccion2)) {
                                $direcciones .= "\n\nDirección: " . $accion->direccion2;
                                if (!empty($accion->linkubicacion2)) {
                                    $direcciones .= " (Ver Ubicación 2: " . $accion->linkubicacion2 . ")";
                                }
                            }
                            if (!empty($accion->direccion3)) {
                                $direcciones .= "\n\nDirección: " . $accion->direccion3;
                                if (!empty($accion->linkubicacion3)) {
                                    $direcciones .= " (Ver Ubicación 3: " . $accion->linkubicacion3 . ")";
                                }
                            }
                            $mensaje .= $direcciones;
                            $mensajeCodificado = urlencode($mensaje);
                        ?>

                        <tr>
                            <td class="align-middle">{{ $accion->id }}</td>
                            <td class="align-middle">{{ $accion->accionnombre }}</td>
                            <td class="align-middle">{{ $accion->proveedornombre }}</td>
                            <td class="align-middle">{{ $accion->fechabateria }}</td>
                            <td class="align-middle">{{ $accion->fechaasignada }}</td>
                            <td class="align-middle">{{ $accion->horadesde }} - {{ $accion->horahasta }}</td>
                            <td width="10px">
                                @if(isset($estadoMapeado[$accion->accionnombre][$accion->fechabateria]))
                                    <i class="fas fa-check-circle fa-2x checkverde"></i>
                                @else
                                    <i class="fas fa-times-circle fa-2x text-danger"></i>
                                @endif
                            </td>                  
                            <td width="10px">
                                @php
                                    $cantidadDirecciones = 0;
                                    if (!empty($accion->direccion)) $cantidadDirecciones++;
                                    if (!empty($accion->direccion2)) $cantidadDirecciones++;
                                    if (!empty($accion->direccion3)) $cantidadDirecciones++;
                                @endphp

                                <abbr title="Recordar">
                                    @if($cantidadDirecciones <= 1)
                                        <a class="btn btn-sm btn-whatsapp 
                                            @if(isset($estadoMapeado[$accion->accionnombre][$accion->fechabateria])) disabled @endif" 
                                            @if(isset($estadoMapeado[$accion->accionnombre][$accion->fechabateria])) 
                                                onclick="return false;" 
                                            @else 
                                                href="https://wa.me/{{ $cliente->celular }}?text={{ $mensajeCodificado }}" 
                                            @endif>
                                            <i class="fas fa-sms"></i>
                                        </a>
                                    @else
                                        <a class="btn btn-sm btn-whatsapp" data-toggle="modal" data-target="#modalDireccion{{ $accion->id }}">
                                            <i class="fas fa-sms"></i>
                                        </a>
                                    @endif
                                </abbr>
                            </td>

                            <!-- Modal -->
                            <div class="modal fade" id="modalDireccion{{ $accion->id }}" tabindex="-1" role="dialog">
                                <div class="modal-dialog" role="document">
                                    <form target="_blank" method="GET" id="formDireccion{{ $accion->id }}">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">ELEGIR DIRECCION PARA ENVIAR</h5>
                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                            </div>
                                            <div class="modal-body">
                                                @if(!empty($accion->direccion))
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="direccion{{ $accion->id }}" id="dir1-{{ $accion->id }}" value="{{ $accion->direccion }} {{ $accion->linkubicacion ? '(Ver Ubicación: ' . $accion->linkubicacion . ')' : '' }}" checked>
                                                    <label class="form-check-label" for="dir1-{{ $accion->id }}">
                                                    <strong>{{ $accion->ciudad }}</strong> - {{ $accion->direccion }}
                                                    </label>
                                                </div>
                                                @endif
                                                @if(!empty($accion->direccion2))
                                                <div class="form-check" style="margin-top: 20px;">
                                                    <input class="form-check-input" type="radio" name="direccion{{ $accion->id }}" id="dir2-{{ $accion->id }}" value="{{ $accion->direccion2 }} {{ $accion->linkubicacion2 ? '(Ver Ubicación: ' . $accion->linkubicacion2 . ')' : '' }}">
                                                    <label class="form-check-label" for="dir2-{{ $accion->id }}">
                                                    <strong>{{ $accion->ciudad2 }}</strong> - {{ $accion->direccion2 }}
                                                    </label>
                                                </div>
                                                @endif
                                                @if(!empty($accion->direccion3))
                                                <div class="form-check" style="margin-top: 20px;">
                                                    <input class="form-check-input" type="radio" name="direccion{{ $accion->id }}" id="dir3-{{ $accion->id }}" value="{{ $accion->direccion3 }} {{ $accion->linkubicacion3 ? '(Ver Ubicación: ' . $accion->linkubicacion3 . ')' : '' }}">
                                                    <label class="form-check-label" for="dir3-{{ $accion->id }}">
                                                    <strong>{{ $accion->ciudad3 }}</strong> - {{ $accion->direccion3 }}
                                                    </label>
                                                </div>
                                                @endif
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-sm btn-outline-danger" data-dismiss="modal">Cancelar</button>
                                                <button type="button" class="btn btn-sm btn-outline-success" onclick="enviarMensajeWhatsApp({{ $accion->id }}, '{{ $cliente->celular }}', '{{ addslashes($accion->proveedornombre) }}', '{{ addslashes($accion->areanombre) }}', '{{ $accion->fechaasignada }}', '{{ $accion->horadesde }}')"><i class="fab fa-whatsapp mr-1"></i> Enviar</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </tr>
                    @endforeach
                    <script>
                        function enviarMensajeWhatsApp(id, celular, proveedor, area, fecha, hora) {
                            const selector = `input[name="direccion${id}"]:checked`;
                            const direccionRadio = document.querySelector(selector);

                            if (!direccionRadio) {
                                alert('Seleccione una dirección antes de enviar.');
                                return;
                            }

                            const direccion = direccionRadio.value;

                            const mensaje = `Hola, le hablo de la empresa GOOD LIFE, le recordamos que tiene una cita con: ${proveedor}, para realizarse: ${area}, para la fecha: ${fecha}, a la hora: ${hora}. Que tenga un excelente día.\n\nDirección: ${direccion}`;

                            const mensajeCodificado = encodeURIComponent(mensaje);
                            const url = `https://wa.me/${celular}?text=${mensajeCodificado}`;

                            window.open(url, '_blank');
                        }
                    </script>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="accionModal" tabindex="-1" role="dialog" aria-labelledby="accionModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="accionModalLabel">ACTUALIZAR ESTADO</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                {!! Form::open(['route' => ['admin.asociados.guardarestadoprogramacionclienteita', $cliente], 'method' => 'POST']) !!}
                {!! Form::hidden('usuarioid', auth()->user()->id) !!}
                {!! Form::hidden('usuarioregistro', auth()->user()->name) !!}
                {!! Form::hidden('clienteitaid', $id) !!}
                {!! Form::hidden('clienteitanombre', $nombreclienteita) !!}
                {!! Form::hidden('accionid', '', ['id' => 'modalAccionId']) !!}

                <div class="form-group">   
                    {!! Form::label('', 'Fecha de Bateria:') !!}
                    <select class="form-control" id="fecha_bateria">
                        <option value="" disabled selected></option>
                        @foreach($accionesPorFecha as $fecha => $acciones)
                            <option value="{{ $fecha }}">{{ $fecha }}</option>
                        @endforeach
                    </select>
                    @error('fechabateria')
                        <small class="text-danger fas fa-exclamation-circle">
                            {{$message}}
                        </small>
                    @enderror
                </div>
                <input type="hidden" id="fechabateria" name="fechabateria">
                
                <script>
                    document.getElementById('fecha_bateria').addEventListener('change', function() {
                        var selectedValue = this.value; 
                        document.getElementById('fechabateria').value = selectedValue;
                    });
                </script>
                
                <div class="form-group"> 
                    <div style="display: flex; align-items: center; justify-content: space-between;">
                        {!! Form::label('', 'Acciones disponibles:') !!}
                        <div id="select-all-container" style="display: none;">
                            <label style="font-weight: bold; font-size: 14px; margin-bottom: 0;">
                                <input type="checkbox" id="select-all" style="margin-right: 5px;">
                                SELECCIONAR TODO
                            </label>
                        </div>
                    </div>
                
                    <div id="acciones_select">
                        @foreach($accionesPorFecha as $fecha => $acciones)
                            <div class="acciones-{{ $fecha }}" style="display:none;">
                                @foreach($acciones as $accion)
                                    @if (in_array($accion, $accionesNoRegistradas))
                                        <div>
                                            <label style="font-weight: normal;">
                                                <input type="checkbox" name="accionesSeleccionadas[]" value="{{ $accion }}" class="accion-checkbox"> {{ $accion }}
                                            </label>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                    @error('accionesSeleccionadas')
                        <small class="text-danger fas fa-exclamation-circle">
                            {{$message}}
                        </small>
                    @enderror
                </div>
                
                <script>
                    document.getElementById('fecha_bateria').addEventListener('change', function() {
                        const selectedFecha = this.value;
                        const accionesDivs = document.querySelectorAll('.acciones-' + selectedFecha);
                        document.querySelectorAll('[class^="acciones-"]').forEach(div => {
                            div.style.display = 'none';
                        });
                        document.getElementById('select-all').checked = false;
                        accionesDivs.forEach(div => {
                            div.style.display = 'block';
                        });
                        const accionesCheckboxes = document.querySelectorAll('.acciones-' + selectedFecha +
                            ' .accion-checkbox');
                        if (accionesCheckboxes.length > 0) {
                            document.getElementById('select-all-container').style.display = 'block';
                        } else {
                            document.getElementById('select-all-container').style.display = 'none';
                        }
                    });

                    document.getElementById('select-all').addEventListener('change', function() {
                        const isChecked = this.checked;
                        const selectedFecha = document.getElementById('fecha_bateria').value;

                        if (selectedFecha) {
                            const accionesCheckboxes = document.querySelectorAll('.acciones-' + selectedFecha +
                                ' .accion-checkbox');
                            accionesCheckboxes.forEach(checkbox => {
                                checkbox.checked = isChecked;
                            });
                        }
                    });
                    document.addEventListener('change', function(e) {
                        if (e.target.classList.contains('accion-checkbox')) {
                            const selectedFecha = document.getElementById('fecha_bateria').value;
                            const accionesCheckboxes = document.querySelectorAll('.acciones-' + selectedFecha +
                                ' .accion-checkbox');
                            const allChecked = Array.from(accionesCheckboxes).every(checkbox => checkbox.checked);
                            document.getElementById('select-all').checked = allChecked;
                        }
                    });
                </script>               
                <div class="form-group" hidden>
                    {!! Form::label('nombrecompleto', 'Nombre del Cliente:') !!}
                    {!! Form::text('nombrecompleto', $cliente->nombrecompleto, ['id' => 'modalNombreCompleto', 'class' => 'form-control', 'readonly']) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('fechaatencionprogramacion', 'Fecha de Atención:') !!}
                    {!! Form::date('fechaatencionprogramacion', \Carbon\Carbon::now(), ['class' => 'form-control']) !!}
                    @error('fechaatencionprogramacion')
                        <small class="text-danger fas fa-exclamation-circle">
                            {{$message}}
                        </small>
                    @enderror
                </div>
                {!! Form::submit('Actualizar', ['class' => 'btn btn-crear']) !!}
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>

@stop
@section('js')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script type="text/javascript" src="https://jeremyfagis.github.io/dropify/dist/js/dropify.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://jeremyfagis.github.io/dropify/dist/css/dropify.min.css"> 
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropify/0.2.2/css/dropify.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/dropify/0.2.2/js/dropify.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
@endsection

@section('css')
<link rel="styleheet" href="/css/admin_custom.css">
<style>
    .table td {
        padding: 5px 10px;
    }
    td {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
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
    .checkverde {
        color:#94c93b; 
        }
    th {
        color:#94c93b; 
        font-family: "Segoe UI";
        font-weight: 900;
        }
    h1 {
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
    .btn-actualizarestado {
        background-color:  #ffffff;
        color: #faa625;
        border-color: #faa625;
        border-radius: 5px;
        padding: 5px 10px;
        margin-left: 10px;
        margin-right: 10px;
        }
    .btn-actualizarestado:hover {
        background-color: #faa625;
        color: #ffffff;
        }
    .btn-generarpdf {
        background-color:  #ffffff;
        color: #94c93b;
        border-color: #94c93b;
        border-radius: 5px;
        padding: 5px 10px;

        }
    .btn-generarpdf:hover {
        background-color: #94c93b;
        color: #ffffff;
        }
    .btn-whatsapp {
        background-color: #ffffff;
        color: #94c93b;
        border-color: #94c93b;
        border-radius: 5px;
        padding: 4px 8px;
    }
    .btn-whatsapp:hover {
        background-color: #94c93b;
        color: #ffffff;
    }
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
</style>
@stop