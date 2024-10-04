@extends('adminlte::page')
<link href="assets/img/logo.png" rel="icon">
@section('content_header')
<a class="btn btn-sm float-right btn-regresar" href="{{ route('admin.roles.index') }}">REGRESAR</a>
<h1>CREAR ROL</h1>
<style>
    h1, th {
        color:#94c93b; 
        font-family: "Segoe UI";
        font-weight: 900;
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
    .btn-crear {
        background-color:  #ffffff;
        color: #94c93b;
        border-color: #94c93b;
        border-radius: 5px;
        padding: 8px 16px;
        }
    
    .btn-crear:hover {
        background-color: #94c93b;
        color: #ffffff;
        }
</style>
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
            {!! Form::open(['route' => 'admin.roles.store']) !!}
                
            @include('admin.roles.partials.form')

            {!! Form::submit('CREAR ROL', ['class' => 'btn btn-crear']) !!}
            {!! Form::close() !!}
        </div>
    </div>
@stop