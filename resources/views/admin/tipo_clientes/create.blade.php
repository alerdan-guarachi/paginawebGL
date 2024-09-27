@extends('adminlte::page')

@section('content_header')
<h1>Crear nuevo tipo de cliente</h1>
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

            {!! Form::open(['route' => 'admin.tipo_clientes.store']) !!}
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('nombre', 'Nombre:') !!}
                        {!! Form::text('nombre', null, ['class' => 'form-control', 'placeholder' => 'Escriba el nombre de la categoría', 'maxlength' => '45']) !!}
                        @error('nombre')
                        <small class="text-danger fas fa-exclamation-circle">
                            {{$message}}
                        </small>  
                        @enderror
                    </div>

                    {!! Form::submit('Crear categoría', ['class' => 'btn btn-success']) !!}

                </div>
            {!! Form::close() !!}
        </div>
    </div>
@stop

@section('css')
<link rel="styleheet" href="/css/admin_custom.css">
<style>
    h1 {color:green; 
        font-family: "Segoe UI";
        font-weight: 900;
        }
    h2 {font-size:  80%;
        text-align : right;
    }
</style>
@stop