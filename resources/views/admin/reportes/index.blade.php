@extends('adminlte::page')

@section('content_header')
<h1>REPORTES</h1>
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
        <form action="{{ route('generar.pdf') }}" method="GET">
            <div class="row">
                <div class="col-lg-6">
                    <div class="form-group">
                        <label for="tabla">Tabla:</label>
                        <select class="form-control" name="tabla" id="tabla">
                            <option value=""></option>
                            <option value="bateriasubclientes">BATERÍA DE CLIENTES</option>
                            <option value="bateriaproveedores">BATERIA DE PROVEEDORES</option>
                            <option value="areaacciones">BATERIA GENERAL</option>
                            <option value="clientescomunes">CLIENTES COMUNES</option>
                            <option value="clientesauditoria">CLIENTES DE AUDITORÍA MÉDICA</option>
                            <option value="clientesbancos">CLIENTES DE BANCOS</option>
                            <option value="contactosubclientes">CONTACTOS DE CLIENTES</option>
                            <option value="clientes">CLIENTES ITA</option>
                            <option value="programacionsubclientescomunes">PROGRAMACIÓN DE CLIENTES COMUNES</option>
                            <option value="programacionsubclientesauditoria">PROGRAMACIÓN DE CLIENTES DE AUDITORÍA MÉDICA</option>
                            <option value="programacionsubclientesita">PROGRAMACIÓN DE CLIENTES ITA</option>
                            <option value="proveedores">PROVEEDORES</option>
                        </select>
                    </div>
                    <div id="sucursalproveedor_div" class="form-group" style="display: none;">
                        <label for="sucursal">Sucursal:</label>
                        <select class="form-control" name="sucursal" id="sucursal">
                            <option value=""></option>
                            <option value="tipo1">COCHABAMBA</option>
                            <option value="tipo2">SANTA CRUZ</option>
                        </select>
                    </div>
                    <div id="sucursalareaaccion_div" class="form-group" style="display: none;">
                        <label for="sucursalareaaccion">Sucursal:</label>
                        <select class="form-control" name="sucursalareaaccion" id="sucursalareaaccion">
                            <option value=""></option>
                            <option value="tipo1">COCHABAMBA</option>
                            <option value="tipo2">SANTA CRUZ</option>
                        </select>
                    </div>
                    <div id="sucursalbateriaproveedores_div" class="form-group" style="display: none;">
                        <label for="sucursalbateriaproveedores">Sucursal:</label>
                        <select class="form-control" name="sucursalbateriaproveedores" id="sucursalbateriaproveedores">
                            <option value=""></option>
                            <option value="tipo1">COCHABAMBA</option>
                            <option value="tipo2">SANTA CRUZ</option>
                        </select>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="form-group">
                        <label for="fecha_inicial">(Opcional) Desde:</label>
                        <input class="form-control" type="date" name="fecha_inicial">
                    </div>
                    <div class="form-group">
                        <label for="fecha_final">(Opcional) Hasta:</label>
                        <input class="form-control" type="date" name="fecha_final">
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-generar">Generar PDF</button>
        </form>
    </div>
</div>
<script>
    document.getElementById('tabla').addEventListener('change', function() {
        var tabla = this.value;
        var tipoAsociadoDiv = document.getElementById('sucursalproveedor_div');

        if (tabla === 'proveedores') {
            tipoAsociadoDiv.style.display = 'block';
        } else {
            tipoAsociadoDiv.style.display = 'none';
        }
    });

    document.getElementById('tabla').addEventListener('change', function() {
        var tabla = this.value;
        var tipoAsociadoDiv = document.getElementById('sucursalareaaccion_div');

        if (tabla === 'areaacciones') {
            tipoAsociadoDiv.style.display = 'block';
        } else {
            tipoAsociadoDiv.style.display = 'none';
        }
    });

    document.getElementById('tabla').addEventListener('change', function() {
        var tabla = this.value;
        var tipoAsociadoDiv = document.getElementById('sucursalbateriaproveedores_div');

        if (tabla === 'bateriaproveedores') {
            tipoAsociadoDiv.style.display = 'block';
        } else {
            tipoAsociadoDiv.style.display = 'none';
        }
    });
</script>
@stop

@section('css')
<link rel="styleheet" href="/css/admin_custom.css">
<style>
    .btn-generar {
    background-color:  #ffffff;
    color: #94c93b;
    border-color: #94c93b;
    border-radius: 5px;
    padding: 10px 20px;
}
.btn-generar:hover {
    background-color: #94c93b;
    color: #ffffff;
}
    h1, th {color:#94c93b; 
        font-family: "Segoe UI";
        font-weight: 900;
        }
        .btn-editar {
                background-color:  #ffffff;
                color: #0400ff;
                border-color: #0400ff;
                border-radius: 5px;
            }
        .btn-editar:hover {
                background-color: #0400ff;
                color: #ffffff;
            }
        .btn-eliminar {
                background-color:  #ffffff;
                color: #ff0000;
                border-color: #ff0000;
                border-radius: 5px;
            }
        .btn-eliminar:hover {
                background-color: #ff0000;
                color: #ffffff;
            }
        .btn-crear {
                background-color:  #ffffff;
                color: #94c93b;
                border-color: #94c93b;
                border-radius: 5px;
                padding: 10px 20px;
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