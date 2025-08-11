@extends('adminlte::page')

@section('content_header')
<a class="btn btn-sm float-right btn-azulgrande" href="{{ route('admin.proveedoresservicios.listapersonal') }}">REGRESAR</a>
<a class="btn btn-sm float-right btn-verdegrande" data-toggle="modal" data-target="#ventanaModal">ACCIONES DEL PERSONAL</a>
<h1>INFORMACIÓN DE {{$proveedoresservicios->razonsocial}}</h1>
@stop

@section('css')
{{-- <link rel="stylesheet" href="{{ asset('css/verproveedor.css') }}"> --}}
<link rel="stylesheet" href="{{ asset('css/verproveedor.css') }}?v={{ time() }}">
@stop

@section('js')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script type="text/javascript" src="https://jeremyfagis.github.io/dropify/dist/js/dropify.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://jeremyfagis.github.io/dropify/dist/css/dropify.min.css"> 
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
            <div class="col-lg-4">
                <div class="profile-feed">
                    <div class="d-flex align-items-start profile-feed-item">
                        <div class="form-group col-md-12">
                            <div class="row">
                                <div class="col-md-12">
                                    <strong >DATOS PERSONALES</strong><br>
                                    <div class="table-responsive">
                                        <table style="width: 100%;">
                                            <tbody>
                                                <tr>
                                                    <th>ID</th>
                                                    <td>{{$proveedoresservicios->id}}</td>
                                                </tr>
                                                <tr>
                                                    <th>CI</th>
                                                    <td>{{$proveedoresservicios->ci}}{{$proveedoresservicios->ciexp}}</td>
                                                </tr> 
                                                <tr>
                                                    <th>Ciudad</th>
                                                    <td>{{$proveedoresservicios->ciudad}}</td>
                                                </tr>
                                                <tr>
                                                    <th>Nacionalidad</th>
                                                    <td>{{$proveedoresservicios->nacionalidad}}</td>
                                                </tr>
                                                <tr> 
                                                    <th>Dirección</th>
                                                    <td>{{$proveedoresservicios->direccion}}</td>
                                                </tr>
                                                <tr>
                                                    <th>Cel.Personal</th>
                                                    <td>{{$proveedoresservicios->celular}}</td>
                                                </tr>
                                                <tr>
                                                    <th>Fecha_Nacimiento</th>
                                                    <td>{{$proveedoresservicios->fechanacimiento}}</td>
                                                </tr>
                                                <tr>
                                                    <th>Sexo</th>
                                                    <td>{{$proveedoresservicios->sexo}}</td>
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
            <div class="col-lg-4">
                <div class="profile-feed">
                    <div class="d-flex align-items-start profile-feed-item">
                        <div class="form-group col-md-12">
                            <div class="row">
                                <div class="col-md-12">
                                    <strong >DATOS EMPRESARIALES</strong><br>
                                    <div class="table-responsive">
                                        <table style="width: 100%;">
                                            <tbody>
                                                <tr>
                                                    <th>Correo</th>
                                                    <td>{{$proveedoresservicios->correo}}</td>
                                                </tr>
                                                <tr>
                                                    <th>Cel.Corporativo</th>
                                                    <td>{{$proveedoresservicios->celularcorporativo}}</td>
                                                </tr>
                                                <tr>
                                                    <th>Cargo</th>
                                                    <td>{{$proveedoresservicios->cargo}}</td>
                                                </tr>
                                                <tr>
                                                    <th>Fecha_Ingreso</th>
                                                    <td>{{$proveedoresservicios->fechaingreso}}</td>
                                                </tr>
                                                <tr>
                                                    <th>Fecha_Salida</th>
                                                    <td>{{$proveedoresservicios->fechasalida ?? 'NO DEFINIDO'}}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <br>
                                    <strong>TIPO DE ORDENES Y PLANILLA</strong><br>
                                    <div class="table-responsive">
                                        <table style="width: 100%;">
                                            <tbody>
                                                <tr>
                                                    <th>Órdenes</th>
                                                    <td>
                                                        {{ $proveedoresservicios->tipoorden1 }}
                                                        @if(!empty($proveedoresservicios->tipoorden2))
                                                            - {{ $proveedoresservicios->tipoorden2 }}
                                                        @endif
                                                        @if(!empty($proveedoresservicios->tipoorden3))
                                                            - {{ $proveedoresservicios->tipoorden3 }}
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>Planilla</th>
                                                    <td>{{$proveedoresservicios->tipoplanilla}}</td>
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
            <div class="col-lg-4">
                <div class="profile-feed">
                    <div class="d-flex align-items-start profile-feed-item">
                        <div class="form-group col-md-12">
                            <div class="row">
                                <div class="col-md-12">
                                    <strong>TIPO DE PAGO</strong><br>
                                    <div class="table-responsive">
                                        <table style="width: 100%;">
                                            <tbody>
                                                <tr>
                                                    <th>Modo_Pago</th>
                                                    <td>{{$proveedoresservicios->tipotransaccion}}</td>
                                                </tr>
                                                <tr>
                                                    <th>Banco</th>
                                                    <td>{{$proveedoresservicios->banco}}</td>
                                                </tr>
                                                <tr>
                                                    <th>Tipo_Cuenta</th>
                                                    <td>{{$proveedoresservicios->tipocuenta}}</td>
                                                </tr>
                                                <tr>
                                                    <th>N.Cuenta</th>
                                                    <td>{{$proveedoresservicios->numcuenta}}</td>
                                                </tr>
                                                <tr>
                                                    <th>Banco Origen</th>
                                                    <td>{{$proveedoresservicios->bancoorigen}}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div><br>

                                    <strong>DATOS REFERENCIALES</strong><br>
                                    <div class="table-responsive">
                                        <table style="width: 100%;">
                                            <tbody>
                                                <tr>
                                                    <th>Contacto_1</th>
                                                    <td>{{$proveedoresservicios->contacto}} - {{$proveedoresservicios->parentesco}}</td>
                                                </tr>
                                                <tr>
                                                    <th>Cel.Contacto_1</th>
                                                    <td>{{$proveedoresservicios->celcontacto}}</td>
                                                </tr>
                                                <tr>
                                                    <th>Contacto_2</th>
                                                    <td>{{$proveedoresservicios->contacto2}} - {{$proveedoresservicios->parentesco2}}</td>
                                                </tr>
                                                <tr>
                                                    <th>Cel.Contacto_2</th>
                                                    <td>{{$proveedoresservicios->celcontacto2}}</td>
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
</div>

