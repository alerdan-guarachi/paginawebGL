@extends('adminlte::page')
    
@section('content_header')
<a class="btn btn-sm float-right btn-regresar" href="{{ route('admin.asociados.verclienteauditoria', $clienteauditoria) }}">REGRESAR</a>
<h5>DOCUMENTACIÓN DE:</h5>
<h3>{{$clienteauditoria->nombrecompleto}}</h3>
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
        <nav class="navbar navbar-expand-lg float-right">
            <div class="container-fluid">
                <div class="d-flex flex-wrap align-items-center">
                    <form action="{{ route('buscardocumentoclienteauditoria', $clienteauditoria) }}" method="get" class="form-inline">
                        <div class="flex-grow-1">
                            <select name="buscarpor" class="form-control mr-sm-2">
                                <option value="" disabled selected>Fecha de Bateria</option>
                                @foreach($fechas as $fecha)
                                    <option value="{{ $fecha }}">{{ $fecha }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button id="btn-buscar" class="btn btn-buscar my-2 my-sm-0" type="submit">Buscar</button>
                    </form>
                </div>
            </div>
        </nav>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Fecha de bateria</th>
                        <th>Accion</th>
                        <th>Documento</th>
                        <th>Imagen 1</th>
                        <th>Imagen 2</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($documentacionclientes as $documentacioncliente)
                    <tr>
                        <td>{{$documentacioncliente->fechabateria}}</td>
                        <td>{{$documentacioncliente->accion}}</td>
                        <td>
                            @if ($documentacioncliente->document)
                            <a href="{{ asset('file/' . $documentacioncliente->document) }}" class="btn btn-verdocumentacion" data-url="{{ asset('file/' . $documentacioncliente->document) }}">
                                <i class="fas fa-eye"></i>
                            </a>
                            
                            <div id="myModal" class="modal">
                                <div class="modal-content">
                                    <h5>VISTA PREVIA DE DOCUMENTO</h5>
                                    <span class="close">&times;</span>
                                    <div class="modal-body">
                                        <iframe id="iframeDocument" frameborder="0" style="width: 100%; height: 100%;"></iframe>
                                    </div>
                                </div>
                            </div>
                            
                            <script>
                                var modal = document.getElementById("myModal");
                                var span = document.getElementsByClassName("close")[0];
                                span.onclick = function () {
                                    modal.style.animationName = "modalclose";
                                    setTimeout(function () {
                                        modal.style.display = "none";
                                        document.getElementById("iframeDocument").setAttribute("src", "");
                                        modal.style.animationName = "";
                                    }, 300);
                                };
                                var documentLinks = document.querySelectorAll(".btn-verdocumentacion");
                                documentLinks.forEach(function (link) {
                                    link.addEventListener("click", function (event) {
                                        event.preventDefault();
                                        var documentUrl = link.getAttribute("data-url");
                                        modal.style.display = "block";
                                        modal.style.animationName = "modalopen";
                                        document.getElementById("iframeDocument").setAttribute("src", documentUrl);
                                    });
                                });

                                modal.onclick = function (event) {
                                    if (event.target == modal) {
                                        return;
                                    }
                                };
                            </script>
                            
                            <style>
                                .modal {
                                    display: none;
                                    position: fixed;
                                    z-index: 9999;
                                    left: 0;
                                    top: 0;
                                    width: 100%;
                                    height: 100%;
                                    overflow: auto;
                                    background-color: rgba(0, 0, 0, 0.7);
                                }
                                .modal-content {
                                    background-color: #fefefe;
                                    margin: auto;
                                    padding: 20px;
                                    border: 1px solid #888;
                                    width: 100%;
                                    max-width: 900px;
                                    height: 100%;
                                    max-height: 100vh;
                                    position: relative;
                                    animation-name: modalopen;
                                    animation-duration: 0.3s;
                                    overflow: hidden;
                                }
                            
                                @keyframes modalopen {
                                    from {
                                        opacity: 0;
                                        transform: translateY(-50px);
                                    }
                                    to {
                                        opacity: 1;
                                        transform: translateY(0);
                                    }
                                }
                                .modal-body {
                                    width: 100%;
                                    height: calc(100% - 40px);
                                    text-align: center;
                                    overflow: auto;
                                }
                                .close {
                                    color: #ff0000;
                                    position: absolute;
                                    top: 10px;
                                    right: 15px;
                                    font-size: 40px;
                                    font-weight: bold;
                                    cursor: pointer;
                                }
                                .close:hover,
                                .close:focus {
                                    color: #000000;
                                    text-decoration: none;
                                }
                            </style>

                            @can('descargardocumentacion')
                            <a href="{{ asset('file/' . $documentacioncliente->document) }}" class="btn btn-descargardocumentacion" download>
                                <i class="fas fa-download"></i>
                            </a>
                            @endcan
                            @endif
                        </td>
                        <td>
                            @if ($documentacioncliente->image)
                            <a href="{{ asset('image/' . $documentacioncliente->image) }}" class="btn btn-verimagen" target="_blank">
                                <i class="fas fa-eye"></i>
                            </a>
                            @can('descargardocumentacion')
                            <a href="{{ asset('image/' . $documentacioncliente->image) }}" class="btn btn-descargarimagen" download>
                                <i class="fas fa-download"></i>
                            </a>
                            @endcan
                            @endif
                        </td>
                        <td>
                            @if ($documentacioncliente->image2)
                            <a href="{{ asset('image/' . $documentacioncliente->image2) }}" class="btn btn-verimagen" target="_blank">
                                <i class="fas fa-eye"></i>
                            </a>
                            @can('descargardocumentacion')
                            <a href="{{ asset('image/' . $documentacioncliente->image2) }}" class="btn btn-descargarimagen" download>
                                <i class="fas fa-download"></i>
                            </a>
                            @endcan
                            @endif
                        </td>
                    </tr>
                    @endforeach
                    </tbody>
                    
            </table>
        </div>
    </div>
</div>
@stop

@section('css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<link rel="styleheet" href="/css/admin_custom.css">
<style>
    h1, th {
        color:#94c93b; 
        font-family: "Segoe UI";
        font-weight: 900;
    }
    h5 {
        color:#94c93b; 
        font-family: "Segoe UI";
        font-weight: 500;
        margin-bottom: 0%;
        }
    h3 {
        color:#94c93b; 
        font-family: "Segoe UI";
        font-weight: 1000;
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
    .btn-descargardocumentacion {
        background-color:  #ffffff;
        color: #94c93b;
        border-color: #94c93b;
        border-radius: 5px;
    }
    .btn-descargardocumentacion:hover {
        background-color: #94c93b;
        color: #ffffff;
    }
    .btn-verdocumentacion {
        background-color:  #ffffff;
        color: #faa625;
        border-color: #faa625;
        border-radius: 5px;
    }
    .btn-verdocumentacion:hover {
        background-color: #faa625;
        color: #ffffff;
    }
    .btn-verimagen {
        background-color:  #ffffff;
        color: #faa625;
        border-color: #faa625;
        border-radius: 5px;
    }
    .btn-verimagen:hover {
        background-color: #faa625;
        color: #ffffff;
    }
    .btn-descargarimagen {
        background-color:  #ffffff;
        color: #94c93b;
        border-color: #94c93b;
        border-radius: 5px;
    }
    .btn-descargarimagen:hover {
        background-color: #94c93b;
        color: #ffffff;
    }
    
    .btn-regresar {
        background-color: #ffffff;
        color: #2926e2;
        border-color: #2926e2;
        border-radius: 5px;
        padding: 10px 10px;
    }
    .btn-regresar:hover {
        background-color: #2926e2;
        color: #ffffff;
    }
</style>
@stop

@section('js')
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script type="text/javascript" src="https://jeremyfagis.github.io/dropify/dist/js/dropify.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://jeremyfagis.github.io/dropify/dist/css/dropify.min.css"> 
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

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
@endsection