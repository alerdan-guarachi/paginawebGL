@extends('adminlte::page')

@section('content_header')
<div class="modal fade" id="crearProductoModal" tabindex="-1" aria-labelledby="crearProductoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="crearProductoModalLabel" style="font-weight: 900;">NUEVA FACTURA</h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                {!! Form::open(['route' => 'admin.facturasegreso.store', 'method'=>'POST']) !!}
                    {!! Form::hidden('usuarioregistroid', auth()->user()->id) !!}
                    {!! Form::hidden('usuarioregistronombre', auth()->user()->name) !!}
                    <div class="row">
                        <div class="col-lg-3 form-group">
                            <label for="tipo">Tipo</label>
                            <select name="tipo" id="tipo" class="form-control" required>
                                <option value=""></option>
                                <option value="1">COMPRA</option>
                                <option value="2">VENTA</option>
                            </select>
                            <input type="hidden" id="especificacion" name="especificacion" class="form-control">
                        </div>
                        <div class="col-lg-3 form-group">
                            <label for="ciudad">Ciudad</label>
                            <select name="ciudad" id="ciudad" class="form-control" required>
                                <option value=""></option>
                                <option value="SANTA CRUZ">SANTA CRUZ</option>
                                <option value="COCHABAMBA">COCHABAMBA</option>
                            </select>
                        </div>
                        <div class="col-lg-3 form-group">
                            <label>Nro. Factura</label>
                            <input type="text" id="nrofactura" name="nrofactura" class="form-control" required>
                        </div>
                        <div class="col-lg-3 form-group">
                            <label>Nro. NIT/CI</label>
                            <input type="text" id="nitci" name="nitci" class="form-control" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-4 form-group">
                            <label>Razón Social</label>
                            <input list="razonSocialList" id="razonsocial" name="razonsocial" class="form-control" required>
                            <datalist id="razonSocialList">
                                @foreach($razonesSociales as $razon)
                                    <option value="{{ $razon }}">
                                @endforeach
                            </datalist>
                        </div>
                        <div id="complemento-container" class="col-lg-1 form-group">
                            <label>Comp.</label>
                            <input type="text" id="complemento" name="complemento" class="form-control">
                        </div>
                        <div id="codigoautorizacion-container" class="col-lg-4 form-group">
                            <label>Cod. Autorización</label>
                            <input type="text" id="codigoautorizacion" name="codigoautorizacion" class="form-control" required>
                        </div>
                        <div class="col-lg-3 form-group">
                            <label>Fecha Factura</label>
                            <input type="date" id="fechafacturaduidim" name="fechafacturaduidim" class="form-control" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-3 form-group">
                            <label>Total</label>
                            <input type="number" id="subtotal" name="total" class="form-control" step="0.01" required>
                        </div>
                        <div class="col-lg-3 form-group">
                            <label>Descuento</label>
                            <input type="number" id="descuento" name="descuento" class="form-control" step="0.01" required>
                        </div>
                        <div class="col-lg-3 form-group">
                            <label>Subtotal</label>
                            <input type="number" id="total" name="subtotal" class="form-control" step="0.01" required readonly>
                        </div>
                        <div class="col-lg-3 form-group">
                            <label>Importe Base CF/DF</label>
                            <input type="number" id="cfdf2" name="importebasecfdf" class="form-control" step="0.01" required readonly>
                        </div>
                    </div>
                    <div class="row">
                        <div id="otronosujcredfiscaloiva-container" class="col-lg-3 form-group">
                            <label>Otros No Sujeto CF</label>
                            <input type="number" id="otronosujcredfiscaloiva" name="otronosujcredfiscaloiva" class="form-control" step="0.01">
                        </div>
                        <div id="tasas-container" class="col-lg-4 form-group">
                            <label>Tasas</label>
                            <input type="number" id="tasas" name="tasas" class="form-control" step="0.01">
                        </div>
                        <div id="creditodebitofiscal-container" class="col-lg-3 form-group">
                            <label>Créd./Déb. Fiscal</label>
                            <input type="number" id="creditodebitofiscal" name="creditodebitofiscal" class="form-control" step="0.01" readonly>
                        </div>
                        <div id="codigocontrol-container" class="col-lg-3 form-group">
                            <label>Cod. Control</label>
                            <input type="number" id="codigocontrol" name="codigocontrol" class="form-control" step="0.01" required>
                        </div>
                        <script>
                            document.addEventListener('DOMContentLoaded', function () {
                                const tipoSelect = document.getElementById('tipo');
                                const estadoSelect = document.getElementById('estado');

                                tipoSelect.addEventListener('change', function () {
                                    if (this.value === '2') { // VENTA
                                        estadoSelect.value = 'ANULADO';
                                        estadoSelect.disabled = true;
                                    } else if (this.value === '1') { // COMPRA
                                        estadoSelect.value = 'VALIDO';
                                        estadoSelect.disabled = false;
                                    } else {
                                        estadoSelect.value = '';
                                        estadoSelect.disabled = false;
                                    }
                                });
                            });
                        </script>

                        <div id="estado-container" class="col-lg-3 form-group">
                            <label for="estado">Estado</label>
                            <select name="estado" id="estado" class="form-control" required>
                                <option value="VALIDO">VALIDO</option>
                                <option value="ANULADO">ANULADO</option>
                            </select>
                        </div>
                        <div id="usuarioentrega-container"  class="col-lg-6 form-group">
                            <label for="usuarioentrega">Usuario Entrega</label>
                            <select name="usuarioentrega" id="usuarioentrega" class="form-control">
                                <option value="">Seleccione un proveedor</option>
                                @foreach ($usuariosEntrega as $proveedor)
                                    <option value="{{ $proveedor->razonsocial }}">{{ $proveedor->razonsocial }}</option>
                                @endforeach
                            </select>
                        </div>
                        <script>
                            document.addEventListener('DOMContentLoaded', function () {
                                const razonInput = document.getElementById('razonsocial');
                                const usuarioEntregaContainer = document.getElementById('usuarioentrega-container');

                                const razonesQueOcultanCampo = [
                                    'TELEFONICA CELULAR DE BOLIVIA S.A.',
                                    'COOPERATIVA DE SERVICIOS PUBLICOS SANTA CRUZ R.L.',
                                    'COOPERATIVA DE TELECOMUNICACIONES SANTA CRUZ R.L.',
                                    'COOPERATIVA RURAL DE ELECTRIFICACIÓN R.L.'
                                ];

                                function verificarOcultamiento() {
                                    const valor = razonInput.value.trim().toUpperCase();
                                    const ocultar = razonesQueOcultanCampo.some(nombre => valor === nombre.toUpperCase());
                                    usuarioEntregaContainer.style.display = ocultar ? 'none' : '';
                                }

                                razonInput.addEventListener('input', verificarOcultamiento);

                                // También ejecutar al cargar (por si el valor ya está seteado)
                                verificarOcultamiento();
                            });
                        </script>

                    </div>
                    {{--<div class="form-group col-lg-4">
                            <label for="pdfFactura">Subir PDF de Factura</label>
                            <input type="file" id="pdfFactura" class="form-control" accept="application/pdf">
                        </div>
                        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.min.js"></script>

                        <script>
                        document.getElementById('pdfFactura').addEventListener('change', function (e) {
                            const file = e.target.files[0];
                            if (!file || file.type !== 'application/pdf') return;

                            const fileReader = new FileReader();
                            fileReader.onload = function () {
                                const typedarray = new Uint8Array(this.result);

                                pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.worker.min.js'; // ← SOLUCIÓN para el warning

                                pdfjsLib.getDocument(typedarray).promise.then(function (pdf) {
                                    pdf.getPage(1).then(function (page) {
                                        return page.getTextContent();
                                    }).then(function (textContent) {
                                        let rawItems = textContent.items.map(item => item.str);
                                        let joinedText = rawItems.join(''); // ← juntamos sin espacios
                                        console.log('Texto unido:', joinedText); // puedes ver qué se forma

                                        // Buscamos un patrón tipo: Nro. Factura: 8381834 (con o sin espacios)
                                        const match = joinedText.match(/Nro?\.?\s*Factura\s*[:\-]?\s*(\d{4,})/i);
                                        if (match && match[1]) {
                                            document.getElementById('nrofactura').value = match[1];
                                        } else {
                                            alert('No se encontró el número de factura.');
                                        }
                                    });
                                });
                            };
                            fileReader.readAsArrayBuffer(file);
                        });
                    </script> --}}

                    <script>
                        document.addEventListener('DOMContentLoaded', function () {
                            const tipo = document.getElementById('tipo');

                            // Elementos para mostrar/ocultar y ajustar clases
                            const complementoContainer = document.getElementById('complemento-container');
                            const usuarioentregaContainer = document.getElementById('usuarioentrega-container');
                            const complemento = document.getElementById('complemento');
                            const codigoautorizacionContainer = document.getElementById('codigoautorizacion-container');
                            const otronosujcredfiscaloivaContainer = document.getElementById('otronosujcredfiscaloiva-container');
                            const otronosujcredfiscaloiva = document.getElementById('otronosujcredfiscaloiva');
                            const creditoContainer = document.getElementById('creditodebitofiscal-container');
                            const controlContainer = document.getElementById('codigocontrol-container');
                            const tasasContainer = document.getElementById('tasas-container');
                            const estadoContainer = document.getElementById('estado-container');
                            const especificacion = document.getElementById('especificacion');
                            
                            const subtotal = document.getElementById('subtotal');
                            const descuento = document.getElementById('descuento');
                            const total = document.getElementById('total');
                            const cfdf = document.getElementById('cfdf2');
                            const credito = document.getElementById('creditodebitofiscal');
                            const tasas = document.getElementById('tasas');

                            function toggleFields() {
                                especificacion.value = tipo.value;
                                if (tipo.value === '2') {
                                    complementoContainer.style.display = '';
                                    usuarioentregaContainer.style.display = 'none';
                                    complemento.value = '0';
                                    codigoautorizacionContainer.classList.remove('col-lg-5');
                                    codigoautorizacionContainer.classList.add('col-lg-4');
                                } else {
                                    complementoContainer.style.display = 'none';
                                    usuarioentregaContainer.style.display = '';
                                    complemento.value = '0';
                                    codigoautorizacionContainer.classList.remove('col-lg-4');
                                    codigoautorizacionContainer.classList.add('col-lg-5');
                                }

                                if (tipo.value === '1') {
                                    otronosujcredfiscaloivaContainer.style.display = '';
                                    usuarioentregaContainer.style.display = '';
                                    tasasContainer.style.display = '';
                                    creditoContainer.classList.remove('col-lg-4');
                                    controlContainer.classList.remove('col-lg-4');
                                    tasasContainer.classList.remove('col-lg-4');
                                    estadoContainer.classList.remove('col-lg-3');
                                    creditoContainer.classList.add('col-lg-3');
                                    controlContainer.classList.add('col-lg-3');
                                    tasasContainer.classList.add('col-lg-3');
                                    estadoContainer.classList.add('col-lg-6');
                                } else {
                                    otronosujcredfiscaloivaContainer.style.display = 'none';
                                    usuarioentregaContainer.style.display = 'none';
                                    tasasContainer.style.display = 'none';
                                    otronosujcredfiscaloiva.value = '';
                                    creditoContainer.classList.remove('col-lg-3');
                                    controlContainer.classList.remove('col-lg-3');
                                    tasasContainer.classList.remove('col-lg-3');
                                    estadoContainer.classList.remove('col-lg-6');
                                    creditoContainer.classList.add('col-lg-4');
                                    controlContainer.classList.add('col-lg-4');
                                    tasasContainer.classList.add('col-lg-4');
                                    estadoContainer.classList.add('col-lg-4');
                                }

                                calcularTotales();
                            }

                            function calcularTotales() {
                                const valSubtotal = parseFloat(subtotal.value) || 0;
                                const valDescuento = parseFloat(descuento.value) || 0;
                                const valImporteNoSujeto = parseFloat(otronosujcredfiscaloiva.value) || 0;
                                const valtasas = parseFloat(tasas.value) || 0;

                                const valTotal = valSubtotal - valDescuento - valtasas;
                                total.value = valTotal.toFixed(2);

                                const valCFDF = valTotal - valImporteNoSujeto;
                                cfdf.value = valCFDF.toFixed(2);

                                const valCredito = valCFDF * 0.13;
                                credito.value = valCredito.toFixed(2);
                            }

                            // Eventos
                            tipo.addEventListener('change', toggleFields);
                            subtotal.addEventListener('input', calcularTotales);
                            descuento.addEventListener('input', calcularTotales);
                            otronosujcredfiscaloiva.addEventListener('input', calcularTotales);
                            tasas.addEventListener('input', calcularTotales); // ← ESTA LÍNEA FALTABA

                            toggleFields(); // Inicializar al cargar
                        });
                    </script>

                    <div class="row" hidden>
                        <div class="col-lg-3 form-group">
                            <label>Nro. DUI/DIM</label>
                            <input type="text" id="nroduidim" name="nroduidim" class="form-control" value="0.00">
                        </div>
                        {{-- <script>
                            document.getElementById('razonsocial').addEventListener('input', function () {
                                const razonSocial = this.value.trim().toUpperCase();
                                const inputNroDuidim = document.getElementById('nroduidim');

                                if (razonSocial === 'COOPERATIVA DE SERVICIOS PUBLICOS SANTA CRUZ R.L.') {
                                    inputNroDuidim.value = '1.00';
                                } else {
                                    inputNroDuidim.value = '0.00';
                                }
                            });
                        </script> --}}
                        <div class="col-lg-3 form-group">
                            <label>Importe ICE</label>
                            <input type="text" id="ice" name="ice" class="form-control" value="0.00">
                        </div>
                        <div class="col-lg-3 form-group">
                            <label>Importe IEHD</label>
                            <input type="text" id="iehd" name="iehd" class="form-control" value="0.00">
                        </div>
                    </div>
                    <div class="row" hidden>
                        <div class="col-lg-3 form-group">
                            <label>No Suj.Cred.Fiscal</label>
                            <input type="text" id="importenosujetocfdf" name="importenosujetocfdf" class="form-control" value="0.00">
                        </div>
                        <div class="col-lg-3 form-group">
                            <label>Importe/Exporte Externo</label>
                            <input type="text" id="importeyexporteexterno" name="importeyexporteexterno" class="form-control" value="0.00">
                        </div>
                        <div class="col-lg-3 form-group">
                            <label>Importe Gift Card</label>
                            <input type="text" id="giftcard" name="giftcard" class="form-control" value="0.00">
                        </div>
                    </div>
                    <div class="row" hidden>
                        <div class="col-lg-3 form-group">
                            <label>Importe IPJ</label>
                            <input type="text" id="ipj" name="ipj" class="form-control" value="0.00">
                        </div>
                        <div class="col-lg-3 form-group">
                            <label>Importe Externo</label>
                            <input type="text" id="importeexterno" name="importeexterno" class="form-control" value="0.00">
                        </div>
                        <div class="col-lg-3 form-group">
                            <label>Importe Grav. Tasa Cero</label>
                            <input type="text" id="tasacero" name="tasacero" class="form-control" value="0.00">
                        </div>
                    </div>
                    {!! Form::submit('GUARDAR FACTURA', ['class' => 'btn btn-sm btn-outline-secondary']) !!}
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>
<a type="button" class="btn float-right btn-outline-secondary btn-sm" data-toggle="modal" data-target="#crearProductoModal">
    AGREGAR FACTURA
