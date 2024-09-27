@extends('adminlte::page')

@section('content_header')
<h1>Nuevo Cliente</h1>
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
<div class="container col-lg-12">
        <div class="card">
            <div class="card-body">
                <div class="row ">
                    <div class="col-lg-4">

                        {!! Form::open(['route' => 'admin.clientes.store', 'method'=>'POST', 'files' => true]) !!}
                
                            {!! Form::hidden('users_id', auth()->user()->id) !!}
                            {!! Form::hidden('usuarioregistro', auth()->user()->name) !!}
                            <div class="form-group">
                                {!! Form::label('sucursal', 'Sucursal:') !!}
                                {!! Form::select('sucursal', $suc, null, ['class' => 'form-control', 'placeholder' => '']) !!}
                                @error('sucursal')
                                    <small class="text-danger fas fa-exclamation-circle">
                                        {{$message}}
                                    </small>
                                @enderror
                            </div>
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        {!! Form::label('apepaterno', 'Apellido Paterno:') !!}
                                        {!! Form::text('apepaterno', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '45']) !!}
                                        @error('apepaterno')
                                            <small class="text-danger fas fa-exclamation-circle">
                                                {{$message}}
                                            </small>
                                        @enderror
                                    </div>
                                </div>
                            
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        {!! Form::label('apematerno', 'Apellido Materno:') !!}
                                        {!! Form::text('apematerno', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '45']) !!}
                                        @error('apematerno')
                                            <small class="text-danger fas fa-exclamation-circle">
                                                {{$message}}
                                            </small>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                {!! Form::label('nombres', 'Nombre(s):') !!}
                                {!! Form::text('nombres', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '45']) !!}
                                @error('nombres')
                                    <small class="text-danger fas fa-exclamation-circle">
                                        {{$message}}
                                    </small>
                                @enderror
                            </div>
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        {!! Form::label('tipoidentificacion', 'Tipo de identificación:') !!}
                                        {!! Form::select('tipoidentificacion', $tipoide, null, ['class' => 'form-control', 'placeholder' => '']) !!}
                                        @error('tipoidentificacion')
                                            <small class="text-danger fas fa-exclamation-circle">
                                                {{$message}}
                                            </small>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        {!! Form::label('ci', 'CI / Identificacion:') !!}
                                        {!! Form::text('ci', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '45']) !!}
                                        @error('ci')
                                            <small class="text-danger fas fa-exclamation-circle">
                                                {{$message}}
                                            </small>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-3">
                                    <div class="form-group">
                                        {!! Form::label('cicomplemento', 'Compl.:') !!}
                                        {!! Form::text('cicomplemento', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '45']) !!}
                                        @error('cicomplemento')
                                            <small class="text-danger fas fa-exclamation-circle">
                                                {{$message}}
                                            </small>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="form-group">
                                        {!! Form::label('ciexp', 'C/ exp.:') !!}
                                        {!! Form::select('ciexp', $ciudadexp, null, ['class' => 'form-control', 'placeholder' => '']) !!}
                                        @error('ciexp')
                                            <small class="text-danger fas fa-exclamation-circle">
                                                {{$message}}
                                            </small>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        {!! Form::label('fechavencci', 'Fecha Venc/CI.:') !!}
                                        {!! Form::date('fechavencci', \Carbon\Carbon::now(), ['class' => 'form-control']) !!}
                                        @error('fechavencci')
                                            <small class="text-danger fas fa-exclamation-circle">
                                                {{ $message }}
                                            </small>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        {!! Form::label('fechanacimiento', 'Fecha de nac.:') !!}
                                        {!! Form::date('fechanacimiento', \Carbon\Carbon::now(), ['class' => 'form-control', 'id' => 'fecha_nacimiento']) !!}
                                        @error('fechanacimiento')
                                            <small class="text-danger fas fa-exclamation-circle">
                                                {{ $message }}
                                            </small>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        {!! Form::label('edad', 'Edad:') !!}
                                        {!! Form::text('edad', null, ['class' => 'form-control', 'readonly' => true, 'id' => 'edad']) !!}
                                        @error('edad')
                                            <small class="text-danger fas fa-exclamation-circle">
                                                {{ $message }}
                                            </small>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        {!! Form::label('lugarnacimiento', 'Ciudad de nac.:') !!}
                                        {!! Form::select('lugarnacimiento', $departamentos, null, ['class' => 'form-control', 'placeholder' => '']) !!}
                                        @error('lugarnacimiento')
                                            <small class="text-danger fas fa-exclamation-circle">
                                                {{$message}}
                                            </small>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        {!! Form::label('genero', 'Genero:') !!}
                                        {!! Form::select('genero', $genero, null, ['class' => 'form-control', 'placeholder' => '']) !!}
                                        @error('genero')
                                            <small class="text-danger fas fa-exclamation-circle">
                                                {{$message}}
                                            </small>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        {!! Form::label('estadocivil', 'Estado civil:') !!}
                                        {!! Form::select('estadocivil', $estciv, null, ['class' => 'form-control', 'placeholder' => '']) !!}
                                        @error('estadocivil')
                                            <small class="text-danger fas fa-exclamation-circle">
                                                {{$message}}
                                            </small>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        {!! Form::label('email', 'Email:') !!}
                                        {!! Form::text('email', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '45']) !!}
                                        @error('email')
                                            <small class="text-danger fas fa-exclamation-circle">
                                                {{$message}}
                                            </small>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        {!! Form::label('ocupacion', 'Profesión / Ocupación:') !!}
                                        {!! Form::text('ocupacion', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '45']) !!}
                                        @error('ocupacion')
                                            <small class="text-danger fas fa-exclamation-circle">
                                                {{$message}}
                                            </small>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        {!! Form::label('gradoinstruccion', 'Grado de Institución:') !!}
                                        {!! Form::select('gradoinstruccion', $gradins, null, ['class' => 'form-control', 'placeholder' => '']) !!}
                                        @error('gradoinstruccion')
                                            <small class="text-danger fas fa-exclamation-circle">
                                                {{$message}}
                                            </small>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        {!! Form::label('celular', 'Celular:') !!}
                                        {!! Form::text('celular', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '45']) !!}
                                        @error('celular')
                                            <small class="text-danger fas fa-exclamation-circle">
                                                {{$message}}
                                            </small>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        {!! Form::label('telefono', 'Telefono:') !!}
                                        {!! Form::text('telefono', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '45']) !!}
                                        @error('telefono')
                                            <small class="text-danger fas fa-exclamation-circle">
                                                {{$message}}
                                            </small>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                {!! Form::label('domicilio', 'Domicilio:') !!}
                                {!! Form::text('domicilio', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '45']) !!}
                                @error('domicilio')
                                    <small class="text-danger fas fa-exclamation-circle">
                                        {{$message}}
                                    </small>
                                @enderror
                            </div>

                    </div> 
                    <div class="col-lg-4">
                            <div class="form-group">
                                {!! Form::label('nuacua', 'NUA / CUA:') !!}
                                {!! Form::text('nuacua', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '45']) !!}
                               
                                @error('nuacua')
                                    <small class="text-danger fas fa-exclamation-circle">
                                        {{$message}}
                                    </small>
                                @enderror
                            </div>

                            <div class="form-group">
                                {!! Form::label('estadolaboral', 'Estado laboral:') !!}
                                {!! Form::select('estadolaboral', $estlab, null, ['class' => 'form-control', 'placeholder' => '']) !!}
                                @error('estadolaboral')
                                    <small class="text-danger fas fa-exclamation-circle">
                                        {{$message}}
                                    </small>
                                @enderror
                            </div>

                            <div class="form-group">
                                {!! Form::label('empresa', 'Empresa:') !!}
                                {!! Form::select('empresa', $empresas, null, ['class' => 'form-control', 'placeholder' => '']) !!}
                                @error('empresa')
                                    <small class="text-danger fas fa-exclamation-circle">
                                        {{$message}}
                                    </small>
                                @enderror
                            </div>
                            <div class="form-group">
                                {!! Form::label('paisresidencia', 'Pais de Residencia:') !!}
                                {!! Form::select('paisresidencia', $pais, null, ['class' => 'form-control', 'placeholder' => '']) !!}
                                @error('paisresidencia')
                                    <small class="text-danger fas fa-exclamation-circle">
                                        {{$message}}
                                    </small>
                                @enderror
                            </div>
                            <div class="form-group">
                                {!! Form::label('departamentoresidencia', 'Departamento de Residencia:') !!}
                                {!! Form::select('departamentoresidencia', $departamentos, null, ['class' => 'form-control', 'placeholder' => '']) !!}
                                @error('departamentoresidencia')
                                    <small class="text-danger fas fa-exclamation-circle">
                                        {{$message}}
                                    </small>
                                @enderror
                            </div>
                            <div class="form-group">
                                {!! Form::label('ciudadresidencia', 'Ciudad de Residencia:') !!}
                                {!! Form::select('ciudadresidencia', $ciudades, null, ['class' => 'form-control', 'placeholder' => '']) !!}
                                @error('ciudadresidencia')
                                    <small class="text-danger fas fa-exclamation-circle">
                                        {{$message}}
                                    </small>
                                @enderror
                            </div>
                            <div class="form-group">
                                {!! Form::label('aseguradora', 'Aseguradora:') !!}
                                {!! Form::select('aseguradora', $aseguradoras, null, ['class' => 'form-control', 'placeholder' => '']) !!}
                                @error('aseguradora')
                                    <small class="text-danger fas fa-exclamation-circle">
                                        {{$message}}
                                    </small>
                                @enderror
                            </div>
                            <div class="form-group">
                                {!! Form::label('referenciador', 'Referenciador:') !!}
                                {!! Form::text('referenciador', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '45']) !!}
                                
                                @error('referenciador')
                                    <small class="text-danger fas fa-exclamation-circle">
                                        {{$message}}
                                    </small>
                                @enderror
                            </div>
                            <div class="form-group">
                                {!! Form::label('afp', 'AFP:') !!}
                                {!! Form::select('afp', $afp, null, ['class' => 'form-control', 'placeholder' => '']) !!}
                                @error('afp')
                                    <small class="text-danger fas fa-exclamation-circle">
                                        {{$message}}
                                    </small>
                                @enderror
                            </div>
                            <div class="form-group">
                                {!! Form::label('numhijosmenores', 'Nro. de hijos menores de 25 años:') !!}
                                {!! Form::text('numhijosmenores', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '45']) !!}
                                @error('numhijosmenores')
                                    <small class="text-danger fas fa-exclamation-circle">
                                        {{$message}}
                                    </small>
                                @enderror
                            </div>
                            <div class="form-group">
                                {!! Form::label('alertas', 'Alertas:') !!}
                                {!! Form::text('alertas', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '45']) !!}
                                
                                @error('alertas')
                                    <small class="text-danger fas fa-exclamation-circle">
                                        {{$message}}
                                    </small>
                                @enderror
                            </div>
                    </div>
                            <div class="col-lg-4">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="file">Foto de perfil:</label>
                                            <input type="file" class="form-control-file" id="picture" name="picture">
                                            <button type="button" id="tomar-foto" class="btningresar2">Tomar foto con la cámara</button>
                                            <canvas id="canvas" style="display: none;"></canvas>
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
                            
                            <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/0.4.1/html2canvas.min.js"></script>
                            <script>
                                var cameraActive = false;
                                var capturarBtn = document.createElement('button');
                                capturarBtn.innerText = 'Capturar imagen';
                                capturarBtn.setAttribute('id', 'capturar-btn');
                                capturarBtn.classList.add('capturar-btn-style');
                            
                                document.getElementById('tomar-foto').addEventListener('click', function() {
                                    if (cameraActive) {
                                        return;
                                    }
                                    capturarBtn.style.display = 'block';
                                    navigator.mediaDevices.getUserMedia({ video: true })
                                    .then(function(stream) {
                                        var video = document.createElement('video');
                                        video.setAttribute('autoplay', '');
                                        video.setAttribute('id', 'camara');
                                        video.srcObject = stream;
                                        video.width = 320;
                                        video.height = 240;
                                        var videoContainer = document.createElement('div');
                                        videoContainer.setAttribute('id', 'video-container');
                                        videoContainer.appendChild(video);
                                        var buttonColumn = document.getElementById('tomar-foto').closest('.col-lg-4');
                                        buttonColumn.appendChild(videoContainer);
                                        cameraActive = true;
                                        capturarBtn.addEventListener('click', function() {
                                            var canvas = document.createElement('canvas');
                                        var ctx = canvas.getContext('2d');
                                        canvas.width = video.videoWidth;
                                        canvas.height = video.videoHeight;
                                        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
                                        var imgBase64 = canvas.toDataURL('image/png');

                                        // Establecemos un nombre para la foto tomada con la cámara
                                        var nombreFoto = 'foto_camara.png'; // Puedes establecer el nombre que desees

                                        // Creamos un elemento 'a' para simular la descarga de la foto con nombre
                                        var link = document.createElement('a');
                                        link.href = imgBase64;
                                        link.download = nombreFoto;
                                        document.body.appendChild(link);
                                        link.click();
                                        document.body.removeChild(link);

                                        // Mostramos un mensaje para que el usuario seleccione la foto descargada
                                        alert('Por favor, seleccione la foto capturada después de descargarla.');

                                        // Establecemos la vista previa de la imagen
                                        var vistaPrevia = document.getElementById('vista-previa');
                                        vistaPrevia.src = imgBase64;
                                        vistaPrevia.alt = 'Vista previa de la imagen: ' + nombreFoto;
                                        vistaPrevia.style.display = 'block';

                                        // Detenemos la cámara y ocultamos el botón de captura
                                        stream.getTracks().forEach(track => track.stop());
                                        video.parentNode.removeChild(video);
                                        cameraActive = false;
                                        capturarBtn.style.display = 'none';
                                        });
                                        buttonColumn.appendChild(capturarBtn);
                                    })
                                    .catch(function(error) {
                                        console.log('Error al acceder a la cámara: ', error);
                                    });
                                });
                            </script>
                            
                            {!! Form::submit('Crear perfil', ['class' => 'btn btn-crear']) !!}
                            {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
</div>

@stop
@section('js')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script type="text/javascript" src="https://jeremyfagis.github.io/dropify/dist/js/dropify.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://jeremyfagis.github.io/dropify/dist/css/dropify.min.css"> 
<script>
    // Función para calcular la edad
    function calcularEdad(fecha_nacimiento) {
        var fecha_actual = new Date();
        var fecha_nacimiento = new Date(fecha_nacimiento);
        var edad = fecha_actual.getFullYear() - fecha_nacimiento.getFullYear();
        var mes = fecha_actual.getMonth() - fecha_nacimiento.getMonth();
        
        if (mes < 0 || (mes === 0 && fecha_actual.getDate() < fecha_nacimiento.getDate())) {
            edad--;
        }
        
        return edad;
    }

    // Obtener la fecha de nacimiento cuando cambie el valor del campo de fecha
    document.getElementById('fecha_nacimiento').addEventListener('change', function() {
        var fecha_nacimiento = this.value;
        var edad = calcularEdad(fecha_nacimiento);
        document.getElementById('edad').value = edad;
    });

    // Calcular la edad inicial al cargar la página
    var fecha_nacimiento = document.getElementById('fecha_nacimiento').value;
    var edad = calcularEdad(fecha_nacimiento);
    document.getElementById('edad').value = edad;
</script>
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
    h1, th {
        color:#94c93b; 
        font-family: "Segoe UI";
        font-weight: 900;
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
    #vista-previa {
        display: block;
        max-width: 50%;
        height: auto;
        margin-top: 10px;
        border: 1px solid #ccc;
        padding: 5px;
        box-shadow: 0px 0px 5px 0px rgba(0, 0, 0, 0.2);
        }
        h2 {font-size:  80%;
        text-align : right;
    }
</style>
<style>
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
    .btningresar {
        background-color: #faa625;
        color: #fff;
        border: none;
        padding: 10px 20px;
        border-radius: 7px;
        text-decoration: none;
        cursor: pointer;
    }
    .btningresar:hover {
        background-color: #94c93b;
    }
    .btningresar2 {
        background-color: #94c93b;
        color: #fff;
        border: none;
        padding: 10px 20px;
        border-radius: 7px;
        text-decoration: none;
        cursor: pointer;
        position: relative;
        margin-top: 10px;
    }
    .btningresar2:hover {
        background-color: #faa625;
    }
    .txt1 {
        font-family: "Segoe UI";
        font-size: 15px;
        line-height: 1.6;
        padding-right: 15px;
        color: #faa625;
    }
    .txt2 {
        font-family: "Segoe UI";
        font-size: 30px;
        line-height: 1.6;
        text-align: center;
        font-weight: 700;
        color: #94c93b;
    }
    .txt3 {
        font-family: "Segoe UI";
        font-size: 14px;
        line-height: 1.6;
        text-align: center;
        font-weight: 400;
    }
    .txt4 {
        font-family: "Segoe UI";
        font-size: 15px;
        line-height: 1.6;
        font-weight: 600;
        color: #94c93b;
    }
    .txt1:hover{
        color: #94c93b;
    }

</style>

@stop