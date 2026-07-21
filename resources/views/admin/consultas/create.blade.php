@extends('adminlte::page')

@section('title', 'Evaluación médica')

@section('content')
<div class="container-fluid mt-3">
    <div class="row">

        {{-- FORMULARIO --}}
        <div class="col-lg-5 mb-3">
            <div class="card shadow border-0 h-100">

                <div class="card-header text-white py-2"
                     style="background:#04A0A2;">

                    <h5 class="mb-0">
                        <i class="fas fa-stethoscope mr-1"></i>
                        Evaluación médica orientativa
                    </h5>

                </div>

                <div class="card-body">

                    <div class="alert alert-warning py-2">
                        <small>
                            Esta herramienta ofrece orientación y
                            no reemplaza la valoración realizada por
                            un profesional médico.
                        </small>
                    </div>

                    <div class="row">

                        <div class="col-md-4 mb-2">
                            <label class="small font-weight-bold">
                                Edad en años
                            </label>

                            <input type="number"
                                id="edadPaciente"
                                class="form-control form-control-sm"
                                min="0"
                                max="120"
                                step="1"
                                placeholder="Ej.: 35">

                            <small class="text-muted">
                                Coloque 0 si tiene menos de un año.
                            </small>
                        </div>

                        <div class="col-md-4 mb-2">
                            <label class="small font-weight-bold">
                                Sexo
                            </label>

                            <select id="sexoPaciente"
                                    class="form-control form-control-sm">

                                <option value="TODOS">
                                    No especificado
                                </option>

                                <option value="MASCULINO">
                                    Masculino
                                </option>

                                <option value="FEMENINO">
                                    Femenino
                                </option>

                            </select>
                        </div>

                        <div class="col-md-4 mb-2">
                            <label class="small font-weight-bold">
                                Embarazo
                            </label>

                            <select id="embarazadaPaciente"
                                    class="form-control form-control-sm"
                                    disabled>

                                <option value="">
                                    No especificado
                                </option>

                                <option value="1">
                                    Sí
                                </option>

                                <option value="0">
                                    No
                                </option>

                            </select>
                        </div>

                    </div>

                    <hr>

                    <label for="textoSintomas"
                           class="font-weight-bold mb-1">

                        Describa todos los síntomas

                    </label>

                    <textarea id="textoSintomas"
                              class="form-control symptoms-textarea"
                              rows="7"
                              maxlength="1500"
                              placeholder="Ejemplo: Dolor de cabeza hace 3 días, debilidad en todo el cuerpo y mareos ocasionales."></textarea>

                    <div class="d-flex justify-content-between mt-1">

                        <small class="text-muted">
                            Puede escribir todos los síntomas
                            seguidos usando lenguaje normal.
                        </small>

                        <small class="text-muted">
                            <span id="contadorCaracteres">0</span>/1500
                        </small>

                    </div>

                    <div class="example-box mt-3">

                        <strong>
                            <i class="fas fa-lightbulb text-warning mr-1"></i>
                            Ejemplo
                        </strong>

                        <div class="mt-1">
                            <small>
                                Dolor de cabeza hace 3 días,
                                debilidad en todo el cuerpo y
                                mareos ocasionales.
                            </small>
                        </div>

                    </div>

                    <button type="button"
                            id="btnEvaluar"
                            class="btn btn-success btn-block mt-3">

                        <i class="fas fa-search mr-1"></i>
                        Evaluar paciente

                    </button>

                    <small class="text-muted d-block mt-2 text-center">
                        También puede presionar Ctrl + Enter.
                    </small>

                </div>
            </div>
        </div>

        {{-- RESULTADOS --}}
        <div class="col-lg-7 mb-3">
            <div class="card shadow border-0 h-100">

                <div class="card-header text-white py-2"
                     id="resultadoHeader"
                     style="background:#343a40;">

                    <h5 class="mb-0">
                        <i class="fas fa-chart-bar mr-1"></i>
                        Resultados de evaluación
                    </h5>

                </div>

                <div class="card-body">

                    <div id="emptyState"
                         class="text-center text-muted mt-5">

                        <i class="fas fa-notes-medical fa-3x mb-3"></i>

                        <h5>Sin evaluación todavía</h5>

                        <p>
                            Escriba los síntomas del paciente
                            y presione “Evaluar paciente”.
                        </p>

                    </div>

                    <div id="resultadoPanel"
                         style="display:none;">

                        <div id="sintomasReconocidos"
                             class="recognized-box mb-3">
                        </div>

                        <div id="alertasMedicas"></div>

                        <div id="diagnosticoPrincipal"></div>

                        <div class="row mt-3">

                            <div class="col-md-6 mb-2">

                                <div class="mini-box border-left-primary">

                                    <h6>
                                        <i class="fas fa-vials mr-1"></i>
                                        Estudios sugeridos
                                    </h6>

                                    <div id="listaEstudios"></div>

                                </div>

                            </div>

                            <div class="col-md-6 mb-2">

                                <div class="mini-box border-left-success">

                                    <h6>
                                        <i class="fas fa-user-md mr-1"></i>
                                        Especialidades sugeridas
                                    </h6>

                                    <div id="listaEspecialidades"></div>

                                </div>

                            </div>

                        </div>

                        <div class="mini-box border-left-danger mt-2">

                            <h6>
                                <i class="fas fa-brain mr-1"></i>
                                Posibles diagnósticos diferenciales
                            </h6>

                            <div id="listaDiagnosticos"></div>

                        </div>

                    </div>

                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@section('css')
