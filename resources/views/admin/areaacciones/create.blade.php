@extends('adminlte::page')

<link href="assets/img/logo.png" rel="icon">

@section('content_header')
<a class="btn btn-sm float-right btn-regresar" href="{{ route('admin.areaacciones.listadoareas') }}">REGRESAR</a>
<h1>NUEVA ÁREA</h1>
@stop

@section('css')
<link rel="stylesheet" href="{{ asset('css/estilogl.css') }}">
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
        {!! Form::model($area, ['route' => ['admin.areaacciones.store', $area], 'method' => 'POST']) !!}
        {!! Form::hidden('usuarioid', auth()->user()->id) !!}
        {!! Form::hidden('usuarioregistro', auth()->user()->name) !!}

        <div class="row ">
            <div class="col-lg-12">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group">
                            {!! Form::label('tipoarea', 'Tipo de Área:') !!}
                            {!! Form::select('tipoarea', $tipoareas, null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '45', 'onchange' => 'setTipoArea()']) !!}
                            @error('tipoarea')
                                <small class="text-danger fas fa-exclamation-circle">
                                    {{$message}}
                                </small>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-12">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group">
                            {!! Form::label('nombrearea', 'Nombre del Área:') !!}
                            {!! Form::text('nombrearea', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '90']) !!}
                            @error('nombrearea')
                                <small class="text-danger fas fa-exclamation-circle">
                                    {{$message}}
                                </small>
                            @enderror
                        </div>
                    </div>  
                </div>
            </div>
            {!! Form::text('idtipoarea', null, ['id' => 'idtipoarea', 'class' => 'form-control', 'placeholder' => '', 'maxlength' => '45']) !!}
        </div>   
        {!! Form::submit('CREAR ÁREA', ['class' => 'btn btn-crear']) !!}
        {!! Form::close() !!}     
    </div>
</div>

@stop
@section('js')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script type="text/javascript" src="https://jeremyfagis.github.io/dropify/dist/js/dropify.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://jeremyfagis.github.io/dropify/dist/css/dropify.min.css"> 
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="{{ asset('js/funciongl.js') }}"></script>
@endsection