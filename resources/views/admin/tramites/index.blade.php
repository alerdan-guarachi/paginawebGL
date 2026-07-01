@extends('adminlte::page')

@section('content_header')
@php
    $tieneRolEjecutivo = auth()->user()->getRoleNames()->contains('EJECUTIVO PRESTACIONES');
@endphp

@if ($bloquearSistema && $tieneRolEjecutivo)
    <div class="alert alert-danger text-center py-4" 
        style="border-radius: 10px; background-color: #f8d7da; color: #842029; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);">
        
        <h4 class="font-weight-bold mb-3" style="text-transform: uppercase;">
            SECCIÓN BLOQUEADA
        </h4>

        <p class="mb-4">
            POR INCUMPLIMIENTO DE REGISTROS DE ACTIVIDADES
        </p>

        <div class="d-flex justify-content-center">
            <div style="width: 300px;">
                <div class="form-group mb-2">
                    <label for="codigo">Ingresa el código asignado:</label>
                    <input type="text" id="codigo" class="form-control text-center" placeholder="Escribir Código...">
                </div>

                <button type="button" onclick="validarCodigo()" class="btn btn-success btn-sm w-100">
                    VALIDAR CÓDIGO
                </button>
            </div>
        </div>
        <br><br>
        <label>ACTIVIDADES INCUMPLIDAS:</label>
        <div class="table-responsive">
            <table class="table table-striped table-bordered table-sm">
                <thead style="background-color: #c9c9c9">
                    <tr>
                        <th>ID_Trámite</th>
                        <th>Trámite</th>
                        <th>Cliente_ID</th>
                        <th>Cliente_Nombre</th>
                        <th>Nivel_Procedimiento</th>
                        <th>Sub_Procedimiento</th>
                        <th>Fecha_Retorno</th>
                        <th>Dias_Restantes</th>
                    </tr>
                </thead>
                <tbody style="background-color: #ffffff">
                    @forelse ($tramitesporvencer as $tramite)
                        <tr>
                            <td>{{ $tramite->idtramite }}</td>
                            <td>{{ $tramite->tramite }}</td>
                            <td>{{ $tramite->clienteid }}</td>
                            <td>{{ $tramite->clientenombre }}</td>
                            <td>{{ $tramite->tipo }} - {{ $tramite->nivelprocedimiento }}</td>
                            <td>
                                {{ $tramite->subprocedimiento }}
                                @if(!empty($tramite->tipocarta))
                                    - {{ $tramite->tipocarta }}
                                @endif
                            </td>
                            <td class="palpitar">{{ \Carbon\Carbon::parse($tramite->fecharetorno)->format('d-m-Y') }}</td>
                            <style>
                                .palpitar {
                                    animation: latido 1s infinite;
                                }

                                @keyframes latido {
                                    0% {
                                        transform: scale(1);
                                        color: inherit;
                                    }
                                    50% {
                                        transform: scale(1.2);
                                        color: red;
                                    }
                                    100% {
                                        transform: scale(1);
                                        color: inherit;
                                    }
                                }
                            </style>
                            <td>
                                @if($tramite->estado_tiempo == 'FALTAN')
                                    Faltan {{ $tramite->dias }} días y {{ $tramite->horas }} horas
                                @else
                                    Vencido hace {{ $tramite->dias }} días y {{ $tramite->horas }} horas
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted">
                                NO HAY ALERTAS POR MOSTRAR
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <script>
        function validarCodigo() {
            let codigo = document.getElementById('codigo').value;

            fetch("{{ route('validar.codigo') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({ codigo: codigo })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert("✅ Código válido");
                    location.reload();
                } else {
                    alert("❌ Código inválido o no autorizado");
                }
            });
        }
    </script>
