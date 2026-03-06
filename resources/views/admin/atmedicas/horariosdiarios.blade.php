@extends('adminlte::page')

@section('content_header')
<a class="btn btn-sm float-right btn-cartareclamo" data-toggle="modal" data-target="#nuevoHorariodiario" style="margin-right: -2px;">AGREGAR HORARIO DIARIO</a>
<h1>HORARIOS DE ATENCIÓN MÉDICA DIARIOS</h1>
@stop

@section('css')
<link rel="stylesheet" href="{{ asset('css/tramitesgestora.css') }}">
@stop

@section('content')
@if(session('error'))
    <div class="alert alert-warning alert-dismissible fade show">
        <strong>Atención:</strong> {{ session('error') }}
        <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
@endif
@if (session('success'))
    <div id="alert-success" class="alert alert-success">
        <strong>{{ session('success') }}</strong>
    </div>
    <script>
        setTimeout(function() {
            $('#alert-success').fadeOut('fast');
        }, 3000);
    </script>
@endif

<div class="card">
    <div class="card-header">
        <h5>LISTA DE HORARIOS</h5>
    </div>
    <div class="card-body" style="margin-top: -20px;">
        <div class="row">
            <div class="container-fluid">
                <div class="card">
                    <div class="card-body p-2">
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered table-striped">
                                <thead class="table-secondary">
                                    <tr>
                                        <th>ID Prov.</th>
                                        <th>Proveedor Médico</th>
                                        <th>Sucursal</th>
                                        <th>Fecha</th>
                                        <th>Horarios</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($atmeddiario as $atmed)
                                        <tr>
                                            <td>{{ $atmed->proveedorid }}</td>
                                            <td>{{ $atmed->proveedornombre }}</td>
                                            <td>{{ $atmed->sucursal }}</td>
                                            <td>{{ $atmed->fecha }}</td>
                                            <td>{{ $atmed->horainicio }} - {{ $atmed->horafin }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ===================== MODAL NUEVO HORARIO ===================== --}}
<div class="modal fade" id="nuevoHorariodiario" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('admin.atmedicas.guardarhorariodiario') }}">
@csrf
<div class="modal-content">

    <div class="modal-header">
        <h5 class="modal-title">NUEVO HORARIO DIARIO</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
    </div>

    <div class="modal-body">

        {{-- PROVEEDOR --}}
        <label>Proveedor</label>
        <select name="proveedorid" id="proveedorSelect" class="form-control mb-2" required>
            <option value="">Seleccione proveedor...</option>
            @foreach($proveedores as $prov)
                <option value="{{ $prov->id }}"
                        data-nombre="{{ $prov->proveedor }}">
                    {{ $prov->proveedor }}
                </option>
            @endforeach
        </select>

        {{-- ESTE CAMPO SE LLENA SOLO --}}
        <input type="hidden" name="proveedornombre" id="proveedornombre">

        {{-- SUCURSAL --}}
        <label>Sucursal</label>
        <select name="sucursal" class="form-control mb-2" required>
            <option value="">Seleccione sucursal...</option>
            <option value="SANTA CRUZ">SANTA CRUZ</option>
            <option value="COCHABAMBA">COCHABAMBA</option>
        </select>

        {{-- FECHA --}}
        <label>Fecha</label>
        <input type="date" name="fecha" class="form-control mb-2"
               min="{{ date('Y-m-d') }}" required>

        <div class="row">
            <div class="col-md-6">
                <label>Hora inicio</label>
                <input type="time" name="horainicio" class="form-control mb-2" required>
            </div>
            <div class="col-md-6">
                <label>Hora fin</label>
                <input type="time" name="horafin" class="form-control mb-2" required>
            </div>
        </div>

    </div>

    <div class="modal-footer">
        <button class="btn btn-crear">GUARDAR</button>
    </div>

</div>
</form>
<script>
document.getElementById('proveedorSelect').addEventListener('change', function () {
    let selected = this.options[this.selectedIndex];
    document.getElementById('proveedornombre').value =
        selected.getAttribute('data-nombre');
});
</script>
    </div>
</div>

@stop
