@extends('adminlte::page')

@section('content_header')
<a class="btn btn-sm float-right btn-regresar" href="{{ route('admin.asociados.vercontactoclienteita', $cliente) }}">REGRESAR</a>
<h5>NUEVO CONTACTO DE:</h5> 
<h3>{{$cliente->nombrecompleto}}</h3>
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
        {!! Form::model($cliente, ['route' => ['admin.asociados.guardarcontactoclienteita', $cliente], 'method' => 'POST']) !!}
        {!! Form::hidden('usuarioid', auth()->user()->id) !!}
        {!! Form::hidden('usuarioregistro', auth()->user()->name) !!}

        {!! Form::hidden('clienteitaid', $id) !!}
        

        <div class="row ">
            <div class="col-lg-12">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="form-group" hidden>
                            {!! Form::label('nombrecompleto', 'Nombre completo:') !!}
                            {!! Form::text('nombrecompleto', null, ['class' => 'form-control', 'placeholder' => '', 'readonly' => 'readonly']) !!}
                            @error('nombrecompleto')
                                <small class="text-danger fas fa-exclamation-circle">
                                    {{$message}}
                                </small>
                            @enderror
                        </div> 
                        <div class="form-group">
                            {!! Form::label('nombrecontacto', 'Nombre del contacto:') !!}
                            {!! Form::text('nombrecontacto', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '90']) !!}
                            @error('nombrecontacto')
                                <small class="text-danger fas fa-exclamation-circle">
                                    {{$message}}
                                </small>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-4">
                        <div class="form-group">
                            {!! Form::label('celularcontacto', 'Celular del contacto:') !!}
                            {!! Form::text('celularcontacto', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '30', 'onkeypress' => 'return (event.charCode >= 48 && event.charCode <= 57) || event.charCode === 45']) !!}
                            @error('celularcontacto')
                                <small class="text-danger fas fa-exclamation-circle">
                                    {{$message}}
                                </small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="form-group">
                            {!! Form::label('telefonocontacto', 'Teléfono:') !!}
                            {!! Form::text('telefonocontacto', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '30', 'onkeypress' => 'return (event.charCode >= 48 && event.charCode <= 57) || event.charCode === 45']) !!}
                            @error('telefonocontacto')
                                <small class="text-danger fas fa-exclamation-circle">
                                    {{$message}}
                                </small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="form-group">
                            {!! Form::label('parentesco', 'Parentesco:') !!}
                            {!! Form::select('parentesco', $parentesco, null, ['class' => 'form-control', 'placeholder' => '' , 'maxlength' => '200']) !!}
                            @error('parentesco')
                                <small class="text-danger fas fa-exclamation-circle">
                                    {{$message}}
                                </small>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>   
        {!! Form::submit('CREAR CONTACTO', ['class' => 'btn btn-crear']) !!}
        {!! Form::close() !!}     
    </div>
</div>

@stop
@section('js')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script type="text/javascript" src="https://jeremyfagis.github.io/dropify/dist/js/dropify.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://jeremyfagis.github.io/dropify/dist/css/dropify.min.css"> 
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    $(document).ready(function() {
        $('#area').change(function() {
            var areaId = $(this).val();
            $('.acciones').hide();
            $('#acciones_' + areaId).show();
        });
    });
</script>

<script>
//CANCELAR FUNCION DE LA TECLA ENTER
    document.addEventListener('DOMContentLoaded', function() {
        document.addEventListener('keypress', function(event) {
            if (event.key === 'Enter') {
                event.preventDefault();
            }
        });
    });
</script>
@endsection

@section('css')
<link rel="styleheet" href="/css/admin_custom.css">
<style>
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
    h1 {
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
    .mensaje-error {
        color: #e1172b;
        font-family: "Times New Roman";
        padding: 10px;
        margin-top: 5px;
        border-radius: 5px;
        font-size: 12.5px;
        font-weight: bold;
        display: inline-block;
        margin-left: -10px;
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