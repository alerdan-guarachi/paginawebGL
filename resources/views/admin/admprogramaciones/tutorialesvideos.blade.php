@extends('adminlte::page')

@section('content_header')
<h1>VIDEO TUTORIALES</h1>
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
        <div class="container py-4 text-center">

            {{-- ÁREA DE PROGRAMACIÓN --}}
            @if(auth()->user()->hasAnyRole(['MAESTRO','ADMINISTRADOR','OPERATIVO']))
            <div class="mb-5">
                <h2 class="mb-3 text-center">ÁREA DE PROGRAMACIÓN</h2>
                <div class="row justify-content-center g-3">
                    {{-- ETAPA 1 --}}
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="card-tutorial h-100 mx-auto">
                            <div class="card-body text-center">
                                <h5>ETAPA 1</h5>
                                <p class="card-text">Creación de clientes y registro de contactos, servicios y requisitos.</p>
                                <a href="https://drive.google.com/file/d/1ue6UelKwVvmqzVzGWr-2nCcvaCS1L3cV/view?usp=drive_link" target="_blank" class="btn btn-crear btn-sm w-100">VER VIDEO TUTORIAL</a>
                                <small class="text-muted mt-2 d-block" style="font-style: italic;">Actualizado el 14-10-2024</small>
                            </div>
                        </div>
                    </div>
                    {{-- ETAPA 2 --}}
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="card-tutorial h-100 mx-auto">
                            <div class="card-body text-center">
                                <h5>ETAPA 2</h5>
                                <p class="card-text">Registro de baterias y cotizaciones de atención médica.</p>
                                <a href="https://drive.google.com/file/d/1mw5gCgVys1TwlEXELL1r03RBt7dkrYIt/view?usp=drive_link" target="_blank" class="btn btn-crear btn-sm w-100">VER VIDEO TUTORIAL</a>
                                <small class="text-muted mt-2 d-block" style="font-style: italic;">Actualizado el 14-10-2024</small>
                            </div>
                        </div>
                    </div>
                    {{-- ETAPA 3 --}}
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="card-tutorial h-100 mx-auto">
                            <div class="card-body text-center">
                                <h5>ETAPA 3</h5>
                                <p class="card-text">Registro de programaciones, actualización de asistencia e informes</p>
                                <a href="https://drive.google.com/file/d/1FbjyE3ATe4sA6Yh5InYZXdig4vZqOfbt/view?usp=drive_link" target="_blank" class="btn btn-crear btn-sm w-100">VER VIDEO TUTORIAL</a>
                                <small class="text-muted mt-2 d-block" style="font-style: italic;">Actualizado el 14-10-2024</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            {{-- ÁREA CONTABLE --}}
            @if(auth()->user()->hasAnyRole(['MAESTRO','ADMINISTRADOR','CONTABLE']))
            <div class="mb-5">
                <h2 class="mb-3 text-center">ÁREA CONTABLE</h2>
                <div class="row justify-content-center g-3">
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="card-tutorial h-100 mx-auto">
                            <div class="card-body text-center">
                                <h5>ÓRDENES</h5>
                                <p class="card-text">Registro de órdenes.</p>
                                <a href="https://drive.google.com/file/d/1BkxKVx0S1xJmhN-VbAa78Ws0BVRkOAzy/view?usp=drive_link" target="_blank" class="btn btn-crear btn-sm w-100">VER VIDEO TUTORIAL</a>
                                <small class="text-muted mt-2 d-block" style="font-style: italic;">Actualizado el 06-05-2025</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="card-tutorial h-100 mx-auto">
                            <div class="card-body text-center">
                                <h5>COMPROBANTES</h5>
                                <p class="card-text">Registro de comprobantes.</p>
                                <a href="https://drive.google.com/file/d/1dXl3fKu3YEtMJ_GvsTJT6F6I-08v3ZVj/view?usp=drive_link" target="_blank" class="btn btn-crear btn-sm w-100">VER VIDEO TUTORIAL</a>
                                <small class="text-muted mt-2 d-block" style="font-style: italic;">Actualizado el 19-05-2025</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            {{-- ÁREA DE PRESTACIONES --}}
            @if(auth()->user()->hasAnyRole(['MAESTRO','ADMINISTRADOR','SUPERVISOR PRESTACIONES','EJECUTIVO PRESTACIONES']))
            <div class="mb-5">
                <h2 class="mb-3 text-center">ÁREA DE PRESTACIONES</h2>
                <div class="row justify-content-center g-3">
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="card-tutorial h-100 mx-auto">
                            <div class="card-body text-center">
                                <h5>TRÁMITES</h5>
                                <p class="card-text">Registro de los procedimientos de los trámites.</p>
                                <a href="https://drive.google.com/file/d/1pCF1I8Y3Qq-Nv6ZfRLUms4V8H2OXchFV/view?usp=sharing" target="_blank" class="btn btn-crear btn-sm w-100">VER VIDEO TUTORIAL</a>
                                <small class="text-muted mt-2 d-block" style="font-style: italic;">Actualizado el 03-10-2025</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            {{-- ÁREA PARA TODOS --}}
            <div class="mb-5">
                <h2 class="mb-3 text-center">PARA TODOS</h2>
                <div class="row justify-content-center g-3">
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="card-tutorial h-100 mx-auto">
                            <div class="card-body text-center">
                                <h5>SOLICITUD DE PRODUCTOS</h5>
                                <p class="card-text">Registro de solicitud de productos de inventario.</p>
                                <a href="https://drive.google.com/file/d/1k1GDjYPmFiypLZ2pXywAR0748JHPrOL1/view?usp=drive_link" target="_blank" class="btn btn-crear btn-sm w-100">VER VIDEO TUTORIAL</a>
                                <small class="text-muted mt-2 d-block" style="font-style: italic;">Actualizado el 03-05-2025</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

@stop

@section('css')
<link rel="styleheet" href="/css/admin_custom.css">
<style>
    .card-tutorial {
        border-radius: 12px;
        background-color: #ffffff;
        box-shadow: 0 6px 13px rgba(0,0,0,0.1);
        transition: transform 0.5s ease, box-shadow 0.5s ease;
    }

    .card-tutorial:hover {
        transform: translateY(-8px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.15);
    }

    .card-tutorial .card-body {
        padding: 20px;
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
    }

    .card-tutorial .card-title {
        font-size: 1.1rem;
        font-weight: 600;
        margin-bottom: 10px;
    }

    .card-tutorial .card-text {
        font-size: 0.95rem;
        color: #555;
        margin-bottom: 15px;
    }
    .card {
        border-radius: 10px;
    }
    .card-title {
        font-size: 1rem;
        font-weight: 600;
        text-align: center !important;
    }
    .card-text {
        font-size: 0.9rem;
        color: #555;
    }
    .btn-outline-primary {
        font-size: 0.85rem;
    }
    .table td {
        padding: 5px 10px;;
    }
    h1, th {color:#94c93b; 
        font-family: "Segoe UI";
        font-weight: 900;
    }
    h2 {color:#94c93b; 
        font-family: "Segoe UI";
        font-weight: 800;
        font-size: 20px;
    }
    h5 {color:#94c93b; 
        font-family: "Segoe UI";
        font-weight: 800;
        font-size: 15px;
    }
    .btn-crear {
        background-color:  #ffffff;
        color: #faa625;
        border-color: #faa625;
        border-radius: 5px;
        padding: 5px 10px;
        font-weight: 800;
    }
    .btn-crear:hover {
        background-color: #faa625;
        color: #ffffff;
        font-weight: 800;
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