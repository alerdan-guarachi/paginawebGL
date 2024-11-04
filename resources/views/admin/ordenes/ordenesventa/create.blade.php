@extends('adminlte::page')

@section('content_header')
    <a class="btn btn-sm float-right btn-regresar" href="{{ route('admin.ordenes.ordenesventa.index') }}">REGRESAR</a>
    <h1>NUEVA NOTA DE VENTA</h1>
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
            {!! Form::model(null, ['route' => ['admin.ordenes.ordenesventa.generarPDF', $clientebanco], 'method' => 'GET']) !!}
            {!! Form::hidden('usuarioid', null) !!}
            {!! Form::hidden('usuarioregistro', null) !!}

            <!-- Datos del Cliente y Personal -->
            {!! Form::hidden('cliente', $asociado->asociado ?? 'No disponible') !!}
            {!! Form::hidden('personal', $clientebanconombre ?? 'No disponible') !!}
            {!! Form::hidden('telefono', $asociado->telefono ?? 'No disponible') !!}
            {!! Form::hidden('ciudad', $asociado->ciudad ?? 'No disponible') !!}
            {!! Form::hidden('direccion', $asociado->direccion ?? 'No disponible') !!}
            {!! Form::hidden('nro_cuenta', $asociado->cuenta ?? 'No disponible') !!}
            {!! Form::hidden('tipo_cuenta', $asociado->tipocuenta ?? 'No disponible') !!}

            <!-- Datos Estáticos de la Empresa -->
            {!! Form::hidden('nit', $empresaDatos['nit']) !!}
            {!! Form::hidden('direccion_santa_cruz', $empresaDatos['direccion_santa_cruz']) !!}
            {!! Form::hidden('direccion_cochabamba', $empresaDatos['direccion_cochabamba']) !!}
            {!! Form::hidden('telefonos', $empresaDatos['telefonos']) !!}

            <!-- Campos de cuenta-->
            <div class="row">
                <div class="col-lg-3">
                    <div class="form-group">
                        {!! Form::label('nombrecuenta', 'Nombre de Cuenta:') !!}
                        {!! Form::select('nombrecuenta', $opcionesCuentas, null, [
                            'class' => 'form-control',
                            'placeholder' => 'Seleccione una cuenta',
                            'id' => 'nombrecuenta',
                        ]) !!}
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="form-group">
                        {!! Form::label('banco', 'Banco:') !!}
                        {!! Form::text('banco', null, [
                            'class' => 'form-control',
                            'placeholder' => '',
                            'id' => 'banco',
                            'readonly' => true,
                        ]) !!}
                        {!! Form::hidden('banco', null, ['id' => 'banco1']) !!}

                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="form-group">
                        {!! Form::label('numerocuenta', 'Número de Cuenta:') !!}
                        {!! Form::text('numerocuenta', null, [
                            'class' => 'form-control',
                            'placeholder' => '',
                            'id' => 'numerocuenta',
                            'readonly' => true,
                        ]) !!}
                        {!! Form::hidden('numerocuenta', null, ['id' => 'numerocuenta1']) !!}
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="form-group">
                        {!! Form::label('titularcuenta', 'Titular:') !!}
                        {!! Form::text('titularcuenta', null, [
                            'class' => 'form-control',
                            'placeholder' => '',
                            'id' => 'titularcuenta',
                            'readonly' => true,
                        ]) !!}
                        {!! Form::hidden('titularcuenta', null, ['id' => 'titularcuenta1']) !!}
                    </div>
                </div>
            </div>

            <!-- Campos de modalidad de pago, forma de pago y fecha de pago -->
            <div class="row mt-4">
                <div class="col-lg-3">
                    <div class="form-group">
                        {!! Form::label('modalidadpago', 'Modalidad de Pago:') !!}
                        {!! Form::select('modalidadpago', ['CRÉDITO' => 'CRÉDITO', 'CONTADO' => 'CONTADO'], null, [
                            'class' => 'form-control',
                            'placeholder' => 'Seleccione la modalidad',
                        ]) !!}
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="form-group">
                        {!! Form::label('formapago', 'Forma de Pago:') !!}
                        {!! Form::select(
                            'formapago',
                            [
                                'EFECTIVO' => 'EFECTIVO',
                                'TRANSFERENCIAS BANCARIAS' => 'TRANSFERENCIAS BANCARIAS',
                                'DEPOSITOS BANCARIOS' => 'DEPOSITOS BANCARIOS',
                                'ATC' => 'ATC',
                                'CHEQUE' => 'CHEQUE',
                            ],
                            null,
                            ['class' => 'form-control', 'placeholder' => 'Seleccione la forma de pago'],
                        ) !!}
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="form-group">
                        {!! Form::label('fechapago', 'Fecha de Pago:') !!}
                        {!! Form::date('fechapago', \Carbon\Carbon::now()->toDateString(), ['class' => 'form-control']) !!}
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="form-group">
                        {!! Form::label('sucursal', 'Sucursal:') !!}
                        {!! Form::select(
                            'sucursal',
                            ['SANTA CRUZ' => 'SANTA CRUZ', 'COCHABAMBA' => 'COCHABAMBA'],
                            null,
                            ['class' => 'form-control', 'placeholder' => 'Seleccione la sucursal'],
                        ) !!}
                    </div>
                </div>
            </div>

            <!-- Campos de salida y destino -->
            <div class="row mt-4">
                <div class="col-lg-3">
                    <div class="form-group">
                        {!! Form::label('salida', 'Salida:') !!}
                        {!! Form::select('salida', ['CAJA' => 'CAJA', 'BANCO' => 'BANCO'], null, [
                            'class' => 'form-control',
                            'placeholder' => 'Seleccione salida',
                        ]) !!}
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="form-group">
                        {!! Form::label('destino', 'Destino:') !!}
                        {!! Form::select('destino', ['CAJA' => 'CAJA', 'BANCO' => 'BANCO'], null, [
                            'class' => 'form-control',
                            'placeholder' => 'Seleccione destino',
                        ]) !!}
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="form-group">
                        {!! Form::label('usuarioaprocredito', 'Usuario que Aprueba el Crédito:') !!}
                        {!! Form::select('usuarioaprocredito', $usuariosPruebaCredito, null, [
                            'class' => 'form-control',
                            'placeholder' => 'Seleccione usuario',
                        ]) !!}
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="form-group">
                        {!! Form::label('detalle', 'Detalle:') !!}
                        {!! Form::select('detalle', ['SERVICIOS MÉDICOS' => 'SERVICIOS MÉDICOS'], null, [
                            'class' => 'form-control',
                            'placeholder' => 'Seleccione el Detalle',
                        ]) !!}
                    </div>
                </div>
            </div>

            <!-- Tabla de Baterías Subclientes -->
            <div class="table-responsive mt-4">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Nombre de la Acción</th>
                            <th>Precio</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($bateriasubclientes as $bateriasubcliente)
                            <tr>
                                <td>{{ $bateriasubcliente->accionnombre }}</td>
                                <td class="precio">{{ $bateriasubcliente->precio }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Observaciones y Totales -->
            <div class="row mt-4">
                <div class="col-lg-6">
                    <div class="form-group">
                        {!! Form::label('observaciones', 'Observaciones:') !!}
                        {!! Form::textarea('observaciones', null, [
                            'class' => 'form-control',
                            'rows' => 4,
                            'placeholder' => 'Ingrese sus observaciones aquí',
                        ]) !!}
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="totals" style="text-align: right;">
                        <p style="margin: 0; padding-bottom: 0;">
                            <strong>NETO:</strong>
                            <span
                                style="border: 0.5px solid lightgray; border-bottom: 2px solid lightgray; padding: 2px; display: inline-block; min-width: 100px; margin-left: 20px; vertical-align: bottom;">
                                {{ $total }}
                            </span>
                        </p>
                        <p style="margin: 0; padding-bottom: 0;">
                            <strong>DESCUENTOS (Bs):</strong>
                            <span
                                style="border: 0.5px solid lightgray; border-bottom: 2px solid black; padding: 2px; display: inline-block; min-width: 100px; margin-left: 20px; vertical-align: bottom;">
                                {!! Form::text('descuento', 0, [
                                    'style' => 'width: 100%; border: none; background: transparent; text-align: right;',
                                    'id' => 'descuento',
                                    'oninput' => 'calcularTotal()',
                                    'pattern' => '[0-9]*',
                                ]) !!}
                            </span>
                        </p>
                        <p style="margin: 0; padding-bottom: 0;">
                            <strong>TOTAL:</strong>
                            <span id="totalFinal"
                                style="border: 0.5px solid lightgray; border-bottom: 2px solid lightgray; padding: 2px; display: inline-block; min-width: 100px; margin-left: 20px; vertical-align: bottom;">
                                {{ $total }}
                            </span>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Botón para Generar PDF -->
            {!! Form::submit('Generar Nota de Venta (PDF)', ['class' => 'btn btn-primary']) !!}

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
            // Escuchar cambios en el campo nombrecuenta
            $('#nombrecuenta').on('change', function() {
                var nombrecuenta = $(this).val();

                if (nombrecuenta) {
                    $.ajax({
                        url: "{{ route('admin.ordenes.ordenesventa.create.crearnotadeventa') }}",
                        type: "POST",
                        data: {
                            nombrecuenta: nombrecuenta,
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(data) {
                            // Asignar datos devueltos a los campos `hidden`
                            $('#banco1').val(data.banco);
                            $('#banco').val(data.banco);
                            $('#numerocuenta1').val(data.numerocuenta);
                            $('#numerocuenta').val(data.numerocuenta);
                            $('#titularcuenta1').val(data.titularcuenta);
                            $('#titularcuenta').val(data.titularcuenta);
                        },
                        error: function() {
                            alert("No se encontraron datos para la cuenta seleccionada.");
                        }
                    });
                }
            });

            // Verificar valores de campos `hidden` antes de enviar
            $('#miFormulario').on('submit', function(e) {
                e.preventDefault();

                var banco = $('#banco').val();
                var numerocuenta = $('#numerocuenta').val();
                var titularcuenta = $('#titularcuenta').val();

                if (banco && numerocuenta && titularcuenta) {
                    this.submit(); // Enviar si los valores están presentes
                } else {
                    alert("Por favor, seleccione un nombre de cuenta válido.");
                }
            });
        });

        function calcularTotal() {
            // Obtener el valor del NETO y el descuento
            var neto = {{ $total }};
            var descuento = parseFloat(document.getElementById('descuento').value) || 0;

            // Calcular el nuevo total
            var totalFinal = neto - descuento;

            // Mostrar el resultado en el campo TOTAL
            document.getElementById('totalFinal').innerText = totalFinal.toFixed(2);
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
@endsection

@section('css')
    <link rel="styleheet" href="/css/admin_custom.css">
    <style>
        h1 {
            color: #94c93b;
            font-family: "Segoe UI";
            font-weight: 900;
        }

        .btn-crear {
            background-color: #ffffff;
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
