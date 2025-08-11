@extends('adminlte::page')

@section('content_header')
<a class="btn btn-sm float-right btn-regresar" href="{{ route('admin.instructivaspoder.index') }}">REGRESAR</a>
<h5>CREAR INSTRUCTIVA DE PODER DE:</h5>
<h3>{{$clienteauditoria->nombrecompleto}}</h3>
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
                {!! Form::open(['route' => ['admin.instructivaspoder.generarpdfinstructivaspoderauditoria', $clienteauditoria], 'method' => 'GET']) !!}
                {!! Form::hidden('usuarioid', auth()->user()->id) !!}
                {!! Form::hidden('usuarioregistro', auth()->user()->name) !!}
                {!! Form::hidden('clienteid', $clienteauditoria->id) !!}
                {!! Form::hidden('clientenombre', $clienteauditoria->nombrecompleto) !!}
                {!! Form::hidden('personal_ids', '', ['id' => 'personalIds']) !!}
                <div class="row">
                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-body">
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        {!! Form::label('tipo_pdf', 'Trámite:') !!}
                                        {!! Form::select('tipo_pdf', $tramitesFiltrados, null, ['class' => 'form-control', 'placeholder' => 'Selecciona una opción', 'id' => 'tipoPdfSelect']) !!}
                                        @error('tipo_pdf')
                                            <small class="text-danger fas fa-exclamation-circle">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        {!! Form::label('personal', 'Apoderado:') !!}
                                        {!! Form::select('personal', $personal->pluck('razonsocial', 'id'), null, ['class' => 'form-control', 'placeholder' => 'Selecciona una opción', 'id' => 'personalSelect']) !!}
                                        @error('personal')
                                            <small class="text-danger fas fa-exclamation-circle">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <button type="button" id="addPersonal" class="btn btn-sm btn-agregar">AGREGAR</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-body">
                                <div class="col-lg-12">
                                    <h5>APODERADOS SELECCIONADOS</h5>
                                    <div class="table-responsive" style="margin-top: 10px;">
                                        <table class="table table-bordered table-striped table-sm">
                                            <thead class="table-secondary">
                                                <tr>
                                                    <th>Nombre</th>
                                                    <th>CI</th>
                                                    <th>CI.Exp</th>
                                                    <th>Ciudad</th>
                                                    <th>Quitar</th>
                                                </tr>
                                            </thead>
                                            <tbody id="selectedPersonalList"></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <script>
                    const personalData = @json($personal);
                    document.getElementById('addPersonal').addEventListener('click', function() {
                        const select = document.getElementById('personalSelect');
                        const selectedValue = select.value;

                        if (selectedValue) {
                            const personalInfo = personalData.find(person => person.id == selectedValue);
                            const row = document.createElement('tr');
                            row.innerHTML = `
                                <td>${personalInfo.razonsocial}</td>
                                <td>${personalInfo.ci}</td>
                                <td>${personalInfo.ciexp}</td>
                                <td>${personalInfo.ciudad}</td>
                                <td><button class="btn btn-quitar btn-sm" onclick="removePersonal(this, '${selectedValue}')"><i class="fas fa-trash"></i></button></td>
                            `;
                            document.getElementById('selectedPersonalList').appendChild(row);
                            updatePersonalIds(selectedValue);
                            select.remove(select.selectedIndex);
                        } else {
                            alert('Por favor, selecciona un personal.');
                        }
                    });

                    function updatePersonalIds(value) {
                        const personalIdsField = document.getElementById('personalIds');
                        const currentIds = personalIdsField.value ? personalIdsField.value.split(',') : [];
                        currentIds.push(value);
                        personalIdsField.value = currentIds.join(',');
                    }

                    function removePersonal(button, value) {
                        const row = button.closest('tr');
                        row.remove();

                        const personalIdsField = document.getElementById('personalIds');
                        const currentIds = personalIdsField.value.split(',');
                        const updatedIds = currentIds.filter(id => id !== value);
                        personalIdsField.value = updatedIds.join(',');
                        const select = document.getElementById('personalSelect');
                        const option = document.createElement('option');
                        option.value = value;
                        option.textContent = personalData.find(person => person.id == value).nombrecompleto;
                        select.appendChild(option);
                    }
                </script>

                {!! Form::submit('CREAR INSTRUCTIVA', ['class' => 'btn btn-crear']) !!}
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>


