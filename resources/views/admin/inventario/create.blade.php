@extends('adminlte::page')

@section('content_header')
<a class="btn float-right btn-outline-secondary" href="{{ route('admin.bienes.index') }}">REGRESAR</a>
<h1>NUEVO PRODUCTO DE ALMACÉN PARA {{ $sucursal }}</h1>
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
            <div class="row ">
                <div class="col-lg-12">
                    {!! Form::open(['route' => 'admin.bienes.store', 'method'=>'POST']) !!}
                        <div class="row">
                            <div class="form-group col-lg-5">
                                {!! Form::label('seccion', 'Sección:') !!}
                                {!! Form::select('seccion', $secciones, null, ['class' => 'form-control', 'placeholder' => '', 'id' => 'seccion']) !!}
                                @error('seccion')
                                    <small class="text-danger fas fa-exclamation-circle">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="form-group col-lg-5">
                                {!! Form::label('subseccion', 'Producto:') !!}
                                {!! Form::select('subseccion', [], null, ['class' => 'form-control', 'placeholder' => '', 'id' => 'subseccion']) !!}
                                @error('subseccion')
                                    <small class="text-danger fas fa-exclamation-circle">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="form-group col-lg-2">
                                {!! Form::label('codigo', 'Código de Producto:') !!}
                                {!! Form::text('codigo', null, ['class' => 'form-control', 'id' => 'codigo', 'readonly' => 'readonly']) !!}
                                @error('codigo')
                                    <small class="text-danger fas fa-exclamation-circle">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-lg-4">
                                {!! Form::label('materiaprima', 'Materia Prima:') !!}
                                {!! Form::select('materiaprima', [], null, ['class' => 'form-control', 'placeholder' => '', 'id' => 'materiaprima']) !!}
                                @error('materiaprima')
                                    <small class="text-danger fas fa-exclamation-circle">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="form-group col-lg-4">
                                {!! Form::label('marca', 'Marca:') !!}
                                {!! Form::select('marca', [], null, ['class' => 'form-control', 'placeholder' => '', 'id' => 'marca']) !!}
                                @error('marca')
                                    <small class="text-danger fas fa-exclamation-circle">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="form-group col-lg-4">
                                {!! Form::label('unidadmedida', 'Unidad de medida:') !!}
                                {!! Form::select('unidadmedida', [], null, ['class' => 'form-control', 'placeholder' => '', 'id' => 'unidadmedida']) !!}
                                @error('unidadmedida')
                                    <small class="text-danger fas fa-exclamation-circle">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-lg-12">
                                {!! Form::label('especificacionmedida', 'Especificacion de medida:') !!}
                                {!! Form::text('especificacionmedida', null, ['class' => 'form-control', 'placeholder' => '']) !!}
                                @error('especificacionmedida')
                                <small class="text-danger fas fa-exclamation-circle">
                                    {{$message}}
                                </small>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-lg-4">
                                {!! Form::label('color', 'Color:') !!}
                                <div class="d-flex align-items-center">
                                    {!! Form::text('color', null, ['class' => 'form-control', 'id' => 'colorInput', 'readonly' => 'readonly', 'placeholder' => '']) !!}
                                    <div class="dropdown ml-2">
                                        <button class="btn btn-secondary dropdown-toggle color-selector" type="button" id="colorDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            ▼
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="colorDropdown">
                                            <div class="d-flex flex-wrap">
                                                <div class="col-4 p-1">
                                                    <div class="color-box" data-color="#000000"></div>
                                                    <div class="color-box" data-color="#FFFFFF"></div>
                                                    <div class="color-box" data-color="#FF0000"></div>
                                                    <div class="color-box" data-color="#008000"></div>
                                                    <div class="color-box" data-color="#0000FF"></div>
                                                </div>
                                                <div class="col-4 p-1">
                                                    <div class="color-box" data-color="#FFFF00"></div>
                                                    <div class="color-box" data-color="#FFA500"></div>
                                                    <div class="color-box" data-color="#800080"></div>
                                                    <div class="color-box" data-color="#FFC0CB"></div>
                                                    <div class="color-box" data-color="#808080"></div>
                                                </div>
                                                <div class="col-4 p-1">
                                                    <div class="color-box" data-color="#A52A2A"></div>
                                                    <div class="color-box" data-color="#00BFFF"></div>
                                                    <div class="color-box" data-color="#FFD700"></div>
                                                    <div class="color-box" data-color="#008B8B"></div>
                                                    <div class="color-box" data-color="#B22222"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @error('color')
                                <small class="text-danger fas fa-exclamation-circle">
                                    {{$message}}
                                </small>
                                @enderror
                            </div>
                            <div class="form-group col-lg-4">
                                {!! Form::label('inventario', 'Inventario:') !!}
                                {!! Form::select('inventario', [
                                    'PRINCIPAL' => 'PRINCIPAL',
                                    'AGOTADO' => 'AGOTADO',
                                    'ASIGNACION Y DEVOLUCION' => 'ASIGNACION Y DEVOLUCION',
                                    'STOCK DEPURADO' => 'STOCK DEPURADO'
                                ], null, ['class' => 'form-control', 'placeholder' => '']) !!}
                                @error('inventario')
                                    <small class="text-danger fas fa-exclamation-circle">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="form-group col-lg-4">
                                {!! Form::label('deposito', 'Depósito:') !!}
                                {!! Form::select('deposito', [
                                    'PRINCIPAL' => 'PRINCIPAL',
                                    'SECUNDARIO' => 'SECUNDARIO'
                                ], null, ['class' => 'form-control', 'placeholder' => '']) !!}
                                @error('deposito')
                                    <small class="text-danger fas fa-exclamation-circle">{{ $message }}</small>
                                @enderror
                            </div> 
                        </div>

                        <div class="row">
                            <div class="form-group col-lg-4">
                                {!! Form::label('stockinicial', 'Stock inicial:') !!}
                                {!! Form::text('stockinicial', null, ['class' => 'form-control', 'placeholder' => '']) !!}
                                @error('stockinicial')
                                <small class="text-danger fas fa-exclamation-circle">
                                    {{$message}}
                                </small>
                                @enderror
                            </div>
                            <div class="form-group col-lg-4">
                                {!! Form::label('stockactual', 'Stock actual:') !!}
                                {!! Form::text('stockactual', null, ['class' => 'form-control', 'placeholder' => '']) !!}
                                @error('stockactual')
                                <small class="text-danger fas fa-exclamation-circle">
                                    {{$message}}
                                </small>
                                @enderror
                            </div>
                            <div class="form-group col-lg-4">
                                {!! Form::label('precio', 'Precio:') !!}
                                <div class="input-group">
                                    {!! Form::text('precio', null, ['class' => 'form-control', 'id' => 'precio', 'placeholder' => '']) !!}
                                    <div class="input-group-append">
                                        <span class="input-group-text" id="basic-addon2">Bs.</span>
                                    </div>
                                </div>
                                @error('precio')
                                    <small class="text-danger fas fa-exclamation-circle">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                        {!! Form::submit('REGISTRAR PRODUCTO', ['class' => 'btn btn-outline-secondary']) !!}
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endsection

