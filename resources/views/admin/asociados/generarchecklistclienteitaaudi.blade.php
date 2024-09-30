@extends('adminlte::page')

@section('content_header')
<a class="btn btn-sm float-right btn-regresar" href="{{ route('admin.asociados.verclienteita', $cliente) }}">REGRESAR</a>
<a class="btn btn-sm float-right btn-subirrequisitos" href="{{ route('admin.asociados.subirdocrequisitosaudi', $cliente) }}">SUBIR REQUISITOS</a>
<h5>REQUISITOS DE AUDITORIA MEDICA:</h5>
<h3>{{$cliente->nombrecompleto}}</h3>
@stop

@section('content') 
<form id="pdfForm" action="{{ route('admin.asociados.descargarchecklistclienteitaaudi', $cliente) }}" method="POST">
    @csrf
    <input type="hidden" name="documentosSeleccionados" id="documentosSeleccionadosInput">
    <input type="hidden" name="documentosSeleccionados2" id="documentosSeleccionados2Input">

    <div class="card col-lg-12"> 
        <div class="card-body">
            <div class="row">
                @if ($tieneauditoriamedica)
                <div class="col-md-12">
                    <h4 style="font-weight: 600; color: #94c93b; margin-bottom: 20px;">DOCUMENTACIÓN A PRESENTAR</h4>
                    <div class="form-group">
                        <input type="checkbox" name="ciasegurado" value="ciasegurado" id="ciasegurado" checked>
                        <label for="ciasegurado">CARNET IDENTIDAD</label>
                    </div>
                    <div class="form-group">
                        <input type="checkbox" name="cnacasegurado" value="cnacasegurado" id="cnacasegurado" checked>
                        <label for="cnacasegurado" style="min-height: 20px;">CERTIFICADO NACIMIENTO ASEGURADO</label>
                    </div>
                </div>
                <div class="col-lg-12">
                    <h4 style="font-weight: 600; color: #94c93b; margin-right: 20px;">PÓLIZAS</h4>
                    <div style="display: flex; align-items: center; margin-bottom: 20px;">
                        <label for="numPolizas" style="margin-right: 10px;">NÚMERO DE PÓLIZAS:</label>
                        <select id="numPolizas" name="numPolizas" onchange="generarFormulario()">
                            <option value=""></option>
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                            <option value="5">5</option>
                            <option value="6">6</option>
                        </select>
                    </div>
                    <div id="formContainer"></div>
                </div>
                @endif
            </div>
            @if (!$tieneRequisitos)
            <button type="button" onclick="generatePDF()" class="btn-crear">GENERAR CHECK LIST</button>
            @endif
        </div>
    </div>
</form>

