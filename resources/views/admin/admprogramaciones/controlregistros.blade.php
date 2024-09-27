@extends('adminlte::page')
    
@section('content_header')
<h1>CONTROL DE REGISTROS</h1>
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
        
    </div>
</div>

@stop

@section('css')
@endsection