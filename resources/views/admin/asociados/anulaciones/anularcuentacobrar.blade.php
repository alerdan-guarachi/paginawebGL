@extends('adminlte::page')
    
@section('content_header')
<a class="btn float-right btn-outline-secondary" data-toggle="modal" data-target="#anulacionesModal">
    ANULACIONES
</a>
<h1>ANULAR CUENTAS POR COBRAR Y PAGAR</h1>
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
        <form method="GET" action="{{ route('admin.caja.anulaciones.anularcaja') }}"> 
            <label for="motivoAnulacion" class="form-label">ID CAJA</label>
            <div class="row mb-3">
                
                <div class="col-lg-2">
                    <input type="text" name="search" class="form-control" placeholder="" value="{{ request('search') }}">
                </div>
                <div class="col-lg-2">
                    <button type="submit" class="btn btn-secondary">Buscar</button>
                </div>
            </div>
        </form>
        
        <form action="{{ route('anularregitrocuentacobrar') }}" method="POST"> 
            @csrf
            <div class="row mb-3">
                <div class="col-lg-6">
                
                    <label for="motivoAnulacion" class="form-label">Motivo de Anulación</label>
                    <textarea id="motivoAnulacion" name="motivoAnulacion" class="form-control" rows="3" placeholder="" required></textarea>
                </div>
            </div>
            
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID Prog</th>
                        <th>Tipo</th>
                        <th>Nombre</th>
                        <th>Tipo Transac.</th>
                        <th>Nro. Recibo</th>
                        <th>Monto Total</th>
                        <th>Ciudad Reg.</th>
                        <th>Usuario Reg.</th>
                        <th>Selec.</th>
                    </tr>
                </thead>
                @if ($registros->isNotEmpty()) 
                    <tbody>
                        @foreach ($registros as $registro)
                            <tr>
                                <td>{{ $registro->id }}</td>
                                <td>{{ $registro->tipocliente }}</td>
                                <td>{{ $registro->clientenombre }} {{ $registro->proveedornombre }}</td>
                                <td>{{ $registro->tipotransaccion }}</td>
                                <td>{{ $registro->nrorecibo }}</td>
                                <td>{{ $registro->montototal }}</td>
                                <td>{{ $registro->ciudadregistro }}</td>
                                <td>{{ $registro->usuarioregistronombre }}</td>
                                <td>
                                    <input type="checkbox" name="seleccionados[]" value="{{ $registro->id }}" class="checkbox-seleccion">
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                @else
                    @if (request()->has('search'))
                        <p>REGISTRO NO ENCONTRADO O ANULADO "{{ request('search') }}".</p>
                    @endif
                @endif
            </table>
            <div class="d-flex">
                <button type="submit" class="btn btn-outline-danger ml-auto" id="btnAsignarCredito" disabled>ANULAR REGISTRO</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="anulacionesModal" tabindex="-1" aria-labelledby="anulacionesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content border-0 shadow-sm">
            <div class="modal-header w-100 text-center">
                <h4 class="modal-title w-100 fw-bold" id="anulacionesModalLabel" style="font-weight: 900">ANULACIONES</h4>
            </div>
                
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID Prog</th>
                                <th>Tipo</th>
                                <th>Nombre</th>
                                <th>Tipo Transac.</th>
                                <th>Nro. Recibo</th>
                                <th>Monto Total</th>
                                <th>Ciudad Reg.</th>
                                <th>Usuario Reg.</th>
                                <th>Fecha Anul.</th>
                                <th>Usuario Anul.</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($anulaciones as $anulacion)
                                <tr>
                                    <td>{{ $anulacion->id }}</td>
                                    <td>{{ $anulacion->tipocliente }}</td>
                                    <td>{{ $anulacion->clientenombre }} {{ $anulacion->proveedornombre }}</td>
                                    <td>{{ $anulacion->tipotransaccion }}</td>
                                    <td>{{ $anulacion->nrorecibo }}</td>
                                    <td>{{ $anulacion->montototal }}</td>
                                    <td>{{ $anulacion->ciudadregistro }}</td>
                                    <td>{{ $anulacion->usuarioregistronombre }}</td>
                                    <td>{{ $anulacion->deleted_at }}</td>
                                    <td>{{ $anulacion->usuarioanulacion }}</td>
                                    </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
<link rel="styleheet" href="/css/admin_custom.css">
<style>
    h1, th {
        color:#000000; 
        font-family: "Segoe UI";
        font-weight: 700;
    }
</style>
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
    $(document).ready(function() {
        $('input[name="buscarpor"]').on('keyup', function() {
            var query = $(this).val();
            var botonBuscar = $('#btn-buscar');
            if (query.trim() === '') {
                botonBuscar.prop('disabled', true);
            } else {
                botonBuscar.prop('disabled', false);
            }
        });
    });
</script>
<script>
    // Obtener el botón y todos los checkboxes
    const btnAsignarCredito = document.getElementById('btnAsignarCredito');
    const checkboxes = document.querySelectorAll('.checkbox-seleccion');

    // Función para habilitar/deshabilitar el botón
    function actualizarBoton() {
        const algunoSeleccionado = Array.from(checkboxes).some(checkbox => checkbox.checked);
        btnAsignarCredito.disabled = !algunoSeleccionado;
    }

    // Monitorear cambios en los checkboxes
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', actualizarBoton);
    });

    // Llamar la función una vez para comprobar el estado inicial
    actualizarBoton();
</script>
@endsection