@section('css')
<link rel="styleheet" href="/css/admin_custom.css">
<style>
    h1, th {
        color:#000000; 
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
</style>
<style>
    .color-box {
        width: 35px;
        height: 35px;
        margin: 5px;
        border: 2px solid #ddd;
        cursor: pointer;
        transition: transform 0.2s;
    }
    .color-box:hover {
        transform: scale(1.1);
    }
    .selected {
        border: 3px solid black !important;
    }
    .color-selector {
        width: 50px;
        height: 38px;
        border: 2px solid #ddd;
        text-align: center;
        padding: 0;
        font-size: 1.2rem;
    }
</style>
@stop

@section('js')
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @if (session('eliminar')=='ok')
    <script>
        Swal.fire(
      '¡Eliminado!',
      'El rol se eliminó con éxito',
      'success')
    </script>
    @endif

<script>
    $('.formulario-eliminar').submit(function(e){
        e.preventDefault();

        Swal.fire({
        title: '¿Estás seguro?',
        text: "El rol se eliminará definitivamente",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: '¡Si, eliminar!',
        cancelButtonText: 'Cancelar'
        }).then((result) => {
        if (result.isConfirmed) {
            this.submit();
        }
        }) 
    });
</script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        let colorBoxes = document.querySelectorAll(".color-box");
        let colorInput = document.getElementById("colorInput");
        let colorDropdown = document.getElementById("colorDropdown");

        const colorNames = {
            "#000000": "NEGRO",
            "#FFFFFF": "BLANCO",
            "#FF0000": "ROJO",
            "#008000": "VERDE",
            "#0000FF": "AZUL",
            "#FFFF00": "AMARILLO",
            "#FFA500": "NARANJA",
            "#800080": "MORADO",
            "#FFC0CB": "ROSADO",
            "#808080": "GRIS",
            "#A52A2A": "MARRON",
            "#00BFFF": "CELESTE",
            "#FFD700": "DORADO",
            "#008B8B": "TURQUESA",
            "#B22222": "GUINDO"
        };

        colorBoxes.forEach(box => {
            box.style.backgroundColor = box.getAttribute("data-color");
            box.addEventListener("click", function() {
                colorBoxes.forEach(b => b.classList.remove("selected"));
                this.classList.add("selected");
                let selectedColor = this.getAttribute("data-color");
                colorInput.value = colorNames[selectedColor];
                colorDropdown.style.backgroundColor = selectedColor;
            });
        });
    });
