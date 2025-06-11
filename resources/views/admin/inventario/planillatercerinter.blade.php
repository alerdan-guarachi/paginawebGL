@extends('adminlte::page')

@section('content_header')
<h1>PLANILLA PAGOS</h1>
@stop

@section('css')
<link rel="stylesheet" href="{{ asset('css/opcionesmultiples.css') }}">
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
    {{-- <nav class="navbar float-right">
        <form class="form-inline" id="search-form">
            <input name="buscarpor" id="buscarpor" class="form-control mr-sm-2" type="search" placeholder="Nombre del producto" aria-label="Search">
            <button class="btn btn-outline-secondary my-2 my-sm-0" type="submit">Buscar</button>
        </form>
    </nav> --}}

    <!-- Tabla de resultados -->
    <div class="card-body">
        <div class="row">
            <div class="col-lg-8">
                <div class="row">
                    <div class="form-group col-lg-4">
                        <label for="numerocuentadebito">Nro. Cuenta Débito:</label>
                        <input type="text" class="form-control" id="numerocuentadebito" name="numerocuentadebito">
                    </div>
                    <div class="form-group col-lg-4">
                        <label for="tipotransaccion">Tipo de Transacción (1):</label>
                        <select class="form-control" id="tipotransaccion" name="tipotransaccion">
                            <option value="Transferencia a Terceros">Transferencia a Terceros</option>
                            <option value="Pago Tarjeta de Crédito">Pago Tarjeta de Crédito</option>
                            <option value="Giro al Interior">Giro al Interior</option>
                            <option value="Transferencia interbancaria Masivo">Transferencia interbancaria Masivo</option>
                            <option value="Cheque Gerencia Masivo">Cheque Gerencia Masivo</option>
                        </select>
                    </div>
                    <div class="form-group col-lg-4">
                        <label for="numeroregistros">Número de Registros:</label>
                        <input type="text" class="form-control" id="numeroregistros" name="numeroregistros">
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-lg-4">
                        <label for="montototalbs">Monto Total Bs. (8):</label>
                        <input type="text" class="form-control" id="montototalbs" name="montototalbs">
                    </div>
                    <div class="form-group col-lg-4">
                        <label for="montototalusd">Monto Total Usd. (8):</label>
                        <input type="text" class="form-control" id="montototalusd" name="montototalusd">
                    </div>
                    <div class="form-group col-lg-4">
                        <label for="montototaleuros">Monto Total Euros (8):</label>
                        <input type="text" class="form-control" id="montototaleuros" name="montototaleuros">
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-lg-4">
                        <label for="nombreabonos">Nombre de Abonos:</label>
                        <input type="text" class="form-control" id="nombreabonos" name="nombreabonos">
                    </div>
                    <div class="form-group col-lg-4">
                        <label for="glosaabonos">Glosa de Abonos:</label>
                        <input type="text" class="form-control" id="glosaabonos" name="glosaabonos">
                    </div>
                    <div class="form-group col-lg-4">
                        <label for="fechaejecucion">Fecha de Ejecución:</label>
                        <input type="date" class="form-control" id="fechaejecucion" name="fechaejecucion">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

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