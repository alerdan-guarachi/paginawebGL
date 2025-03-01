@extends('adminlte::page')

@section('content_header')
<a class="btn btn-sm float-right btn-regresar" href="{{ route('admin.asociados.verclientecomun', $clientecomun) }}">REGRESAR</a>
<a class="btn btn-actualizarestado btn-sm float-right" data-toggle="modal" data-target="#accionModal">ACTUALIZAR ESTADO</a>
{{-- <a class="btn btn-sm float-right btn-generarpdf" href="{{ route('admin.asociados.generarpdfprogramacionclientecomun', ['clientecomun' => $clientecomun, 'buscarpor' => $fechaSeleccionada]) }}">GENERAR PDF</a> --}}
<h5>ESTADO DE PROGRAMACIÓN DE:</h5>
<h3>{{$clientecomun->nombrecompleto}}</h3>
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
        <nav class="navbar navbar-expand-lg float-right">
            <div class="container-fluid">
                <div class="d-flex flex-wrap align-items-center">
                    <form action="{{ route('buscarprogramacionclientescomun', $clientecomun) }}" method="get" class="form-inline">
                        <div class="flex-grow-1">
                            <select name="buscarpor" class="form-control mr-sm-2">
                                <option value="" disabled selected>Fecha de Bateria</option>
                                @foreach($fechas as $fecha)
                                    <option value="{{ $fecha }}">{{ $fecha }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button id="btn-buscar" class="btn btn-buscar my-2 my-sm-0" type="submit">Buscar</button>
                    </form>
                </div>
            </div>
        </nav>     
        {!! Form::hidden('usuarioid', auth()->user()->id) !!}
        {!! Form::hidden('usuarioregistro', auth()->user()->name) !!}
        {!! Form::hidden('clientecomunid', $id) !!}
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Acciones</th>
                        <th>Proveedor</th>
                        <th>Fecha bateria</th>
                        <th>Fecha asignada</th>
                        <th>Hora asignada</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($accionesDisponibles as $accion)
                    <?php
                    // Mensaje principal
                    $mensaje = "Hola, le hablo de la empresa GOOD LIFE, le recordamos que tiene una cita con: " .
                            $accion->proveedornombre . ", para realizarse: " .
                            $accion->areanombre . ", para la fecha: " .
                            $accion->fechaasignada . ", a la hora: " . 
                            $accion->horadesde . " en: " . 
                            $accion->direccion . ". Que tenga un excelente día.";
                    
                    // Mensaje de ubicación en un párrafo separado, si está disponible
                    if (!empty($accion->linkubicacion)) {
                        $mensaje .= "\n\nVer ubicación: " . $accion->linkubicacion;
                    }
                    
                    $mensajeCodificado = urlencode($mensaje);
                    ?>
                    <tr>
                        <td class="align-middle">{{ $accion->id }}</td>
                        <td class="align-middle">{{ $accion->accionnombre }} {{ $accion->nrosesion }}</td>
                        <td class="align-middle">{{ $accion->proveedornombre }}</td>
                        <td class="align-middle">{{ $accion->fechabateria }}</td>
                        <td class="align-middle">{{ $accion->fechaasignada }}</td>
                        <td class="align-middle">{{ $accion->horadesde }} - {{ $accion->horahasta }}</td>
                        {{-- <td width="10px">
                            @if(isset($estadoMapeado[$accion->accionnombre][$accion->fechabateria]))
                                <i class="fas fa-check-circle fa-2x checkverde"></i>
                            @else
                                <i class="fas fa-times-circle fa-2x text-danger"></i>
                            @endif
                        </td> --}}
                        <td width="10px"> 
                            @if(isset($estadoMapeado[$accion->accionnombre][$accion->fechabateria][$accion->nrosesion]))
                                <i class="fas fa-check-circle fa-2x checkverde"></i>
                            @else
                                <i class="fas fa-times-circle fa-2x text-danger"></i>
                            @endif
                        </td>
                        
                        
                        <td width="10px"> 
                            <abbr title="Recordar">
                                <a class="btn btn-sm btn-whatsapp 
                                    @if(isset($estadoMapeado[$accion->accionnombre][$accion->fechabateria])) disabled @endif" 
                                    @if(isset($estadoMapeado[$accion->accionnombre][$accion->fechabateria])) 
                                        onclick="return false;" 
                                    @else 
                                        href="https://wa.me/{{ $clientecomun->celular }}?text={{ $mensajeCodificado }}" 
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
                {!! Form::open(['route' => ['admin.asociados.guardarestadoprogramacionclientecomun', $clientecomun], 'method' => 'POST']) !!}
                {!! Form::hidden('usuarioid', auth()->user()->id) !!}
                {!! Form::hidden('usuarioregistro', auth()->user()->name) !!}
                {!! Form::hidden('clientecomunid', $clientecomun->id) !!}
                {!! Form::hidden('clientecomunnombre', $clientecomun->nombrecompleto) !!}
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

                        <!-- Checkbox "Seleccionar Todo" -->
                        <div id="select-all-container" style="display: none;">
                            <label style="font-weight: bold; font-size: 14px; margin-bottom: 0;">
                                <input type="checkbox" id="select-all" style="margin-right: 5px;">
                                SELECCIONAR TODO
                            </label>
                        </div>
                    </div>
                
                    {{-- <div id="acciones_select">
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
                    </div> --}}
                    <div id="acciones_select">
                        @foreach($accionesPorFecha as $fecha => $acciones)
                            <div class="acciones-{{ $fecha }}" style="display:none;">
                                @foreach($acciones as $accion)
                                    <div>
                                        <label style="font-weight: normal;">
                                            <input type="checkbox" name="accionesSeleccionadas[]" value="{{ $accion['nombre'] }}" class="accion-checkbox"> 
                                            {{ $accion['nombre'] }} {{ isset($accion['nrosesion']) ?  $accion['nrosesion'] : '' }}
                                        </label>
                                    </div>
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

                        // Oculta todas las acciones
                        document.querySelectorAll('[class^="acciones-"]').forEach(div => {
                            div.style.display = 'none';
                        });

                        // Reinicia el estado del checkbox "Seleccionar Todo"
                        document.getElementById('select-all').checked = false;

                        // Muestra las acciones para la fecha seleccionada
                        accionesDivs.forEach(div => {
                            div.style.display = 'block';
                        });

                        // Obtener todos los checkboxes de acciones visibles
                        const accionesCheckboxes = document.querySelectorAll('.acciones-' + selectedFecha +
                            ' .accion-checkbox');

                        // Muestra el checkbox "Seleccionar Todo" si hay acciones disponibles
                        if (accionesCheckboxes.length > 0) {
                            document.getElementById('select-all-container').style.display = 'block';
                        } else {
                            document.getElementById('select-all-container').style.display = 'none';
                        }
                    });

                    // Función para manejar el "Seleccionar Todo"
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

                    // Opcional: Actualizar el estado del checkbox "Seleccionar Todo" si se desmarca algún checkbox individual
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
                    {!! Form::text('nombrecompleto', $clientecomun->nombrecompleto, ['id' => 'modalNombreCompleto', 'class' => 'form-control', 'readonly']) !!}
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
@endsection

@section('css')
<link rel="styleheet" href="/css/admin_custom.css">
<style>
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
    .dropify-wrapper {
        height: 125px !important;
    }
    .dropify-message p {
        font-size: 14px;
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
        padding: 10px 20px;
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
        padding: 10px 20px;

        }
    .btn-generarpdf:hover {
        background-color: #94c93b;
        color: #ffffff;
        }
    .mensaje-error {
        color: #e1172b;
        font-family: "Times New Roman";
        padding: 10px;
        margin-top: 5px;
        border-radius: 5px;
        font-size: 12.5px;
        font-weight: bold;
        display: inline-block;
        margin-left: -10px;
    }
    .custom-button {
        background-color: #ffffff;
        color: #faa625;
        border-color: #faa625;
        border-radius: 5px;
        padding: 5px 40px;
    }
    .custom-button:hover {
        background-color: #faa625;
        color: #ffffff;
    }
    .custom2-button {
        background-color: #ffffff;
        color: #faa625;
        border-color: #faa625;
        border-radius: 5px;
        padding: 5px 20px;
    }
    .custom2-button:hover {
        background-color: #faa625;
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