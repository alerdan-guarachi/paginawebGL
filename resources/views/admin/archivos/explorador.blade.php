@extends('adminlte::page')

@section('content_header')
    <h1>EXPLORADOR DE ARCHIVOS</h1>
@stop

@section('content')
<style>
    h1 {
    color:#94c93b; 
    font-family: "Segoe UI";
    font-weight: 900;
}
</style>
<div class="card">
    <div class="card-body">

        @if($ruta)
            @php
                $partes = explode('/', $ruta);
                array_pop($partes);
                $rutaAnterior = implode('/', $partes);
            @endphp

            <a href="{{ $rutaAnterior ? route('admin.archivos.explorador', $rutaAnterior) : route('admin.archivos.explorador') }}"
               class="btn btn-secondary btn-sm mb-3">
                ← Volver
            </a>
        @endif

        <div class="form-group mb-3">
            <input type="text"
                   id="buscarArchivo"
                   class="form-control"
                   placeholder="Buscar carpeta o archivo...">
        </div>

        <table class="table table-bordered table-striped table-sm" id="tablaArchivos">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Tipo</th>
                    <th>Acción</th>
                </tr>
            </thead>

            <tbody>
                @foreach($carpetas as $carpeta)
                    @php
                        $nombreCarpeta = basename($carpeta);
                        $nuevaRuta = $ruta ? $ruta . '/' . $nombreCarpeta : $nombreCarpeta;
                    @endphp

                    <tr>
                        <td>
                            <i class="fas fa-folder text-warning"></i>
                            {{ $nombreCarpeta }}
                        </td>
                        <td>Carpeta</td>
                        <td>
                            <a href="{{ route('admin.archivos.explorador', $nuevaRuta) }}"
                               class="btn btn-primary btn-sm">
                                Abrir
                            </a>
                        </td>
                    </tr>
                @endforeach

                @foreach($archivos as $archivo)
                    @php
                        $nombreArchivo = $archivo->getFilename();
                        $rutaArchivo = $ruta
                            ? 'tramitesclientesita/' . $ruta . '/' . $nombreArchivo
                            : 'tramitesclientesita/' . $nombreArchivo;
                    @endphp

                    <tr>
                        <td>
                            <i class="fas fa-file text-info"></i>
                            {{ $nombreArchivo }}
                        </td>
                        <td>Archivo</td>
                        <td>
                            <a href="{{ asset($rutaArchivo) }}"
                               target="_blank"
                               class="btn btn-success btn-sm">
                                Ver
                            </a>
                        </td>
                    </tr>
                @endforeach

                <tr id="sinResultados" style="display:none;">
                    <td colspan="3" class="text-center text-muted">
                        No se encontraron resultados.
                    </td>
                </tr>
            </tbody>
        </table>

    </div>
</div>

@stop

@section('js')
<script>
    document.getElementById('buscarArchivo').addEventListener('keyup', function () {
        let filtro = this.value.toLowerCase();
        let filas = document.querySelectorAll('#tablaArchivos tbody tr:not(#sinResultados)');
        let visibles = 0;

        filas.forEach(function (fila) {
            let texto = fila.innerText.toLowerCase();

            if (texto.includes(filtro)) {
                fila.style.display = '';
                visibles++;
            } else {
                fila.style.display = 'none';
            }
        });

        document.getElementById('sinResultados').style.display = visibles === 0 ? '' : 'none';
    });
</script>
@stop