@else
    @can('admin.tramites.interrumpirtramite')
    <button type="button" class="btn btn-sm float-right btn-subirarchivos" data-toggle="modal" data-target="#modalCambioApoderado">
        FINALIZAR O INTERRUMPIR TRÁMITE
    </button>
    <a class="btn btn-sm float-right btn-tramitesfinalizados" href="{{ route('admin.tramites.tramitesfinalizados') }}">
        TRÁMITES FINALIZADOS E INTERRUMPIDOS
    </a>
    @endcan
    <div class="modal fade" id="modalCambioApoderado" tabindex="-1" aria-labelledby="modalCambioApoderadoLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalCambioApoderadoLabel">FINALIZAR O INTERRUMPIR TRÁMITE</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <label>Buscar</label>
                    <input type="text" id="buscarTramiteId" class="form-control mb-2 form-control-sm" placeholder="INGRESE EL ID DEL TRÁMITE...">
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered table-striped" id="resultadoTramite">
                            <thead class="table-secondaty">
                                <tr>
                                    <th>ID</th>
                                    <th>Trámite</th>
                                    <th>Cliente_ID</th>
                                    <th>Cliente_Nombre</th>
                                    <th>Apoderado_Asignado</th>
                                    <th>Fecha_Asignación</th>
                                    <th>Ciudad</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                    <form id="formCambioApoderado" method="POST" action="{{ route('tramites.interrumpirtramite') }}">
                        @csrf
                        <input type="hidden" name="tramiteid" id="tramiteid">
                        <input type="hidden" name="clienteid" id="clienteid">
                        <input type="hidden" name="clientenombre" id="clientenombre">
                        <input type="hidden" name="apoderadoanterior" id="apoderadoanterior">
                        <input type="hidden" name="fechaasignacionanterior" id="fechaasignacionanterior">
                        <div class="mb-2">
                            <label for="estadointerrupcion">Estado</label>
                            <select class="form-control" name="estadointerrupcion">
                                <option value="">Selecciona una opción...</option>
                                <option value="FINALIZADO">FINALIZAR</option>
                                <option value="INTERRUMPIDO">INTERRUMPIR</option>
                            </select>
                        </div>
                        <div class="mb-2">
                            <label for="motivocambio">Motivo de la Finalización/Interrupción</label>
                            <textarea name="motivocambio" id="motivocambio" class="form-control" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-sm btn-subirarchivos">GUARDAR</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const buscarInput = document.getElementById('buscarTramiteId');
            const tabla = document.getElementById('resultadoTramite').querySelector('tbody');
            buscarInput.addEventListener('keyup', function(e) {
            if(e.key !== 'Enter') return;
                const id = this.value.trim();
                if(!id) return tabla.innerHTML = '';
                fetch("{{ url('tramites/buscarpendiente') }}/" + id)
                .then(res => res.json())
                .then(data => {
                    tabla.innerHTML = '';
                    if(!data) return;
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${data.id}</td>
                        <td>${data.tramite}</td>
                        <td>${data.clienteitaid}</td>
                        <td>${data.clienteitanombre}</td>
                        <td>${data.apoderadoasignado}</td>
                        <td>${data.fechaasignacion}</td>
                        <td>${data.ciudad}</td>
                    `;
                    tabla.appendChild(row);
                    document.getElementById('tramiteid').value = data.id;
                    document.getElementById('clienteid').value = data.clienteitaid;
                    document.getElementById('clientenombre').value = data.clienteitanombre;
                    document.getElementById('apoderadoanterior').value = data.apoderadoasignado;
                    document.getElementById('fechaasignacionanterior').value = data.fechaasignacion;
                });
            });
        });
    </script>
    <h1>TRÁMITES PARA LA GESTORA PÚBLICA</h1>
    @stop

    @section('css')
    <link rel="stylesheet" href="{{ asset('css/tramitesgestora.css') }}">
    <style>
        #tablaTramites2 thead th {
            position: sticky;
            top: 0;
            background-color: #f8fdf2;
            z-index: 10;
        }
        #tablaTramites thead th {
            position: sticky;
            top: 0;
            background-color: #f8fdf2;
            z-index: 10;
        }
        #tablaProgramaciones thead th {
            position: sticky;
            top: 0;
            background-color: #f8fdf2;
            z-index: 10;
        }
        #tablaTramites3 thead th {
            position: sticky;
            top: 0;
            background-color: #f8fdf2;
            z-index: 10;
        }
        #tablaTramites4 thead th {
            position: sticky;
            top: 0;
            background-color: #f8fdf2;
            z-index: 10;
        }
        .table-responsive {
            max-height: 65vh;
            overflow-y: auto;
        }
        .btn-vertramite {
            background-color: #ffffff;
            color: #faa625;
            border-color: #faa625;
            border-radius: 5px;
            padding: 2px 5px;
        }
        .btn-vertramite:hover {
            background-color: #faa625;
            color: #ffffff;
        }
        .btn-verhistoria {
            background-color: #ffffff;
            color: #226acf;
            border-color: #226acf;
            border-radius: 5px;
            padding: 2px 5px;
        }
        .btn-verhistoria:hover {
            background-color: #226acf;
            color: #ffffff;
        }
        .btn-verrequisito {
            background-color: #ffffff;
            color: #8c28f0;
            border-color: #8c28f0;
            border-radius: 5px;
            padding: 2px 5px;
        }
        .btn-verrequisito:hover {
            background-color: #8c28f0;
            color: #ffffff;
        }
        .btn-procedimientos {
            background-color:  #ffffff;
            color: #faa625;
            border-color: #faa625;
            border-radius: 5px;
            padding: 2px 5px;
        }
        .btn-procedimientos:hover {
            background-color: #faa625;
            color: #ffffff;
        }
        .btn-tramitesfinalizados {
            background-color: #ffffff;
            color: #faa625;
            border-color: #faa625;
            border-radius: 5px;
            padding: 5px 10px;
            margin-right: 5px;
        }
        .btn-tramitesfinalizados:hover {
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
            }, 3000);
        </script>
    @endif

    <div class="card">
        <div class="card-header">
            <ul class="nav nav-tabs card-header-tabs" id="myTabs">
                <li class="nav-item">
                    <a class="nav-link active" id="tab-7" data-toggle="tab" href="#tab-content-7" role="tab" aria-controls="tab-content-7" aria-selected="true">
                        AGENDAMIENTO
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="tab-3" data-toggle="tab" href="#tab-content-3" role="tab" aria-controls="tab-content-3" aria-selected="true">
                        NO INICIADO
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="tab-1" data-toggle="tab" href="#tab-content-1" role="tab" aria-controls="tab-content-1" aria-selected="true">
                        INICIADO
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="tab-6" data-toggle="tab" href="#tab-content-6" role="tab" aria-controls="tab-content-6" aria-selected="true">
                        PROGRAMACIONES
                    </a>
                </li>
                @php
                    $roles = auth()->user()->roles->pluck('name')
                                ->map(fn($r) => strtoupper(trim($r)))
                                ->toArray();

                    $esEjecutivo = in_array('EJECUTIVO PRESTACIONES', $roles);
                @endphp
                @if($esEjecutivo)
                    <li class="nav-item">
                        <a class="nav-link" id="tab-9" data-toggle="tab" href="#tab-content-9" role="tab" aria-controls="tab-content-9" aria-selected="true">
                            ALERTAS VENCIMIENTO
                        </a>
                    </li>
                @endif
            </ul>
        </div>
        <script>
            document.addEventListener("DOMContentLoaded", function () {
                document.querySelectorAll('a[data-toggle="tab"]').forEach(tab => {
                    tab.addEventListener('click', function () {
                        localStorage.setItem('pestana_activa', this.getAttribute('href'));
                    });
                });
                let pestana = localStorage.getItem('pestana_activa');
                if (pestana) {
                    const tabElement = document.querySelector(`a[href="${pestana}"]`);
                    if (tabElement) {
                        new bootstrap.Tab(tabElement).show();
                    }
                }
            });
        </script>
        <div class="card-body">
            <div class="tab-content" id="myTabContent">
                {{-- AGENDAMIENTO --}}
                <div class="tab-pane fade show active" id="tab-content-7" role="tabpanel" aria-labelledby="tab-7">
                    <div class="card">
                        <div class="card-body">
                            <form action="{{ route('admin.tramites.guardaragendamiento') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <h5 class="text-center d-block w-100" style="margin-bottom: 10px;">NUEVO AGENDAMIENTO</h5>
                                    <div class="col-lg-2">
                                        <label for="cliente_id">Cliente_Nombre</label>
                                        <input list="lista_clientes" id="cliente_id" class="form-control" onchange="mostrarClienteId(this)" required>
                                        <datalist id="lista_clientes">
                                            @foreach ($todosclientes as $cliente)
                                                <option data-id="{{ $cliente->id }}" value="{{ $cliente->nombrecompleto }}"></option>
                                            @endforeach
                                        </datalist>
                                        <input type="hidden" name="clientenombre2" id="clientenombre2">
                                    </div>

                                    <div class="col-lg-1">
                                        <label for="cliente_id_visible">Cliente_ID</label>
                                        <input type="text" name="clienteidvisible" id="cliente_id_visible" class="form-control no-edit" required>
                                    </div>
                                    <style>
                                        .no-edit {
                                            pointer-events: none;
                                            background-color: #e9ecef;
                                        }
                                    </style>
                                    <script>
                                        function mostrarClienteId(input) {
                                            const valor = input.value;
                                            const opciones = document.querySelectorAll("#lista_clientes option");
                                            let idSeleccionado = "";

                                            opciones.forEach(opt => {
                                                if (opt.value === valor) {
                                                    idSeleccionado = opt.dataset.id;
                                                }
                                            });
                                            document.getElementById('cliente_id_visible').value = idSeleccionado;
                                            document.getElementById('clientenombre2').value = valor;
                                        }
                                    </script>
                                    <div class="col-lg-2">
                                        <label for="tramite">Trámite</label>
                                        <select name="tramite" id="tramite" class="form-control" required>
                                            <option value="">Seleccione...</option>
                                            <option value="INVALIDEZ">INVALIDEZ</option>
                                            <option value="APELACIÓN">APELACIÓN</option>
                                            <option value="SEGUNDA SOLICITUD">SEGUNDA SOLICITUD</option>
                                            <option value="APELACIÓN SEGUNDA SOLICITUD">APELACIÓN SEGUNDA SOLICITUD</option>
                                            <option value="TERCERA SOLICITUD">TERCERA SOLICITUD</option>
                                            <option value="APELACIÓN TERCERA SOLICITUD">APELACIÓN TERCERA SOLICITUD</option>
                                            <option value="RECALIFICACIÓN">RECALIFICACIÓN</option>
                                            <option value="APELACIÓN DE RECALIFICACIÓN">APELACIÓN DE RECALIFICACIÓN</option>
                                            <option value="RECALIFICACIÓN SEGUNDA SOLICITUD">RECALIFICACIÓN SEGUNDA SOLICITUD</option>
                                            <option value="APELACIÓN DE RECALIFICACIÓN SEGUNDA SOLICITUD">APELACIÓN DE RECALIFICACIÓN SEGUNDA SOLICITUD</option>
                                            <option value="JUBILACIÓN">JUBILACIÓN</option>
                                            <option value="PENSIÓN POR MUERTE">PENSIÓN POR MUERTE</option>
                                            <option value="MASA HEREDITARIA">MASA HEREDITARIA</option>
                                            <option value="RETIRO DE APORTES TOTAL">RETIRO DE APORTES TOTAL</option>
                                            <option value="RETIRO DE APORTES PARCIAL">RETIRO DE APORTES PARCIAL</option>
                                            <option value="COMPENSACIÓN DE COTIZACIONES (SENASIR)">COMPENSACIÓN DE COTIZACIONES (SENASIR)</option>
                                            <option value="INCLUSIÓN CC (GESTORA)">INCLUSIÓN CC (GESTORA)</option>
                                            <option value="PENSIÓN POR MUERTE CON DERIVACIÓN A RETIRO DE APORTES">PENSIÓN POR MUERTE CON DERIVACIÓN A RETIRO DE APORTES</option>
                                        </select>
                                    </div>
                                    <div class="col-lg-2">
                                        <label for="fecha">Fecha</label>
                                        <input type="date" name="fechaprogramacion" id="fecha" class="form-control" required>
                                    </div>
                                    <div class="col-lg-2">
                                        <label for="hora">Hora</label>
                                        <input type="time" name="horaprogramacion" id="hora" class="form-control" required>
                                    </div>
                                    <div class="col-lg-2">
                                        <label for="documento">Documento</label>
                                        <input type="file" name="documentoagendamiento" id="documento" class="form-control" required>
                                    </div>
                                    <div class="col-lg-1">
                                        <label for="">Acción</label>
                                        <button type="submit" class="btn btn-sm btn-subirarchivos">GUARDAR</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <h5 class="text-center d-block w-100" style="margin-bottom: 10px;">LISTA DE AGENDAMIENTOS</h5>
                            <div class="table-responsive">
                                <form action="{{ route('admin.tramites.confirmarasistencia') }}" method="POST">
                                    @csrf
                                    <div class="d-flex align-items-center mb-3">
                                        <input 
                                            type="text" 
                                            id="buscarCliente" 
                                            class="form-control form-control-sm w-25 shadow-sm" 
                                            placeholder="BUSCAR POR ID O NOMBRE DEL CLIENTE..."
                                        >
                                    </div>
                                    <table class="table table-bordered table-sm" id="tablaAgendamientos"> 
                                        <thead class="table-secondary text-center">
                                            <tr>
                                                <th>ID_Reg.</th>
                                                <th>Cliente_ID</th>
                                                <th>Cliente_Nombre</th>
                                                <th>Trámite</th>
                                                <th>Fecha</th>
                                                <th>Hora</th>
                                                <th>Doc.</th>
                                                <th>Reprog.</th>
                                                <th>Asistencia</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($agendamientosNoAsistidos as $item)
                                                <tr class="text-center">
                                                    <td>{{ $item->id }}</td>
                                                    <td>{{ $item->clienteid }}</td>
                                                    <td>{{ $item->clientenombre }}</td>
                                                    <td>{{ $item->tramite }}</td>
                                                    <td>{{ \Carbon\Carbon::parse($item->fechaprogramacion)->format('d-m-Y') }}</td>
                                                    <td>{{ \Carbon\Carbon::parse($item->horaprogramacion)->format('H:i') }}</td>
                                                    <td>
                                                        <a href="{{ asset('agendamiento/'.$item->clienteid.'/'.$item->tramite.'/'.$item->documentoagendamiento) }}" target="_blank" class="btn btn-verdocumento btn-sm">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                    </td>
                                                    <td>
                                                        <button type="button" class="btn btn-verdocumento btn-sm" data-toggle="modal" data-target="#reprogramarModal{{ $item->id }}">
                                                            REPROG.
                                                        </button>
                                                    </td>
                                                    <td class="text-center">
                                                        <input type="checkbox" name="asistencia[]" value="{{ $item->id }}">
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    <script>
                                        const inputBuscar = document.getElementById('buscarCliente');
                                        const tabla = document.getElementById('tablaAgendamientos').getElementsByTagName('tbody')[0];

                                        inputBuscar.addEventListener('keyup', function() {
                                            const filtro = this.value.toLowerCase();
                                            const filas = tabla.getElementsByTagName('tr');

                                            for (let i = 0; i < filas.length; i++) {
                                                const clienteID = filas[i].cells[1].textContent.toLowerCase();
                                                const clienteNombre = filas[i].cells[2].textContent.toLowerCase();

                                                if (clienteID.includes(filtro) || clienteNombre.includes(filtro)) {
                                                    filas[i].style.display = '';
                                                } else {
                                                    filas[i].style.display = 'none';
                                                }
                                            }
                                        });
                                    </script>

                                    <div class="d-flex justify-content-between mt-3">
                                        @if($agendamientosAsistidos->count() > 0)
                                            <button type="button" class="btn btn-sm btn-adjuntosrespuestas" data-toggle="modal" data-target="#modalAsistidos">
                                                VER AGEND. ASISTIDOS
                                            </button>
                                        @else
                                            <div></div>
                                        @endif
                                        <button type="submit" class="btn btn-sm btn-subirarchivos">
                                            CONFIRMAR ASIST.
                                        </button>
                                    </div>

                                    <div class="modal fade" id="modalAsistidos" tabindex="-1" aria-labelledby="modalAsistidosLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-xl">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="modalAsistidosLabel">AGENDAMIENTOS ASISTIDOS</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="d-flex align-items-center mb-3">
                                                        <input 
                                                            type="text" 
                                                            id="buscarCliente2" 
                                                            class="form-control form-control-sm w-25 shadow-sm" 
                                                            placeholder="BUSCAR POR ID O NOMBRE DEL CLIENTE..."
                                                        >
                                                    </div>
                                                    <div class="table-responsive">
                                                        <table class="table table-bordered table-striped table-sm" id="tablaAgendamientos2">
                                                            <thead class="table-secondary text-center">
                                                                <tr>
                                                                    <th>ID_Reg.</th>
                                                                    <th>Cliente_ID</th>
                                                                    <th>Cliente_Nombre</th>
                                                                    <th>Trámite</th>
                                                                    <th>Fecha</th>
                                                                    <th>Hora</th>
                                                                    <th>Doc.</th>
                                                                    <th>Motivo_Reprog.</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach ($agendamientosAsistidos as $item)
                                                                    <tr class="text-center">
                                                                        <td>{{ $item->id }}</td>
                                                                        <td>{{ $item->clienteid }}</td>
                                                                        <td>{{ $item->clientenombre }}</td>
                                                                        <td>{{ $item->tramite }}</td>
                                                                        <td>{{ \Carbon\Carbon::parse($item->fechaprogramacion)->format('d-m-Y') }}</td>
                                                                        <td>{{ \Carbon\Carbon::parse($item->horaprogramacion)->format('H:i') }}</td>
                                                                        <td>
                                                                            <a href="{{ asset('agendamiento/'.$item->clienteid.'/'.$item->tramite.'/'.$item->documentoagendamiento) }}" target="_blank" class="btn btn-verdocumento btn-sm">
                                                                                <i class="fas fa-eye"></i>
                                                                            </a>
                                                                        </td>
                                                                        <td>{{ $item->motivoreprogramacion ?? 'NINGUNO' }}</td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    <script>
                                                        document.addEventListener('DOMContentLoaded', function() {
                                                            const inputBuscar = document.getElementById('buscarCliente2');
                                                            const tabla = document.getElementById('tablaAgendamientos2').getElementsByTagName('tbody')[0];

                                                            if(inputBuscar && tabla) {
                                                                inputBuscar.addEventListener('keyup', function() {
                                                                    const filtro = this.value.toLowerCase();
                                                                    const filas = tabla.getElementsByTagName('tr');

                                                                    for (let i = 0; i < filas.length; i++) {
                                                                        const clienteID = filas[i].cells[1].textContent.toLowerCase();
                                                                        const clienteNombre = filas[i].cells[2].textContent.toLowerCase();

                                                                        filas[i].style.display = (clienteID.includes(filtro) || clienteNombre.includes(filtro)) ? '' : 'none';
                                                                    }
                                                                });
                                                            }
                                                        });
                                                    </script>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                                @foreach ($agendamientos as $item)
                                    <div class="modal fade" id="reprogramarModal{{ $item->id }}" tabindex="-1">
                                        <div class="modal-dialog">
                                            <form action="{{ route('admin.tramites.reprogramaragendamiento', $item->id) }}" method="POST" enctype="multipart/form-data">
                                                @csrf
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">REPROGRAMAR AGENDAMIENTO</h5>
                                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="row">
                                                            <div class="form-group col-lg-6">
                                                                <label>Nueva Fecha</label>
                                                                <input type="date" name="fechaprogramacion" class="form-control" value="{{ $item->fechaprogramacion }}">
                                                            </div>
                                                            <div class="form-group col-lg-6">
                                                                <label>Nueva Hora</label>
                                                                <input type="time" name="horaprogramacion" class="form-control" value="{{ $item->horaprogramacion }}">
                                                            </div>
                                                            <div class="form-group col-lg-12">
                                                                <label>Nuevo Documento</label>
                                                                <input type="file" name="documentoagendamiento" class="form-control">
                                                            </div>
                                                            <div class="form-group col-lg-12">
                                                                <label>Motivo Reprogramación</label>
                                                                <textarea name="motivoreprogramacion" class="form-control">{{ $item->motivoreprogramacion }}</textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="submit" class="btn btn-subirarchivos">REPROGRAMAR</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                {{-- NO INICIADO --}}
                <div class="tab-pane fade" id="tab-content-3" role="tabpanel" aria-labelledby="tab-3">
                    <div class="d-flex align-items-center mb-3">
                        <input 
                            type="text" 
                            id="buscadorTramites" 
                            class="form-control form-control-sm w-25 shadow-sm" 
                            placeholder="BUSCAR POR ID O NOMBRE DEL CLIENTE..."
                        >
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-sm" id="tablaTramites">
                            <thead style="background-color: #f8fdf2">
                                <tr>
                                    <th class="text-center">Iniciar</th>
                                    <th>ID_Trámite</th>
                                    <th>Trámite</th>
                                    <th>Cliente_ID</th>
                                    <th>Cliente_Nombre</th>
                                    <th>Fecha_Bateria</th>
                                    <th>Apoderado_Asignado</th>
                                    <th>Agendamiento</th>
                                    <th>Fecha_Hora_Asig.</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($todostramitesnoiniciado as $tramite)
                                    <tr>
                                        @php
                                            $rutasTramites = [
                                                'INVALIDEZ' => 'admin.tramites.procinvalidez',
                                                'APELACIÓN' => 'admin.tramites.procapelacion',
                                                'SEGUNDA SOLICITUD' => 'admin.tramites.procsegundasolicitud',
                                                'APELACIÓN SEGUNDA SOLICITUD' => 'admin.tramites.procapelsegsolicitud',
                                                'TERCERA SOLICITUD' => 'admin.tramites.proctercerasolicitud',
                                                'APELACIÓN TERCERA SOLICITUD' => 'admin.tramites.procapeltercersolicitud',
                                                'RECALIFICACIÓN' => 'admin.tramites.procrecalificacion',
                                                'APELACIÓN DE RECALIFICACIÓN' => 'admin.tramites.procapelrecalificacion',
                                                'RECALIFICACIÓN SEGUNDA SOLICITUD' => 'admin.tramites.procrecalsegsolicitud',
                                                'APELACIÓN DE RECALIFICACIÓN SEGUNDA SOLICITUD' => 'admin.tramites.procapelrecalsegsolicitud',
                                                'JUBILACIÓN' => 'admin.tramites.procjubilacion',
                                                'PENSIÓN POR MUERTE' => 'admin.tramites.procpensionmuerte',
                                                'RETIRO DE APORTES TOTAL' => 'admin.tramites.procretiroaportestotal',
                                                'RETIRO DE APORTES PARCIAL' => 'admin.tramites.procretiroaportesparcial',
                                                'MASA HEREDITARIA' => 'admin.tramites.procmasahereditaria',
                                                'COMPENSACIÓN DE COTIZACIONES (SENASIR)' => 'admin.tramites.proccompensacionsenasir',
                                                'PENSIÓN POR MUERTE CON DERIVACIÓN A RETIRO DE APORTES' => 'admin.tramites.procpensionderivretiro',
                                            ];
                                        @endphp
                                        <td class="text-center">
                                            @if(isset($rutasTramites[$tramite->tramite]))
                                                <a class="btn btn-sm fas fa-file-archive btn-editar" title="INICIAR TRÁMITE"
                                                href="{{ route($rutasTramites[$tramite->tramite], ['cliente' => $tramite->clienteitaid]) }}">
                                                </a>
                                            @endif
                                        </td>
                                        <td>{{ $tramite->id }}</td>
                                        <td>{{ $tramite->tramite }}</td>
                                        <td>{{ $tramite->clienteitaid }}</td>
                                        <td>{{ $tramite->clienteitanombre }}</td>
                                        <td>
                                            @if(is_null($tramite->fechabateria))
                                                @if(in_array($tramite->tramite, [
                                                    'INVALIDEZ',
                                                    'APELACIÓN',
                                                    'SEGUNDA SOLICITUD',
                                                    'APELACIÓN SEGUNDA SOLICITUD',
                                                    'TERCERA SOLICITUD',
                                                    'APELACIÓN TERCERA SOLICITUD',
                                                    'RECALIFICACIÓN',
                                                    'APELACIÓN DE RECALIFICACIÓN',
                                                    'RECALIFICACIÓN SEGUNDA SOLICITUD',
                                                    'APELACIÓN DE RECALIFICACIÓN SEGUNDA SOLICITUD']))
                                                    PENDIENTE
                                                @else
                                                    NO REQUIERE
                                                @endif
                                            @else
                                                {{ \Carbon\Carbon::parse($tramite->fechabateria)->format('d-m-Y') }}
                                            @endif
                                        </td>
                                        <td>{{ $tramite->apoderadoasignado }}</td>
                                        <td>
                                            @if($tramite->asistencia === 'SI')
                                                <span class="badge badge-success">ASISTENCIA COMPLETA</span>
                                            @elseif($tramite->asistencia === 'NO')
                                                <span class="badge badge-secondary">ASISTENCIA PENDIENTE</span>
                                            @else
                                                <span class="badge badge-danger">NO AGENDADO</span>
                                            @endif
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($tramite->fechaasignacion)->format('d-m-Y / H:i') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const buscador = document.getElementById('buscadorTramites');
                            const tabla = document.getElementById('tablaTramites');
                            const filas = tabla.getElementsByTagName('tbody')[0].getElementsByTagName('tr');

                            buscador.addEventListener('keyup', function() {
                                const filtro = this.value.toLowerCase();
                                Array.from(filas).forEach(function(fila) {
                                    const clienteId = fila.cells[3].textContent.toLowerCase();
                                    const clienteNombre = fila.cells[4].textContent.toLowerCase();
                                    if (clienteId.includes(filtro) || clienteNombre.includes(filtro)) {
                                        fila.style.display = '';
                                    } else {
                                        fila.style.display = 'none';
                                    }
                                });
                            });
                        });
                    </script>
                </div>

                {{-- INICIADO --}}
                <div class="tab-pane fade" id="tab-content-1" role="tabpanel" aria-labelledby="tab-1">
                    <div class="d-flex align-items-center mb-3">
                        <input 
                            type="text" 
                            id="buscadorTramites2" 
                            class="form-control form-control-sm w-25 shadow-sm" 
                            placeholder="BUSCAR POR ID O NOMBRE DEL CLIENTE..."
                        >
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-sm" id="tablaTramites2">
                            <thead style="background-color: #f8fdf2">
                                <tr>
                                    <th class="text-center">Ver</th>
                                    <th>ID_Trámite</th>
                                    <th>Trámite</th>
                                    <th>Cliente_ID</th>
                                    <th>Cliente_Nombre</th>
                                    <th>Fecha_Bateria</th>
                                    <th>Apoderado</th>
                                    <th>Fecha_Hora_Asig.</th>
                                    <th>Inicio_Trámite</th>
                                    <th>Nivel_Procedimiento</th>
                                    <th>Sub_Procedimiento</th>
                                    <th>Fecha_Retorno</th>
                                    <th>Cuenta_Regresiva</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($todostramitesiniciado as $tramite)
                                    <tr>
                                        @php
                                            $rutasTramites = [
                                                'INVALIDEZ' => 'admin.tramites.procinvalidez',
                                                'APELACIÓN' => 'admin.tramites.procapelacion',
                                                'SEGUNDA SOLICITUD' => 'admin.tramites.procsegundasolicitud',
                                                'APELACIÓN SEGUNDA SOLICITUD' => 'admin.tramites.procapelsegsolicitud',
                                                'TERCERA SOLICITUD' => 'admin.tramites.proctercerasolicitud',
                                                'APELACIÓN TERCERA SOLICITUD' => 'admin.tramites.procapeltercersolicitud',
                                                'RECALIFICACIÓN' => 'admin.tramites.procrecalificacion',
                                                'APELACIÓN DE RECALIFICACIÓN' => 'admin.tramites.procapelrecalificacion',
                                                'RECALIFICACIÓN SEGUNDA SOLICITUD' => 'admin.tramites.procrecalsegsolicitud',
                                                'APELACIÓN DE RECALIFICACIÓN SEGUNDA SOLICITUD' => 'admin.tramites.procapelrecalsegsolicitud',
                                                'JUBILACIÓN' => 'admin.tramites.procjubilacion',
                                                'PENSIÓN POR MUERTE' => 'admin.tramites.procpensionmuerte',
                                                'RETIRO DE APORTES TOTAL' => 'admin.tramites.procretiroaportestotal',
                                                'RETIRO DE APORTES PARCIAL' => 'admin.tramites.procretiroaportesparcial',
                                                'MASA HEREDITARIA' => 'admin.tramites.procmasahereditaria',
                                                'COMPENSACIÓN DE COTIZACIONES (SENASIR)' => 'admin.tramites.proccompensacionsenasir',
                                                'PENSIÓN POR MUERTE CON DERIVACIÓN A RETIRO DE APORTES' => 'admin.tramites.procpensionderivretiro',
                                            ];
                                        @endphp
                                        <td class="text-center">
                                            @if(isset($rutasTramites[$tramite->tramite]))
                                                <a class="btn btn-sm fas fa-file-archive btn-editar" title="VER TRÁMITE"
                                                href="{{ route($rutasTramites[$tramite->tramite], ['cliente' => $tramite->clienteitaid]) }}">
                                                </a>
                                            @endif
                                            <a class="btn btn-sm btn-procedimientos" title="VER PROCEDIMIENTOS"
                                                data-tramite-id="{{ $tramite->id }}"
                                                data-tramite-nombre="{{ $tramite->clienteitanombre }}"
                                                data-tramite-tipo="{{ $tramite->tramite }}"
                                                data-procedimientos='@json($tramite->procedimientos)'>
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                        <td>{{ $tramite->id }}</td>
                                        <td>{{ $tramite->tramite }}</td>
                                        <td>{{ $tramite->clienteitaid }}</td>
                                        <td>{{ $tramite->clienteitanombre }}</td>
                                        <td>
                                            @if(is_null($tramite->fechabateria))
                                                @if(in_array($tramite->tramite, [
                                                    'INVALIDEZ',
                                                    'APELACIÓN',
                                                    'SEGUNDA SOLICITUD',
                                                    'APELACIÓN SEGUNDA SOLICITUD',
                                                    'TERCERA SOLICITUD',
                                                    'APELACIÓN TERCERA SOLICITUD',
                                                    'RECALIFICACIÓN',
                                                    'APELACIÓN DE RECALIFICACIÓN',
                                                    'RECALIFICACIÓN SEGUNDA SOLICITUD',
                                                    'APELACIÓN DE RECALIFICACIÓN SEGUNDA SOLICITUD']))
                                                    PENDIENTE
                                                @else
                                                    NO REQUIERE
                                                @endif
                                            @else
                                                {{ \Carbon\Carbon::parse($tramite->fechabateria)->format('d-m-Y') }}
                                            @endif
                                        </td>
                                        <td>{{ $tramite->apoderadoasignado }}</td>
                                        <td>{{ \Carbon\Carbon::parse($tramite->fechaasignacion)->format('d-m-Y / H:i') }}</td>
                                        <td>
                                            {{ optional($tramite->procedimientos->first())->created_at?->format('d-m-Y') ?? '---' }}
                                        </td>
                                        <td>
                                            {{ optional($tramite->procedimientos->last())->nivelprocedimiento ?? '---' }}
                                        </td>
                                        <td>
                                            @if($tramite->procedimientos->isNotEmpty())
                                                {{ $tramite->procedimientos->last()->subprocedimiento }}
                                                @if(!is_null($tramite->procedimientos->last()->tipocarta))
                                                    - ({{ $tramite->procedimientos->last()->tipocarta }})
                                                @endif
                                            @else
                                                ---
                                            @endif
                                        </td>
                                        <td>
                                            {{ optional($tramite->procedimientos->last())->fecharetorno ?? '---' }}
                                        </td>
                                        <td>
                                            @php
                                                $fechaRetorno = optional($tramite->procedimientos->last())->fecharetorno;
                                            @endphp

                                            @if($fechaRetorno)
                                                @php
                                                    $dias = \Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::parse($fechaRetorno), false);
                                                @endphp
                                                @if($dias >= 0)
                                                    {{ $dias }} {{ $dias == 1 ? 'día' : 'días' }}
                                                @else
                                                    Vencido ({{ abs($dias) }} {{ abs($dias) == 1 ? 'día' : 'días' }})
                                                @endif
                                            @else
                                                ---
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="modal fade" id="modalProcedimientos" tabindex="-1" aria-labelledby="modalProcedimientosLabel" aria-hidden="true">
                        <div class="modal-dialog modal-xl modal-dialog-scrollable">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="modalProcedimientosLabel">Procedimientos</h5>
                                </div>
                                <div class="modal-body">
                                    <table class="table table-sm table-bordered" id="tablaModalProcedimientos">
                                        <thead class="table-secondary">
                                            <tr>
                                                <th>ID</th>
                                                <th>Nivel Procedimiento</th>
                                                <th>Sub Procedimiento</th>
                                                <th>Fecha Registro</th>
                                                <th>Fecha Retorno</th>
                                                <th class="text-center">Archivo</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                        </tbody>
                                    </table>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-sm btn-outline-danger" data-dismiss="modal">Cerrar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- <script>
                        const BASE_TRAMITE_URL = "{{ url('/tramitesclientesita') }}";

                        document.addEventListener('DOMContentLoaded', function () {
                            const modal = new bootstrap.Modal(document.getElementById('modalProcedimientos'));
                            const modalTitle = document.getElementById('modalProcedimientosLabel');
                            const tbody = document.querySelector('#tablaModalProcedimientos tbody');

                            document.querySelectorAll('.btn-procedimientos').forEach(button => {
                                button.addEventListener('click', function () {
                                    const procedimientos = JSON.parse(this.dataset.procedimientos);

                                    tbody.innerHTML = '';

                                    procedimientos.forEach(proc => {
                                        const clienteNombre = this.dataset.tramiteNombre;
                                        const tramiteTipo = this.dataset.tramiteTipo;
                                        const tramiteId = this.dataset.tramiteId;

                                        modalTitle.textContent = `${tramiteTipo} DE ${clienteNombre} (ID: ${tramiteId})`;

                                        const tieneDocumento = proc.document && proc.document.trim() !== '';
                                        const urlDocumento = tieneDocumento
                                            ? `${BASE_TRAMITE_URL}/${proc.clienteid}/${proc.tramite}/${proc.nivelprocedimiento}/${proc.document}`
                                            : '';

                                        const botonDocumento = tieneDocumento
                                            ? `<a href="${urlDocumento}" class="btn btn-sm btn-verdoc" target="_blank">VER</a>`
                                            : `---`;
                                        tbody.insertAdjacentHTML('beforeend', `
                                            <tr>
                                                <td>${proc.id ?? '---'}</td>
                                                <td>${proc.nivelprocedimiento ?? '---'}</td>
                                                <td>${proc.subprocedimiento ?? '---'}${proc.tipocarta ? ' - ' + proc.tipocarta : ''}</td>
                                                <td>${proc.fechasubida ?? '---'}</td>
                                                <td>${proc.fecharetorno ?? '---'}</td>
                                                <td class="text-center">${botonDocumento}</td>
                                            </tr>
                                        `);
                                    });

                                    modal.show();
                                });
                            });
                        });
                    </script> --}}
                    <script>
                    const BASE_TRAMITE_URL = "{{ url('/tramitesclientesita') }}";

                    document.addEventListener('DOMContentLoaded', function () {
                        const modal = new bootstrap.Modal(document.getElementById('modalProcedimientos'));
                        const modalTitle = document.getElementById('modalProcedimientosLabel');
                        const tbody = document.querySelector('#tablaModalProcedimientos tbody');

                        const tiposEspeciales = [
                            'SOLICITUD',
                            'ADJUNTO / RESPUESTA',
                            'CARTA / RECLAMO',
                            'MISIVA LIBRE'
                        ];

                        document.querySelectorAll('.btn-procedimientos').forEach(button => {
                            button.addEventListener('click', function () {

                                let procedimientos = [];

                                try {
                                    procedimientos = JSON.parse(this.dataset.procedimientos);
                                } catch (e) {
                                    console.error('Error al parsear procedimientos:', e);
                                    alert('Error al cargar los procedimientos');
                                    return;
                                }

                                tbody.innerHTML = '';

                                if (!procedimientos.length) {
                                    tbody.innerHTML = `
                                        <tr>
                                            <td colspan="6" class="text-center">No hay procedimientos</td>
                                        </tr>
                                    `;
                                    modal.show();
                                    return;
                                }

                                procedimientos.forEach(proc => {

                                    const clienteNombre = this.dataset.tramiteNombre;
                                    const tramiteTipo = this.dataset.tramiteTipo;
                                    const tramiteId = this.dataset.tramiteId;

                                    modalTitle.textContent = `${tramiteTipo} DE ${clienteNombre} (ID: ${tramiteId})`;

                                    const tieneDocumento = proc.document && proc.document.trim() !== '';

                                    // NORMALIZAR TIPO (MUY IMPORTANTE)
                                    const tipoNormalizado = (proc.tipo ?? '')
                                        .toUpperCase()
                                        .trim();

                                    const carpetasPorTipo = {
                                        'SOLICITUD': 'SOLICITUDES',
                                        'ADJUNTO / RESPUESTA': 'ADJUNTOS Y RESPUESTAS',
                                        'CARTA / RECLAMO': 'CARTAS Y RECLAMOS',
                                        'MISIVA LIBRE': 'MISIVAS LIBRES'
                                    };

                                    const carpeta = carpetasPorTipo[tipoNormalizado]
                                        ?? proc.nivelprocedimiento;


                                    const urlDocumento = tieneDocumento
                                        ? `${BASE_TRAMITE_URL}/${proc.clienteid}/${proc.tramite}/${carpeta}/${proc.document}`
                                        : '';

                                    const botonDocumento = tieneDocumento
                                        ? `<a href="${urlDocumento}" class="btn btn-sm btn-verdoc" target="_blank">VER</a>`
                                        : `---`;

                                    tbody.insertAdjacentHTML('beforeend', `
                                        <tr>
                                            <td>${proc.id ?? '---'}</td>
                                            <td>${proc.nivelprocedimiento ?? '---'}</td>
                                            <td>${proc.subprocedimiento ?? '0'}${proc.tipocarta ? ' - ' + proc.tipocarta : ''}</td>
                                            <td>${proc.fechasubida ?? '---'}</td>
                                            <td>${proc.fecharetorno ?? '---'}</td>
                                            <td class="text-center">${botonDocumento}</td>
                                        </tr>
                                    `);
                                });

                                modal.show();
                            });
                        });
                    });
                    </script>


                    <style>
                        .btn-verdoc {
                            background-color:  #ffffff;
                            color: #faa625;
                            border-color: #faa625;
                            border-radius: 5px;
                            padding: 1px 5px;
                        }
                        .btn-verdoc:hover {
                            background-color: #faa625;
                            color: #ffffff;
                        }
                    </style>
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const buscador = document.getElementById('buscadorTramites2');
                            const tabla = document.getElementById('tablaTramites2');
                            const filas = tabla.getElementsByTagName('tbody')[0].getElementsByTagName('tr');

                            buscador.addEventListener('keyup', function() {
                                const filtro = this.value.toLowerCase();
                                Array.from(filas).forEach(function(fila) {
                                    const clienteId = fila.cells[3].textContent.toLowerCase();
                                    const clienteNombre = fila.cells[4].textContent.toLowerCase();
                                    if (clienteId.includes(filtro) || clienteNombre.includes(filtro)) {
                                        fila.style.display = '';
                                    } else {
                                        fila.style.display = 'none';
                                    }
                                });
                            });
                        });
                    </script>
                </div>

                {{-- PROGRAMACIONES --}}
                <div class="tab-pane fade" id="tab-content-6" role="tabpanel" aria-labelledby="tab-6">
                    <div class="d-flex align-items-center mb-3">
                        <input 
                            type="text" 
                            id="buscadorProgramaciones" 
                            class="form-control form-control-sm w-25 shadow-sm" 
                            placeholder="BUSCAR POR ID, NOMBRE DEL CLIENTE O ESTADO..."
                        >
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered" id="tablaProgramaciones">
                            <thead style="background-color: #f8fdf2" class="table-sm">
                                <tr>
                                    <th>Cliente_ID</th>
                                    <th>Cliente_Nombre</th>
                                    <th>Tipo</th>
                                    <th>Trámite</th>
                                    <th>Última Fecha</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($progcajapetrolera as $prog)
                                    <tr>
                                        <td>{{ $prog->idcliente }}</td>
                                        <td>{{ $prog->cliente }}</td>
                                        <td>{{ $prog->tipo }}</td>
                                        <td>{{ $prog->tramite }}</td>
                                        <td>
                                            @if ($prog->ultima_fecha && $prog->ultima_fecha !== '—')
                                                {{ \Carbon\Carbon::parse($prog->ultima_fecha)->format('d-m-Y - H:i') }}
                                            @else
                                                —
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                                $colores = [
                                                    'SEGUIMIENTO A CLIENTE' => 'badge-success',
                                                    'PROGRAMACIÓN PENDIENTE' => 'badge-primary',
                                                    'PROGRAMACIÓN RETRASADA' => 'badge-warning',
                                                    'PROGRAMACIÓN EN MORA' => 'badge-danger',
                                                ];
                                            @endphp

                                            <span class="badge {{ $colores[$prog->estado] ?? 'badge-secondary' }}">
                                                {{ $prog->estado }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const buscador = document.getElementById('buscadorProgramaciones');
                            const tabla = document.getElementById('tablaProgramaciones');
                            const filas = tabla.getElementsByTagName('tbody')[0].getElementsByTagName('tr');

                            buscador.addEventListener('keyup', function() {
                                const filtro = this.value.toLowerCase();
                                Array.from(filas).forEach(function(fila) {
                                    const clienteId = fila.cells[0].textContent.toLowerCase();
                                    const clienteNombre = fila.cells[1].textContent.toLowerCase()
                                    const estado = fila.cells[5].textContent.toLowerCase();
                                    if (clienteId.includes(filtro) || clienteNombre.includes(filtro) || estado.includes(filtro)) {
                                        fila.style.display = '';
                                    } else {
                                        fila.style.display = 'none';
                                    }
                                });
                            });
                        });
                    </script>
                </div>

                {{-- ALERTAS VENCIMIENTO --}}
                @if($esEjecutivo)
                    <div class="tab-pane fade" id="tab-content-9" role="tabpanel" aria-labelledby="tab-9">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-sm">
                                <thead style="background-color: #f8fdf2">
                                    <tr>
                                        <th class="text-center">Ver</th>
                                        <th>ID_Trámite</th>
                                        <th>Trámite</th>
                                        <th>Cliente_ID</th>
                                        <th>Cliente_Nombre</th>
                                        <th>Nivel_Procedimiento</th>
                                        <th>Sub_Procedimiento</th>
                                        <th>Fecha_Retorno</th>
                                        <th>Dias_Restantes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($tramitesporvencer as $tramite)
                                        <tr>
                                            @php
                                                $rutasTramites = [
                                                    'INVALIDEZ' => 'admin.tramites.procinvalidez',
                                                    'APELACIÓN' => 'admin.tramites.procapelacion',
                                                    'SEGUNDA SOLICITUD' => 'admin.tramites.procsegundasolicitud',
                                                    'APELACIÓN SEGUNDA SOLICITUD' => 'admin.tramites.procapelsegsolicitud',
                                                    'TERCERA SOLICITUD' => 'admin.tramites.proctercerasolicitud',
                                                    'APELACIÓN TERCERA SOLICITUD' => 'admin.tramites.procapeltercersolicitud',
                                                    'RECALIFICACIÓN' => 'admin.tramites.procrecalificacion',
                                                    'APELACIÓN DE RECALIFICACIÓN' => 'admin.tramites.procapelrecalificacion',
                                                    'RECALIFICACIÓN SEGUNDA SOLICITUD' => 'admin.tramites.procrecalsegsolicitud',
                                                    'APELACIÓN DE RECALIFICACIÓN SEGUNDA SOLICITUD' => 'admin.tramites.procapelrecalsegsolicitud',
                                                    'JUBILACIÓN' => 'admin.tramites.procjubilacion',
                                                    'PENSIÓN POR MUERTE' => 'admin.tramites.procpensionmuerte',
                                                    'RETIRO DE APORTES TOTAL' => 'admin.tramites.procretiroaportestotal',
                                                    'RETIRO DE APORTES PARCIAL' => 'admin.tramites.procretiroaportesparcial',
                                                    'MASA HEREDITARIA' => 'admin.tramites.procmasahereditaria',
                                                    'COMPENSACIÓN DE COTIZACIONES (SENASIR)' => 'admin.tramites.proccompensacionsenasir',
                                                    'PENSIÓN POR MUERTE CON DERIVACIÓN A RETIRO DE APORTES' => 'admin.tramites.procpensionderivretiro',
                                                ];
                                            @endphp
                                            <td class="text-center">
                                                @if(isset($rutasTramites[$tramite->tramite]))
                                                    <a class="btn btn-sm fas fa-file-archive btn-editar" title="VER TRÁMITE"
                                                    href="{{ route($rutasTramites[$tramite->tramite], ['cliente' => $tramite->clienteid]) }}">
                                                    </a>
                                                @endif
                                            </td>
                                            <td>{{ $tramite->idtramite }}</td>
                                            <td>{{ $tramite->tramite }}</td>
                                            <td>{{ $tramite->clienteid }}</td>
                                            <td>{{ $tramite->clientenombre }}</td>
                                            <td>{{ $tramite->tipo }} - {{ $tramite->nivelprocedimiento }}</td>
                                            <td>
                                                {{ $tramite->subprocedimiento }}
                                                @if(!empty($tramite->tipocarta))
                                                    - {{ $tramite->tipocarta }}
                                                @endif
                                            </td>
                                            <td>{{ \Carbon\Carbon::parse($tramite->fecharetorno)->format('d-m-Y') }}</td>
                                            <td>
                                                @if($tramite->estado_tiempo == 'FALTAN')
                                                    Faltan {{ $tramite->dias }} días y {{ $tramite->horas }} horas
                                                @else
                                                    Vencido hace {{ $tramite->dias }} días y {{ $tramite->horas }} horas
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="text-center text-muted">
                                                NO HAY ALERTAS POR MOSTRAR
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            </div>
        </div> 
    </div>
@endif
@stop

@section('js')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script type="text/javascript" src="https://jeremyfagis.github.io/dropify/dist/js/dropify.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://jeremyfagis.github.io/dropify/dist/css/dropify.min.css"> 
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropify/0.2.2/css/dropify.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/dropify/0.2.2/js/dropify.min.js"></script>
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
    $(document).ready(function() {
        $('input[name="buscarporfecha"], input[name="buscarporarea"]').on('keyup change', function() {
            var fechaSeleccionada = $('input[name="buscarporfecha"]').val();
            var areaSeleccionada = $('input[name="buscarporarea"]').val();
            var botonBuscar = $('#btn-buscar');
            
            if (fechaSeleccionada.trim() === '' && areaSeleccionada.trim() === '') {
                botonBuscar.prop('disabled', true);
            } else {
                botonBuscar.prop('disabled', false);
            }
        });
    });
</script>

<script>
    function cargarVistaPrevia() {
        var document = document.getElementById('document').files[0];
        if (document) {
        var reader = new FileReader();
        reader.onload = function(e) {
            var previewIframe = document.getElementById('document-preview');
            previewIframe.src = e.target.result;
        };
        reader.readAsDataURL(document);
        }
    }
    document.getElementById('document').addEventListener('change', function() {
        cargarVistaPrevia();
    });
</script>

<script>
    $(document).ready(function() {
        $('.dropify').dropify({
            messages: {
                'default': 'Arrastre y suelte un archivo o haga clic aquí',
                'replace': 'Arrastre y suelte o haga clic para reemplazar',
                'remove': 'Eliminar',
                'error': 'Ooops, algo salió mal.'
            }
        });
    
        $('.dropify').on('dropify.error.fileSize', function(event, element) {
            var maxSize = element.input.files[0].size / (1024 * 1024);
            var errorMessage = 'El archivo es demasiado grande (' + maxSize.toFixed(2) + ' MB máx.).';
            $(element.input).siblings('.dropify-error').text(errorMessage);
        });
    });

    document.getElementById('document').addEventListener('change', function(event) {
        var file = event.target.files[0];
        if (file) {
            var fileURL = URL.createObjectURL(file);
            var previewCard = document.getElementById('preview-card');
            var documentPreview = document.getElementById('document-preview');
    
            previewCard.style.display = 'block';
            documentPreview.src = fileURL;
        } else {
            var previewCard = document.getElementById('preview-card');
            previewCard.style.display = 'none';
            documentPreview.src = '';
        }
    });
</script>

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