@stop
@section('js')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script type="text/javascript" src="https://jeremyfagis.github.io/dropify/dist/js/dropify.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://jeremyfagis.github.io/dropify/dist/css/dropify.min.css"> 
<script src="https://cdnjs.cloudflare.com/ajax/libs/dropify/0.2.2/js/dropify.min.js"></script>
<script>
    $(document).ready(function() {
        $('.dropify').dropify({
            messages: {
                'default': 'Arrastre y suelte un archivo o haga clic aquí',
                'replace': 'Arrastre y suelte o haga clic para reemplazar',
                'remove': 'Eliminar',
                'error': 'Ooops, algo salió mal.'
            }
        });
        $('.dropify').on('dropify.error.fileSize', function(event, element) {
            var maxSize = element.input.files[0].size / (1024 * 1024);
            var errorMessage = 'El archivo es demasiado grande (' + maxSize.toFixed(2) + ' MB máx.).';
            $(element.input).siblings('.dropify-error').text(errorMessage);
        });
    });
    $(document).ready(function() {
        $('#area').change(function() {
            var areaId = $(this).val();
            $('.acciones').hide();
            $('#acciones_' + areaId).show();
        });
    });
    document.getElementById('archivo').addEventListener('change', function(event) {
        var file = event.target.files[0];
        if (file) {
            var fileURL = URL.createObjectURL(file);
            var previewCard = document.getElementById('preview-card');
            var documentPreview = document.getElementById('document-preview');
    
            previewCard.style.display = 'block';
            documentPreview.src = fileURL;
        } else {
            var previewCard = document.getElementById('preview-card');
            previewCard.style.display = 'none';
            documentPreview.src = '';
        }
    });
    document.addEventListener('DOMContentLoaded', function() {
        document.addEventListener('keypress', function(event) {
            if (event.key === 'Enter') {
                event.preventDefault();
            }
        });
    });

    document.getElementById('area_select').addEventListener('change', function() {
        var select = document.getElementById('area_select');
        var selectedOption = select.options[select.selectedIndex];
        if (selectedOption.value !== '') {
            select.style.display = 'none';
            document.getElementById('area_label').style.display = 'none';
            var areaName = selectedOption.text;
            var accionesDiv = document.getElementById('acciones_' + selectedOption.value);
            accionesDiv.style.display = 'block';
            if (!document.getElementById('acciones_label_' + selectedOption.value)) {
                var accionesLabel = document.createElement('label');
                accionesLabel.innerHTML = 'Acciones para: ' + areaName;
                accionesLabel.id = 'acciones_label_' + selectedOption.value;
                accionesDiv.prepend(accionesLabel);
            }
            var resetButton = document.getElementById('reset_button');
            if (!resetButton) {
                var button = document.createElement('button');
                button.type = 'button';
                button.innerHTML = 'Elegir otra area';
                button.classList.add('custom-button');

                button.id = 'reset_button';
                button.onclick = resetSelectAndCheckboxes;
                document.getElementById('reset_button_container').appendChild(button);
            }
        }
    });

    function resetSelectAndCheckboxes() {
        var select = document.getElementById('area_select');
        select.style.display = 'block';
        select.value = '';
        document.getElementById('area_label').style.display = 'block';
        var checkboxes = document.querySelectorAll('[id^="accionnombre_"]');
        checkboxes.forEach(function(checkbox) {
            checkbox.checked = false;
        });
        var accionesDivs = document.querySelectorAll('[id^="acciones_"]');
        accionesDivs.forEach(function(div) {
            div.style.display = 'none';
            var label = document.getElementById('acciones_label_' + div.id.split('_')[1]);
            if (label) {
                label.remove();
            }
        });
        var resetButton = document.getElementById('reset_button');
        if (resetButton) {
            resetButton.remove();
        }
    }
</script>
@endsection

@section('css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropify/0.2.2/css/dropify.min.css">
<link rel="styleheet" href="/css/admin_custom.css">
<style>
    .dropify-wrapper {
        height: 125px !important;
    }
    .dropify-message p {
        font-size: 14px;
    }
    h1 {
        color:#94c93b; 
        font-family: "Segoe UI";
        font-weight: 900;
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
    h6 {
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
    .btn-quitar {
        background-color:  #ffffff;
        color: #d52222;
        border-color: #d52222;
        border-radius: 5px;
        padding: 3px 6px;
        }
    .btn-quitar:hover {
        background-color: #d52222;
        color: #ffffff;
        }
    .btn-agregar {
        background-color:  #ffffff;
        color: #faa625;
        border-color: #faa625;
        border-radius: 5px;
        padding: 5px 10px;
        }
    .btn-agregar:hover {
        background-color: #faa625;
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
    .custom-button {
        background-color: #ffffff;
        color: #faa625;
        border-color: #faa625;
        border-radius: 5px;
        padding: 5px 40px;
    }
    .custom-button:hover {
        background-color: #faa625;
        color: #ffffff;
    }
    .btn-cerrar {
        background-color: #ffffff;
        color: #d32727;
        border-color: #d32727;
        border-radius: 5px;
        padding: 5px 10px;
    }
    .btn-cerrar:hover {
        background-color: #d32727;
        color: #ffffff;
    }
    .btn-regresar {
        background-color: #ffffff;
        color: #2926e2;
        border-color: #2926e2;
        border-radius: 5px;
        padding: 5px 10px;
    }
    .btn-regresar:hover {
        background-color: #2926e2;
        color: #ffffff;
    }
</style>
@stop