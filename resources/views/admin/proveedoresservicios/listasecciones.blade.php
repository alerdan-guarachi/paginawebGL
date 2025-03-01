@extends('adminlte::page')
    
@section('content_header')
{{-- <a class="btn btn-crear btn-sm float-right" href="{{route('admin.proveedoresservicios.create')}}">CREAR PROVEEDOR</a> --}}
<h1>SECCIONES DE SERVICIOS</h1>
@stop

@section('css')
<link rel="stylesheet" href="{{ asset('css/proveedoresinformes.css') }}">
@stop



                         {{-- <a class="btn btn-sm btn-verinformefirmado btn-sm" data-toggle="modal" data-target="#listSubsectionsModal" onclick="listSubsections({{ $listaseccion->id }})" title="VER SUB SECCIONES">
                            <i class="fas fa-file"></i></a>

                        <a class="btn btn-sm btn-crear2 btn-sm" data-toggle="modal" data-target="#createSubsectionModal" onclick="setSeccionId({{ $listaseccion->id }})" title="CREAR SUB SECCION">
                            <i class="fas fa-plus"></i></a> --}}

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
        <table class="table table-striped">
            <thead>
                <tr>
                    <th style="width: 10%;">ID</th>
                    <th style="width: 60%;">Sección</th>
                    <th style="width: 20%;">Estado</th>
                    <th style="width: 10%;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($listasecciones as $listaseccion)
                    <tr>
                        <td>{{$listaseccion->id}}</td>
                        <td>{{$listaseccion->nombreseccion}}</td>
                        <td>
                            @if ($listaseccion->estado == 'ACTIVO')
                                <span class="badge badge-success">{{ $listaseccion->estado }}</span>
                            @else
                                <span class="badge badge-danger">{{ $listaseccion->estado }}</span>
                            @endif
                        </td>
                        <td>
                            <a class="btn btn-sm btn-buscar" data-toggle="modal" data-target="#editModal" onclick="editSeccion({{ $listaseccion->id }}, '{{ $listaseccion->nombreseccion }}', '{{ $listaseccion->estado }}')" title="EDITAR SECCION">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a class="btn btn-sm btn-crear" data-toggle="modal" data-target="#createSubseccionModal" onclick="setSeccionData({{ $listaseccion->id }}, '{{ $listaseccion->nombreseccion }}')" title="AGREGAR SUBSECCION">
                                <i class="fas fa-plus"></i>
                            </a>
                            <a class="btn btn-sm btn-verinformefirmado" data-toggle="modal" data-target="#subseccionesModal-{{ $listaseccion->id }}" title="VER SUBSECCIONES">
                                <i class="fas fa-file"></i>
                            </a>
                            <div class="modal fade" id="subseccionesModal-{{ $listaseccion->id }}" tabindex="-1" aria-labelledby="subseccionesModalLabel-{{ $listaseccion->id }}" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header" style="display: block; text-align: left;">
                                            <h6 class="modal-title" id="subseccionesModalLabel-{{ $listaseccion->id }}" style="font-size: 16px; font-weight: normal; margin-bottom: 0;">
                                                Subsecciones de la Sección:
                                            </h6>
                                            <h5 class="modal-title" id="subseccionesModalLabel-{{ $listaseccion->id }}" style="font-weight: bold; margin-top: 5px;">
                                                {{ $listaseccion->nombreseccion }}
                                            </h5>
                                        </div>
                                        <div class="modal-body">
                                            @php
                                                $subseccionesParaEstaSeccion = $subsecciones->get($listaseccion->id, []);
                                            @endphp

                                            <table class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th style="background-color: white">ID</th>
                                                        <th style="background-color: white">Subsección</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($subseccionesParaEstaSeccion as $subseccion)
                                                        <tr>
                                                            <td>{{ $subseccion->id }}</td>
                                                            <td>{{ $subseccion->subseccion }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                            <div class="btn-container" style=" display: flex; justify-content: center;">
                                                <button type="button" class="btn btn-outline-danger" data-dismiss="modal">CERRAR</button>
                                            </div>
                                        </div>
                                        
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        
        <!-- MODAL PARA EDITAR SECCION -->
        <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editModalLabel" style="font-weight: 900;">EDITAR SECCIÓN</h5>
                    </div>
                    <div class="modal-body">
                        <form id="editForm" method="POST" action="{{ route('seccion.actualizar') }}">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="id" id="seccionId">
                            <div class="form-group">
                                <label for="nombreSeccion">Nombre Sección</label>
                                <input type="text" class="form-control" id="nombreSeccion" name="nombreseccion" required>
                            </div>
                            <div class="form-group">
                                <label for="estado">Estado</label>
                                <select class="form-control" id="estado" name="estado" required>
                                    <option value="ACTIVO">ACTIVO</option>
                                    <option value="INACTIVO">INACTIVO</option>
                                </select>
                            </div>
                            <div class="btn-container" style=" display: flex; justify-content: center;">
                                <button type="submit" class="btn btn-crear">ACTUALIZAR</button>
                                <button type="button" class="btn btn-outline-danger" data-dismiss="modal">CERRAR</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <script>
            function editSeccion(id, nombreseccion, estado) {
                document.getElementById('seccionId').value = id;
                document.getElementById('nombreSeccion').value = nombreseccion;
                document.getElementById('estado').value = estado;
            }
        </script>
        
        {{-- MODAL AGREGAR SUB SECCIONES --}}
        <div class="modal fade" id="createSubseccionModal" tabindex="-1" aria-labelledby="createSubseccionModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="createSubseccionModalLabel" style="font-weight: 900;">AGREGAR SUB SECCIÓN</h5>
                    </div>
                    <div class="modal-body">
                        <form id="crearForm" method="POST" action="{{ route('subseccion.crear') }}">
                            @csrf
                            @method('POST')
                            {!! Form::hidden('usuarioregistroid', auth()->user()->id) !!}
                            {!! Form::hidden('usuarioregistronombre', auth()->user()->name) !!}
                            <input type="hidden" name="seccionid" id="seccionId2">
                            <div class="form-group">
                                <label for="nombreSeccion2">Nombre Sección</label>
                                <input type="text" class="form-control" id="nombreSeccion2" name="seccionnombre" readonly>
                            </div>
                            <div class="form-group">
                                <label for="subseccion">Sub Sección</label>
                                <input type="text" class="form-control" id="subseccion" name="subseccion" required>
                            </div>
                            <div class="btn-container" style=" display: flex; justify-content: center;">
                                <button type="submit" class="btn btn-crear">GUARDAR</button>
                                <button type="button" class="btn btn-outline-danger" data-dismiss="modal">CERRAR</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <script>
            function setSeccionData(id, nombreseccion, estado) {
                document.getElementById('seccionId2').value = id;
                document.getElementById('nombreSeccion2').value = nombreseccion;
                document.getElementById('estado').value = estado;
            }
        </script>

    </div>
</div>
@stop

@section('js')
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script type="text/javascript" src="https://jeremyfagis.github.io/dropify/dist/js/dropify.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://jeremyfagis.github.io/dropify/dist/css/dropify.min.css"> 
<script>
$('.dropify').dropify();
</script>
    @if (session('eliminar')=='ok')
    <script>
        Swal.fire(
      '¡Eliminado!',
      'El perfil se eliminó con éxito',
      'success')
    </script>
    @endif

<script>
    $('.formulario-eliminar').submit(function(e){
        e.preventDefault();

        Swal.fire({
        title: '¿Estás seguro?',
        text: "Este perfil se eliminará definitivamente",
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