</a>
<a class="btn float-right btn-outline-secondary btn-sm" style="margin-right: 10px;" data-toggle="modal" data-target="#modalCodigo">
    CODIGO PERMISO
</a>
<div class="modal fade" id="modalCodigo" tabindex="-1" role="dialog" aria-labelledby="modalCodigoLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="formCodigo">
                <div class="modal-header">
                    <h3 class="modal-title" id="modalCodigoLabel" style="font-weight: 900;">INGRESAR CÓDIGO</h3>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="text" id="codigoInput" name="codigo" class="form-control" placeholder="Ingrese el código" required>
                    <div id="codigoMensaje" class="mt-2 text-danger" style="display: none;"></div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-sm btn-outline-secondary">VALIDAR</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    document.getElementById('formCodigo').addEventListener('submit', function(e) {
        e.preventDefault();
        const codigo = document.getElementById('codigoInput').value.trim();
        const mensaje = document.getElementById('codigoMensaje');
        mensaje.style.display = 'none';

        fetch('{{ route("permisoscodigo.codigocambiofacturacambiorsocial") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
            },
            body: JSON.stringify({ codigo: codigo })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                $('#modalCodigo').modal('hide');
                alert('Código validado correctamente');
                location.reload();
            } else {
                mensaje.textContent = data.message;
                mensaje.style.display = 'block';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            mensaje.textContent = 'Ocurrió un error al procesar la solicitud.';
            mensaje.style.display = 'block';
        });
    });
</script>

<h1>FACTURAS IMPUESTOS</h1>
@stop

