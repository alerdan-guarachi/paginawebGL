@extends('adminlte::page')

@section('content_header')
<a class="btn btn-sm float-right btn-regresar" href="{{ route('admin.asociados.verclienteauditoria', $clienteauditoria) }}">REGRESAR</a>
    <h1>REQUISITOS</h1>
@stop

@section('content')
    <form id="pdfForm" action="{{ route('admin.asociados.descargarchecklistclienteauditoria', $clienteauditoria) }}" method="POST" style="display: none;">
        @csrf
        <input type="hidden" name="documentosSeleccionados" id="documentosSeleccionadosInput">
    </form>
    <div class="card"> 
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <input type="hidden" name="poder_estado" id="poder_estado">
                        <input type="checkbox" name="poder" value="poder" id="poder">
                        <label for="poder">PODER</label>
                    </div>
                    <div class="form-group">
                        <input type="checkbox" name="avcci" value="avcci" id="avcci">
                        <label for="avcci">AVC/CARNET ASEGURADO</label>
                    </div>
                    <div class="form-group">
                        <input type="checkbox" name="cnacasegurado" value="cnacasegurado" id="cnacasegurado">
                        <label for="cnacasegurado" style="min-height: 20px;">CERTIFICADO NACIMIENTO ASEGURADO</label>
                    </div>
                    <div class="form-group">
                        <input type="checkbox" name="ciasegurado" value="ciasegurado" id="ciasegurado">
                        <label for="ciasegurado" style="min-height: 20px;">CARNET IDENTIDAD ASEGURADO</label>
                    </div>
                    <div class="form-group">
                        <input type="checkbox" name="cmatrimonio" value="cmatrimonio" id="cmatrimonio">
                        <label for="cmatrimonio" style="min-height: 20px;">CERTIFICADO DE MATRIMONIO</label>
                    </div>
                    <div class="form-group">
                        <input type="checkbox" name="cnacconyuge" value="cnacconyuge" id="cnacconyuge">
                        <label for="cnacconyuge" style="min-height: 20px;">CERTIFICADO NACIMIENTO CONYUGE</label>
                    </div>
                </div>
    
    
                <div class="col-md-6">
                    <div class="form-group">
                        <input type="checkbox" name="ciconyuge" value="ciconyuge" id="ciconyuge">
                        <label for="ciconyuge" style="min-height: 20px;">CARNET IDENTIDAD CONYUGE</label>
                    </div>
                    <div class="form-group">
                        <input type="checkbox" name="cnacjihos" value="cnacjihos" id="cnacjihos">
                        <label for="cnacjihos" style="min-height: 20px;">CERTIFICADO NACIMIENTO HIJOS < 25</label>
                    </div>
                    <div class="form-group">
                        <input type="checkbox" name="cihijos" value="cihijos" id="cihijos">
                        <label for="cihijos" style="min-height: 20px;">CARNET IDENTIDAD HIJOS < 25</label>
                    </div>
                    <div class="form-group">
                        <input type="checkbox" name="denfaccidente" value="denfaccidente" id="denfaccidente">
                        <label for="denfaccidente" style="min-height: 20px;">DENUNCIA ENFERMEDAD ACCIDENTE</label>
                    </div>
                    <div class="form-group">
                        <input type="checkbox" name="crodomicilio" value="crodomicilio" id="crodomicilio">
                        <label for="crodomicilio" style="min-height: 20px;">CROQUIS DE DOMICILIO</label>
                    </div>
                    <div class="form-group">
                        <input type="checkbox" name="contrato" value="contrato" id="contrato">
                        <label for="contrato" style="min-height: 20px;">CONTRATO</label>
                    </div>
                </div>
            </div>
    
            @if (!$tieneRequisitos)
                <button onclick="generatePDF()" class="btn-crear">Generar Check List</button>
            @endif
    
    
            <script>
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
        </div>
    </div>
@stop
@section('css')
<style>
    input[type="checkbox"] {
        transform: scale(1.5);
        margin-right: 5px;
        }
    input[type="checkbox"]:checked {
        background-color: green; /* Cambia el color de fondo a verde cuando el checkbox está marcado */
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


{{-- @extends('adminlte::page')

@section('content_header')
<h1>Check List</h1>
@stop

@section('content')
<body>
    <table>
        <caption>DOCUMENTACIÓN A PRESENTAR</caption>
        <tr>
            <th>PODER</th>
            <th>AVC/CARNET ASEGURADO</th>
            <th>CERTIFICADO NACIMIENTO ASEGURADO</th>
            <th>CARNET IDENTIDAD ASEGURADO</th>
            <th>CERTIFICADO DE MATRIMONIO</th>
            <th>CERTIFICADO NACIMIENTO CONYUGE</th>
            <th>CARNET IDENTIDAD CONYUGE</th>
            <th>CERTIFICADO NACIMIENTO HIJOS &lt; 25</th>
            <th>CARNET IDENTIDAD HIJOS &lt; 25</th>
            <th>DENUNCIA ENFERMEDAD ACCIDENTE</th>
            <th>CROQUIS DE DOMICILIO</th>
            <th>CONTRATO</th>
        </tr>
        <tr id="documentos">
            <td><input type="checkbox" name="documentos[]" value="poder"></td>
            <td><input type="checkbox" name="documentos[]" value="avc"></td>
            <td><input type="checkbox" name="documentos[]" value="certificado_nacimiento_asegurado"></td>
            <td><input type="checkbox" name="documentos[]" value="carnet_identidad_asegurado"></td>
            <td><input type="checkbox" name="documentos[]" value="certificado_matrimonio"></td>
            <td><input type="checkbox" name="documentos[]" value="certificado_nacimiento_conyuge"></td>
            <td><input type="checkbox" name="documentos[]" value="carnet_identidad_conyuge"></td>
            <td><input type="checkbox" name="documentos[]" value="certificado_nacimiento_hijos"></td>
            <td><input type="checkbox" name="documentos[]" value="carnet_identidad_hijos"></td>
            <td><input type="checkbox" name="documentos[]" value="denuncia_enfermedad_accidente"></td>
            <td><input type="checkbox" name="documentos[]" value="croquis_domicilio"></td>
            <td><input type="checkbox" name="documentos[]" value="contrato"></td>
        </tr>
    </table>
    <form id="pdfForm" action="{{ route('admin.clientes.print2', $cliente) }}" method="POST" style="display: none;">
        @csrf
        <input type="hidden" name="documentosSeleccionados" id="documentosSeleccionadosInput">
    </form>
    
    <button onclick="generatePDF()">Generar PDF</button>

    <script>
        function generatePDF() {
            var checkboxes = document.querySelectorAll('#documentos input[type="checkbox"]');
            var documentosSeleccionados = [];
            checkboxes.forEach(function(checkbox) {
                if (checkbox.checked) {
                    documentosSeleccionados.push(checkbox.value);
                }
            });
            document.getElementById('documentosSeleccionadosInput').value = JSON.stringify(documentosSeleccionados);
            // Ahora envía el formulario
            document.getElementById('pdfForm').submit();
        }
    </script>
    
</body>
</html>
@endsection
@section('css')
<style>
    table {
        width: 100%;
        border-collapse: collapse;
    }
    th, td {
        border: 1px solid black;
        padding: 8px;
        text-align: center;
    }
    caption {
        caption-side: top;
        text-align: center;
        font-size: 18px;
        font-weight: bold;
        margin-bottom: 20px;
    }
</style>
@stop --}}