<style>
.card {
    border-radius: 12px !important;
}

.symptoms-textarea {
    min-height: 155px;
    resize: vertical;
    border: 2px solid #04A0A2;
    border-radius: 10px;
    font-size: 14px;
    line-height: 1.5;
}

.symptoms-textarea:focus {
    border-color: #054B81;
    box-shadow: 0 0 0 .2rem rgba(4, 160, 162, .18);
}

.example-box {
    padding: 10px;
    background: #f8f9fa;
    border: 1px dashed #adb5bd;
    border-radius: 8px;
    color: #495057;
}

.recognized-box {
    padding: 10px;
    background: #f8ffff;
    border: 1px solid #b8e6e7;
    border-radius: 10px;
}

.recognized-title {
    margin-bottom: 7px;
    color: #054B81;
    font-size: 13px;
    font-weight: 700;
}

.recognized-chip {
    display: inline-flex;
    align-items: center;
    margin: 3px 4px 3px 0;
    padding: 6px 9px;
    color: #ffffff;
    background: #04A0A2;
    border-radius: 18px;
    font-size: 12px;
}

.recognized-chip.critical {
    background: #dc3545;
}

.mini-box {
    min-height: 160px;
    padding: 11px;
    background: #ffffff;
    border: 1px solid #dddddd;
    border-radius: 10px;
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
    padding: 8px 9px;
    margin-bottom: 8px;
    background: #ffffff;
    border: 1px solid #dddddd;
    border-radius: 8px;
    font-size: 12px;
    line-height: 1.35;
}

.result-card strong {
    font-size: 13px;
}

.result-card small {
    font-size: 11px;
}

.main-diagnosis {
    padding: 13px;
    background: #fff5f5;
    border-left: 6px solid #dc3545;
    border-radius: 10px;
}

.main-diagnosis h5 {
    margin-bottom: 5px;
    font-weight: 700;
}

.alert {
    font-size: 12px;
}

.badge-warning {
    color: #212529;
}

@media (max-width: 767px) {
    .mini-box {
        min-height: auto;
    }

    .symptoms-textarea {
        min-height: 130px;
    }
}
</style>
@endsection

