@extends('adminlte::page')

@section('content_header')
<h1>Editar Perfil</h1>
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

<div class="container">
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-lg-6">
                    {!! Form::model($profile, ['route' => ['admin.profiles.update', $profile], 'method' => 'put' ,'files' => true]) !!}
                        
                    {!! Form::hidden('users_id', auth()->user()->id) !!}
                    <div class="form-group">
                        {!! Form::label('name', 'Nombres:') !!}
                        {!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => 'Ingrese sus nombres', 'maxlength' => '45']) !!}
                        @error('name')
                        <small class="text-danger fas fa-exclamation-circle">
                            {{$message}}
                        </small>  
                        @enderror
                    </div>

                    <div class="form-group">
                        {!! Form::label('last_name', 'Apellidos:') !!}
                        {!! Form::text('last_name', null, ['class' => 'form-control', 'placeholder' => 'Ingrese sus apellidos', 'maxlength' => '45']) !!}
                        @error('last_name')
                        <small class="text-danger fas fa-exclamation-circle">
                            {{$message}}
                        </small>  
                        @enderror
                    </div>

                    <div class="form-group">
                        {!! Form::label('email', 'Gmail:') !!}
                        {!! Form::text('email', null, ['class' => 'form-control', 'placeholder' => 'Ingrese su gmail', 'maxlength' => '45']) !!}
                        @error('email')
                        <small class="text-danger fas fa-exclamation-circle">
                            {{$message}}
                        </small>  
                        @enderror
                    </div>

                    <div class="form-group">
                        {!! Form::label('position', 'Cargo:') !!}
                        {!! Form::text('position', null, ['class' => 'form-control', 'placeholder' => 'Ingrese su cargo en FHV', 'maxlength' => '45']) !!}
                        @error('position')
                        <small class="text-danger fas fa-exclamation-circle">
                            {{$message}}
                        </small>  
                        @enderror
                    </div>

                    <div class="form-group">
                        {!! Form::label('phone', 'Teléfono:') !!}
                        {!! Form::text('phone', null, ['class' => 'form-control', 'placeholder' => 'Ingrese su número de teléfono', 'maxlength' => '45']) !!}
                        @error('phone')
                        <small class="text-danger fas fa-exclamation-circle">
                            {{$message}}
                        </small>  
                        @enderror
                    </div>

                    <div class="form-group">
                        {!! Form::label('direction', 'Dirección:') !!}
                        {!! Form::text('direction', null, ['class' => 'form-control', 'placeholder' => 'Ingrese su dirección de su domicilio', 'maxlength' => '45']) !!}
                        @error('direction')
                        <small class="text-danger fas fa-exclamation-circle">
                            {{$message}}
                        </small>  
                        @enderror
                    </div>
                </div>
                <div class="col-lg-6">

                    <div class="form-group">
                        <label for="file">Foto de perfil:</label>
                        <input type="file" class="form-control-file" id="picture" name="picture">
                        @if ($profile->image)
                            <img id="vista-previa" src="{{ asset('/image/' . $profile->image) }}" alt="{{ $profile->name }}">
                        @endif
                        @error('picture')
                        <small class="text-danger fas fa-exclamation-circle">
                            {{$message}}
                        </small>  
                        @enderror
                    </div>
                        
                </div><br> 
                
                {!! Form::submit('Actualizar perfil', ['class' => 'btn btn-success']) !!}
                {!! Form::close() !!}

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
    h1 {color:green; 
        font-family: "Segoe UI";
        font-weight: 900;
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
</style>
@stop