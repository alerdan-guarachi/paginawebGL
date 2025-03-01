@extends('adminlte::page')

@section('content_header')
<a class="btn float-right btn-outline-secondary" href="{{ route('admin.caja.cuentaspagar.cppregistradas') }}">REGRESAR</a>
<h1>NUEVA CUENTA POR PAGAR</h1>
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
        {!! Form::model(['route' => ['guardar.cuenta.pagar'], 'method' => 'POST']) !!}
            {!! Form::hidden('usuarioregistroid', auth()->user()->id) !!}
            {!! Form::hidden('usuarioregistronombre', auth()->user()->name) !!}

            <div class="row">
                <div class="col-lg-4"> 
                    <div class="form-group">
                        {!! Form::label('tipoproveedor', 'Tipo de Proveedor:') !!}
                        {!! Form::select('tipoproveedor', [
                            '' => '',
                            'PERSONAL INTERNO' => 'PERSONAL INTERNO',
                            'PERSONAL EXTERNO' => 'PERSONAL EXTERNO',
                            'PERSONAL MEDICO' => 'PERSONAL MEDICO',
                            'PERSONAL LIMPIEZA' => 'PERSONAL LIMPIEZA',
                            'PASANTE' => 'PASANTE'
                        ], null, ['class' => 'form-control', 'maxlength' => '90']) !!}
                        @error('tipoproveedor')
                            <small class="text-danger fas fa-exclamation-circle">
                                {{$message}}
                            </small>
                        @enderror
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-group">
                        {!! Form::label('proveedornombre', 'Nombre de Proveedor:') !!}
                        {!! Form::select('proveedornombre', [], null, ['class' => 'form-control', 'placeholder' => '', 'id' => 'proveedornombre']) !!}
                        {!! Form::hidden('proveedornombre_text', null, ['class' => 'form-control', 'readonly' => true, 'id' => 'proveedornombre_text']) !!}
                        @error('proveedornombre')
                            <small class="text-danger fas fa-exclamation-circle">
                                {{$message}}
                            </small>
                        @enderror
                    </div>
                </div>
                
                <div class="col-lg-2">
                    <div class="form-group">
                        {!! Form::label('proveedorid', 'ID Proveedor:') !!}
                        {!! Form::text('proveedorid', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '90', 'readonly' => 'readonly']) !!}
                        @error('proveedorid')
                            <small class="text-danger fas fa-exclamation-circle">
                                {{$message}}
                            </small>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-8">
                    <div class="form-group">
                        {!! Form::label('detalle', 'Detalle:') !!}
                        {!! Form::text('detalle', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '90']) !!}
                        @error('detalle')
                            <small class="text-danger fas fa-exclamation-circle">
                                {{$message}}
                            </small>
                        @enderror
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="form-group">
                        {!! Form::label('fechaasignada', 'Fecha de pago:') !!}
                        {!! Form::date('fechaasignada', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '90']) !!}
                        @error('fechaasignada')
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
                        {!! Form::label('subtotal', 'Sub Total:') !!}
                        {!! Form::text('subtotal', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '90']) !!}
                        @error('subtotal')
                            <small class="text-danger fas fa-exclamation-circle">
                                {{$message}}
                            </small>
                        @enderror
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="form-group">
                        {!! Form::label('descuentosancion', 'Descuento:') !!}
                        {!! Form::text('descuentosancion', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '90']) !!}
                        @error('descuentosancion')
                            <small class="text-danger fas fa-exclamation-circle">
                                {{$message}}
                            </small>
                        @enderror
                    </div>
                </div>
                {{-- <div class="col-lg-3">
                    <div class="form-group">
                        {!! Form::label('descuentoafp', 'Descuento AFP:') !!}
                        {!! Form::text('descuentoafp', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '90']) !!}
                        @error('descuentoafp')
                            <small class="text-danger fas fa-exclamation-circle">
                                {{$message}}
                            </small>
                        @enderror
                    </div>
                </div> --}}
                <div class="col-lg-4">
                    <div class="form-group">
                        {!! Form::label('montototal', 'Monto Total a pagar:') !!}
                        {!! Form::text('montototal', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '90', 'readonly' => 'readonly']) !!}
                        @error('montototal')
                            <small class="text-danger fas fa-exclamation-circle">
                                {{$message}}
                            </small>
                        @enderror
                    </div>
                </div>
            </div>
            
            {!! Form::submit('CREAR CPP', ['class' => 'btn btn-crear']) !!}
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
    document.getElementById('proveedornombre').addEventListener('change', function () {
    const selectedOption = this.options[this.selectedIndex];
    const proveedorNombreText = document.getElementById('proveedornombre_text');
    
    // Asigna el nombre del proveedor al campo de texto
    proveedorNombreText.value = selectedOption.text || '';  // Usamos el texto del proveedor seleccionado
});

</script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const tipoProveedor = document.querySelector('[name="tipoproveedor"]');
        const descuentoAFP = document.querySelector('[name="descuentoafp"]');
        const subtotal = document.querySelector('[name="subtotal"]');
        const descuentoSancion = document.querySelector('[name="descuentosancion"]');
        const montoTotal = document.querySelector('[name="montototal"]');

        // Función para calcular el monto total
        function calcularMontoTotal() {
            const subtotalValue = parseFloat(subtotal.value) || 0;
            const descuentoSancionValue = parseFloat(descuentoSancion.value) || 0;

            // Monto total = Subtotal - Descuento AFP - Descuento Sanción
            const total = subtotalValue - descuentoSancionValue;
            montoTotal.value = total.toFixed(2);
        }

        

        // Evento cuando cambia el subtotal o descuento sanción
        [subtotal, descuentoSancion].forEach(input => {
            input.addEventListener('input', function () {
                calcularMontoTotal();
            });
        });
    });
</script>


<script>
    document.addEventListener('DOMContentLoaded', function () {
        const tipoProveedor = document.querySelector('[name="tipoproveedor"]');
        const proveedorId = document.querySelector('[name="proveedorid"]');
        const proveedorNombre = document.querySelector('[name="proveedornombre"]');

        tipoProveedor.addEventListener('change', function () {
            const tipo = this.value;

            if (tipo) {
                fetch('{{ route("obtener.proveedores") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ tipoProveedor: tipo })
                })
                .then(response => response.json())
                .then(data => {
                    proveedorNombre.innerHTML = '<option value=""></option>';
                    data.forEach(item => {
                        const option = document.createElement('option');
                        option.value = item.proveedor_id;
                        option.textContent = item.proveedor_nombre;
                        proveedorNombre.appendChild(option);
                    });
                });
            } else {
                proveedorNombre.innerHTML = '<option value=""></option>';
                proveedorId.value = '';
            }
        });

        proveedorNombre.addEventListener('change', function () {
            const selectedOption = this.options[this.selectedIndex];
            proveedorId.value = selectedOption.value || '';
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
    h1 {
        font-weight: 900;
        }
    .btn-crear {
        background-color:  #ffffff;
        color: #94c93b;
        border-color: #94c93b;
        border-radius: 5px;
        padding: 5px 10px;
        }
    .btn-crear:hover {
        background-color: #94c93b;
        color: #ffffff;
        }
</style>
@stop