</script>

<script>
    const subsecciones = @json($subsecciones);
    const materiaprimas = @json($materiaprimas);
    const marcas = @json($marcas);
    const unidadmedidas = @json($unidadmedidas);
    const seccionSelect = document.getElementById('seccion');
    const subseccionSelect = document.getElementById('subseccion');
    const materiaprimaSelect = document.getElementById('materiaprima');
    const marcaSelect = document.getElementById('marca');
    const unidadmedidaSelect = document.getElementById('unidadmedida');
    const codigoSubseccionInput = document.getElementById('codigo');

    function updateSubsecciones() {
        const seccion = seccionSelect.value;
        subseccionSelect.innerHTML = '<option value=""></option>';
        codigoSubseccionInput.value = '';
        if (seccion) {
            const subseccionesFiltradas = subsecciones.filter(sub => sub.seccion === seccion);
            subseccionesFiltradas.forEach(sub => {
                const option = document.createElement('option');
                option.value = sub.subseccion;
                option.textContent = sub.subseccion;
                subseccionSelect.appendChild(option);
            });
        }
    }

    function updateMateriaprima() {
        const seccion = seccionSelect.value;
        materiaprimaSelect.innerHTML = '<option value=""></option>';
        if (seccion) {
            const materiaprimasFiltradas = materiaprimas.filter(sub => sub.seccion === seccion);
            materiaprimasFiltradas.forEach(sub => {
                const option = document.createElement('option');
                option.value = sub.subseccion;
                option.textContent = sub.subseccion;
                materiaprimaSelect.appendChild(option);
            });
        }
    }

    function updateMarca() {
        const seccion = seccionSelect.value;
        marcaSelect.innerHTML = '<option value=""></option>';
        if (seccion) {
            const marcasFiltradas = marcas.filter(sub => sub.seccion === seccion);
            marcasFiltradas.forEach(sub => {
                const option = document.createElement('option');
                option.value = sub.subseccion;
                option.textContent = sub.subseccion;
                marcaSelect.appendChild(option);
            });
        }
    }

    function updateUnidadmedida() {
        const seccion = seccionSelect.value;
        unidadmedidaSelect.innerHTML = '<option value=""></option>';
        if (seccion) {
            const unidadmedidasFiltradas = unidadmedidas.filter(sub => sub.seccion === seccion);
            unidadmedidasFiltradas.forEach(sub => {
                const option = document.createElement('option');
                option.value = sub.subseccion;
                option.textContent = sub.subseccion;
                unidadmedidaSelect.appendChild(option);
            });
        }
    }

    function updateCodigoSubseccion() {
        const subseccion = subseccionSelect.value;
        
        if (subseccion) {
            const subseccionSeleccionada = subsecciones.find(sub => sub.subseccion === subseccion);
            if (subseccionSeleccionada) {
                codigoSubseccionInput.value = subseccionSeleccionada.codigo;
            }
        }
    }

    seccionSelect.addEventListener('change', updateSubsecciones);
    seccionSelect.addEventListener('change', updateMateriaprima);
    seccionSelect.addEventListener('change', updateMarca);
    seccionSelect.addEventListener('change', updateUnidadmedida);
    subseccionSelect.addEventListener('change', updateCodigoSubseccion);
</script>

<script>
    document.getElementById('precio').addEventListener('input', function(e) {
        let value = e.target.value;
        value = value.replace(/[^0-9.]/g, '');
        if (value.split('.').length > 2) {
            value = value.slice(0, value.lastIndexOf('.'));
        }
        e.target.value = value;
    });
</script>
@endsection