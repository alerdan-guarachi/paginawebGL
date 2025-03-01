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
            <!-- Las páginas se generarán aquí dinámicamente -->
            <div class="page" id="page-1">
                <div id="word-preview" style="font-family: Arial, sans-serif; font-size: 12pt; line-height: 1.2; margin-left:40px; margin-right:40px;  margin-top:40px;">
                    <!-- Ciudad y Fecha -->
                    <div style="text-align: right; margin-top: 20px;">
                        <p id="preview-ciudad-fecha">
                            <span id="preview-ciudad">Santa Cruz de la Sierra</span>, 
                            <span id="preview-fecha-value">[Sin seleccionar]</span>
                        </p>
                    </div>
                    <!-- Señores, Opción y Referencia -->
                    <div style="text-align: left; margin-top: 50px;">
                        <p><strong>Señores:</strong></p>
                        <p id="preview-opcion" style="margin-top: 10px;">[Sin seleccionar]</p>
                        <p><strong>Presente.-</strong></p>
                        <p><strong>REF.: <span id="preview-ref">[Sin información]</span></strong></p>
                        <p>De mi consideración:</p>
                        <p><span id="preview-introduccion">[Sin Introducción]</span></p>
                        <p><span id="preview-pieintroduccion">[Sin pie de Introducción]</span></p>
                        <!-- Artículos seleccionados -->
                        <div id="preview-articulos">
                            <!-- Artículos se llenarán dinámicamente -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    

    <!-- Lado derecho: Formulario -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('admin.cartaspolizas.downloadPdf') }}">
                    @csrf
                    <!-- Campo de ciudad -->
                    <div class="form-group">
                        <label for="ciudad">Ciudad:</label>
                        <select id="ciudad" name="ciudad" class="form-control" required onchange="updatePreview()">
                            <option value="Santa Cruz de la Sierra" selected>Santa Cruz de la Sierra</option>
                            <option value="Cochabamba">Cochabamba</option>
                        </select>
                    </div>

                    <!-- Campo de fecha -->
                    <div class="form-group">
                        <label for="fecha">Seleccionar fecha:</label>
                        <input type="date" id="fecha" name="fecha" class="form-control" required onchange="updatePreview()">
                    </div>

                    <!-- Campo select con opciones combinadas -->
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

                    <!-- Campo de referencia -->
                    <div class="form-group">
                        <label for="ref">Referencia (REF.):</label>
                        <input type="text" id="ref" name="ref" class="form-control" placeholder="Escriba la referencia" oninput="updateRefPreview()">
                    </div>

                    <div class="form-group">
                        <label for="introduccion">Introducción:</label>
                        <input type="text" id="introduccion" name="introduccion" class="form-control" placeholder="Escriba la introduccion" oninput="updateIntroPreview()">
                    </div>
                    <div class="form-group">
                        <label for="pieintroduccion">Pie Introducción:</label>
                        <input type="text" id="pieintroduccion" name="pieintroduccion" class="form-control" placeholder="Escriba el pie introduccion" oninput="updatepieIntroPreview()">
                    </div>

                    <!-- Campos de Artículos -->
                    <div class="form-group">
                        <label for="articulos">Seleccionar artículos:</label><br>
                        <input type="checkbox" id="articulo1031" name="articulo1031" value="Art. 1031.- (INFORMES Y EVIDENCIAS)">
                        <label for="articulo1031">Art. 1031.- (INFORMES Y EVIDENCIAS)</label><br>

                        <input type="checkbox" id="articulo1033" name="articulo1033" value="Art. 1033.- (PLAZO PARA PRONUNCIARSE)">
                        <label for="articulo1033">Art. 1033.- (PLAZO PARA PRONUNCIARSE)</label><br>
                    </div>
                    <div class="form-group">
                        <label for="texto_inicio">Texto al principio:</label><br>
                        <input type="text" id="texto_inicio" placeholder="Escribe algo aquí" />
                    </div>
                    
                    <div class="form-group">
                        <label for="texto_medio">Texto en el medio:</label><br>
                        <input type="text" id="texto_medio" placeholder="Escribe algo aquí" />
                    </div>
                    
                    <div class="form-group">
                        <label for="texto_final">Texto al final:</label><br>
                        <input type="text" id="texto_final" placeholder="Escribe algo aquí" />
                    </div>

                    <button type="submit" class="btn btn-primary btn-block">Descargar PDF</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function updatePreview() {
    const ciudad = document.getElementById('ciudad').value || 'Santa Cruz de la Sierra';
    const fecha = document.getElementById('fecha').value || '[Sin seleccionar]';
    const opcion = document.getElementById('opcion').value || '[Sin seleccionar]';

    document.getElementById('preview-ciudad').innerText = ciudad;
    document.getElementById('preview-fecha-value').innerText = formatFecha(fecha);
    document.getElementById('preview-opcion').innerText = opcion;
}