@section('js')
<script>
$(document).ready(function () {

    const urlEvaluar = '{{ url("/consultas/evaluar") }}';
    const csrfToken = '{{ csrf_token() }}';

    function escaparHtml(valor) {
        return $('<div>')
            .text(valor ?? '')
            .html();
    }

    function claseUrgencia(urgencia) {
        const valor = String(
            urgencia ?? ''
        ).toUpperCase();

        if (valor === 'EMERGENCIA') {
            return 'dark';
        }

        if (valor === 'ALTO') {
            return 'danger';
        }

        if (valor === 'MEDIO') {
            return 'warning';
        }

        return 'success';
    }

    function actualizarEmbarazo() {
        const sexo = $('#sexoPaciente').val();

        if (sexo === 'FEMENINO') {
            $('#embarazadaPaciente')
                .prop('disabled', false);

            return;
        }

        $('#embarazadaPaciente')
            .val('')
            .prop('disabled', true);
    }

    function limpiarResultados() {
        $('#sintomasReconocidos').empty();
        $('#alertasMedicas').empty();
        $('#diagnosticoPrincipal').empty();
        $('#listaDiagnosticos').empty();
        $('#listaEstudios').empty();
        $('#listaEspecialidades').empty();

        $('#resultadoHeader')
            .css('background', '#343a40');
    }

    function mostrarSintomasReconocidos(sintomas) {
        const contenedor = $('#sintomasReconocidos');

        contenedor.empty();

        if (
            !Array.isArray(sintomas) ||
            sintomas.length === 0
        ) {
            contenedor.html(`
                <small class="text-muted">
                    No se reconocieron síntomas.
                </small>
            `);

            return;
        }

        contenedor.append(`
            <div class="recognized-title">
                <i class="fas fa-check-circle mr-1"></i>
                Síntomas reconocidos (${sintomas.length})
            </div>
        `);

        sintomas.forEach(function (sintoma) {
            const esCritico =
                sintoma.is_critical === true ||
                Number(sintoma.is_critical) === 1;

            const claseCritico = esCritico
                ? 'critical'
                : '';

            const icono = esCritico
                ? '<i class="fas fa-exclamation-triangle mr-1"></i>'
                : '<i class="fas fa-check mr-1"></i>';

            contenedor.append(`
                <span class="recognized-chip ${claseCritico}">
                    ${icono}
                    ${escaparHtml(sintoma.name)}
                </span>
            `);
        });
    }

    function mostrarAlertas(alertas) {
        if (
            !Array.isArray(alertas) ||
            alertas.length === 0
        ) {
            return;
        }

        let html = `
            <div class="card border-danger mb-3 shadow-sm">

                <div class="card-header bg-danger text-white py-2">

                    <strong>
                        <i class="fas fa-exclamation-triangle mr-1"></i>
                        Alertas clínicas
                    </strong>

                </div>

                <div class="card-body py-2">

                    <ul class="mb-0 pl-3">
        `;

        alertas.forEach(function (alerta) {
            html += `
                <li class="mb-1">
                    ${escaparHtml(alerta)}
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

    function mostrarPrincipal(principal) {
        const contenedor = $('#diagnosticoPrincipal');

        if (!principal) {
            contenedor.html(`
                <div class="alert alert-info">

                    <strong>
                        No se encontró una regla con suficiente
                        compatibilidad.
                    </strong>

                    <div class="mt-1">
                        Revise los síntomas reconocidos o describa
                        los síntomas con más detalle.
                    </div>

                </div>
            `);

            return;
        }

        const urgencia = String(
            principal.urgency_level ?? 'BAJO'
        ).toUpperCase();

        const badge = claseUrgencia(urgencia);

        const score = Number(
            principal.score ?? 0
        ).toFixed(2);

        const coincidencias = Number(
            principal.matches ?? 0
        );

        const totalSintomas = Number(
            principal.total_symptoms ?? 0
        );

        contenedor.html(`
            <div class="main-diagnosis mb-3">

                <div class="d-flex justify-content-between
                            align-items-start">

                    <h5>
                        <i class="fas fa-star text-warning mr-1"></i>
                        Mayor compatibilidad: ${escaparHtml(principal.regla)}
                    </h5>

                    <span class="badge badge-${badge}">
                        ${escaparHtml(urgencia)}
                    </span>

                </div>

                <p class="mb-2">
                    <small>
                        ${escaparHtml(
                            principal.description ?? ''
                        )}
                    </small>
                </p>

                <small>
                    Compatibilidad:
                    <strong>${score}%</strong>

                    &nbsp;•&nbsp;

                    Confianza:
                    <strong>
                        ${escaparHtml(
                            principal.confidence ?? 'BAJA'
                        )}
                    </strong>

                    &nbsp;•&nbsp;

                    Coincidencias:
                    <strong>
                        ${coincidencias}/${totalSintomas}
                    </strong>
                </small>

                <div class="mt-2">
                    <strong>Recomendación:</strong>

                    <div>
                        <small>
                            ${
                                principal.recommendation
                                    ? escaparHtml(
                                        principal.recommendation
                                    )
                                    : 'Sin recomendación registrada.'
                            }
                        </small>
                    </div>
                </div>

            </div>
        `);

        if (urgencia === 'EMERGENCIA') {
            $('#resultadoHeader')
                .css('background', '#8B0000');

        } else if (urgencia === 'ALTO') {
            $('#resultadoHeader')
                .css('background', '#dc3545');

        } else if (urgencia === 'MEDIO') {
            $('#resultadoHeader')
                .css('background', '#d39e00');

        } else {
            $('#resultadoHeader')
                .css('background', '#28a745');
        }
    }

    function mostrarEstudios(estudios) {
    const contenedor = $('#listaEstudios');

    if (
        !Array.isArray(estudios) ||
        estudios.length === 0
    ) {
        contenedor.html(`
            <small class="text-muted">
                Sin estudios sugeridos.
            </small>
        `);

        return;
    }

    estudios.forEach(function (estudio, index) {
        const necesitaAyuno =
            estudio.requires_fasting === true ||
            Number(estudio.requires_fasting) === 1;

        const necesitaOrden =
            estudio.requires_medical_order === true ||
            Number(estudio.requires_medical_order) === 1;

        const tipo = String(estudio.type ?? '')
            .replaceAll('_', ' ')
            .trim();

        const subtipo = String(estudio.subtype ?? '')
            .replaceAll('_', ' ')
            .trim();

        const nombre = String(estudio.name ?? '')
            .trim();

        /*
         * Construye:
         * TYPE - SUBTYPE - NAME
         *
         * Si subtype está vacío, no muestra guiones duplicados.
         */
        const tituloEstudio = [
            tipo,
            subtipo,
            nombre
        ]
            .filter(function (valor) {
                return valor !== '';
            })
            .join(' - ');

        const badgeAyuno = necesitaAyuno
            ? `
                <span class="badge badge-warning mr-1">
                    <i class="fas fa-utensils mr-1"></i>
                    Ayuno
                </span>
            `
            : '';

        const badgeOrden = necesitaOrden
            ? `
                <span class="badge badge-info">
                    <i class="fas fa-file-medical mr-1"></i>
                    Orden médica
                </span>
            `
            : '';

        contenedor.append(`
            <div class="result-card">

                <strong>
                    ${index + 1}.
                    ${escaparHtml(tituloEstudio)}
                </strong>

                <div class="mt-1">
                    <small class="text-muted">
                        ${
                            estudio.motive
                                ? escaparHtml(estudio.motive)
                                : 'Estudio complementario.'
                        }
                    </small>
                </div>

                <div class="mt-1">
                    ${badgeAyuno}
                    ${badgeOrden}
                </div>

            </div>
        `);
    });
}

    function mostrarEspecialidades(especialidades) {
        const contenedor = $('#listaEspecialidades');

        if (
            !Array.isArray(especialidades) ||
            especialidades.length === 0
        ) {
            contenedor.html(`
                <small class="text-muted">
                    Sin especialidades sugeridas.
                </small>
            `);

            return;
        }

        especialidades.forEach(
            function (especialidad, index) {
                contenedor.append(`
                    <div class="result-card">

                        <strong>
                            ${index + 1}.
                            ${escaparHtml(especialidad.name)}
                        </strong>

                        <div class="mt-1">
                            <small>
                                ${escaparHtml(
                                    especialidad.description ?? ''
                                )}
                            </small>
                        </div>

                    </div>
                `);
            }
        );
    }

    function mostrarDiferenciales(diferenciales) {
        const contenedor = $('#listaDiagnosticos');

        if (
            !Array.isArray(diferenciales) ||
            diferenciales.length === 0
        ) {
            contenedor.html(`
                <small class="text-muted">
                    Sin diagnósticos diferenciales relevantes.
                </small>
            `);

            return;
        }

        diferenciales.forEach(function (diagnostico) {
            const urgencia = String(
                diagnostico.urgency_level ?? 'BAJO'
            ).toUpperCase();

            const badge = claseUrgencia(urgencia);

            const score = Number(
                diagnostico.score ?? 0
            ).toFixed(2);

            const coincidencias = Number(
                diagnostico.matches ?? 0
            );

            const total = Number(
                diagnostico.total_symptoms ?? 0
            );

            contenedor.append(`
                <div class="result-card">

                    <div class="d-flex justify-content-between">

                        <strong>
                            ${escaparHtml(diagnostico.regla)}
                        </strong>

                        <span class="badge badge-${badge}">
                            ${escaparHtml(urgencia)}
                        </span>

                    </div>

                    <small>
                        Compatibilidad:
                        ${score}%

                        &nbsp;•&nbsp;

                        Coincidencias:
                        ${coincidencias}/${total}
                    </small>

                </div>
            `);
        });
    }

    function obtenerMensajeError(xhr) {
        let mensaje =
            'Ocurrió un error al realizar la evaluación.';

        if (!xhr.responseJSON) {
            return mensaje;
        }

        if (xhr.responseJSON.message) {
            mensaje = xhr.responseJSON.message;
        }

        if (xhr.responseJSON.errors) {
            const errores = Object.values(
                xhr.responseJSON.errors
            ).flat();

            if (errores.length > 0) {
                mensaje = errores[0];
            }
        }

        if (xhr.responseJSON.sugerencia) {
            mensaje += '\n\n' +
                xhr.responseJSON.sugerencia;
        }

        return mensaje;
    }

    function ejecutarEvaluacion() {
        const textoSintomas = $('#textoSintomas')
            .val()
            .trim();

        if (textoSintomas.length < 3) {
            alert(
                'Escriba los síntomas del paciente.'
            );

            $('#textoSintomas').focus();

            return;
        }

        const boton = $('#btnEvaluar');

        boton
            .prop('disabled', true)
            .html(`
                <span class="spinner-border
                             spinner-border-sm mr-1">
                </span>
                Evaluando...
            `);

        $.ajax({
            url: urlEvaluar,
            type: 'POST',

            data: {
                _token: csrfToken,

                texto_sintomas: textoSintomas,

                edad:
                    $('#edadPaciente').val(),

                sexo:
                    $('#sexoPaciente').val(),

                embarazada:
                    $('#embarazadaPaciente').val()
            },

            success: function (respuesta) {
                limpiarResultados();

                $('#emptyState').hide();
                $('#resultadoPanel').show();

                mostrarSintomasReconocidos(
                    respuesta.sintomas_detectados
                );

                mostrarAlertas(
                    respuesta.alertas
                );

                mostrarPrincipal(
                    respuesta.principal
                );

                mostrarEstudios(
                    respuesta.estudios
                );

                mostrarEspecialidades(
                    respuesta.especialidades
                );

                mostrarDiferenciales(
                    respuesta.diferenciales
                );
            },

            error: function (xhr) {
                console.error(xhr.responseText);

                alert(
                    obtenerMensajeError(xhr)
                );
            },

            complete: function () {
                boton
                    .prop('disabled', false)
                    .html(`
                        <i class="fas fa-search mr-1"></i>
                        Evaluar paciente
                    `);
            }
        });
    }

    $('#sexoPaciente').on(
        'change',
        function () {
            actualizarEmbarazo();
        }
    );

    $('#textoSintomas').on(
        'input',
        function () {
            $('#contadorCaracteres')
                .text($(this).val().length);
        }
    );

    $('#textoSintomas').on(
        'keydown',
        function (event) {
            if (
                event.ctrlKey &&
                event.key === 'Enter'
            ) {
                event.preventDefault();
                ejecutarEvaluacion();
            }
        }
    );

    $('#btnEvaluar').on(
        'click',
        function () {
            ejecutarEvaluacion();
        }
    );

    actualizarEmbarazo();
});
</script>
@endsection