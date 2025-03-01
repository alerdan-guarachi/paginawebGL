@extends('adminlte::page')
    
@section('content_header')
<h1>ASIGNAR CRÉDITO</h1>
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
        <!-- Formulario de búsqueda -->
        <form method="GET" action="{{ route('admin.caja.cuentascobrar.ccporcredito') }}">  
            <label for="gerenteSelect">BUSCAR CLIENTE:</label>
            <div class="row mb-3">
                <div class="col-lg-2">
                    <select name="tipo_cliente" class="form-control">
                        <option value="" disabled selected>Tipo de Cliente</option>
                        <option value="CLIENTE ITA" {{ request('tipo_cliente') == 'CLIENTE ITA' ? 'selected' : '' }}>CLIENTE ITA</option>
                        <option value="CLIENTE BANCO" {{ request('tipo_cliente') == 'CLIENTE BANCO' ? 'selected' : '' }}>CLIENTE BANCO</option>
                        <option value="CLIENTE AUDITORIA" {{ request('tipo_cliente') == 'CLIENTE AUDITORIA' ? 'selected' : '' }}>CLIENTE AUDITORIA</option>
                        <option value="CLIENTE COMUN" {{ request('tipo_cliente') == 'CLIENTE COMUN' ? 'selected' : '' }}>CLIENTE COMUN</option>
                    </select>
                </div>
                <div class="col-lg-2">
                    <input type="text" name="search" class="form-control" placeholder="ID Cliente" value="{{ request('search') }}">
                </div>
                <div class="col-lg-2">
                    <button type="submit" class="btn btn-secondary">BUSCAR</button>
                </div>
            </div>
        </form>
        

        <!-- Tabla de resultados -->
        <form action="{{ route('actualizarRegistros') }}" method="POST" enctype="multipart/form-data">
            @csrf

            @if (request('search'))
            <div class="row">
                <!-- Select para gerentes -->
                <div class="form-group col-lg-4">
                    <label for="gerenteSelect">USUARIO AUTORIZADOR:</label>
                    <select id="gerenteSelect" name="gerente" class="form-control">
                        <option value=""></option>
                        @foreach ($gerentes as $gerente)
                            <option value="{{ $gerente->id }}">{{ $gerente->nombrecompleto }}</option>
                        @endforeach
                    </select>
                </div>
                <!-- Campo para subir documentos -->
                <div class="form-group col-lg-4">
                    <label for="documento">DOCUMENTO RESPALDO:</label>
                    <input type="file" id="documento" name="documento" class="form-control" accept=".pdf,.doc,.docx">
                </div>

                <div class="form-group col-lg-4">
                    <label for="documentolcambio">LETRA DE CAMBIO:</label>
                    <input type="file" id="documentolcambio" name="documentolcambio" class="form-control" accept=".pdf,.doc,.docx">
                </div>
            </div>
            @endif
            <!-- Tabla y lógica existentes -->
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID Prog</th>
                            <th>Tipo Cliente</th>
                            <th>ID Cliente</th>
                            <th>Cliente</th>
                            <th>Proveedor</th>
                            <th>Acción</th>
                            <th hidden>Fecha batería</th>
                            <th>Servicio</th>
                            <th>Precio</th>
                            <th>Fecha Crédito</th>
                            <th>Autorizador</th>
                            <th>Documento</th>
                            <th>Letra de cambio</th>
                            <th>Selec.</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($registros->isNotEmpty())
                            @foreach ($registros as $registro)
                                <tr>
                                    <td>{{ $registro->id }}</td>
                                    <td>
                                        @if ($registro->clienteitaid)
                                            CLIENTE ITA
                                        @elseif ($registro->clienteauditoriaid)
                                            CLIENTE AUDITORIA
                                        @elseif ($registro->clientecomunid)
                                            CLIENTE COMUN
                                        @elseif ($registro->clientebancoid)
                                            CLIENTE BANCO
                                        @else
                                            SIN TIPO
                                        @endif
                                    </td>
                                    <td>
                                        {{ $registro->clienteitaid ?? '' }}
                                        {{ $registro->clienteauditoriaid ?? '' }}
                                        {{ $registro->clientecomunid ?? '' }}
                                        {{ $registro->clientebancoid ?? '' }}
                                    </td>
                                    <td>
                                        {{ $registro->clienteitanombre ?? '' }}
                                        {{ $registro->clienteauditorianombre ?? '' }}
                                        {{ $registro->clientecomunnombre ?? '' }}
                                        {{ $registro->clientenombre ?? '' }}
                                    </td>
                                    <td>{{ $registro->proveedornombre }} {{ $registro->proveedorasignado }}</td>
                                    <td>{{ $registro->accionnombre }}</td>
                                    <td hidden>{{ $registro->fechabateria }}</td>
                                    <td>
                                        @if ($registro->tramite)
                                            {{ $registro->tramite->tramite ?? 'No tiene trámite' }}
                                        @else
                                            NO ASIGNADO
                                        @endif
                                    </td>
                                    <td>{{ $registro->precio }}</td>
                                    <td>
                                        @if ($registro->fechacredito)
                                            {{ $registro->fechacredito }}
                                        @else
                                            <input type="date" class="form-control campo-fecha" name="campo_fecha[{{ $registro->id }}]" value="">
                                        @endif
                                    </td>
                                    <td>
                                        {{ $registro->usuarioautorizador ?? 'NO ASIGNADO' }}
                                    </td>
                                    <td>
                                        @if ($registro->documentocredito)
                                        <a href="{{ asset('creditos/'. $registro->documentocredito) }}" target="_blank" class="btn btn-sm btn-secondary" title="VER DOCUMENTO"><i class="fa fa-eye"></i></a>
                                        
                                        @else
                                            NO ASIGNADO
                                        @endif
                                    </td>
                                    <td>
                                        @if ($registro->documentolcambio)
                                        <a href="{{ asset('creditos/'. $registro->documentolcambio) }}" target="_blank" class="btn btn-sm btn-secondary" title="VER LETRA DE CAMBIO"><i class="fa fa-eye"></i></a>
                                        
                                        @else
                                            NO ASIGNADO
                                        @endif
                                    </td>
                                    <td>
                                        @if (!$registro->documentocredito)
                                            <input type="checkbox" name="seleccionados[]" value="{{ $registro->id }}" class="checkbox-seleccion">
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="13">NO SE ENCONTRARON REGISTROS</td>
                            </tr>
                        @endif
                    </tbody>
                    
                </table>
            </div>
            <div class="d-flex">
                <button type="submit" class="btn btn-outline-success ml-auto" id="btnAsignarCredito" disabled>ASIGNAR CREDITO</button>
            </div>
        </form>
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
    const btnAsignarCredito = document.getElementById('btnAsignarCredito');
    const checkboxes = document.querySelectorAll('.checkbox-seleccion');
    const fechas = document.querySelectorAll('.campo-fecha');
    const gerenteSelect = document.querySelector('[name="gerente"]');
    const documentoInput = document.querySelector('[name="documento"]');
    const documentolcambioInput = document.querySelector('[name="documentolcambio"]');

    function actualizarBoton() {
        const algunCheckboxSeleccionado = Array.from(checkboxes).some(checkbox => checkbox.checked);
        const algunaFechaSeleccionada = Array.from(fechas).some(fecha => fecha.value !== '');
        const gerenteSeleccionado = gerenteSelect && gerenteSelect.value !== '';
        const documentoSeleccionado = documentoInput && documentoInput.files.length > 0;
        const documentolcambioSeleccionado = documentolcambioInput && documentolcambioInput.files.length > 0;

        btnAsignarCredito.disabled = !(
            algunCheckboxSeleccionado &&
            algunaFechaSeleccionada &&
            gerenteSeleccionado &&
            documentoSeleccionado &&
            documentolcambioSeleccionado
        );
    }

    checkboxes.forEach(checkbox => checkbox.addEventListener('change', actualizarBoton));
    fechas.forEach(fecha => fecha.addEventListener('input', actualizarBoton));
    gerenteSelect.addEventListener('change', actualizarBoton);
    documentoInput.addEventListener('change', actualizarBoton);
    documentolcambioInput.addEventListener('change', actualizarBoton);

    // Inicializar estado del botón
    actualizarBoton();
</script>
@endsection