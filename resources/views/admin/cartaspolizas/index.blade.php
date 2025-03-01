@extends('adminlte::page')

@section('content_header')
<h1>CARTAS</h1>
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


<div class="row">
    <div class="col-md-8"> 
        <div id="pages-container" class="pages-container">
            <div class="page" id="page-1">
                <div id="word-preview" style="font-family: Arial, sans-serif; font-size: 12pt; line-height: 1.2; margin-left:40px; margin-right:40px;  margin-top:40px;">
                    <div style="text-align: right; margin-top: 20px;">
                        <p id="preview-ciudad-fecha">
                            <span id="preview-ciudad">Santa Cruz</span>, 
                            <span id="preview-fecha-value">[Sin seleccionar]</span>
                        </p>
                    </div>
                    <div style="text-align: left; margin-top: 50px;">
                        <p>Señores:</p>
                        <p id="preview-opcion" style="margin-top: 10px;"><strong>[Sin seleccionar]</strong></p>
                        <p style="margin-bottom: 40px;">Presente.-</p>
                        <p style="text-align: center; margin-bottom: 40px;"><strong><u>REF.: SOLICITUD DE POLIZAS</u></strong></p>
                        <p>Mediante la presente carta reciba usted mis más cordiales saludos, deseándole éxitos en sus labores.</p>
                        <p>Por la presente realizo la Solicitud de:</p>
                        <p style="margin-left: 40px; margin-bottom:1px;">1.	Pólizas Generales</p>
                        <p style="margin-left: 40px; margin-top:1px; margin-bottom:1px;">2.	Declaración de Salud </p>
                        <p style="margin-left: 40px; margin-top:1px;">3.	Póliza de Seguro de Desgravamen</p>
                        <p>Aclaro que el tomador del Seguro es el Banco, por lo mismo solicito las gestiones ante la Compañía Aseguradora.</p>
                        <p>Agradeciendo su atención.</p>

                        <p style="text-align: center; margin-top: 90px;"><span id="preview-clienteuno">[Sin Cliente 1]</span></p>
                        <p style="text-align: center; margin-top: -10px;">CI: <span id="preview-clienteunoci">[Sin CI 1]</span></p>

                        <p style="text-align: center; margin-top: 90px;"><span id="preview-clientedos"></span></p>
                        <p style="text-align: center; margin-top: -10px;"><span id="preview-clientedosci"></span></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('admin.cartaspolizas.descargarsolicitudpolizas') }}">
                    @csrf
                    <div class="row">
                        <div class="form-group col-lg-7">
                            <label for="ciudad">Ciudad:</label>
                            <select id="ciudad" name="ciudad" class="form-control" required onchange="updatePreview()">
                                <option value="Santa Cruz" selected>Santa Cruz</option>
                                <option value="Cochabamba">Cochabamba</option>
                            </select>
                        </div>
                        <div class="form-group col-lg-5">
                            <label for="fecha">Fecha:</label>
                            <input type="date" id="fecha" name="fecha" class="form-control" required onchange="updatePreview()">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="opcion">Seleccionar Banco o Seguro:</label>
                        <select id="opcion" name="opcion" class="form-control" required onchange="updatePreview()">
                            <option value="" disabled selected>Seleccione una opción</option>
                            @foreach($opciones as $opcion)
                                <option value="{{ $opcion->nombreBanco ?? $opcion->nombreSeguro }}">
                                    {{ $opcion->nombreBanco ?? $opcion->nombreSeguro }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="clienteuno">Nombre Cliente 1:</label>
                        <input type="text" id="clienteuno" name="clienteuno" class="form-control" placeholder="Nombre del cliente uno" required oninput="updateClienteunoPreview()">
                    </div>
                    <div class="form-group">
                        <label for="clienteunoci">CI Cliente 1:</label>
                        <input type="text" id="clienteunoci" name="clienteunoci" class="form-control" placeholder="CI del cliente uno" required oninput="updateCiClienteunoPreview()">
                    </div>

                    <div class="form-group">
                        <label for="clientedos">Nombre Cliente 2:</label>
                        <input type="text" id="clientedos" name="clientedos" class="form-control" placeholder="Nombre del cliente dos" oninput="updateClientedosPreview()">
                    </div>
                    <div class="form-group">
                        <label for="clientedosci">CI Cliente 2:</label>
                        <input type="text" id="clientedosci" name="clientedosci" class="form-control" placeholder="CI del cliente dos" oninput="updateCiClientedosPreview()">
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">Guardar y Descargar PDF</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function updatePreview() {
    const ciudad = document.getElementById('ciudad').value || 'Santa Cruz';
    const fecha = document.getElementById('fecha').value || '[Sin seleccionar]';
    const opcion = document.getElementById('opcion').value || '[Sin seleccionar]';

    document.getElementById('preview-ciudad').innerText = ciudad;
    document.getElementById('preview-fecha-value').innerText = formatFecha(fecha);
    document.getElementById('preview-opcion').innerHTML = `<strong>${opcion}</strong>`;
}

function formatFecha(fecha) {
    if (!fecha) return '[Sin seleccionar]';

    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    const date = new Date(fecha);
    return date.toLocaleDateString('es-BO', options);
}

function updateClienteunoPreview() {
    const clienteuno = document.getElementById('clienteuno').value || '[Sin Cliente 1]';
    document.getElementById('preview-clienteuno').innerText = clienteuno;

}
function updateCiClienteunoPreview() {
    const clienteunoci = document.getElementById('clienteunoci').value || '[Sin CI 1]';
    document.getElementById('preview-clienteunoci').innerText = clienteunoci;

}

function updateClientedosPreview() {
    const clientedos = document.getElementById('clientedos').value || '';
    document.getElementById('preview-clientedos').innerText = clientedos;

}
function updateCiClientedosPreview() {
    const clientedosci = document.getElementById('clientedosci').value || '';
    document.getElementById('preview-clientedosci').innerText = clientedosci;

}
</script>

@stop

@section('css')
<link rel="styleheet" href="/css/admin_custom.css">
<style>
    /* Estilo para la hoja carta */
.page {
    width: 812px; /* 8.5 pulgadas en píxeles a 72 DPI */
    height: 992px; /* 11 pulgadas en píxeles a 72 DPI */
    border: 1px solid #ddd;
    margin: auto;
    padding: 20px;
    background: #fff;
    page-break-before: always; /* Se asegura que cada nueva página comience en una nueva hoja */
}

.pages-container {
    display: flex;
    flex-direction: column;
    overflow-y: auto; /* Permite que el contenido se desborde verticalmente */
    max-height: none; /* Quitar el límite de altura para que pueda contener más páginas */
}


</style>
@stop
