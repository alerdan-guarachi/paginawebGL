@extends('adminlte::page')

@section('content_header')
<h1>Editar tipo de cliente</h1>
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

        {!! Form::model($tipo_cliente, ['route' => ['admin.tipo_clientes.update', $tipo_cliente], 'method' => 'put']) !!}
            <div class="col-md-6">
                <div class="form-group">
                    {!! Form::label('name', 'Nombre:') !!}
                    {!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => 'Ingrese el nombre de la categoria', 'maxlength' => '45']) !!}
                    @error('name')
                    <small class="text-danger fas fa-exclamation-circle">
                        {{$message}}
                    </small> 
                    @enderror
                </div>
        
                {!! Form::submit('Actualizar categoría', ['class' => 'btn btn-success']) !!}

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