@section('css')
<link rel="stylesheet" href="{{ asset('css/opcionesmultiples.css') }}">
<style>
    .table td {
        padding: 2px 10px;
    }
    .table th {
        padding: 6px 10px;
    }
    .btn-registrar {
        background-color:  #ffffff;
        color: #94c93b;
        border-color: #94c93b;
        border-radius: 5px;
        padding: 2px 20px;
        }
    .btn-registrar:hover {
        background-color: #94c93b;
        color: #ffffff;
        }
    .btn-guardarformulario {
        background-color:  #ffffff;
        color: #000000;
        border-color: #000000;
        border-radius: 5px;
        padding: 2px 20px;
        }
    .btn-guardarformulario:hover {
        background-color: #94c93b;
        color: #ffffff;
        }
    .btn-actualizar {
        background-color:  #ffffff;
        color: #e8932b;
        border-color: #e8932b;
        border-radius: 5px;
        padding: 2px 6px;
        }
    .btn-actualizar:hover {
        background-color: #e8932b;
        color: #ffffff;
        }
    .truncar {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 180px;
    }
    .truncar2 {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 150px;
    }
    .btn-verregistros {
        background-color:  #ffffff;
        color: #787878;
        border-color: #787878;
        border-radius: 5px;
        padding: 2px 6px;
        }
    .btn-verregistros:hover {
        background-color: #787878;
        color: #ffffff;
        }
    .btn-custom2 {
        background-color:  #ffffff;
        color: #9d9d9d;
        border-color: #9d9d9d;
        border-radius: 20px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        transition: background-color 0.3s, transform 0.3s, box-shadow 0.3s;
        height: 200px;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0;
    }
    .btn-custom2:hover {
        background-color: #9d9d9d;
        color: #ffffff;
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        transform: scale(1.05);
    }
    .btn-custom2:disabled {
        background-color: #d6d6d6;
        color: #a0a0a0;
        cursor: not-allowed;
    }
</style>
<style>
    .alerta-pulso {
        animation: pulso 1s infinite;
    }

    @keyframes pulso {
        0% { transform: scale(1); opacity: 1; }
        50% { transform: scale(1.2); opacity: 0.7; }
        100% { transform: scale(1); opacity: 1; }
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
        }, 3000);
    </script>
