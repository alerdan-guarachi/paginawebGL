@extends('adminlte::page')

@section('content_header')
<h1>CREAR PERFIL </h1>
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
        <div class="row ">
            <div class="col-lg-9">
                {!! Form::open(['route' => 'admin.personal.store', 'method'=>'POST', 'files' => true]) !!}
                {!! Form::hidden('usuarioid', auth()->user()->id) !!}
                {!! Form::hidden('usuarioregistro', auth()->user()->name) !!}
                
                <div class="row">
                    <div class="form-group col-lg-8">
                        {!! Form::label('nombrecompleto', 'Nombre Completo:') !!}
                        {!! Form::text('nombrecompleto', $usuarionombre,['class' => 'form-control', 'maxlength' => '250', 'readonly' => 'readonly']) !!}
                        @error('nombrecompleto')
                            <small class="text-danger fas fa-exclamation-circle">
                                {{$message}}
                            </small>
                        @enderror
                    </div>
                    <div class="form-group col-lg-4">
                        {!! Form::label('sucursal', 'Sucursal:') !!}
                        {!! Form::select('sucursal', $sucursal, null, ['class' => 'form-control', 'maxlength' => '45', 'placeholder' => '']) !!}
                        @error('sucursal')
                            <small class="text-danger fas fa-exclamation-circle">
                                {{$message}}
                            </small>
                        @enderror
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-lg-2">
                        {!! Form::label('ci', 'CI:') !!}
                        {!! Form::text('ci', null, ['class' => 'form-control', 'maxlength' => '45']) !!}
                        @error('ci')
                            <small class="text-danger fas fa-exclamation-circle">
                                {{$message}}
                            </small>
                        @enderror
                    </div>
                    <div class="form-group col-lg-2">
                        {!! Form::label('ciexp', 'CI exp.:') !!}
                        {!! Form::select('ciexp', $ciudadexp, null, ['class' => 'form-control', 'maxlength' => '45', 'placeholder' => '']) !!}
                        @error('ciexp')
                            <small class="text-danger fas fa-exclamation-circle">
                                {{$message}}
                            </small>
                        @enderror
                    </div>
                    <div class="form-group col-lg-5">
                        {!! Form::label('direccion', 'Dirección:') !!}
                        {!! Form::text('direccion', null, ['class' => 'form-control', 'maxlength' => '255']) !!}
                        @error('direccion')
                            <small class="text-danger fas fa-exclamation-circle">
                                {{$message}}
                            </small>
                        @enderror
                    </div>
                    <div class="form-group col-lg-3">
                        {!! Form::label('cargo', 'Cargo:') !!}
                        {!! Form::text('cargo', null, ['class' => 'form-control', 'maxlength' => '90']) !!}
                        @error('cargo')
                            <small class="text-danger fas fa-exclamation-circle">
                                {{$message}}
                            </small>
                        @enderror
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-lg-4">
                        {!! Form::label('nit', 'NIT:') !!}
                        {!! Form::text('nit', null, ['class' => 'form-control', 'maxlength' => '45']) !!}
                        @error('nit')
                            <small class="text-danger fas fa-exclamation-circle">
                                {{$message}}
                            </small>
                        @enderror
                    </div>
                    <div class="form-group col-lg-4">
                        {!! Form::label('celular', 'Celular:') !!}
                        {!! Form::text('celular', null, ['class' => 'form-control', 'maxlength' => '45']) !!}
                        @error('celular')
                            <small class="text-danger fas fa-exclamation-circle">
                                {{$message}}
                            </small>
                        @enderror
                    </div>
                    <div class="form-group col-lg-4">
                        {!! Form::label('email', 'Email:') !!}
                        {!! Form::text('email', null, ['class' => 'form-control', 'maxlength' => '45']) !!}
                        @error('email')
                            <small class="text-danger fas fa-exclamation-circle">
                                {{$message}}
                            </small>
                        @enderror
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-lg-6">
                        {!! Form::label('banco', 'Nombre de Banco:') !!}
                        {!! Form::select('banco', $bancos, null, ['class' => 'form-control', 'maxlength' => '255', 'placeholder' => '']) !!}
                        @error('banco')
                            <small class="text-danger fas fa-exclamation-circle">
                                {{$message}}
                            </small>
                        @enderror
                    </div>
                    <div class="form-group col-lg-6">
                        {!! Form::label('numcuenta', 'Nro. de Cuenta:') !!}
                        {!! Form::text('numcuenta', null, ['class' => 'form-control', 'maxlength' => '45']) !!}
                        @error('numcuenta')
                            <small class="text-danger fas fa-exclamation-circle">
                                {{$message}}
                            </small>
                        @enderror
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-lg-6">
                        {!! Form::label('contacto', 'Nombre de Contacto:') !!}
                        {!! Form::text('contacto', null, ['class' => 'form-control', 'maxlength' => '45']) !!}
                        @error('contacto')
                            <small class="text-danger fas fa-exclamation-circle">
                                {{$message}}
                            </small>
                        @enderror
                    </div>
                    <div class="form-group col-lg-6">
                        {!! Form::label('celcontacto', 'Celular de Contacto:') !!}
                        {!! Form::text('celcontacto', null, ['class' => 'form-control', 'maxlength' => '45']) !!}
                        @error('celcontacto')
                            <small class="text-danger fas fa-exclamation-circle">
                                {{$message}}
                            </small>
                        @enderror
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-lg-4">
                        {!! Form::label('fechaingreso', 'Fecha de ingreso:') !!}
                        {!! Form::date('fechaingreso', null, ['class' => 'form-control', 'maxlength' => '45']) !!}
                        @error('fechaingreso')
                            <small class="text-danger fas fa-exclamation-circle">
                                {{$message}}
                            </small>
                        @enderror
                    </div>
                    <div class="form-group col-lg-4">
                        {!! Form::label('fechasalida', 'Fecha de salida:') !!}
                        {!! Form::date('fechasalida', null, ['class' => 'form-control', 'maxlength' => '45']) !!}
                        @error('fechasalida')
                            <small class="text-danger fas fa-exclamation-circle">
                                {{$message}}
                            </small>
                        @enderror
                    </div>
                    <div class="form-group col-lg-4">
                        {!! Form::label('estado', 'Estado:') !!}
                        {!! Form::select('estado', $estado, null, ['class' => 'form-control', 'maxlength' => '45', 'placeholder' => '']) !!}
                        @error('estado')
                            <small class="text-danger fas fa-exclamation-circle">
                                {{$message}}
                            </small>
                        @enderror
                    </div>
                    
                </div>
            </div>
            <div class="col-lg-3">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="form-group">
                            <label for="file">Foto de perfil:</label>
                            <input type="file" class="form-control-file" id="picture" name="picture">
                            
                            <img id="vista-previa" src="#" alt="Vista previa de la imagen" style="display: none;">
                            @error('picture')
                            <small class="text-danger fas fa-exclamation-circle">
                                {{$message}}
                            </small>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
            <script>
                document.getElementById('abrir-modal').addEventListener('click', function(event) {
                    event.preventDefault();
                    document.getElementById('cameraModal').style.display = 'block';
                    iniciarCamara();
                });
                document.getElementById('cerrar-modal').addEventListener('click', function() {
                    detenerCamara();
                    document.getElementById('cameraModal').style.display = 'none';
                });
                var cameraStream = null;
                function iniciarCamara() {
                    navigator.mediaDevices.getUserMedia({ video: true })
                    .then(function(stream) {
                        cameraStream = stream;
                        var video = document.getElementById('camara');
                        video.srcObject = stream;
                        video.play();
                    })
                    .catch(function(error) {
                        console.log('Error al acceder a la cámara: ', error);
                    });
                }
                function detenerCamara() {
                    if (cameraStream) {
                        cameraStream.getTracks().forEach(track => track.stop());
                    }
                }
                document.getElementById('capturar-btn').addEventListener('click', function() {
                    var canvas = document.createElement('canvas');
                    var video = document.getElementById('camara');
                    canvas.width = video.videoWidth;
                    canvas.height = video.videoHeight;
                    var ctx = canvas.getContext('2d');
                    ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
                    canvas.toBlob(function(blob) {
                        var pictureInput = document.getElementById('picture');
                        pictureInput.value = null;
                        var newFile = new File([blob], 'photo.png', { type: 'image/png' });
                        var dataTransfer = new DataTransfer();
                        dataTransfer.items.add(newFile);
                        pictureInput.files = dataTransfer.files;
                        var event = new Event('change');
                        pictureInput.dispatchEvent(event);
                        var vistaPrevia = document.getElementById('vista-previa');
                        vistaPrevia.src = URL.createObjectURL(blob);
                        vistaPrevia.style.display = 'block';
                        detenerCamara();
                        document.getElementById('cameraModal').style.display = 'none';
                    });
                });
            </script>
            <style>
                .buttons-container {
                    display: flex;
                    justify-content: space-between;
                }

                .modal {
                    position: fixed;
                    top: 50%;
                    left: 50%;
                    transform: translate(-50%, -50%);
                    border-radius: 20px;
                    padding: 20px;
                    z-index: 1000;
                    max-width: 700px;
                    width: 100%;
                    margin-top: 50px;
                }
                .modal-header {
                    text-align: center;
                }
                .modal-header h2 {
                    margin: 0;
                    color: #333;
                }
                .modal-body {
                    text-align: center;
                }
                #camara {
                    width: 100%;
                    height: auto;
                    border-radius: 15px;
                }
                .capturar-btn-style {
                    display: block;
                    width: 30%;
                    padding: 10px;
                    margin-top: 10px;
                    background-color: #faa625;
                    color: #fff;
                    border: none;
                    border-radius: 8px;
                    cursor: pointer;
                    margin: auto;
                }
                .modal-footer {
                    text-align: center;
                    margin-top: -20px;
                }
                .btn-cancelar {
                    background-color: #dc3545;
                    color: #fff;
                    border: none;
                    border-radius: 8px;
                    padding: 10px 10px;
                    cursor: pointer;
                    margin-top: 0px;
                }
                .text-danger {
                    color: #dc3545;
                }
            </style>
            
        </div>
        {!! Form::submit('CREAR PERFIL', ['class' => 'btn btn-crear']) !!}
        {!! Form::close() !!}
    </div>