<div class="modal fade" id="ventanaModal" tabindex="-1" role="dialog" aria-labelledby="ventanaModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <strong style="text-align: center; font-size:20px; margin-top: 20px;">ACCIONES DEL PERSONAL</strong>
            <div class="modal-body">
                <div style="background-color: #e9fbff;  border-radius: 40px;">
                    <div style="text-align: center;padding: 1.5px; margin-bottom: 10px;">
                        <strong style="color: #26a1c0; font-size:20px;"></strong>
                    </div>
                    <div class="row text-center">
                        @can('admin.asociados.crearbateriaclientecomun')
                        <div class="col-6 mb-3 d-flex justify-content-center align-items-center">
                            <a href="{{ route('admin.proveedoresservicios.editarpersonal', $id) }}" class="btn btn-etapa1 btn-icono btn-block" data-toggle="tooltip" data-placement="top" title="CREAR BATERÍA">
                                <i class="fas fa-edit"></i>
                                <strong>EDITAR</strong>
                            </a>
                        </div>
                        @endcan
                        @can('admin.asociados.crearbateriaclientecomun')
                        <div class="col-6 mb-3 d-flex justify-content-center align-items-center">
                            <a type="button" class="btn btn-etapa1 btn-icono btn-block" data-toggle="modal" data-target="#documentacion" data-placement="top" title="VER DOCUMENTACIÓN">
                                <i class="fas fa-folder-open"></i>
                                <strong>DOCUMENT.</strong>
                            </a>
                        </div>
                        @endcan
                        {{-- @can('admin.asociados.crearbateriaclientecomun')
                        <div class="col-4 mb-3 d-flex justify-content-center align-items-center">
                            <a href="{{ route('admin.proveedoresservicios.editarpersonal', $id) }}" class="btn btn-etapa1 btn-icono btn-block" data-toggle="tooltip" data-placement="top" title="CREAR BATERÍA">
                                <i class="fas fa-clock"></i>
                                <strong>ASISTENCIA</strong>
                            </a>
                        </div>
                        @endcan --}}
                    </div>
                    <div class="row text-center">
                        {{-- @can('admin.asociados.crearbateriaclientecomun')
                        <div class="col-4 mb-3 d-flex justify-content-center align-items-center">
                            <a href="{{ route('admin.proveedoresservicios.editarpersonal', $id) }}" class="btn btn-etapa1 btn-icono btn-block" data-toggle="tooltip" data-placement="top" title="CREAR BATERÍA">
                                <i class="fas fa-user-check"></i>
                                <strong>PERMISOS</strong>
                            </a>
                        </div>
                        @endcan --}}
                        @can('admin.asociados.crearbateriaclientecomun')
                        <div class="col-6 mb-3 d-flex justify-content-center align-items-center">
                            <a href="{{ route('admin.proveedoresservicios.vacacionespersonal', $id) }}" class="btn btn-etapa1 btn-icono btn-block" data-toggle="tooltip" data-placement="top" title="GESTIONAR VACACIONES">
                                <i class="fas fa-umbrella-beach"></i>
                                <strong>VACACIONES</strong>
                            </a>
                        </div>
                        @endcan
                        @can('admin.asociados.crearbateriaclientecomun')
                        <div class="col-6 mb-3 d-flex justify-content-center align-items-center">
                            <a href="{{ route('admin.proveedoresservicios.viajespersonal', $id) }}" class="btn btn-etapa1 btn-icono btn-block" data-toggle="tooltip" data-placement="top" title="GESTIONAR VIAJES">
                                <i class="fas fa-plane"></i>
                                <strong>VIAJES</strong>
                            </a>
                        </div>
                        @endcan
                        {{-- @can('admin.asociados.crearbateriaclientecomun')
                        <div class="col-4 mb-3 d-flex justify-content-center align-items-center">
                            <a href="{{ route('admin.proveedoresservicios.editarpersonal', $id) }}" class="btn btn-etapa1 btn-icono btn-block" data-toggle="tooltip" data-placement="top" title="CREAR BATERÍA">
                                <i class="fas fa-money-bill-wave"></i>
                                <strong>SALARIO</strong>
                            </a>
                        </div>
                        @endcan --}}
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-rojopequeno" data-dismiss="modal">CERRAR</button>
            </div>
        </div>
    </div>