@endif
<div class="card">
    <div class="card-header">
        <form method="GET" action="{{ route('admin.facturasegreso.index') }}" class="row g-3">
            <div class="col-md-2">
                <label>Fecha Desde</label>
                <input type="date" name="fechaDesde" value="{{ $fechaDesde }}" class="form-control">
            </div>
            <div class="col-md-2">
                <label>Fecha Hasta</label>
                <input type="date" name="fechaHasta" value="{{ $fechaHasta }}" class="form-control">
            </div>
            <div class="col-md-2 align-self-end">
                <button class="btn btn-outline-secondary" type="submit">Filtrar</button>
            </div>
        </form>
        <br>
        <ul class="nav nav-tabs card-header-tabs" id="myTabs">
            <li class="nav-item">
                <a class="nav-link active" id="tab-1" data-toggle="tab" href="#tab-content-1" role="tab" aria-controls="tab-content-1" aria-selected="true">
                    IMPUESTO DETERMINADO
                </a>
            </li>   
            <li class="nav-item">
                <a class="nav-link" id="tab-6" data-toggle="tab" href="#tab-content-6" role="tab" aria-controls="tab-content-6" aria-selected="true">
                    FORMULARIOS
                </a>
            </li> 
            <li class="nav-item">
                <a class="nav-link" id="tab-2" data-toggle="tab" href="#tab-content-2" role="tab" aria-controls="tab-content-2" aria-selected="true">
                    COMPRAS SCZ
                </a>
            </li>     
            <li class="nav-item">
                <a class="nav-link" id="tab-3" data-toggle="tab" href="#tab-content-3" role="tab" aria-controls="tab-content-3" aria-selected="true">
                    VENTAS SCZ
                </a>
            </li>    
            <li class="nav-item">
                <a class="nav-link" id="tab-4" data-toggle="tab" href="#tab-content-4" role="tab" aria-controls="tab-content-4" aria-selected="true">
                    COMPRAS CBBA
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="tab-5" data-toggle="tab" href="#tab-content-5" role="tab" aria-controls="tab-content-5" aria-selected="true">
                    VENTAS CBBA
                </a>
            </li> 
            <li class="nav-item">
                <a class="nav-link" id="tab-7" data-toggle="tab" href="#tab-content-7" role="tab" aria-controls="tab-content-7" aria-selected="true">
                    OTRAS FACTURAS
                </a>
            </li> 
        </ul>
    </div>
    <div class="card-body">
        <div class="tab-content" id="myTabContent">
            {{-- IMPUESTO DETERMINADO --}}
            <div class="tab-pane fade show active" id="tab-content-1" role="tabpanel" aria-labelledby="tab-1">
                <div class="row">
                    <div class="col-lg-7">
                        <div class="card">
                            <div class="card-body">
                                <h5 style="font-weight: 900; margin-top: -10px;">VENTAS</h5>
                                <div class="table-responsive" style="margin-bottom: 10px;">
                                    <table class="table table-bordered">
                                        <thead style="background-color: #f3f3f3">
                                            <tr>
                                                <th>Sucursal</th>
                                                <th style="text-align: right;">Ventas</th>
                                                <th style="text-align: right;">Débito Fiscal</th>
                                                <th style="text-align: right;">IT</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($ventas as $ciudad => $registros)
                                                @php
                                                    $total = $registros->sum('importebasecfdf');
                                                    $iva = /* $total * 0.13; */ $registros->sum('creditodebitofiscal');
                                                    $it = $total * 0.03;
                                                @endphp
                                                <tr>
                                                    <td>{{ $ciudad }}</td>
                                                    <td style="text-align: right;">{{ number_format($total, 2) }}</td>
                                                    <td style="text-align: right;">{{ number_format($iva, 2) }}</td>
                                                    <td style="text-align: right;">{{ number_format($it, 2) }}</td>
                                                </tr>
                                            @endforeach
                                            <tr style="background-color: #f3f9e5;">
                                                <th>Total</th>
                                                <th style="text-align: right;">{{ number_format($totales['totalVentas'], 2) }}</th>
                                                <th style="text-align: right;">{{ number_format($totales['ivaDebito'], 2) }}</th>
                                                <th style="text-align: right;">{{ number_format($totales['itVentas'], 2) }}</th>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <h5 style="font-weight: 900;">COMPRAS</h5>
                                <div class="table-responsive" style="margin-bottom: -20px;">
                                    <table class="table table-bordered">
                                        <thead style="background-color: #f3f3f3">
                                            <tr>
                                                <th>Sucursal</Canvas></th>
                                                <th style="text-align: right;">Compras</th>
                                                <th style="text-align: right;">Crédito Fiscal</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($compras as $ciudad => $registros)
                                                @php
                                                    $total = $registros->sum('importebasecfdf');
                                                    $iva = /* $total * 0.13; */ $registros->sum('creditodebitofiscal');
                                                @endphp
                                                <tr>
                                                    <td>{{ $ciudad }}</td>
                                                    <td style="text-align: right;">{{ number_format($total, 2) }}</td>
                                                    <td style="text-align: right;">{{ number_format($iva, 2) }}</td>
                                                </tr>
                                            @endforeach
                                            <tr style="background-color: #f3f9e5">
                                                <th>Total</th>
                                                <th style="text-align: right;">{{ number_format($totales['totalCompras'], 2) }}</th>
                                                <th style="text-align: right;">{{ number_format($totales['ivaCredito'], 2) }}</th>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="mt-4">
                                    <div class="d-flex justify-content-between align-items-center p-2" style="background-color: #fff2dc; font-weight: 900;">
                                        <h5 class="mb-0" style="font-weight: 900;">DIFERENCIA:</h5>
                                        <h5 class="mb-0" style="font-weight: 900;">{{ number_format($totales['diferencia'], 2) }}</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-5">
                        <div class="card">
                            <div class="card-body" style="margin-bottom: -18px;">
                                @php
                                    use Illuminate\Support\Facades\Auth;
                                    use App\Models\SaldoCreditoFiscal;

                                    $ultimoSaldo = SaldoCreditoFiscal::latest()->first();
                                    $saldoAnterior = $ultimoSaldo?->saldo ?? 0;
                                @endphp
                                <h5 style="font-weight: 900; margin-top: -10px; margin-bottom: -15px;">MONTOS PERIODO ANTERIOR</h5>
                                <div class="mt-4" style="margin-bottom: -10px;"> 
                                    <div class="p-2 d-flex justify-content-between align-items-center" style="background-color: #f3f3f3">
                                        <h6 class="mb-0">Saldo Crédito Fiscal a favor del periodo anterior:</h6>
                                        <div class="d-flex align-items-center">
                                            <button id="guardarSaldo" class="btn btn-sm btn-secondary" disabled><i class="fas fa-print"></i></button>
                                            <input type="number" 
                                                id="saldocreditoanterior" 
                                                class="form-control form-control-sm me-2" 
                                                style="width: 100px; text-align: right;" 
                                                value="{{ $saldoAnterior }}" 
                                                step="0.01">
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-4"> 
                                    <div class="p-2 d-flex justify-content-between align-items-center" style="background-color: #f3f3f3">
                                        <h6 class="mb-0">Total de Compras a favor del periodo anterior:</h6>
                                        <input type="number" 
                                            id="totalcomprasperiodoanterior" 
                                            name="totalcomprasperiodoanterior" 
                                            class="form-control form-control-sm" 
                                            style="width: 100px; text-align: right;" 
                                            value="{{ number_format($saldoAnterior / 0.13, 2, '.', '') }}" 
                                            step="0.01" 
                                            readonly>
                                    </div>
                                </div>

                                <script>
                                    document.addEventListener('DOMContentLoaded', function () {
                                        const inputSaldo = document.getElementById('saldocreditoanterior');
                                        const inputCompras = document.getElementById('totalcomprasperiodoanterior');
                                        const boton = document.getElementById('guardarSaldo');
                                        const saldoOriginal = parseFloat(inputSaldo.value);

                                        function actualizarCompras() {
                                            const monto = parseFloat(inputSaldo.value);
                                            if (!isNaN(monto)) {
                                                inputCompras.value = (monto / 0.13).toFixed(2);
                                            } else {
                                                inputCompras.value = '';
                                            }
                                        }

                                        inputSaldo.addEventListener('input', function () {
                                            const valorActual = parseFloat(inputSaldo.value);
                                            boton.disabled = (valorActual === saldoOriginal || isNaN(valorActual));
                                            actualizarCompras();
                                        });

                                        boton.addEventListener('click', function () {
                                            const monto = parseFloat(inputSaldo.value);

                                            fetch("{{ route('saldocreditofiscal.guardar') }}", {
                                                method: 'POST',
                                                headers: {
                                                    'Content-Type': 'application/json',
                                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                                },
                                                body: JSON.stringify({ monto: monto })
                                            })
                                            .then(response => response.json())
                                            .then(data => {
                                                alert("Saldo guardado correctamente.");
                                                location.reload(); // Opcional
                                            })
                                            .catch(error => {
                                                console.error(error);
                                                alert("Error al guardar.");
                                            });
                                        });

                                        actualizarCompras(); // cálculo inicial
                                    });
                                </script>

                                <h5 class="mt-4" style="font-weight: 900;">RESUMEN GENERAL</h5>
                                <div class="table-responsive">
                                    <table class="table table-bordered" style="width: 100%;">
                                        <tbody>
                                            <tr style="background-color: #fff2dc">
                                                <td>Total Ventas</td>
                                                <th style="text-align: right;">{{ number_format($totales['totalVentas'], 2) }}</th>
                                            </tr>
                                            <tr style="background-color: #fff2dc">
                                                <td>Total Compras</td>
                                                <th style="text-align: right;">{{ number_format($totales['totalCompras'], 2) }}</th>
                                            </tr>
                                            <tr style="background-color: #f3f9e5">
                                                <td>Impuesto al Valor Agregado</td>
                                                <th style="text-align: right;">
                                                    {{ number_format(($totales['totalVentas'] * 0.13) - ($totales['totalCompras'] * 0.13), 2) }}
                                                </th>
                                            </tr>
                                            <tr style="background-color: #f3f9e5">
                                                <td>Impuesto a las Transacciones</td>
                                                <th style="text-align: right;">{{ number_format($totales['totalVentas'] * 0.03, 2) }}</th>
                                            </tr>
                                            @php
                                                $iva = ($totales['totalVentas'] * 0.13) - ($totales['totalCompras'] * 0.13);
                                                $saldoFiscoContribuyente = $saldoAnterior - $iva;
                                                $compensacionIVACompras = $iva / 0.13;
                                            @endphp
                                            <tr style="background-color: #fff2dc">
                                                <td>Saldo a favor del Fisco o del Contribuyente</td>
                                                <th style="text-align: right;">{{ number_format($saldoFiscoContribuyente, 2) }}</th>
                                            </tr>
                                            <tr style="background-color: #fff2dc">
                                                <td>Compensación de IVA Compras</td>
                                                <th style="text-align: right;">{{ number_format($compensacionIVACompras, 2) }}</th>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @php
                use Carbon\Carbon;

                $mesAnterior = Carbon::now()->subMonth();
                $periodoLiteral = strtoupper($mesAnterior->locale('es')->translatedFormat('F'));
                $periodo = $mesAnterior->format('Y-m');

                /* $iva = number_format(($totales['totalVentas'] * 0.13) - ($totales['totalCompras'] * 0.13), 2, '.', ''); */
                $ivaBruto = ($totales['totalVentas'] * 0.13) - ($totales['totalCompras'] * 0.13);
                $iva = $ivaBruto < 0 ? '0.00' : number_format($ivaBruto, 2, '.', '');
                $it = number_format(($totales['totalVentas'] * 0.03), 2, '.', '');


                $limite = $mesAnterior->copy()->addMonth()->startOfMonth()->addDays(15);

                // Resto igual
                $hoy = now();
                $faltanFormularios = !in_array('IMPUESTO AL VALOR AGREGADO', $nombresExistentes) ||
                                    !in_array('IMPUESTO A LAS TRANSACCIONES', $nombresExistentes);

                $mostrarFormulario = $hoy->lte($limite) && $faltanFormularios;
            @endphp

            {{-- FORMULARIOS --}}
            <div class="tab-pane fade" id="tab-content-6" role="tabpanel" aria-labelledby="tab-6">
                @if ($mostrarFormulario)
                    <form method="POST" action="{{ route('registro-impuestos.doble') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="card mb-4" style="background-color:#fdfdfd">
                            <div class="card-header bg-secondary text-white">
                                <div class="d-flex justify-content-between align-items-center w-100" style="margin-bottom: -7px; margin-top: -7px;">
                                    <strong>REGISTRO DE MONTOS FORMULARIOS DE IMPUESTOS - PERIODO: {{ strtoupper(Carbon::parse($periodo)->translatedFormat('F Y')) }}</strong>
                                    <button type="submit" class="btn btn-guardarformulario">GUARDAR MONTOS</button>
                                </div>
                            </div>

                            <div class="card-body row" style="margin-bottom: -30px;">
                                {{-- IVA --}}
                                <div class="col-md-6 mb-4">
                                    <label class="fw-bold">IMPUESTO AL VALOR AGREGADO</label>
                                    <input type="hidden" name="nombre_iva" value="IMPUESTO AL VALOR AGREGADO">

                                    <div class="mb-2" hidden>
                                        <label>Periodo</label>
                                        <input type="text" class="form-control" name="periodo_iva" value="{{ $periodo }}" readonly>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="mb-2 col-lg-12">
                                            <label>Monto IVA</label>
                                            <input type="text" class="form-control" name="monto_iva" value="{{ $iva }}" readonly>
                                        </div>
                                    </div>
                                </div>

                                {{-- IT --}}
                                <div class="col-md-6 mb-4">
                                    <label class="fw-bold">IMPUESTO A LAS TRANSACCIONES</label>
                                    <input type="hidden" name="nombre_it" value="IMPUESTO A LAS TRANSACCIONES">

                                    <div class="mb-2" hidden>
                                        <label>Periodo</label>
                                        <input type="text" class="form-control" name="periodo_it" value="{{ $periodo }}" readonly>
                                    </div>
                                    <div class="row">
                                        <div class="mb-2 col-lg-12">
                                            <label>Monto IT</label>
                                            <input type="text" class="form-control" name="monto_it" value="{{ $it }}" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                @else
                    <div class="alert alert-secondary">
                        @if ($hoy->gt($limite))
                            <strong>¡Atención!</strong> El plazo para subir los formularios del mes {{ Carbon::parse($periodo)->translatedFormat('F') }} ha vencido (solo hasta el 16 de {{ now()->translatedFormat('F') }}).
                        @else
                            <strong>¡Ya registrados!</strong> Ambos montos para el periodo {{ $periodo }} ya han sido registrados.
                        @endif
                    </div>
                @endif

                {{-- LISTADO DE REGISTROS --}}
                <h5 class="mb-3" style="font-weight: 900">LISTA DE FORMULARIOS</h5>
                <table class="table table-bordered table-sm">
                    <thead class="bg-light">
                        <tr>
                            <th>Tipo Formulario</th>
                            <th>Periodo</th>
                            <th>Usuario_Reg_GL</th>
                            <th>Fecha_Reg_GL</th>
                            <th style="text-align: right;">Monto_GL</th>
                            <th>Usuario_Reg_CE</th>
                            <th>Fecha_Reg_CE</th>
                            <th style="text-align: right;">Monto_CE</th>
                            <th>Form_CE</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($registrosImpuestos as $registro)
                        <tr>
                            <td>{{ $registro->nombre }}</td>
                            <td>{{ strtoupper(Carbon::parse($registro->periodo)->translatedFormat('F Y')) }}</td>
                            <td title="{{ $registro->usuarioregistronombre }}" class="truncar" style="background-color: #f9ffef">{{ $registro->usuarioregistronombre }}</td>
                            <td style="background-color: #f9ffef">{{ $registro->created_at->format('Y-m-d') }}</td>
                            <td style="text-align: right; background-color: #f9ffef">
                                @if ($registro->monto != $registro->montocontador && $registro->montocontador)
                                    <i class="fas fa-exclamation-circle text-warning alerta-pulso me-1" title="LOS MONTOS PARA ESTE MES NO COINCIDEN"></i>
                                @endif
                                {{ number_format($registro->monto, 2) }}
                            </td>
                            <td title="{{ $registro->registrocontadornombre }}" class="truncar" style="background-color: #fff8ef">
                                @if ($registro->registrocontadornombre)
                                    {{ $registro->registrocontadornombre }}
                                @else
                                    <span class="badge badge-danger">PENDIENTE</span>
                                @endif
                            </td>
                            <td style="background-color: #fff8ef">
                                @if ($registro->fecharegistrocontador)
                                    {{ $registro->fecharegistrocontador }}
                                @else
                                    <span class="badge badge-danger">PENDIENTE</span>
                                @endif
                            </td>
                            <td style="text-align: right; background-color: #fff8ef">
                                @if ($registro->montocontador)
                                    @if ($registro->monto != $registro->montocontador)
                                        <i class="fas fa-exclamation-circle text-warning alerta-pulso me-1" title="LOS MONTOS PARA ESTE MES NO COINCIDEN"></i>
                                    @endif
                                    {{ number_format($registro->montocontador, 2) }}
                                @else
                                    <span class="badge badge-danger">PENDIENTE</span>
                                @endif
                            </td>
                            <td style="background-color: #fff8ef">
                                @if ($registro->archivo)
                                    <a class="btn btn-verregistros btn-sm" data-toggle="modal" data-target="#modalArchivo{{ $registro->id }}"><i class="fas fa-eye"></i></a>

                                    <div class="modal fade" id="modalArchivo{{ $registro->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" style="font-weight: 900">FORMULARIO</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <iframe src="{{ asset('formulariosimpuestos/' . $registro->archivo) }}" width="100%" height="700px" style="border: none;"></iframe>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <span class="badge badge-danger">PENDIENTE</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            {{-- COMPRAS SCZ --}}
            <div class="tab-pane fade" id="tab-content-2" role="tabpanel" aria-labelledby="tab-2">
                <div class="table-responsive">
                    <form action="{{ route('compras.actualizar.estado') }}" method="POST">
                        @csrf
                        <table class="table table-bordered">
                            <thead style="background-color: #f3f3f3">
                                <tr>
                                    @can('admin.facturasegreso.cambiarvalidofacturas')
                                    <th><input type="checkbox" id="select_all">Sel.</th>
                                    @endcan
                                    <th>Nro.</th>
                                    <th>Especif.</th>
                                    <th>Nit_Proveedor</th>
                                    <th>Razon_Social_Proveedor</th>
                                    <th>Codigo_de_Autorización</th>
                                    <th>Nro.Factura</th>
                                    <th>Nro.DUI/DIM</th>
                                    <th>Fecha de Factura_DUI/DIM</th>
                                    <th>Importe Total Compra</th>
                                    <th>Importe ICE</th>
                                    <th>Importe IEHD</th>
                                    <th>Importe IPJ</th>
                                    <th>Tasas</th>
                                    <th>Otro no Sujeto a Credito_Fiscal</th>
                                    <th>Importes Externos</th>
                                    <th>Importe_Compras Gravadas a Tasa_Cero</th>
                                    <th>Subtotal</th>
                                    <th>Descuento</th>
                                    {{-- <th>Bonif._Reb. Sujetas a IVA</th> --}}
                                    <th>Importe GIFT CARD</th>
                                    <th>Importe Base CF</th>
                                    <th>Credito Fiscal</th>
                                    <th>Tipo Compra</th>
                                    <th>Estado</th>
                                    <th>Usuario_Entrega</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($comprasscz as $compra)
                                    <tr>
                                        @can('admin.facturasegreso.cambiarvalidofacturas')
                                        <td><input type="checkbox" name="seleccionados[]" value="{{ $compra->id }}"></td>
                                        @endcan
                                        <td>{{ $compra->id }}</td>
                                        <td>{{ $compra->especificacion }}</td>
                                        <td>{{ $compra->nitci }}</td>
                                        <td>{{ $compra->razonsocial }}</td>
                                        <td>{{ $compra->codigoautorizacion }}</td>
                                        <td>{{ $compra->nrofactura }}</td>
                                        <td>{{ $compra->nroduidim }}</td>
                                        <td>{{ $compra->fechafacturaduidim }}</td>
                                        <td>{{ $compra->total }}</td>
                                        <td>{{ $compra->ice }}</td>
                                        <td>{{ $compra->iehd }}</td>
                                        <td>{{ $compra->ipj }}</td>
                                        <td>{{ $compra->tasas }}</td>
                                        <td>{{ $compra->otronosujcredfiscaloiva }}</td>
                                        <td>{{ $compra->importeyexporteexterno }}</td>
                                        <td>{{ $compra->tasacero ?? 0 }}</td>
                                        <td>{{ $compra->subtotal }}</td>
                                        <td>{{ $compra->descuento }}</td>
                                        {{-- <td>{{ $compra->importenosujetocfdf }}</td> --}}
                                        <td>{{ $compra->giftcard }}</td>
                                        <td>{{ $compra->importebasecfdf }}</td>
                                        <td>{{ $compra->creditodebitofiscal }}</td>
                                        <td>{{ $compra->tipo }}</td>
                                        <td>{{ $compra->estado }}</td>
                                        <td>{{ $compra->usuarioentrega ?? 0 }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @can('admin.facturasegreso.cambiarvalidofacturas')
                        <div class="form-group mt-3 d-flex align-items-center gap-3">
                            <input type="text" name="motivo" id="motivo" placeholder="MOTIVO PASAR A PENDIENTE/RECHAZADO..." class="form-control form-control-sm w-25" required>

                            <button type="submit" name="accion" value="pendiente" class="btn btn-outline-warning btn-sm ms-3">
                                PASAR A PENDIENTE
                            </button>

                            <button type="submit" name="accion" value="rechazado" class="btn btn-outline-danger btn-sm ms-2">
                                PASAR A RECHAZADO
                            </button>
                        </div>
                        @endcan
                    </form>
                </div>
            </div>

            {{-- VENTAS SCZ --}}
            <div class="tab-pane fade" id="tab-content-3" role="tabpanel" aria-labelledby="tab-3">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead style="background-color: #f3f3f3">
                            <tr>
                                <th>Nro.</th>
                                <th>Especif.</th>
                                <th>Fecha_de_Factura</th>
                                <th>Nro.Factura</th>
                                <th>Codigo_de_Autorización</th>
                                <th>Nit/CI_Cliente</th>
                                <th>Complemento</th>
                                <th>Nombre_o_Razon_Social</th>
                                <th>Importe Total Venta</th>
                                <th>Importe ICE</th>
                                <th>Importe IEHD</th>
                                <th>Importe IPJ</th>
                                <th>Tasas</th>
                                <th>Otro no Sujeto_al_IVA</th>
                                <th>Importes Externos</th>
                                <th>Importe_Compras Gravadas a Tasa_Cero</th>
                                <th>Subtotal</th>
                                <th>Descuento</th>
                                {{-- <th>Bonif._Reb. Sujetas a IVA</th> --}}
                                <th>Importe GIFT CARD</th>
                                <th>Importe Base DF</th>
                                <th>Débito Fiscal</th>
                                <th>Estado</th>
                                <th>Codigo Control</th>
                                <th>Tipo Compra</th>
                                {{-- <th>Importe Sujeto_DF</th> --}}
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($ventasscz as $venta)
                                <tr>
                                    <td>{{ $venta->id }}</td>
                                    <td>{{ $venta->especificacion }}</td>
                                    <td>{{ $venta->fechafacturaduidim }}</td>
                                    <td>{{ $venta->nrofactura }}</td>
                                    <td>{{ $venta->codigoautorizacion }}</td>

                                    @php
                                        $updated = $venta->updated_at;
                                        $esEditable = in_array($venta->id, $codigosPermitidos) &&
                                                    (is_null($updated) || !\Carbon\Carbon::parse($updated)->isToday());
                                    @endphp
                                    @if($esEditable)
                                        <form method="POST" action="{{ route('facturasegreso.actualizarfacturaimpuestos', $venta->id) }}" class="d-flex align-items-center">
                                            @csrf
                                            @method('PUT')
                                            <td>
                                                <input type="text" name="nitci" value="{{ $venta->nitci }}" class="form-control form-control-sm w-100 me-1">
                                            </td>
                                            <td>
                                                <input type="text" name="complemento" value="{{ $venta->complemento }}" class="form-control form-control-sm w-100 me-1">
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <input type="text" name="razonsocial" value="{{ $venta->razonsocial }}" class="form-control form-control-sm w-200 me-1">
                                                    <button type="submit" class="btn btn-success btn-sm" title="Actualizar Datos">
                                                        <i class="fas fa-save"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </form>
                                    @else
                                        <td>{{ $venta->nitci }}</td>
                                        <td>{{ $venta->complemento }}</td>
                                        <td>{{ $venta->razonsocial }}</td>
                                    @endif

                                    <td>{{ $venta->total }}</td>
                                    <td>{{ $venta->ice }}</td>
                                    <td>{{ $venta->iehd }}</td>
                                    <td>{{ $venta->ipj }}</td>
                                    <td>{{ $venta->tasas }}</td>
                                    <td>{{ $venta->otronosujcredfiscaloiva }}</td>
                                    <td>{{ $venta->importeyexporteexterno }}</td>
                                    <td>{{ $venta->tasacero ?? 0 }}</td>
                                    <td>{{ $venta->subtotal }}</td>
                                    <td>{{ $venta->descuento }}</td>
                                    {{-- <td>{{ $venta->importenosujetocfdf }}</td> --}}
                                    <td>{{ $venta->giftcard }}</td>
                                    <td>{{ $venta->importebasecfdf }}</td>
                                    <td>{{ $venta->creditodebitofiscal }}</td>
                                    <td>{{ $venta->estado }}</td>
                                    <td>{{ $venta->codigocontrol }}</td>
                                    <td>{{ $venta->tipo }}</td>
                                    {{-- <td>{{ $venta->importenosujetocfdf }}</td> --}}
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            
            {{-- COMPRAS CBBA --}}
            <div class="tab-pane fade" id="tab-content-4" role="tabpanel" aria-labelledby="tab-4">
                <div class="table-responsive">
                    <form action="{{ route('compras.actualizar.estado') }}" method="POST">
                        @csrf
                        <table class="table table-bordered">
                            <thead style="background-color: #f3f3f3">
                                <tr>
                                    @can('admin.facturasegreso.cambiarvalidofacturas')
                                    <th><input type="checkbox" id="select_all">Sel.</th>
                                    @endcan
                                    <th>Nro.</th>
                                    <th>Especif.</th>
                                    <th>Nit_Proveedor</th>
                                    <th>Razon_Social_Proveedor</th>
                                    <th>Codigo_de_Autorización</th>
                                    <th>Nro.Factura</th>
                                    <th>Nro.DUI/DIM</th>
                                    <th>Fecha de Factura_DUI/DIM</th>
                                    <th>Importe Total Compra</th>
                                    <th>Importe ICE</th>
                                    <th>Importe IEHD</th>
                                    <th>Importe IPJ</th>
                                    <th>Tasas</th>
                                    <th>Otro no Sujeto a Credito_Fiscal</th>
                                    <th>Importes Externos</th>
                                    <th>Importe_Compras Gravadas a Tasa_Cero</th>
                                    <th>Subtotal</th>
                                    <th>Descuento</th>
                                    {{-- <th>Bonif._Reb. Sujetas a IVA</th> --}}
                                    <th>Importe GIFT CARD</th>
                                    <th>Importe Base CF</th>
                                    <th>Credito Fiscal</th>
                                    <th>Tipo Compra</th>
                                    <th>Estado</th>
                                    <th>Usuario_Entrega</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($comprascbba as $compra)
                                    <tr>
                                        @can('admin.facturasegreso.cambiarvalidofacturas')
                                        <td><input type="checkbox" name="seleccionados[]" value="{{ $compra->id }}"></td>
                                        @endcan
                                        <td>{{ $compra->id }}</td>
                                        <td>{{ $compra->especificacion }}</td>
                                        <td>{{ $compra->nitci }}</td>
                                        <td>{{ $compra->razonsocial }}</td>
                                        <td>{{ $compra->codigoautorizacion }}</td>
                                        <td>{{ $compra->nrofactura }}</td>
                                        <td>{{ $compra->nroduidim }}</td>
                                        <td>{{ $compra->fechafacturaduidim }}</td>
                                        <td>{{ $compra->total }}</td>
                                        <td>{{ $compra->ice }}</td>
                                        <td>{{ $compra->iehd }}</td>
                                        <td>{{ $compra->ipj }}</td>
                                        <td>{{ $compra->tasas }}</td>
                                        <td>{{ $compra->otronosujcredfiscaloiva }}</td>
                                        <td>{{ $compra->importeyexporteexterno }}</td>
                                        <td>{{ $compra->tasacero ?? 0 }}</td>
                                        <td>{{ $compra->subtotal }}</td>
                                        <td>{{ $compra->descuento }}</td>
                                        {{-- <td>{{ $compra->importenosujetocfdf }}</td> --}}
                                        <td>{{ $compra->giftcard }}</td>
                                        <td>{{ $compra->importebasecfdf }}</td>
                                        <td>{{ $compra->creditodebitofiscal }}</td>
                                        <td>{{ $compra->tipo }}</td>
                                        <td>{{ $compra->estado }}</td>
                                        <td>{{ $compra->usuarioentrega ?? 0 }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @can('admin.facturasegreso.cambiarvalidofacturas')
                        <div class="form-group mt-3 d-flex align-items-center gap-3">
                            <input type="text" name="motivo" id="motivo" placeholder="MOTIVO PASAR A PENDIENTE/RECHAZADO..." class="form-control form-control-sm w-25" required>

                            <button type="submit" name="accion" value="pendiente" class="btn btn-outline-warning btn-sm ms-3">
                                PASAR A PENDIENTE
                            </button>

                            <button type="submit" name="accion" value="rechazado" class="btn btn-outline-danger btn-sm ms-2">
                                PASAR A RECHAZADO
                            </button>
                        </div>
                        @endcan
                    </form>
                </div>
            </div>

            {{-- VENTAS CBBA --}}
            <div class="tab-pane fade" id="tab-content-5" role="tabpanel" aria-labelledby="tab-5">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead style="background-color: #f3f3f3">
                            <tr>
                                <th>Nro.</th>
                                <th>Especif.</th>
                                <th>Fecha_de_Factura</th>
                                <th>Nro.Factura</th>
                                <th>Codigo_de_Autorización</th>
                                <th>Nit/CI_Cliente</th>
                                <th>Complemento</th>
                                <th>Nombre_o_Razon_Social</th>
                                <th>Importe Total Venta</th>
                                <th>Importe ICE</th>
                                <th>Importe IEHD</th>
                                <th>Importe IPJ</th>
                                <th>Tasas</th>
                                <th>Otro no Sujeto_al_IVA</th>
                                <th>Importes Externos</th>
                                <th>Importe_Compras Gravadas a Tasa_Cero</th>
                                <th>Subtotal</th>
                                <th>Descuento</th>
                                {{-- <th>Bonif._Reb. Sujetas a IVA</th> --}}
                                <th>Importe GIFT CARD</th>
                                <th>Importe Base DF</th>
                                <th>Débito Fiscal</th>
                                <th>Estado</th>
                                <th>Codigo Control</th>
                                <th>Tipo Compra</th>
                                {{-- <th>Importe Sujeto_DF</th> --}}
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($ventascbba as $venta)
                                <tr>
                                    <td>{{ $venta->id }}</td>
                                    <td>{{ $venta->especificacion }}</td>
                                    <td>{{ $venta->fechafacturaduidim }}</td>
                                    <td>{{ $venta->nrofactura }}</td>
                                    <td>{{ $venta->codigoautorizacion }}</td>
                                    
                                    @php
                                        $updated = $venta->updated_at;
                                        $esEditable = in_array($venta->id, $codigosPermitidos) &&
                                                    (is_null($updated) || !\Carbon\Carbon::parse($updated)->isToday());
                                    @endphp
                                    @if($esEditable)
                                        <form method="POST" action="{{ route('facturasegreso.actualizarfacturaimpuestos', $venta->id) }}" class="d-flex align-items-center">
                                            @csrf
                                            @method('PUT')
                                            <td>
                                                <input type="text" name="nitci" value="{{ $venta->nitci }}" class="form-control form-control-sm w-100 me-1">
                                            </td>
                                            <td>
                                                <input type="text" name="complemento" value="{{ $venta->complemento }}" class="form-control form-control-sm w-100 me-1">
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <input type="text" name="razonsocial" value="{{ $venta->razonsocial }}" class="form-control form-control-sm w-200 me-1">
                                                    <button type="submit" class="btn btn-success btn-sm" title="Actualizar Datos">
                                                        <i class="fas fa-save"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </form>
                                    @else
                                        <td>{{ $venta->nitci }}</td>
                                        <td>{{ $venta->complemento }}</td>
                                        <td>{{ $venta->razonsocial }}</td>
                                    @endif

                                    <td>{{ $venta->total }}</td>
                                    <td>{{ $venta->ice }}</td>
                                    <td>{{ $venta->iehd }}</td>
                                    <td>{{ $venta->ipj }}</td>
                                    <td>{{ $venta->tasas ?? 0 }}</td>
                                    <td>{{ $venta->otronosujcredfiscaloiva }}</td>
                                    <td>{{ $venta->importeyexporteexterno }}</td>
                                    <td>{{ $venta->tasacero }}</td>
                                    <td>{{ $venta->subtotal }}</td>
                                    <td>{{ $venta->descuento }}</td>
                                    {{-- <td>{{ $venta->importenosujetocfdf }}</td> --}}
                                    <td>{{ $venta->giftcard }}</td>
                                    <td>{{ $venta->importebasecfdf }}</td>
                                    <td>{{ $venta->creditodebitofiscal }}</td>
                                    <td>{{ $venta->estado }}</td>
                                    <td>{{ $venta->codigocontrol }}</td>
                                    <td>{{ $venta->tipo }}</td>
                                    {{-- <td>{{ $venta->importenosujetocfdf }}</td> --}}
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <script>
                document.getElementById('select_all').addEventListener('change', function () {
                    document.querySelectorAll('input[name="seleccionados[]"]').forEach(cb => cb.checked = this.checked);
                });
            </script>

            {{-- OTRAS FACTURAS --}}
            <div class="tab-pane fade" id="tab-content-7" role="tabpanel" aria-labelledby="tab-7">
                <div class="table-responsive">
                    {{-- <form action="{{ route('compras.actualizar.estado') }}" method="POST">
                        @csrf --}}

                        {{-- FACTURAS ANULADAS, PENDIENTES Y RECHAZADAS DE COMPRAS SCZ --}}
                        @if($otrascomprasscz->isNotEmpty())
                            <p style="margin-bottom: -2px;"><strong>FACTURAS ANULADAS, PENDIENTES Y RECHAZADAS DE COMPRAS SCZ</strong></p>
                            <table class="table table-bordered">
                                <thead style="background-color: #f3f3f3">
                                    <tr>
                                        {{-- <th><input type="checkbox" id="select_all">Sel.</th> --}}
                                        <th>Estado</th>
                                        <th>Nro.</th>
                                        <th>Especif.</th>
                                        <th>Nit_Proveedor</th>
                                        <th>Razon_Social_Proveedor</th>
                                        <th>Codigo_de_Autorización</th>
                                        <th>Nro.Factura</th>
                                        <th>Nro.DUI/DIM</th>
                                        <th>Fecha de Factura_DUI/DIM</th>
                                        <th>Importe Total Compra</th>
                                        <th>Importe ICE</th>
                                        <th>Importe IEHD</th>
                                        <th>Importe IPJ</th>
                                        <th>Tasas</th>
                                        <th>Otro no Sujeto a Credito_Fiscal</th>
                                        <th>Importes Externos</th>
                                        <th>Importe_Compras Gravadas a Tasa_Cero</th>
                                        <th>Subtotal</th>
                                        <th>Descuento</th>
                                        {{-- <th>Bonif._Reb. Sujetas a IVA</th> --}}
                                        <th>Importe GIFT CARD</th>
                                        <th>Importe Base CF</th>
                                        <th>Credito Fiscal</th>
                                        <th>Tipo Compra</th>
                                        <th>Usuario_Entrega</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($otrascomprasscz as $compra)
                                        <tr>
                                            {{-- <td><input type="checkbox" name="seleccionados[]" value="{{ $compra->id }}"></td> --}}
                                            <td>{{ $compra->estado }}</td>
                                            <td>{{ $compra->id }}</td>
                                            <td>{{ $compra->especificacion }}</td>
                                            <td>{{ $compra->nitci }}</td>
                                            <td>{{ $compra->razonsocial }}</td>
                                            <td>{{ $compra->codigoautorizacion }}</td>
                                            <td>{{ $compra->nrofactura }}</td>
                                            <td>{{ $compra->nroduidim }}</td>
                                            <td>{{ $compra->fechafacturaduidim }}</td>
                                            <td>{{ $compra->total }}</td>
                                            <td>{{ $compra->ice }}</td>
                                            <td>{{ $compra->iehd }}</td>
                                            <td>{{ $compra->ipj }}</td>
                                            <td>{{ $compra->tasas }}</td>
                                            <td>{{ $compra->otronosujcredfiscaloiva }}</td>
                                            <td>{{ $compra->importeyexporteexterno }}</td>
                                            <td>{{ $compra->tasacero }}</td>
                                            <td>{{ $compra->subtotal }}</td>
                                            <td>{{ $compra->descuento }}</td>
                                            {{-- <td>{{ $compra->importenosujetocfdf }}</td> --}}
                                            <td>{{ $compra->giftcard }}</td>
                                            <td>{{ $compra->importebasecfdf }}</td>
                                            <td>{{ $compra->creditodebitofiscal }}</td>
                                            <td>{{ $compra->tipo }}</td>
                                            <td>{{ $compra->usuarioentrega ?? 0 }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endif

                        {{-- FACTURAS ANULADAS, PENDIENTE, RECHADO VENTAS SCZ --}}
                        @if($otrasventasscz->isNotEmpty())
                            <p style="margin-bottom: -2px;"><strong>FACTURAS ANULADAS, PENDIENTES Y RECHAZADAS DE VENTAS SCZ</strong></p>
                            <table class="table table-bordered">
                                <thead style="background-color: #f3f3f3">
                                    <tr>
                                        <th>Nro.</th>
                                        <th>Especif.</th>
                                        <th>Fecha_de_Factura</th>
                                        <th>Nro.Factura</th>
                                        <th>Codigo_de_Autorización</th>
                                        <th>Nit/CI_Cliente</th>
                                        <th>Complemento</th>
                                        <th>Nombre_o_Razon_Social</th>
                                        <th>Importe Total Venta</th>
                                        <th>Importe ICE</th>
                                        <th>Importe IEHD</th>
                                        <th>Importe IPJ</th>
                                        <th>Tasas</th>
                                        <th>Otro no Sujeto_al_IVA</th>
                                        <th>Importes Externos</th>
                                        <th>Importe_Compras Gravadas a Tasa_Cero</th>
                                        <th>Subtotal</th>
                                        <th>Descuento</th>
                                        {{-- <th>Bonif._Reb. Sujetas a IVA</th> --}}
                                        <th>Importe GIFT CARD</th>
                                        <th>Importe Base DF</th>
                                        <th>Débito Fiscal</th>
                                        <th>Estado</th>
                                        <th>Codigo Control</th>
                                        <th>Tipo Compra</th>
                                        {{-- <th>Importe Sujeto_DF</th> --}}
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($otrasventasscz as $venta)
                                        <tr>
                                            <td>{{ $venta->id }}</td>
                                            <td>{{ $venta->especificacion }}</td>
                                            <td>{{ $venta->fechafacturaduidim }}</td>
                                            <td>{{ $venta->nrofactura }}</td>
                                            <td>{{ $venta->codigoautorizacion }}</td>
                                            <td>{{ $venta->nitci }}</td>
                                            <td>{{ $venta->complemento }}</td>
                                            <td>{{ $venta->razonsocial }}</td>
                                            <td>{{ $venta->total }}</td>
                                            <td>{{ $venta->ice }}</td>
                                            <td>{{ $venta->iehd }}</td>
                                            <td>{{ $venta->ipj }}</td>
                                            <td>{{ $venta->tasas }}</td>
                                            <td>{{ $venta->otronosujcredfiscaloiva }}</td>
                                            <td>{{ $venta->importeyexporteexterno }}</td>
                                            <td>{{ $venta->tasacero }}</td>
                                            <td>{{ $venta->subtotal }}</td>
                                            <td>{{ $venta->descuento }}</td>
                                            {{-- <td>{{ $venta->importenosujetocfdf }}</td> --}}
                                            <td>{{ $venta->giftcard }}</td>
                                            <td>{{ $venta->importebasecfdf }}</td>
                                            <td>{{ $venta->creditodebitofiscal }}</td>
                                            <td>{{ $venta->estado }}</td>
                                            <td>{{ $venta->codigocontrol }}</td>
                                            <td>{{ $venta->tipo }}</td>
                                            {{-- <td>{{ $venta->importenosujetocfdf }}</td> --}}
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endif

                        {{-- FACTURAS ANULADAS, PENDIENTE, RECHADO COMPRAS CBBA --}}
                        @if($otrascomprascbba->isNotEmpty())
                            <p style="margin-bottom: -2px;"><strong>FACTURAS ANULADAS, PENDIENTES Y RECHAZADAS DE COMPRAS CBBA</strong></p>
                            <table class="table table-bordered">
                                <thead style="background-color: #f3f3f3">
                                    <tr>
                                        {{-- <th><input type="checkbox" id="select_all">Sel.</th> --}}
                                        <th>Estado</th>
                                        <th>Nro.</th>
                                        <th>Especif.</th>
                                        <th>Nit_Proveedor</th>
                                        <th>Razon_Social_Proveedor</th>
                                        <th>Codigo_de_Autorización</th>
                                        <th>Nro.Factura</th>
                                        <th>Nro.DUI/DIM</th>
                                        <th>Fecha de Factura_DUI/DIM</th>
                                        <th>Importe Total Compra</th>
                                        <th>Importe ICE</th>
                                        <th>Importe IEHD</th>
                                        <th>Importe IPJ</th>
                                        <th>Tasas</th>
                                        <th>Otro no Sujeto a Credito_Fiscal</th>
                                        <th>Importes Externos</th>
                                        <th>Importe_Compras Gravadas a Tasa_Cero</th>
                                        <th>Subtotal</th>
                                        <th>Descuento</th>
                                        {{-- <th>Bonif._Reb. Sujetas a IVA</th> --}}
                                        <th>Importe GIFT CARD</th>
                                        <th>Importe Base CF</th>
                                        <th>Credito Fiscal</th>
                                        <th>Tipo Compra</th>
                                        <th>Usuario_Entrega</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($otrascomprascbba as $compra)
                                        <tr>
                                            {{-- <td><input type="checkbox" name="seleccionados[]" value="{{ $compra->id }}"></td> --}}
                                            <td>{{ $compra->estado }}</td>
                                            <td>{{ $compra->id }}</td>
                                            <td>{{ $compra->especificacion }}</td>
                                            <td>{{ $compra->nitci }}</td>
                                            <td>{{ $compra->razonsocial }}</td>
                                            <td>{{ $compra->codigoautorizacion }}</td>
                                            <td>{{ $compra->nrofactura }}</td>
                                            <td>{{ $compra->nroduidim }}</td>
                                            <td>{{ $compra->fechafacturaduidim }}</td>
                                            <td>{{ $compra->total }}</td>
                                            <td>{{ $compra->ice }}</td>
                                            <td>{{ $compra->iehd }}</td>
                                            <td>{{ $compra->ipj }}</td>
                                            <td>{{ $compra->tasas }}</td>
                                            <td>{{ $compra->otronosujcredfiscaloiva }}</td>
                                            <td>{{ $compra->importeyexporteexterno }}</td>
                                            <td>{{ $compra->tasacero }}</td>
                                            <td>{{ $compra->subtotal }}</td>
                                            <td>{{ $compra->descuento }}</td>
                                            {{-- <td>{{ $compra->importenosujetocfdf }}</td> --}}
                                            <td>{{ $compra->giftcard }}</td>
                                            <td>{{ $compra->importebasecfdf }}</td>
                                            <td>{{ $compra->creditodebitofiscal }}</td>
                                            <td>{{ $compra->tipo }}</td>
                                            <td>{{ $compra->usuarioentrega ?? 0 }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endif

                        {{-- FACTURAS ANULADAS, PENDIENTE, RECHADO VENTAS CBBA --}}
                        @if($otrasventascbba->isNotEmpty())
                            <p style="margin-bottom: -2px;"><strong>FACTURAS ANULADAS, PENDIENTES Y RECHAZADAS DE VENTAS CBBA</strong></p>
                            <table class="table table-bordered">
                                <thead style="background-color: #f3f3f3">
                                    <tr>
                                        <th>Nro.</th>
                                        <th>Especif.</th>
                                        <th>Fecha_de_Factura</th>
                                        <th>Nro.Factura</th>
                                        <th>Codigo_de_Autorización</th>
                                        <th>Nit/CI_Cliente</th>
                                        <th>Complemento</th>
                                        <th>Nombre_o_Razon_Social</th>
                                        <th>Importe Total Venta</th>
                                        <th>Importe ICE</th>
                                        <th>Importe IEHD</th>
                                        <th>Importe IPJ</th>
                                        <th>Tasas</th>
                                        <th>Otro no Sujeto_al_IVA</th>
                                        <th>Importes Externos</th>
                                        <th>Importe_Compras Gravadas a Tasa_Cero</th>
                                        <th>Subtotal</th>
                                        <th>Descuento</th>
                                        {{-- <th>Bonif._Reb. Sujetas a IVA</th> --}}
                                        <th>Importe GIFT CARD</th>
                                        <th>Importe Base DF</th>
                                        <th>Débito Fiscal</th>
                                        <th>Estado</th>
                                        <th>Codigo Control</th>
                                        <th>Tipo Compra</th>
                                        {{-- <th>Importe Sujeto_DF</th> --}}
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($otrasventascbba as $venta)
                                        <tr>
                                            <td>{{ $venta->id }}</td>
                                            <td>{{ $venta->especificacion }}</td>
                                            <td>{{ $venta->fechafacturaduidim }}</td>
                                            <td>{{ $venta->nrofactura }}</td>
                                            <td>{{ $venta->codigoautorizacion }}</td>
                                            <td>{{ $venta->nitci }}</td>
                                            <td>{{ $venta->complemento }}</td>
                                            <td>{{ $venta->razonsocial }}</td>
                                            <td>{{ $venta->total }}</td>
                                            <td>{{ $venta->ice }}</td>
                                            <td>{{ $venta->iehd }}</td>
                                            <td>{{ $venta->ipj }}</td>
                                            <td>{{ $venta->tasas }}</td>
                                            <td>{{ $venta->otronosujcredfiscaloiva }}</td>
                                            <td>{{ $venta->importeyexporteexterno }}</td>
                                            <td>{{ $venta->tasacero }}</td>
                                            <td>{{ $venta->subtotal }}</td>
                                            <td>{{ $venta->descuento }}</td>
                                            {{-- <td>{{ $venta->importenosujetocfdf }}</td> --}}
                                            <td>{{ $venta->giftcard }}</td>
                                            <td>{{ $venta->importebasecfdf }}</td>
                                            <td>{{ $venta->creditodebitofiscal }}</td>
                                            <td>{{ $venta->estado }}</td>
                                            <td>{{ $venta->codigocontrol }}</td>
                                            <td>{{ $venta->tipo }}</td>
                                            {{-- <td>{{ $venta->importenosujetocfdf }}</td> --}}
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endif

                        {{-- <div class="form-group mt-3 d-flex align-items-center gap-3">
                            <input type="text" name="motivo" id="motivo" placeholder="MOTIVO PASAR A PENDIENTE/RECHAZADO..." class="form-control form-control-sm w-25" required>

                            <button type="submit" name="accion" value="pendiente" class="btn btn-outline-warning btn-sm ms-3">
                                PASAR A PENDIENTE
                            </button>

                            <button type="submit" name="accion" value="rechazado" class="btn btn-outline-danger btn-sm ms-2">
                                PASAR A RECHAZADO
                            </button>
                        </div>
                    </form> --}}
                </div>
            </div>
        </div>
    </div>
</div>
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
@endsection