@extends('adminlte::page')

@section('content_header')

<a class="btn btn-sm float-right btn-outline-secondary" data-toggle="modal" data-target="#modalNuevaOpcion">NUEVA OPCIÓN</a>
<!-- Modal -->
<div class="modal fade" id="modalNuevaOpcion" tabindex="-1" role="dialog" aria-labelledby="modalNuevaOpcionLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form action="{{ route('admin.opcionesinventario.guardaropcioninventario') }}" method="POST">
        @csrf
            <div class="modal-content">
                <div class="modal-header">
                <h5 class="modal-title" style="font-weight: 700;">NUEVA OPCIÓN DE INVENTARIO</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="tipo">Tipo</label>
                        <select name="tipo" id="tipo" class="form-control" required>
                            <option value="">-- Seleccione --</option>
                            <option value="ALMACEN">ALMACÉN</option>
                            <option value="ACTIVO FIJO">ACTIVO FIJO</option>
                        </select>
                    </div>

                    <div class="form-group" id="grupo-seccion" style="display: none;">
                        <label for="seccion">Sección</label>
                        <input type="text" name="seccion" id="seccion" class="form-control" list="datalist-seccion">
                            <datalist id="datalist-seccion">
                                @foreach($seccionesAlmacen as $item)
                                <option value="{{ $item }}">
                                @endforeach
                            </datalist>
                        </div>

                        <div class="form-group" id="grupo-tiposeccion" style="display: none;">
                        <label for="tiposeccion">Tipo Sección</label>
                        <input type="text" name="tiposeccion" id="tiposeccion" class="form-control" list="datalist-tiposeccion">
                            <datalist id="datalist-tiposeccion">
                                @foreach($tiposeccionesAlmacen->merge($tiposeccionesActivoFijo)->unique() as $item)
                                <option value="{{ $item }}">
                                @endforeach
                            </datalist>
                        </div>


                    <div class="form-group" id="grupo-opcion" style="display: none;">
                        <label for="opcion">Opción</label>
                        <input type="text" name="opcion" id="opcion" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-sm btn-outline-secondary">GUARDAR</button>
                </div>
            </div>
            <style>
                input[type="text"] {
                    text-transform: uppercase;
                }
            </style>
            <script>
                document.addEventListener("DOMContentLoaded", function () {
                    document.querySelectorAll('input[type="text"]').forEach(function (input) {
                        input.addEventListener('input', function () {
                            this.value = this.value.toUpperCase();
                        });
                    });
                });
            </script>
        </form>
    </div>
</div>
<script>
    const tiposecciones = {
        almacen: @json($tiposeccionesAlmacen),
        activo: @json($tiposeccionesActivoFijo)
    };

    document.addEventListener('DOMContentLoaded', function () {
        const tipoSelect = document.getElementById('tipo');
        const grupoSeccion = document.getElementById('grupo-seccion');
        const grupoTiposeccion = document.getElementById('grupo-tiposeccion');
        const grupoOpcion = document.getElementById('grupo-opcion');
        const datalistTiposeccion = document.getElementById('datalist-tiposeccion');

        tipoSelect.addEventListener('change', function () {
            const tipo = tipoSelect.value;

            // Mostrar/Ocultar según tipo
            grupoSeccion.style.display = (tipo === 'ALMACEN') ? 'block' : 'none';
            grupoTiposeccion.style.display = (tipo === 'ALMACEN' || tipo === 'ACTIVO FIJO') ? 'block' : 'none';
            grupoOpcion.style.display = (tipo === 'ALMACEN' || tipo === 'ACTIVO FIJO') ? 'block' : 'none';

            // Cambiar datalist de tiposeccion
            datalistTiposeccion.innerHTML = '';

            if (tipo === 'ALMACEN') {
                tiposecciones.almacen.forEach(item => {
                    datalistTiposeccion.innerHTML += `<option value="${item}">`;
                });
            } else if (tipo === 'ACTIVO FIJO') {
                tiposecciones.activo.forEach(item => {
                    datalistTiposeccion.innerHTML += `<option value="${item}">`;
                });
            }
        });
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const tipoSelect = document.getElementById('tipo');
        const grupoSeccion = document.getElementById('grupo-seccion');
        const grupoTiposeccion = document.getElementById('grupo-tiposeccion');
        const grupoOpcion = document.getElementById('grupo-opcion');

        tipoSelect.addEventListener('change', function () {
            const tipo = tipoSelect.value;

            if (tipo === 'ALMACEN') {
                grupoSeccion.style.display = 'block';
                grupoTiposeccion.style.display = 'block';
                grupoOpcion.style.display = 'block';
            } else if (tipo === 'ACTIVO FIJO') {
                grupoSeccion.style.display = 'none';
                grupoTiposeccion.style.display = 'block';
                grupoOpcion.style.display = 'block';
            } else {
                grupoSeccion.style.display = 'none';
                grupoTiposeccion.style.display = 'none';
                grupoOpcion.style.display = 'none';
            }
        });
    });
</script>

<h1>OPCIONES DE INVENTARIO</h1>
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
    <div class="card-body">
        <div class="row">
            <div class="col-lg-12">
                <div class="mb-3">
                    <label for="">BUSCAR:</label>
                    <input type="text" id="buscadorOpciones" class="form-control" placeholder="Buscar por sección, tipo sección u opción...">
                </div>
            </div>
            <script>
                document.getElementById('buscadorOpciones').addEventListener('keyup', function () {
                    const filtro = this.value.toLowerCase();
                    const tablas = document.querySelectorAll('.tabla-opciones tbody');

                    tablas.forEach(tbody => {
                        Array.from(tbody.getElementsByTagName('tr')).forEach(fila => {
                            const textoFila = fila.textContent.toLowerCase();
                            fila.style.display = textoFila.includes(filtro) ? '' : 'none';
                        });
                    });
                });
            </script>

            <div class="col-lg-6">
                <h5 style="font-weight: 900">OPCIONES DE ALMACEN</h5>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered tabla-opciones">
                        <thead class="table-secondary">
                            <tr>
                                <th>ID</th>
                                <th>Sección</th>
                                <th>Tipo_Sección</th>
                                <th>Opción</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($opcionesalmacen as $opcion)
                                <tr>
                                    <td>{{$opcion->id}}</td>
                                    <td>{{$opcion->seccion}}</td>
                                    <td>{{$opcion->tiposeccion}}</td>
                                    <td>{{$opcion->opcion}}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div class="col-lg-6">
                <h5 style="font-weight: 900">OPCIONES DE ACTIVO FIJO</h5>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered tabla-opciones">
                        <thead class="table-secondary">
                            <tr>
                                <th>ID</th>
                                <th>Tipo_Sección</th>
                                <th>Opción</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($opcionesactivofijo as $opcion)
                                <tr>
                                    <td>{{$opcion->id}}</td>
                                    <td>{{$opcion->tiposeccion}}</td>
                                    <td>{{$opcion->opcion}}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
<link rel="styleheet" href="/css/admin_custom.css">
<style>
    .table td {
        padding: 5px 10px;
    }
    h1, th {color:#000000; 
        font-family: "Segoe UI";
        font-weight: 700;
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