</div>

{{-- DOCUMENTACION --}}
<div class="modal fade modal-custom-height" id="documentacion" tabindex="-1" aria-labelledby="documentacionLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="titulo" style="margin-top: 10px; margin-left: 10px;">
                <h4 class="modal-title" id="documentacionLabel" style="font-weight: 900">DOCUMENTACIÓN DEL PERSONAL</h4>
            </div>
            <div class="modal-body">
                {!! Form::model($id, ['route' => ['admin.proveedoresservicios.guardardocumentacionproveedor', $id], 'method' => 'POST', 'files' => true]) !!}
                {!! Form::hidden('usuarioid', auth()->user()->id) !!}
                {!! Form::hidden('usuarioregistro', auth()->user()->name) !!}
                {!! Form::hidden('proveedornombre', $proveedoresservicios->razonsocial) !!}
                {!! Form::hidden('proveedorid', $proveedoresservicios->id) !!}

                <div class="card">
                    <div class="card-body">
                        <button type="button" class="btn btn-verdepequeno" id="toggle-content" style="margin-top:-10px; margin-bottom:-5px;">AGREGAR</button>
    
                        <div id="toggle-section" style="display: none;">
                            <div class="row"> 
                                <div class="col-lg-6">
                                    <div class="form-group" style="margin-top: 15px"> 
                                        <strong>Tipo de Documento:</strong>
                                        <select id="tipoDocumento" name="tipoDocumento" class="form-control">
                                            <option value=""></option>
                                            <option value="DOCUMENTO PERSONAL">DOCUMENTO PERSONAL</option>
                                            <option value="ACTA DE ENTREGA">ACTA DE ENTREGA</option>
                                            <option value="MINISTERIO DE TRABAJO">MINISTERIO DE TRABAJO</option>
                                            <option value="CAJA PETROLERA DE SALUD">CAJA PETROLERA DE SALUD</option>
                                            <option value="GESTORA">GESTORA</option>
                                        </select>
                                    </div>
                                    
                                    <div class="form-group" style="margin-top: 15px"> 
                                        <strong>Nombre de Documento:</strong>
                                        <select id="nombreDocumento" name="nombreDocumento" class="form-control">
                                            <option value=""></option>
                                        </select>
                                        <input type="text" id="nombreDocumentoTexto" name="nombreDocumentoTexto" class="form-control" style="display: none;" placeholder="Nombre Adicional">
                                    </div>
                                </div>
                            
                                <div class="col-lg-6">
                                    <div class="form-group" style="margin-top: 15px">
                                        <strong>Documento:</strong>
                                        <input type="file" name="documento" class="dropify form-control" accept=".pdf"/>
                                        @error('carta')
                                            <small class="text-danger fas fa-exclamation-circle">
                                                {{ $message }}
                                            </small>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="modal-footer" style="margin-top: -10px; margin-bottom: -10px; margin-right: -12px;">
                                <button type="submit" class="btn btn-verdegrande">GUARDAR</button>
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                    document.getElementById('toggle-content').addEventListener('click', function() {
                        let content = document.getElementById('toggle-section');
                        content.style.display = (content.style.display === 'none' || content.style.display === '') ? 'block' : 'none';
                    });

                    document.getElementById('tipoDocumento').addEventListener('change', function() {
                        let tipo = this.value;
                        let selectDocumento = document.getElementById('nombreDocumento');
                        let inputDocumento = document.getElementById('nombreDocumentoTexto');

                        let opciones = {
                            "DOCUMENTO PERSONAL": ["CARNET DE IDENTIDAD", 
                                                    "CARNET DE GARANTE",
                                                    "COMPROBANTE DE PAGO DE LUZ", 
                                                    "CONTRATO TEMPORAL", 
                                                    "CURRICULUM VITAE",
                                                    "DIRECCION PARTICULAR",  
                                                    "DOCUMENTO CONFIDENCIAL", 
                                                    "LETRA DE CAMBIO", 
                                                    "NUMERO DE CUENTA BANCARIA"
                                                    ],
                            "MINISTERIO DE TRABAJO": ["ASIGNACION",
                                                        "CONTRATO INDEFINIDO", 
                                                        "FINIQUITO",
                                                        "MEMORANDUM", 
                                                        "QUINQUENIO", 
                                                        "VACACION"
                                                        ],
                            "CAJA PETROLERA DE SALUD": ["AVISO DE AFILIACION",
                                                        "CARNET DE ASEGURADO", 
                                                        "EXAMEN OCUPACIONAL", 
                                                        "EXAMEN POST OCUPACIONAL",
                                                        "FORMULARIO DE INCAPACIDAD"
                                                        ],
                            "GESTORA": ["ESTADO AHORRO PREVISIONAL", 
                                        "EXTRACTO DE APORTES"
                                        ],
                            "ACTA DE ENTREGA": ["ACTA DE ENTREGA"
                                        ]
                        };

                            selectDocumento.style.display = "block";
                            inputDocumento.style.display = "block";
                            selectDocumento.innerHTML = '<option value=""></option>';
                            if (opciones[tipo]) {
                                opciones[tipo].forEach(function(opcion) {
                                    let newOption = document.createElement("option");
                                    newOption.value = opcion;
                                    newOption.textContent = opcion;
                                    selectDocumento.appendChild(newOption);
                                });
                            }
                    });
                </script>
                <script>
                    const tipoSelect    = document.getElementById('tipoDocumento');
                    const nombreSelect  = document.getElementById('nombreDocumento');
                    const nombreInput   = document.getElementById('nombreDocumentoTexto');
                
                    const opciones = {
                        "DOCUMENTO PERSONAL": ["CARNET DE IDENTIDAD", 
                                                    "CARNET DE GARANTE",
                                                    "COMPROBANTE DE PAGO DE LUZ", 
                                                    "CONTRATO TEMPORAL", 
                                                    "CURRICULUM VITAE",
                                                    "DIRECCION PARTICULAR",  
                                                    "DOCUMENTO CONFIDENCIAL", 
                                                    "LETRA DE CAMBIO", 
                                                    "NUMERO DE CUENTA BANCARIA"
                                                    ],
                            "MINISTERIO DE TRABAJO": ["ASIGNACION",
                                                        "CONTRATO INDEFINIDO", 
                                                        "FINIQUITO",
                                                        "MEMORANDUM", 
                                                        "QUINQUENIO", 
                                                        "VACACION"
                                                        ],
                            "CAJA PETROLERA DE SALUD": ["AVISO DE AFILIACION",
                                                        "CARNET DE ASEGURADO", 
                                                        "EXAMEN OCUPACIONAL", 
                                                        "EXAMEN POST OCUPACIONAL",
                                                        "FORMULARIO DE INCAPACIDAD"
                                                        ],
                            "GESTORA": ["ESTADO AHORRO PREVISIONAL", 
                                        "EXTRACTO DE APORTES"
                                        ],
                            "ACTA DE ENTREGA": ["ACTA DE ENTREGA"
                                        ]
                    };
                
                    // Cuando cambie el tipo, rellenamos el select y reseteamos el input
                    tipoSelect.addEventListener('change', function() {
                        const tipo = this.value;
                        nombreSelect.innerHTML = '<option value=""></option>';
                        nombreInput.value = '';
                        nombreInput.style.display = 'block';
                        nombreSelect.style.display = 'block';
                
                        if (opciones[tipo]) {
                            opciones[tipo].forEach(function(op) {
                                const opt = document.createElement('option');
                                opt.dataset.base = op;       // guardamos el texto base
                                opt.value     = op;          // inicialmente igual
                                opt.textContent = op;
                                nombreSelect.appendChild(opt);
                            });
                        }
                    });
                
                    // Función para actualizar la opción seleccionada
                    function actualizarOpcion() {
                        const selOpt = nombreSelect.selectedOptions[0];
                        if (!selOpt) return;
                        const base   = selOpt.dataset.base || '';
                        const extra  = nombreInput.value.trim();
                        const combinado = extra ? `${base} ${extra}` : base;
                        selOpt.value = combinado;
                        selOpt.textContent = combinado;
                    }
                
                    // Cuando cambie la selección o el texto, actualizamos
                    nombreSelect.addEventListener('change', actualizarOpcion);
                    nombreInput.addEventListener('input', actualizarOpcion);
                </script>
                

                {!! Form::close() !!}
                
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-12">
                                <strong style="color: #94c93b" style="margin-block: 30px;">LISTA DE DOCUMENTOS</strong>
                                <div class="table-responsive">
                                    @php
                                        $grouped = $proveedordocumentos->groupBy('tipodocumento');
                                        $index = 0;
                                    @endphp
                                
                                    @foreach ($grouped as $tipo => $docs)
                                        <div class="group-container">
                                            <div class="group-header" data-target="group-{{ $index }}">
                                                <span><i class="fas fa-folder"></i>{{ $tipo }}</span>
                                                <i class="fas fa-chevron-right"></i>
                                            </div>
                                
                                            <div id="group-{{ $index }}" class="group-content">
                                                <table class="table table-sm table-inner">
                                                    <thead>
                                                        <tr>
                                                            <th>Nombre del Documento</th>
                                                            <th>Ver</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($docs as $doc)
                                                            <tr>
                                                                <td>{{ $doc->nombredocumento }}</td>
                                                                <td>
                                                                    @if ($doc->documento)
                                                                        <a href="{{ asset('proveedoresdocumentos/' . $doc->proveedorid . '/' . $doc->documento) }}" 
                                                                           class="btn btn-sm btn-verdepequeno" target="_blank" title="VER DOCUMENTO">
                                                                            <i class="fas fa-eye"></i>
                                                                        </a>
                                                                    @else
                                                                        <span class="badge badge-danger">VACIO</span>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        @php $index++; @endphp
                                    @endforeach
                                </div>
                                
                                <script>
                                    document.addEventListener('DOMContentLoaded', function () {
                                        const headers = document.querySelectorAll('.group-header');
                                
                                        headers.forEach(header => {
                                            header.addEventListener('click', function () {
                                                const targetId = this.dataset.target;
                                                const content = document.getElementById(targetId);
                                
                                                // Cerrar todos
                                                document.querySelectorAll('.group-content').forEach(group => {
                                                    if (group.id !== targetId) group.classList.remove('show');
                                                });
                                
                                                document.querySelectorAll('.group-header').forEach(h => {
                                                    if (h !== this) h.classList.remove('open');
                                                });
                                
                                                // Alternar actual
                                                content.classList.toggle('show');
                                                this.classList.toggle('open');
                                            });
                                        });
                                    });
                                </script>
                                
                                
                                
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="margin-top: -10px; margin-bottom: -10px; margin-right: -12px;">
                    <button type="button" class="btn btn-rojopequeno" data-dismiss="modal" aria-label="Cerrar">CERRAR</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