function updateRefPreview() {
    const ref = document.getElementById('ref').value || '[Sin información]';
    document.getElementById('preview-ref').innerText = ref;

}

function updateIntroPreview() {
    const introduccion = document.getElementById('introduccion').value || '[Sin Introducción]';
    document.getElementById('preview-introduccion').innerText = introduccion;

}

function updatepieIntroPreview() {
    const pieintroduccion = document.getElementById('pieintroduccion').value || '[Sin pie de Introducción]';
    document.getElementById('preview-pieintroduccion').innerText = pieintroduccion;

}

// Actualización dinámica de los artículos seleccionados
document.getElementById('articulo1031').addEventListener('change', updateArticulosPreview);
document.getElementById('articulo1033').addEventListener('change', updateArticulosPreview);

function updateArticulosPreview() {
    const articulo1031 = document.getElementById('articulo1031').checked;
    const articulo1033 = document.getElementById('articulo1033').checked;
    
    const previewArticulos = document.getElementById('preview-articulos');
    previewArticulos.innerHTML = ''; // Limpiar el contenido anterior

    if (articulo1031) {
        previewArticulos.innerHTML += `<p><i>“Art. 1031.- (INFORMES Y EVIDENCIAS). El asegurado o beneficiario, según el caso, tienen la obligación de facilitar, a requerimiento del asegurador, todas las informaciones que tengan sobre los hechos y circunstancias del siniestro, a suministrar las evidencias conducentes a la determinación de la causa, identidad de las personas o intereses asegurados y cuantía de los daños, así como permitir las indagaciones pertinentes necesarias a tal objeto.”</i></p>`;
    }
    if (articulo1033) {
        previewArticulos.innerHTML += `<p><i>“Art. 1033.- (PLAZO PARA PRONUNCIARSE). El asegurador debe pronunciarse sobre el derecho del asegurado o beneficiario dentro de los treinta días de recibidas la información y evidencia citadas en el artículo 1031. Se dejará constancia escrita de la fecha de recepción de la información y evidencias a efecto del cómputo del plazo.
        <br>En caso de demora u omisión del asegurado o beneficiario en proporcionar la información y evidencias sobre el siniestro, el término señalado no corre hasta el cumplimiento de estas obligaciones.
        <brEl silencio del asegurador, vencido el término para pronunciarse, importa la aceptación del reclamo.”</i>
        </p>`;
    }

}

function formatFecha(fecha) {
    if (!fecha) return '[Sin seleccionar]';

    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    const date = new Date(fecha);
    return date.toLocaleDateString('es-BO', options); // Formato de Bolivia
}

</script>

@stop

@section('css')
<link rel="styleheet" href="/css/admin_custom.css">
<style>
    /* Estilo para la hoja carta */
.page {
    width: 812px; /* 8.5 pulgadas en píxeles a 72 DPI */
    height: 3992px; /* 11 pulgadas en píxeles a 72 DPI */
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
