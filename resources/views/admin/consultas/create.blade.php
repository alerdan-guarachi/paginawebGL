@extends('adminlte::page')

@section('plugins.Select2', true)

@section('content')
<div class="container-fluid mt-3">
    <div class="row">

        <div class="col-md-5">
            <div class="card shadow border-0 h-100">
                <div class="card-header bg-primary text-white py-2">
                    <h5 class="mb-0">🩺 Evaluación Médica</h5>
                </div>

                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-md-4">
                            <label class="small font-weight-bold">Edad</label>
                            <input type="number" id="edadPaciente" class="form-control form-control-sm" min="0" max="120">
                        </div>

                        <div class="col-md-4">
                            <label class="small font-weight-bold">Sexo</label>
                            <select id="sexoPaciente" class="form-control form-control-sm">
                                <option value="todos">No especificado</option>
                                <option value="masculino">Masculino</option>
                                <option value="femenino">Femenino</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="small font-weight-bold">Embarazo</label>
                            <select id="embarazadaPaciente" class="form-control form-control-sm">
                                <option value="">No aplica</option>
                                <option value="1">Sí</option>
                                <option value="0">No</option>
                            </select>
                        </div>
                    </div>

                    <hr>

                    <label class="font-weight-bold mb-2">Buscar y seleccionar síntomas</label>

                    <select class="form-control select2" name="sintomas[]" id="sintomasSelect" multiple="multiple" style="width:100%;"></select>

                    <button type="button" id="btnEvaluar" class="btn btn-success btn-block mt-3">
                        🔍 Evaluar paciente
                    </button>

                    <hr>

                    <small class="text-muted">Puede buscar por nombre, descripción o sinónimos.</small>
                </div>
            </div>
        </div>

        <div class="col-md-7">
            <div class="card shadow border-0 h-100">
                <div class="card-header text-white py-2" id="resultadoHeader" style="background:#343a40;">
                    <h5 class="mb-0">📊 Resultados de evaluación</h5>
                </div>

                <div class="card-body">

                    <div id="emptyState" class="text-center text-muted mt-5">
                        <h5>Sin evaluación aún</h5>
                        <p>Seleccione síntomas y presione evaluar.</p>
                    </div>

                    <div id="resultadoPanel" style="display:none;">

                        <div id="alertasMedicas"></div>
                        <div id="diagnosticoPrincipal"></div>

                        <div class="row mt-2">
                            <div class="col-md-6">
                                <div class="mini-box border-left-primary">
                                    <h6>🧪 Estudios sugeridos</h6>
                                    <div id="listaEstudios"></div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mini-box border-left-success">
                                    <h6>👨‍⚕️ Especialidades sugeridas</h6>
                                    <div id="listaEspecialidades"></div>
                                </div>
                            </div>
                        </div>

                        <div class="mini-box border-left-danger mt-2">
                            <h6>🧠 Diagnósticos diferenciales</h6>
                            <div id="listaDiagnosticos"></div>
                        </div>

                    </div>

                </div>
            </div>
        </div>

    </div>
</div>

<style>
.select2-container--bootstrap4 .select2-selection--multiple {
    min-height: 95px !important;
    padding: 8px !important;
    font-size: 14px !important;
    border-radius: 10px !important;
    border: 2px solid #007bff !important;
}

.select2-search__field {
    font-size: 14px !important;
    padding: 6px !important;
    min-width: 220px !important;
}

.select2-container {
    width: 100% !important;
}

.card {
    border-radius: 12px !important;
}

.mini-box {
    border: 1px solid #ddd;
    border-radius: 10px;
    padding: 10px;
    background: #fff;
}

.border-left-danger {
    border-left: 5px solid #dc3545 !important;
}

.border-left-primary {
    border-left: 5px solid #007bff !important;
}

.border-left-success {
    border-left: 5px solid #28a745 !important;
}

.result-card {
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 7px 9px;
    margin-bottom: 7px;
    background: #fff;
    font-size: 12px;
    line-height: 1.3;
}

.result-card strong {
    font-size: 13px;
}

.result-card small {
    font-size: 11px;
}

.main-diagnosis {
    border-radius: 10px;
    padding: 12px;
    border-left: 6px solid #dc3545;
    background: #fff5f5;
}

.main-diagnosis h5 {
    margin-bottom: 5px;
    font-weight: bold;
}

.alert {
    font-size: 12px;
    padding: 6px 10px;
    margin-bottom: 6px;
}
</style>
@endsection

