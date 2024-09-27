@extends('adminlte::page')

@section('content_header')
<a class="btn btn-sm float-right btn-regresar" href="{{ route('admin.asociados.index') }}">REGRESAR</a>
<h1>NUEVA ACCIÓN</h1>
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
        {!! Form::model($asociado, ['route' => ['admin.asociados.guardarbateriabanco', $asociado], 'method' => 'POST']) !!}
        {{-- {!! Form::hidden('usuarioid', auth()->user()->id) !!}
        {!! Form::hidden('usuarioregistro', auth()->user()->name) !!} --}}

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
                            
                            // Limpiar opciones actuales
                            areasSelect.innerHTML = '';
                            
                            // Agregar opción en blanco
                            var blankOption = document.createElement('option');
                            blankOption.value = '';
                            blankOption.text = '';
                            areasSelect.appendChild(blankOption);
                            
                            // Filtrar y agregar opciones según el tipo seleccionado
                            @foreach($areas as $area)
                                if ("{{ $area->tipoarea }}" === selectedTipo) {
                                    var option = document.createElement('option');
                                    option.value = "{{ $area->id }}";
                                    option.text = "{{ $area->nombrearea }}";
                                    areasSelect.appendChild(option);
                                }
                            @endforeach
                            
                            // Actualizar el valor del campo areasid al seleccionar un área
                            areasSelect.addEventListener('change', function() {
                                areasIdInput.value = this.value;
                                areaInput.value = this.options[this.selectedIndex].text;
                            });
                        }
                        
                        // Llamar a filterAreas() al cargar la página si hay un tipo preseleccionado
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
                            @error('accion')
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
                            {!! Form::label('categoria', 'Categoría:') !!}
                            {!! Form::select('categoria', $categoria, null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '90']) !!}
                            @error('categoria')
                                <small class="text-danger fas fa-exclamation-circle">
                                    {{$message}}
                                </small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-lg-6" hidden>
                        <div class="form-group">
                            {!! Form::label('asociado', 'Tipo de cliente:') !!}
                            {!! Form::text('asociado', $asociado->asociado, null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '45', 'onchange' => 'setAsociado()']) !!}
                            @error('asociado')
                                <small class="text-danger fas fa-exclamation-circle">
                                    {{$message}}
                                </small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-lg-6" hidden>
                        <div class="form-group">
                            {!! Form::label('asociadoid', 'ID de asociado:') !!}
                            {!! Form::text('asociadoid', $asociado->id, null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '45']) !!}
                            @error('asociadoid')
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
                            @error('precio')
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
    $(document).ready(function() {
        $('#area').change(function() {
            var areaId = $(this).val();
            $('.acciones').hide();
            $('#acciones_' + areaId).show();
        });
    });
</script>
<script>
    function setAsociado() {
        var asociado = document.getElementById('asociado').value;
        var asociadoid = document.getElementById('asociadoid');

        // Logic to set idtipoarea based on tipoarea selection
        if (asociado === 'CLIENTES ITA') {
            asociadoid.value = 6;
        } else if (asociado === 'CLIENTES COMUNES') {
            asociadoid.value = 3;
        } else {
            asociadoid.value = ''; // Handle any other cases here
        }
    }
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
<script>
    function setTiponombre() {
        var tiponombre = document.getElementById('tiponombre').value;
        var tipoid = document.getElementById('tipoid');

        // Logic to set idtipoarea based on tipoarea selection
        if (tiponombre === 'ESTUDIO') {
            tipoid.value = 1;
        } else if (tiponombre === 'ESPECIALIDAD') {
            tipoid.value = 2;
        } else {
            tipoid.value = ''; // Handle any other cases here
        }
    }
</script>

@endsection

@section('css')
<link rel="styleheet" href="/css/admin_custom.css">
<style>
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