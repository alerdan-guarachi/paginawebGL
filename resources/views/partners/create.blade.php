@extends('adminlte::page')

@section('content_header')
<a class="btn btn-sm float-right btn-primary" href="{{ route('partners.index') }}">LISTA</a>
<h1>SOCIOS</h1>
@stop

@section('css')

@stop

@section('content')
@if (session('info'))
    <div id="alert-info" class="alert alert-success">
        <strong>{{ session('info') }}</strong>
    </div>
    <script>
        setTimeout(function() {
            $('#alert-info').fadeOut('fast');
        }, 3000);
    </script>
@endif
<div class="card">
    <div class="card-body">
        <form action="{{ route('partners.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label>Nombres:</label>
                <input type="text" name="name" class="form-control" required>
            </div>

            <div class="form-group">
                <label>Apellidos:</label>
                <input type="text" name="last_name" class="form-control" required>
            </div>

            <div class="form-group">
                <label>C.I.:</label>
                <input type="text" name="ci" class="form-control" required>
            </div>

            <div class="form-group">
                <label>Rubro:</label>
                <input type="text" name="category" class="form-control">
            </div>

            <button type="submit" class="btn btn-success">Guardar y Generar QR</button>
        </form>
    </div>
</div>
@stop

@section('js')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script type="text/javascript" src="https://jeremyfagis.github.io/dropify/dist/js/dropify.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://jeremyfagis.github.io/dropify/dist/css/dropify.min.css"> 
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropify/0.2.2/css/dropify.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/dropify/0.2.2/js/dropify.min.js"></script>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endsection