<script>
    function generarFormulario() {
        var cantidad = document.getElementById('numPolizas').value;
        var contenedor = document.getElementById('formContainer');
        contenedor.innerHTML = ''; // Limpiar contenedor

        for (var i = 1; i <= cantidad; i++) {
            var group = document.createElement('div');
            group.className = 'poliza-group';

            var bancoItem = document.createElement('div');
            bancoItem.className = 'form-item';
            var bancoLabel = document.createElement('label');
            bancoLabel.innerHTML = 'BANCO ' + i;
            var bancoSelect = document.createElement('select');
            bancoSelect.name = 'banco' + i;
            bancoSelect.innerHTML = `<option value=""></option>
                                     @foreach($bancos as $id => $nombrebanco)
                                         <option value="{{ $id }}">{{ $nombrebanco }}</option>
                                     @endforeach`;
            bancoItem.appendChild(bancoLabel);
            bancoItem.appendChild(bancoSelect);

            var polizaNumItem = document.createElement('div');
            polizaNumItem.className = 'form-item';
            var polizaNumLabel = document.createElement('label');
            polizaNumLabel.innerHTML = 'Nro. PÓLIZA GENERAL ' + i;
            var polizaNumInput = document.createElement('input');
            polizaNumInput.type = 'text';
            polizaNumInput.name = 'nropolizageneral' + i;
            polizaNumItem.appendChild(polizaNumLabel);
            polizaNumItem.appendChild(polizaNumInput);

            var polizaGenItem = document.createElement('div');
            polizaGenItem.className = 'form-item';
            var polizaGenLabel = document.createElement('label');
            var polizaGenInput = document.createElement('input');
            polizaGenInput.type = 'checkbox';
            polizaGenInput.name = 'polizageneral' + i;
            polizaGenLabel.innerHTML = 'PÓLIZA GENERAL';
            polizaGenInput.checked = true;
            polizaGenItem.appendChild(polizaGenLabel);
            polizaGenItem.appendChild(polizaGenInput);

            var saludItem = document.createElement('div');
            saludItem.className = 'form-item';
            var saludLabel = document.createElement('label');
            var saludInput = document.createElement('input');
            saludInput.type = 'checkbox';
            saludInput.name = 'declasalud' + i;
            saludLabel.innerHTML = 'DECLARACIÓN DE SALUD';
            saludInput.checked = true;
            saludItem.appendChild(saludLabel);
            saludItem.appendChild(saludInput);

            var polizaDesgraItem = document.createElement('div');
            polizaDesgraItem.className = 'form-item';
            var polizaDesgraLabel = document.createElement('label');
            polizaDesgraLabel.innerHTML = 'Nro. PÓLIZA DESGRAVAMEN ' + i;
            var polizaDesgraInput = document.createElement('input');
            polizaDesgraInput.type = 'text';
            polizaDesgraInput.name = 'nropolizadesgravamen' + i;
            polizaDesgraItem.appendChild(polizaDesgraLabel);
            polizaDesgraItem.appendChild(polizaDesgraInput);

            var polizaSegDesgraItem = document.createElement('div');
            polizaSegDesgraItem.className = 'form-item';
            var polizaSegDesgraLabel = document.createElement('label');
            var polizaSegDesgraInput = document.createElement('input');
            polizaSegDesgraInput.type = 'checkbox';
            polizaSegDesgraInput.name = 'polizasegurodesgravamen' + i;
            polizaSegDesgraLabel.innerHTML = 'PÓLIZA SEGURO DESGRAVAMEN';
            polizaSegDesgraInput.checked = true;
            polizaSegDesgraItem.appendChild(polizaSegDesgraLabel);
            polizaSegDesgraItem.appendChild(polizaSegDesgraInput);
            
            group.appendChild(bancoItem);
            group.appendChild(polizaNumItem);
            group.appendChild(polizaGenItem);
            group.appendChild(saludItem);
            group.appendChild(polizaDesgraItem);
            group.appendChild(polizaSegDesgraItem);
            contenedor.appendChild(group);
        }
    }

    function generatePDF() {
        var checkboxes = document.querySelectorAll('input[type="checkbox"]');
        var documentosSeleccionados = [];
        checkboxes.forEach(function(checkbox) {
            if (checkbox.checked) {
                documentosSeleccionados.push(checkbox.value);
            }
        });
        document.getElementById('documentosSeleccionadosInput').value = JSON.stringify(documentosSeleccionados);
        document.getElementById('pdfForm').submit();
    }
</script>
@stop


@section('css')
<style>
    /* Estilos para alinear horizontalmente los elementos */
    .poliza-group {
        display: flex;
        justify-content: center;
        gap: 20px; /* Separación entre grupos de elementos */
        margin-bottom: 20px;
        width: 100%;
    }

    /* Para centrar el título sobre cada input o checkbox */
    .form-item {
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .form-item label {
        margin-bottom: 5px;
        font-weight: bold;
        text-align: center;
    }

    .form-item select,
    .form-item input[type="text"],
    .form-item input[type="checkbox"] {
        width: 150px;
    }

    form {
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    button {
        margin-top: 20px;
    }
</style>
<style>
    .color-toggle {
        min-height: 20px;
        color: red;
        cursor: pointer;
    }
    .color-toggle.black {
        color: black;
    }
    h5 {
        color:#94c93b; 
        font-family: "Segoe UI";
        font-weight: 500;
        margin-bottom: 0%;
        }
    h3 {
        color:#94c93b; 
        font-family: "Segoe UI";
        font-weight: 1000;
        }
    input[type="checkbox"] {
        transform: scale(1.5);
        margin-right: 5px;
        }
    input[type="checkbox"]:checked {
        background-color: green;
    }
    h1{
        color:#94c93b; 
        font-family: "Segoe UI";
        font-weight: 900;
        }
    .btn-crear {
        background-color:  #ffffff;
        color: #94c93b;
        border-color: #94c93b;
        border-radius: 5px;
        padding: 5px 15px;
        }
    
    .btn-crear:hover {
        background-color: #94c93b;
        color: #ffffff;
        }
        .btn-derivar {
        background-color:  #ffffff;
        color: #faa625;
        border-color: #faa625;
        border-radius: 5px;
        padding: 10px 15px;
        }
    
    .btn-derivar:hover {
        background-color: #faa625;
        color: #ffffff;
        }
        .btn-aprobarbateria {
        background-color:  #ffffff;
        color: #25b6fa;
        border-color: #25b6fa;
        border-radius: 5px;
        padding: 10px 15px;
        }
    
    .btn-aprobarbateria:hover {
        background-color: #25b6fa;
        color: #ffffff;
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
    .btn-subirrequisitos {
        background-color: #ffffff;
        color: #faa625;
        border-color: #faa625;
        border-radius: 5px;
        padding: 10px 10px;
        margin-left: 10px;
        margin-right: 10px;
    }
    .btn-subirrequisitos:hover {
        background-color: #faa625;
        color: #ffffff;
    }
</style>
@stop