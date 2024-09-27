@extends('adminlte::page')

<link href="assets/img/logo.png" rel="icon">

@section('content_header')
<a class="btn btn-sm float-right btn-regresar" href="{{ route('admin.acciones.index') }}">REGRESAR</a>
<h1>NUEVA ÁCCION</h1>
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
        {!! Form::open(['route' => 'admin.acciones.store', 'method'=>'POST']) !!}
        <div class="row ">
            <div class="col-lg-12">
                <div class="row">
                    <div class="col-lg-2" hidden>
                        <div class="form-group">
                            {!! Form::label('tipoid', 'Tipo ID:') !!}
                            {!! Form::text('tipoid', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '45']) !!}
                            @error('tipoid')
                                <small class="text-danger fas fa-exclamation-circle">
                                    {{$message}}
                                </small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="form-group">
                            {!! Form::label('tiponombre', 'Tipo de Área:') !!}
                            {!! Form::select('tiponombre', $tiponombre, null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '45', 'onchange' => 'filterAreas(), setTiponombre()']) !!}
                            @error('tiponombre')
                                <small class="text-danger fas fa-exclamation-circle">
                                    {{$message}}
                                </small>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-lg-4">
                        <div class="form-group">
                            {!! Form::label('tipoarea', 'Área:') !!}
                            {!! Form::select('tipoarea', [], null, ['class' => 'form-control', 'placeholder' => 'Seleccione un tipo de área primero', 'maxlength' => '45', 'id' => 'tipoareaSelect']) !!}
                            @error('area')
                                <small class="text-danger fas fa-exclamation-circle">
                                    {{$message}}
                                </small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-lg-4" hidden>
                        <div class="form-group">
                            {!! Form::label('area', 'Área:') !!}
                            {!! Form::text('area', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '45', 'readonly' => true, 'id' => 'area']) !!}
                        </div>
                    </div>
                    
                    <script>
                        function filterAreas() {
                            var selectedTipo = document.getElementById('tiponombre').value;
                            var areasSelect = document.getElementById('tipoareaSelect');
                            var areasIdInput = document.getElementById('areasid');
                            var areaInput = document.getElementById('area');
                            areasSelect.innerHTML = '';
                            var blankOption = document.createElement('option');
                            blankOption.value = '';
                            blankOption.text = '';
                            areasSelect.appendChild(blankOption);
                            @foreach($areas as $area)
                                if ("{{ $area->tipoarea }}" === selectedTipo) {
                                    var option = document.createElement('option');
                                    option.value = "{{ $area->id }}";
                                    option.text = "{{ $area->nombrearea }}";
                                    areasSelect.appendChild(option);
                                }
                            @endforeach
                            areasSelect.addEventListener('change', function() {
                                areasIdInput.value = this.value;
                                areaInput.value = this.options[this.selectedIndex].text;
                            });
                        }
                        document.addEventListener('DOMContentLoaded', function() {
                            filterAreas();
                        });
                    </script>
                                        
                    <div class="col-lg-2" hidden>
                        <div class="form-group">
                            {!! Form::label('areasid', 'ID Área:') !!}
                            {!! Form::text('areasid', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '45', 'readonly' => true, 'id' => 'areasid']) !!}
                            @error('areasid')
                                <small class="text-danger fas fa-exclamation-circle">
                                    {{$message}}
                                </small>
                            @enderror
                        </div>
                    </div>
                                        
                    <div class="col-lg-4">
                        <div class="form-group">
                            {!! Form::label('accion', 'Acción:') !!}
                            {!! Form::text('accion', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '90']) !!}
                            @error('nombrearea')
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
                            {!! Form::label('sucursal', 'Sucursal:') !!}
                            {!! Form::select('sucursal', $sucursal, null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '90']) !!}
                            @error('sucursal')
                                <small class="text-danger fas fa-exclamation-circle">
                                    {{$message}}
                                </small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group">
                            {!! Form::label('asociado', 'Tipo de cliente:') !!}
                            {!! Form::select('asociado', $tipocliente, null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '45', 'onchange' => 'setAsociado()']) !!}
                            @error('asociado')
                                <small class="text-danger fas fa-exclamation-circle">
                                    {{$message}}
                                </small>
                            @enderror
                        </div>
                    </div>
                    {!! Form::hidden('asociadoid', null, ['id' => 'asociadoid', 'class' => 'form-control', 'placeholder' => '', 'maxlength' => '45']) !!}
                </div>
                <div class="row">
                    <div class="col-lg-4">
                        <div class="form-group">
                            {!! Form::label('preciocompra', 'Precio de Compra:') !!}
                            {!! Form::text('preciocompra', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '45']) !!}
                            @error('preciocompra')
                                <small class="text-danger fas fa-exclamation-circle">
                                    {{$message}}
                                </small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="form-group">
                            {!! Form::label('precio', 'Precio de Venta:') !!}
                            {!! Form::text('precio', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '90']) !!}
                            @error('nombrearea')
                                <small class="text-danger fas fa-exclamation-circle">
                                    {{$message}}
                                </small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="form-group">
                            {!! Form::label('estado', 'Estado:') !!}
                            {!! Form::select('estado', $estado, null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '90']) !!}
                            @error('estado')
                                <small class="text-danger fas fa-exclamation-circle">
                                    {{$message}}
                                </small>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>   
        {!! Form::submit('CREAR ACCIÓN', ['class' => 'btn btn-crear']) !!}
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
    function setAsociado() {
        var asociado = document.getElementById('asociado').value;
        var asociadoid = document.getElementById('asociadoid');
        if (asociado === 'CLIENTES ITA') {
            asociadoid.value = 6;
        } else if (asociado === 'CLIENTES COMUNES') {
            asociadoid.value = 3;
        } else {
            asociadoid.value = '';
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        document.addEventListener('keypress', function(event) {
            if (event.key === 'Enter') {
                event.preventDefault();
            }
        });
    });

    function setTiponombre() {
        var tiponombre = document.getElementById('tiponombre').value;
        var tipoid = document.getElementById('tipoid');
        if (tiponombre === 'ESTUDIO') {
            tipoid.value = 2;
        } else if (tiponombre === 'ESPECIALIDAD') {
            tipoid.value = 1;
        } else {
            tipoid.value = '';
        }
    }
</script>
@endsection