@extends('adminlte::page')

@section('content_header')
<link rel="stylesheet" href="{{ asset('css/estilosnuevos.css') }}">
<a class="btn btn-sm btn-regresar float-right" href="{{route('admin.clientes.index')}}">REGRESAR</a>
<h4>NUEVO PRÉSTAMO PARA:</h4>
<h1>{{$cliente->nombrecompleto}}</h1>
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
        <div class="row ">
            <div class="col-lg-12">

                {!! Form::model($cliente, ['route' => ['admin.clientes.guardarnuevoprestamo', $cliente], 'method' => 'POST']) !!}
        
                    {!! Form::hidden('registroid', auth()->user()->id) !!}
                    {!! Form::hidden('registronombre', auth()->user()->name) !!}
                    {!! Form::hidden('clienteprestamo', $cliente->nombrecompleto) !!}
                    {!! Form::hidden('clienteid', $cliente->id) !!}
                    {!! Form::hidden('usuarioreferido', $cliente->usuarioreferido) !!}
                    {!! Form::hidden('idusuarioreferido', $cliente->idusuarioreferido) !!}
                    {{-- <div class="row mb-4">     
                        <div class="col-lg-4"> 
                            <div class="d-flex align-items-center justify-content-center border rounded p-2" style="background-color: #e6fdd3; font-size: 14px; 
                                 box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2); 
                                 transition: transform 0.3s ease, box-shadow 0.3s ease; border-radius: 20px;">
                                <span class="font-weight-bold text-dark mr-2 text-center">CAPITAL ACTUAL DE {{ $cliente->usuarioreferido }}:</span>
                                <span class="font-weight-bold h5" style="color: #ff9900; text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.2);">
                                    {{ number_format($capitalactual, 2) }}
                                </span>
                                <span class="font-weight-bold h5" style="color: #ff9900;">Bs.</span>
                            </div>
                        </div>
                    </div>
                    
                    <style>
                        .d-flex:hover {
                            transform: scale(1.05);
                            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
                        }
                    </style> --}}
                    
                    
                    
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="form-group">
                                {!! Form::label('fecha', 'Fecha de inicio:') !!}
                                {!! Form::date('fecha', \Carbon\Carbon::now(), ['class' => 'form-control', 'placeholder' => '']) !!}
                                @error('fecha')
                                    <small class="text-danger fas fa-exclamation-circle">
                                        {{$message}}
                                    </small>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-4"> 
                            <div class="form-group">
                                {!! Form::label('periodopago', 'Periodo de pago:') !!}
                                {!! Form::select('periodopago', $periodopago, null, ['class' => 'form-control', 'placeholder' => '']) !!} <!-- Valor por defecto 'DIARIO' -->
                                @error('periodopago')
                                    <small class="text-danger fas fa-exclamation-circle">
                                        {{$message}}
                                    </small>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-4"> 
                            <div class="form-group">
                                {!! Form::label('numeromeses', 'Número de meses:') !!}
                                {!! Form::text('numeromeses', 1, ['class' => 'form-control', 'placeholder' => '', 'readonly' => 'readonly']) !!} <!-- Valor por defecto 1 -->
                                @error('numeromeses')
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
                                {!! Form::label('valorprestamo', 'Valor de préstamo:') !!}
                                {!! Form::text('valorprestamo', null, ['class' => 'form-control', 'placeholder' => '', 'id' => 'valorprestamo']) !!}
                                @error('valorprestamo')
                                    <small class="text-danger fas fa-exclamation-circle">
                                        {{$message}}
                                    </small>
                                @enderror
                            </div>
                        </div>

                        
                        <div class="col-lg-4"> 
                            <div class="form-group">
                                {!! Form::label('porcentajeinteres', 'Porcentaje de interés:') !!}
                                <div class="input-group">
                                    {!! Form::text('porcentajeinteres', null, ['class' => 'form-control', 'placeholder' => '', 'id' => 'porcentajeinteres']) !!}
                                    <div class="input-group-append">
                                        <span class="input-group-text">%</span>
                                    </div>
                                </div>
                                @error('porcentajeinteres')
                                    <small class="text-danger fas fa-exclamation-circle">
                                        {{$message}}
                                    </small>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                {!! Form::label('numerocuotas', 'Número de cuotas:') !!}
                                {!! Form::text('numerocuotas', null, ['class' => 'form-control', 'placeholder' => '', 'readonly' => 'readonly']) !!}
                                @error('numerocuotas')
                                    <small class="text-danger fas fa-exclamation-circle">
                                        {{$message}}
                                    </small>
                                @enderror
                            </div>
                        </div>
                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                const periodopagoSelect = document.getElementById('periodopago');
                                const numerocuotasInput = document.getElementById('numerocuotas');
                        
                                // Función para actualizar el número de cuotas
                                function updateNumerocuotas() {
                                    const selectedValue = periodopagoSelect.value;
                        
                                    // Actualizar el valor según el periodo de pago
                                    if (selectedValue === 'DIARIO') {
                                        numerocuotasInput.value = 24;
                                    } else if (selectedValue === 'SEMANAL') {
                                        numerocuotasInput.value = 4;
                                    } else {
                                        numerocuotasInput.value = ''; // O cualquier otro valor por defecto
                                    }
                                }
                        
                                // Escuchar cambios en el select
                                periodopagoSelect.addEventListener('change', updateNumerocuotas);
                        
                                // Llamar a la función inicialmente para asegurarse de que el valor por defecto esté correcto
                                updateNumerocuotas();
                            });
                        </script>
                    </div>
                        
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="form-group">
                                {!! Form::label('valorinteres', 'Valor interés:') !!}
                                {!! Form::text('valorinteres', null, ['class' => 'form-control', 'id' => 'valorinteres', 'readonly' => 'readonly']) !!}
                                @error('valorinteres')
                                    <small class="text-danger fas fa-exclamation-circle">
                                        {{ $message }}
                                    </small>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                {!! Form::label('capitalinteres', 'Capital e interés:') !!}
                                {!! Form::text('capitalinteres', null, ['class' => 'form-control', 'id' => 'capitalinteres', 'readonly' => 'readonly']) !!}
                                @error('capitalinteres')
                                    <small class="text-danger fas fa-exclamation-circle">
                                        {{ $message }}
                                    </small>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-4"> 
                            <div class="form-group">
                                {!! Form::label('deudaactual', 'Deuda actual:') !!}
                                {!! Form::text('deudaactual', null, ['class' => 'form-control', 'id' => 'deudaactual', 'readonly' => 'readonly']) !!}
                                @error('deudaactual')
                                    <small class="text-danger fas fa-exclamation-circle">
                                        {{ $message }}
                                    </small>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <script>
                        document.getElementById('valorprestamo').addEventListener('input', calcularValores);
                        document.getElementById('porcentajeinteres').addEventListener('input', calcularValores);
                    
                        function calcularValores() {
                            var valorPrestamo = parseFloat(document.getElementById('valorprestamo').value) || 0;
                            var porcentajeInteres = parseFloat(document.getElementById('porcentajeinteres').value) || 0;
                    
                            // Cálculo del valor de interés y el capital total (capital + interés)
                            var valorInteres = valorPrestamo * (porcentajeInteres / 100);
                            var capitalInteres = valorPrestamo + valorInteres;
                            
                            // Autocompletar los campos correspondientes
                            document.getElementById('capitalinteres').value = capitalInteres.toFixed(2);
                            document.getElementById('deudaactual').value = capitalInteres.toFixed(2); // La deuda actual ahora es igual al capital más interés
                            document.getElementById('valorinteres').value = valorInteres.toFixed(2);
                        }
                    </script>
                    {!! Form::submit('CREAR PRÉSTAMO', ['class' => 'btn btn-crear']) !!}
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>
@stop