@section('js')
<script>
$(document).ready(function () {

    $('#sintomasSelect').select2({
        theme: 'bootstrap4',
        placeholder: 'Escriba un síntoma...',
        minimumInputLength: 1,
        width: '100%',
        ajax: {
            url: '{{ url("/sintomas/buscar") }}', 
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return { q: params.term };
            },
            processResults: function (data) {
                return {
                    results: $.map(data, function (item) {
                        return {
                            id: item.id,
                            text:
                                (item.is_critical == 1 ? '🚨 ' : '') + 
                                item.name + 
                                ' (' + (item.category ?? 'Sin categoría') + ')' 
                        };
                    })
                };
            }
        }
    });

    $('#btnEvaluar').click(function () {

        let sintomas = $('#sintomasSelect').val();

        if (!sintomas || sintomas.length === 0) {
            alert('Seleccione al menos un síntoma');
            return;
        }

        $.ajax({
            url: '{{ url("/consultas/evaluar") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                sintomas: sintomas,
                edad: $('#edadPaciente').val(),
                sexo: $('#sexoPaciente').val(),
                embarazada: $('#embarazadaPaciente').val()
            },
            success: function (res) {

                $('#emptyState').hide();
                $('#resultadoPanel').show();

                $('#diagnosticoPrincipal').html('');
                $('#listaDiagnosticos').html('');
                $('#listaEstudios').html('');
                $('#listaEspecialidades').html('');
                $('#alertasMedicas').html('');

                if (!res.principal) {
                    $('#diagnosticoPrincipal').html(`
                        <div class="alert alert-info">
                            No se encontraron reglas para los datos ingresados.
                        </div>
                    `);
                    return;
                }

                let principal = res.principal;
                let badge = 'success';

                // Corregido: urgency_level
                let urgencia = principal.urgency_level ? principal.urgency_level.toLowerCase() : 'bajo';
                if (urgencia === 'medio') badge = 'warning';
                else if (urgencia === 'alto') badge = 'danger';
                else if (urgencia === 'emergencia') badge = 'dark';

                if (res.alertas && res.alertas.length > 0) {

                    let html = `
                        <div class="card border-danger mb-3 shadow-sm">
                            <div class="card-header bg-danger text-white py-2">
                                <strong>
                                    <i class="fas fa-exclamation-triangle"></i>
                                    Alertas clínicas
                                </strong>
                            </div>

                            <div class="card-body py-2">
                                <ul class="mb-0 pl-3">
                    `;

                    res.alertas.forEach(function(a){
                        html += `
                            <li class="mb-1">
                                ${a}
                            </li>
                        `;
                    });

                    html += `
                                </ul>
                            </div>
                        </div>
                    `;

                    $('#alertasMedicas').html(html);
                }

                // Corregido: total_sintomas -> total_symptoms | matches -> coincidencia
                $('#diagnosticoPrincipal').append(`
                    <div class="main-diagnosis mb-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5>⭐ ${principal.regla}</h5>
                            <span class="badge badge-${badge}">${urgencia.toUpperCase()}</span>
                        </div>

                        <small>
                            Probabilidad: ${principal.percentage}% • 
                            Coincidencias: ${principal.matches}/${principal.total_symptoms} • 
                            Score: ${principal.score}
                        </small>

                        <div class="mt-2">
                            <strong>Recomendación:</strong><br>
                            <small>
                                ${principal.recommendation && principal.recommendation !== 'NULL'
                                    ? principal.recommendation
                                    : 'Sin recomendación registrada'}
                            </small>
                        </div>
                    </div>
                `);

                if (urgencia === 'emergencia') {
                    $('#resultadoHeader').css('background', '#8B0000');
                } else if (urgencia === 'alto') {
                    $('#resultadoHeader').css('background', '#dc3545');
                } else {
                    $('#resultadoHeader').css('background', '#343a40');
                }

                res.estudios.forEach(function (e, index) {
                    let necesitaAyuno = (e.requires_fasting == 1 || e.requires_fasting === true);
                    let necesitaOrden = (e.requires_medical_order == 1 || e.requires_medical_order === true);

                    let badgeAyuno = necesitaAyuno ? '<span class="badge badge-warning text-dark mr-1"><i class="fas fa-utensils"></i> Requiere Ayuno</span>' : '';
                    let badgeOrden = necesitaOrden ? '<span class="badge badge-info"><i class="fas fa-file-medical"></i> Requiere Orden</span>' : '';

                    $('#listaEstudios').append(`
                        <div class="result-card mb-2 p-2 border-bottom">
                            <strong>${index + 1}. 🧪 ${e.name}</strong><br>
                            <small class="text-muted">
                                ${e.motive && e.motive !== 'NULL' ? e.motive : 'Estudio complementario'}
                            </small>
                            <div class="mt-1">
                                <small>
                                    ${badgeAyuno}
                                    ${badgeOrden}
                                </small>
                            </div>
                        </div>
                    `);
                });

                // Corregido: nombre -> name
                res.especialidades.forEach(function (e, index) {
                    $('#listaEspecialidades').append(`
                        <div class="result-card">
                            <strong>${index + 1}. 👨‍⚕️ ${e.name}</strong><br>
                            <small>${e.description ?? ''}</small>
                        </div>
                    `);
                });

                // Corregido: d.urgencia -> d.urgency_level
                if (res.diferenciales && res.diferenciales.length > 0) {
                    res.diferenciales.forEach(function (d) {
                        let b = 'success';
                        let urg = d.urgency_level ? d.urgency_level.toLowerCase() : 'bajo';

                        if (urg === 'medio') b = 'warning';
                        else if (urg === 'alto') b = 'danger';
                        else if (urg === 'emergencia') b = 'dark';

                        $('#listaDiagnosticos').append(`
                            <div class="result-card">
                                <div class="d-flex justify-content-between">
                                    <strong>${d.regla}</strong>
                                    <span class="badge badge-${b}">${urg.toUpperCase()}</span>
                                </div>
                                <small>
                                    Probabilidad: ${d.percentage}% • 
                                    Coincidencias: ${d.matches}/${d.total_symptoms}
                                </small>
                            </div>
                        `);
                    });
                } else {
                    $('#listaDiagnosticos').html('<small class="text-muted">Sin diagnósticos diferenciales relevantes.</small>');
                }
            },
            error: function (xhr) {
                console.log(xhr.responseText);
                alert('Ocurrió un error al evaluar. Revisa la consola F12.');
            }
        });

    });

    function actualizarEmbarazo() {

        const sexo = $('#sexoPaciente').val();

        if (sexo === 'femenino') {
            $('#embarazadaPaciente').prop('disabled', false);

            if ($('#embarazadaPaciente').val() === '') {
                $('#embarazadaPaciente').val('0');
            }
        } else {
            $('#embarazadaPaciente')
                .val('')
                .prop('disabled', true);
        }
    }

    actualizarEmbarazo();

    $('#sexoPaciente').on('change', function () {
        actualizarEmbarazo();
    });

});
</script>
@endsection