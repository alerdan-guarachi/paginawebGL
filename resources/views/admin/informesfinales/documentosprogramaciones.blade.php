@extends('adminlte::page')

@section('content_header')
<h1>ESTADO DE PROGRAMACIONES</h1>
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
        <nav class="navbar float-right">
            <form class="form-inline">
                <input name="buscarpor" class="form-control mr-sm-2" type="search" placeholder="Cliente" aria-label="Search">
                <button class="btn btn-buscar my-2 my-sm-0" type="submit">Buscar</button>
            </form>
        </nav>
        <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Cliente</th>
                    <th>Proveedor</th>
                    <th>Fecha de Batería</th>
                    {{-- <th>Área</th> --}}
                    <th>Acción</th>
                    <th>Fecha asignada</th>
                    <th>Fecha de atención</th>
                    <th>Estado Doc.</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($programacionclientes as $programacioncliente)
                <tr>
                    <td>{{ $programacioncliente->id }}</td>
                    <td>{{ $programacioncliente->clienteitanombre }}</td>
                    <td>{{ $programacioncliente->proveedornombre }}</td>
                    <td>{{ $programacioncliente->fechabateria }}</td>
                    {{-- <td>{{ $programacioncliente->areanombre }}</td> --}}
                    <td>{{ $programacioncliente->accionnombre }}</td>
                    <td>{{ $programacioncliente->fechaasignada }}</td>
                    <td>
                        @if ($programacioncliente->fechaatencionprogramacion !== "PENDIENTE" && $programacioncliente->fechaatencionprogramacion !== null)
                            {{ $programacioncliente->fechaatencionprogramacion }}
                        @else
                        <div class="pendiente">PENDIENTE</div>
                        @endif
                    </td>
                    
                    <td>
                        @if ($programacioncliente->document !== "PENDIENTE" && $programacioncliente->document !== null)
                        <abbr title="VER DOCUMENTO">
                            <a href="{{ asset('/documentacionclientesita/' . $programacioncliente->clienteitaid . '/' . $programacioncliente->document) }}" class="btn btn-verdocumentacion" target="_blank">
                                <i class="fas fa-eye"></i>
                            </a>
                        </abbr>
                        @else
                        <div class="pendiente">PENDIENTE</div>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@stop

@section('css')
<link rel="styleheet" href="/css/admin_custom.css">
<style>
    h1, th {color:#94c93b; 
        font-family: "Segoe UI";
        font-weight: 900;
        }
        .btn-editar {
                background-color:  #ffffff;
                color: #94c93b;
                border-color: #94c93b;
                border-radius: 5px;
            }
        .btn-editar:hover {
                background-color: #94c93b;
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
            .btn-verdocumentacion {
        background-color:  #ffffff;
        color: #94c93b;
        border-color: #94c93b;
        border-radius: 5px;
        padding: 2px 10px;
    }
    .btn-verdocumentacion:hover {
        background-color: #94c93b;
        color: #ffffff;
    }
    .pendiente {color:#fb2525; 
        font-weight: 900;
        font-size: 15px;
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