</div>
@stop
@section('js')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script type="text/javascript" src="https://jeremyfagis.github.io/dropify/dist/js/dropify.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://jeremyfagis.github.io/dropify/dist/css/dropify.min.css"> 
<script>
$('.dropify').dropify();
</script>
<script>
    function mostrarVistaPrevia(input) {
        if (input.files && input.files[0]) {
            var lector = new FileReader();
            lector.onload = function(e) {
                $('#vista-previa').attr('src', e.target.result);
                $('#vista-previa').show();
            }
            lector.readAsDataURL(input.files[0]);
        }
    }

    $(document).ready(function() {
        $("#picture").change(function() {
            mostrarVistaPrevia(this);
        });
    });
</script>

@endsection
@section('css')
<link rel="styleheet" href="/css/admin_custom.css">
<style>
    h1, th {color:#94c93b; 
        font-family: "Segoe UI";
        font-weight: 900;
        }
        #vista-previa {
        display: block;
        max-width: 100%;
        max-height: 100%;
        margin-top: 10px;
        border: 1px solid #ccc;
        padding: 5px;
        box-shadow: 0px 0px 5px 0px rgba(0, 0, 0, 0.2);
        }
        h2 {font-size:  80%;
        text-align : right;
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
            .capturar-btn-style {
        background-color: #faa625;
        color: #fff;
        border: none;
        padding: 10px 20px;
        border-radius: 7px;
        text-decoration: none;
        cursor: pointer;
    }
    .capturar-btn-style:hover {
        background-color: #94c93b;
    }
</style>
@stop