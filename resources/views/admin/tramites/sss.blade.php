<div class="tab-pane fade" id="tab-content-4" role="tabpanel" aria-labelledby="tab-4">
    <div class="card-body">
        <div class="row">
            <div class="col-lg-7">
                <div class="card">
                    <div class="card-body">
                        <h5 style="margin-bottom: 15px; font-weight: 700;">DATOS DE LA MISIVA LIBRE</h5>
                        <form action="{{ route('admin.tramites.generaradjuntoyrespuesta', $cliente) }}" method="GET" enctype="multipart/form-data" id="formLibre">
                            {!! Form::hidden('usuarioid4', auth()->user()->id) !!}
                            {!! Form::hidden('usuarioregistro4', auth()->user()->name) !!}
                            {!! Form::hidden('clienteid4', $cliente->id) !!}
                            {!! Form::hidden('clientenombre4', $cliente->nombrecompleto) !!}
                            {!! Form::hidden('apoderado4', $apoderadoAsignado) !!}
                            {!! Form::hidden('idtramite4', $idTramite) !!}
                            <input type="text" class="form-control" id="tramite4" name="tramite4" value="INVALIDEZ" hidden>
                            <input type="date" class="form-control" id="fechasubida4" name="fechasubida4" value="{{ \Carbon\Carbon::now()->toDateString() }}" hidden>
                            @csrf
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="row">
                                        <div class="form-group col-lg-5">
                                            {!! Form::label('nivelprocedimiento4', 'Nivel Procedimiento:') !!}
                                            {!! Form::select('nivelprocedimiento4', [
                                                'SOLICITUD DE INFORMACIÓN TÉCNICO MÉDICO' => 'SOLICITUD DE INFORMACIÓN TÉCNICO MÉDICO',
                                                'COMPRA DE SERVICIOS' => 'COMPRA DE SERVICIOS',
                                                'SOLICITUD DE INFORMACIÓN COMPLEMENTARIA' => 'SOLICITUD DE INFORMACIÓN COMPLEMENTARIA',
                                                'COMPLEMENTACIÓN DEL TRÁMITE' => 'COMPLEMENTACIÓN DEL TRÁMITE',
                                            ], null, ['class' => 'form-control', 'placeholder' => '', 'required' => 'required']) !!}
                                        </div>
                                        <div class="form-group col-lg-5">
                                            {!! Form::label('tipo_pdf4', 'Tipo de Adjunto y Respuesta:') !!}
                                            {!! Form::select('tipo_pdf4', [
                                                'ADJUNTO DE DOCUMENTOS' => 'ADJUNTO DE DOCUMENTOS',
                                                'ADJUNTO DE DOCUMENTACIÓN MÉDICA' => 'ADJUNTO DE DOCUMENTACIÓN MÉDICA',
                                                'ADJUNTO Y RESPUESTA DE INFORME DEL EMPLEADOR' => 'ADJUNTO Y RESPUESTA DE INFORME DEL EMPLEADOR',
                                                'ADJUNTO Y RESPUESTA A NOTIFICACIÓN TMC' => 'ADJUNTO Y RESPUESTA A NOTIFICACIÓN TMC',
                                                'ADJUNTO Y RESPUESTA AL TÉCNICO MÉDICO' => 'ADJUNTO Y RESPUESTA AL TÉCNICO MÉDICO',
                                                'ADJUNTO Y RESPUESTA AL COMPLEMENTARIO' => 'ADJUNTO Y RESPUESTA AL COMPLEMENTARIO',
                                                'CARTA ACLARATIVA' => 'CARTA ACLARATIVA'                                
                                            ], null, ['class' => 'form-control', 'placeholder' => '', 'required' => 'required', 'id' => 'tipoPdfSelect44']) !!}
                                        </div>
                                        <div class="form-group col-lg-2">
                                            {!! Form::label('fechaemitido4', 'Fecha Carta:') !!}
                                            <input type="date" class="form-control" id="fechaactual4" name="fechaactual4" value="{{ \Carbon\Carbon::now()->toDateString() }}" min="{{ \Carbon\Carbon::now()->toDateString() }}">
                                        </div>
                                        <div class="form-group col-lg-4">
                                            {!! Form::label('apoderado4', 'Emisor Apoderado:') !!}
                                            {!! Form::select('apoderado4', 
                                                array_combine($apoderados, $apoderados),
                                                $apoderadoAsignado,
                                                ['class' => 'form-control']
                                            ) !!}
                                        </div>
                                        <div class="form-group col-lg-2">
                                            <label for="fontsize4">Tamaño_Fuente:</label>
                                            <select id="fontsize4" class="form-control">
                                                <option value="12px">9</option>
                                                <option value="13px">10</option>
                                                <option value="14px">11</option>
                                                <option value="15px" selected>12</option>
                                            </select>
                                        </div>
                                        <div class="form-group col-lg-2">
                                            <label for="marginSize4">Márgenes:</label>
                                            <select id="marginSize4" class="form-control">
                                                <option value="1cm 1.5cm 1cm 1.5cm">BAJO</option>
                                                <option value="1.5cm 3cm 1.5cm 3cm" selected>MEDIO</option>
                                                <option value="2cm 3.5cm 2cm 3.5cm">ALTO</option>
                                            </select>
                                        </div>
                                        <div class="form-group col-lg-4" id="nombremedico4Container" style="display: none;">
                                            {!! Form::label('nombremedico4', 'Destinatario:') !!}
                                            {!! Form::text('nombremedico4', null, ['class' => 'form-control', 'placeholder' => '']) !!}
                                        </div>
                                        <div class="form-group col-lg-4" id="cargomedico4Container" style="display: none;">
                                            {!! Form::label('cargomedico4', 'Cargo Destinatario:') !!}
                                            {!! Form::text('cargomedico4', null, ['class' => 'form-control', 'placeholder' => '']) !!}
                                        </div>
                                        <div class="form-group col-lg-4" id="notatecnicomedico4Container" style="display: none;">
                                            {!! Form::label('notatecnicomedico4', 'Nota Cite:') !!}
                                            {!! Form::text('notatecnicomedico4', null, ['class' => 'form-control']) !!}
                                        </div>
                                        <div class="form-group col-lg-4" id="fechanotatecnicomedico4Container" style="display: none;">
                                            {!! Form::label('fechanotatecnicomedico4', 'Fecha Nota Cite:') !!}
                                            {!! Form::date('fechanotatecnicomedico4', null, ['class' => 'form-control', 'value' => '{{ \Carbon\Carbon::now()->toDateString() }}']) !!}
                                        </div>
                                        <div class="form-group col-lg-4" id="documentoadjunto4Container" style="display: none;">
                                            {!! Form::label('documentoadjunto4', 'Documento Adjunto:') !!}
                                            {!! Form::select('documentoadjunto4', [
                                                '' => '',
                                                'CARNET DE IDENTIDAD' => 'CARNET DE IDENTIDAD',
                                                'CERTIFICADO DE MATRIMONIO' => 'CERTIFICADO DE MATRIMONIO',
                                                'CERTIFICADO DE NACIMIENTO' => 'CERTIFICADO DE NACIMIENTO',
                                                'CERTIFICADO DE NACIMIENTO (EXTRANJERO)' => 'CERTIFICADO DE NACIMIENTO (EXTRANJERO)',
                                                'CROQUIS DE DOMICILIO' => 'CROQUIS DE DOMICILIO',
                                            ], null, ['class' => 'form-control']) !!}
                                        </div>
                                        <div class="form-group col-lg-4" id="textocomplementario4Container" style="display: none;">
                                            {!! Form::label('textocomplementario4', 'Texto Complementario:') !!}
                                            {!! Form::text('textocomplementario4', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => 150]) !!}
                                        </div>
                                    </div>

                                    <div class="row" id="tablaEspecialistas44" style="display: none;">
                                        <div class="col-lg-5">
                                            <div id="contenedor_areas" class="mt-3">
                                                @foreach ($programaciones as $fecha => $grupos)
                                                    <div class="card shadow-sm border mb-2">
                                                        
                                                        <div class="card-header py-2 px-3 bg-secondary text-white">
                                                            <button class="btn btn-link text-white text-left w-100 p-0" type="button"
                                                                data-toggle="collapse" data-target="#fecha_{{ \Str::slug($fecha) }}">
                                                                <strong>FECHA BATERIA:</strong> {{ $fecha }}
                                                            </button>
                                                        </div>

                                                        <div id="fecha_{{ \Str::slug($fecha) }}" class="collapse">
                                                            <div class="card-body py-2 px-3">
                                                                @foreach ($grupos->groupBy('areanombre') as $area => $acciones)
                                                                    <div class="card border mb-2">
                                                                        <div class="card-header py-2 px-3 bg-light">
                                                                            <button class="btn btn-sm btn-outline-secondary w-100 text-left p-1"
                                                                                type="button"
                                                                                data-toggle="collapse"
                                                                                data-target="#area_{{ \Str::slug($fecha . '_' . $area) }}">
                                                                                <strong>ÁREA:</strong> {{ $area }}
                                                                            </button>
                                                                        </div>

                                                                        <div id="area_{{ \Str::slug($fecha . '_' . $area) }}" class="collapse" style="margin-top: -30px;">
                                                                            <div class="card-body pt-2 pb-1 px-3">
                                                                                <div class="table-responsive">
                                                                                    <table class="table table-sm table-bordered mb-3" style="white-space: nowrap;">
                                                                                        <thead class="thead-light text-center">
                                                                                            <tr>
                                                                                                <th class="text-center"><input type="checkbox" class="seleccionar-todo-area4" data-area="{{ \Str::slug($fecha . '_' . $area) }}" /></th>
                                                                                                <th>Ver</th>
                                                                                                <th>ID</th>
                                                                                                <th>Estudio/Espec.</th>
                                                                                                <th>Proveedor</th>
                                                                                                <th>Hojas</th>
                                                                                            </tr>
                                                                                        </thead>
                                                                                        <tbody>
                                                                                            @foreach ($acciones as $doc)
                                                                                                <tr>
                                                                                                    <td class="text-center align-middle">
                                                                                                        <input type="checkbox" class="documento-checkbox4"
                                                                                                            data-proveedor="{{ $doc->proveedor_real }}"
                                                                                                            data-area="{{ $doc->areanombre }}"
                                                                                                            data-accion="{{ $doc->accionnombre }}"
                                                                                                            data-hojas="{{ $doc->nro_hojas ?? 0 }}">
                                                                                                    </td>
                                                                                                    <td class="text-center align-middle">
                                                                                                        <a href="{{ url("documentacionclientesita/{$cliente->id}/{$doc->document}") }}"
                                                                                                            target="_blank" class="btn btn-sm btn-verdoc" title="Ver documento">
                                                                                                            <i class="fas fa-eye"></i>
                                                                                                        </a>
                                                                                                        @if(!empty($doc->image))
                                                                                                        <a href="{{ url("documentacionclientesita/{$cliente->id}/{$doc->image}") }}"
                                                                                                        target="_blank" class="btn btn-sm btn-verdoc" title="Ver Imagen 1">
                                                                                                            <i class="fas fa-image"></i>
                                                                                                        </a>
                                                                                                        @endif
                                                                                                        @if(!empty($doc->image2))
                                                                                                        <a href="{{ url("documentacionclientesita/{$cliente->id}/{$doc->image2}") }}"
                                                                                                        target="_blank" class="btn btn-sm btn-verdoc" title="Ver Imagen 2">
                                                                                                            <i class="fas fa-image"></i>
                                                                                                        </a>
                                                                                                        @endif
                                                                                                    </td>
                                                                                                    <td class="align-middle">{{ $doc->doc_id }}</td>
                                                                                                    <td class="align-middle">{{ $doc->accionnombre }}</td>
                                                                                                    <td class="align-middle">{{ $doc->proveedor_real ?? 'Sin proveedor' }}</td>
                                                                                                    <td class="text-center align-middle">
                                                                                                        <span class="badge" style="background-color: #faa625; color: #ffffff; font-size: 0.8rem; padding: 0.2em 0.3em; line-height: 1;">
                                                                                                            {{ $doc->nro_hojas ?? '?' }}
                                                                                                        </span>
                                                                                                    </td>
                                                                                                </tr>
                                                                                            @endforeach
                                                                                        </tbody>
                                                                                    </table>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                                <div class="text-right mt-3">
                                                                    <div class="form-group row justify-content-end align-items-center">
                                                                        <div class="col-sm-8">
                                                                            <select id="tipoDocumento4" name="tipoDocumento4" class="form-control form-control-sm">
                                                                                <option value="">Seleccione una opción...</option>
                                                                                <option value="INFORME MÉDICO DE">INFORME MÉDICO DE</option>
                                                                                <option value="CERTIFICADO MÉDICO DE">CERTIFICADO MÉDICO DE</option>
                                                                                <option value="ESTUDIO DE">ESTUDIO DE</option>
                                                                            </select>
                                                                        </div>
                                                                        <div class="col-auto">
                                                                            <button id="btnAgregarSeleccionados4" type="button" class="btn btn-sm btn-adjuntosrespuestas44">
                                                                                <i class="fas fa-plus"></i>
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                        <div class="col-lg-7">
                                            <div class="table-responsive">
                                                <table class="table table-bordered">
                                                    <thead class="table-secondary">
                                                        <tr style="text-align: center">
                                                            <th class="col-lg-5">ESPECIALISTA</th>
                                                            <th class="col-lg-5">DETALLE DE ESTUDIO/ESPECIALIDAD</th>
                                                            <th class="col-lg-2">CANTIDAD</th>
                                                            <th class="col-lg-2">QUITAR</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="tabla-especialistas4">

                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <script>
                                    $(document).ready(function() {
                                        $('#tipoPdfSelect44').on('change', function() {
                                            $('#notatecnicomedico4Container').hide();
                                            $('#fechanotatecnicomedico4Container').hide();
                                            $('#documentoadjunto4Container').hide();
                                            $('#textocomplementario4Container').hide();
                                            $('#tablaEspecialistas44').hide();
                                            $('#nombremedico4Container').hide();
                                            $('#cargomedico4Container').hide();

                                            var selectedValue = $(this).val();
                                
                                            if (selectedValue === 'ADJUNTO DE DOCUMENTOS') {
                                                $('#notatecnicomedico4Container').show();
                                                $('#fechanotatecnicomedico4Container').show();
                                                $('#documentoadjunto4Container').show();
                                                $('#textocomplementario4Container').show();
                                                $('#nombremedico4Container').show();
                                            } else if (selectedValue === 'ADJUNTO DE DOCUMENTACIÓN MÉDICA') {
                                                $('#notatecnicomedico4Container').show();
                                                $('#fechanotatecnicomedico4Container').show();
                                                $('#tablaEspecialistas44').show();
                                                $('#nombremedico4Container').show();
                                            } else if (selectedValue === 'ADJUNTO Y RESPUESTA DE INFORME DEL EMPLEADOR') {
                                                $('#notatecnicomedico4Container').show();
                                                $('#fechanotatecnicomedico4Container').show();
                                                $('#tablaEspecialistas44').show();
                                                $('#nombremedico4Container').show();
                                            } else if (selectedValue === 'ADJUNTO Y RESPUESTA A NOTIFICACIÓN TMC') {
                                                $('#notatecnicomedico4Container').show();
                                                $('#fechanotatecnicomedico4Container').show();
                                                $('#tablaEspecialistas44').show();
                                                $('#nombremedico4Container').show();
                                            } else if (selectedValue === 'ADJUNTO Y RESPUESTA AL TÉCNICO MÉDICO') {
                                                $('#notatecnicomedico4Container').show();
                                                $('#fechanotatecnicomedico4Container').show();
                                                $('#tablaEspecialistas44').show();
                                                $('#nombremedico4Container').show();
                                            } else if (selectedValue === 'ADJUNTO Y RESPUESTA AL COMPLEMENTARIO') {
                                                $('#notatecnicomedico4Container').show();
                                                $('#fechanotatecnicomedico4Container').show();
                                                $('#tablaEspecialistas44').show();
                                                $('#nombremedico4Container').show();
                                            }
                                        });
                                    });
                                </script>
                            </div>
                            <button type="submit" id="btnGenerarAdjunto44" class="btn btn-sm btn-subirarchivos d-block mx-auto mb-3" target="_blank" style="width: fit-content; margin-top: 20px;">GENERAR MISIVA</button>
                            <script>
                                document.addEventListener('DOMContentLoaded', function() {
                                    const btn = document.getElementById('btnGenerarAdjunto44');
                                    const form = document.getElementById('formLibre');
                                    btn.addEventListener('click', function(e) {
                                        e.preventDefault();
                                        const confirmar = confirm("¿Está seguro de que desea generar la misiva?");
                                        if (confirmar) {
                                            form.submit();
                                        }
                                    });
                                });
                            </script>
                        </form>
                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                const tabla = document.getElementById('tabla-especialistas4');
                                
                                function llenarTablaConSeleccionados4() {
                                    const seleccionados = document.querySelectorAll('.documento-checkbox4:checked');
                                    const tipoDocumento4 = document.getElementById('tipoDocumento4').value.trim();
                                    console.log('Checkbox seleccionados:', seleccionados.length);

                                    const agrupados = {};

                                    seleccionados.forEach(input => {
                                        const proveedor = input.dataset.proveedor;
                                        const area = input.dataset.area;
                                        const hojas = parseInt(input.dataset.hojas || 0);

                                        if (!agrupados[proveedor]) {
                                            agrupados[proveedor] = {
                                                area: area,
                                                totalHojas: 0
                                            };
                                        }

                                        agrupados[proveedor].totalHojas += hojas;
                                    });

                                    let filasActuales = tabla.querySelectorAll('tr').length;
                                    let i = filasActuales + 1;

                                    for (const proveedor in agrupados) {
                                        if (i > 10) break;

                                        const area = agrupados[proveedor].area || '';
                                        const detalle = tipoDocumento4 && area ? `${tipoDocumento4} ${area}` : area || tipoDocumento4 || '';

                                        const hojas = agrupados[proveedor].totalHojas;

                                        const fila = `<tr>
                                            <td><input type="text" name="especialista4${i}" class="form-control" value="${proveedor}" /></td>
                                            <td><input type="text" name="detalle4${i}" class="form-control" value="${detalle}" /></td>
                                            <td><input type="text" name="cantidad4${i}" class="form-control" value="${hojas}" /></td>
                                            <td><button type="button" class="btn btn-outline-danger btn-sm quitar-fila"><i class="fas fa-trash"></i></button></td>
                                        </tr>`;

                                        tabla.insertAdjacentHTML('beforeend', fila);
                                        i++;
                                        // Agrega el evento al nuevo botón
                                        const ultimaFila = tabla.querySelector('tr:last-child');
                                        const btnQuitar = ultimaFila.querySelector('.quitar-fila');

                                        btnQuitar.addEventListener('click', function () {
                                            ultimaFila.remove();
                                        });
                                    }
                                }

                                document.getElementById('btnAgregarSeleccionados4').addEventListener('click', function() {
                                    llenarTablaConSeleccionados4();

                                    const seleccionados = document.querySelectorAll('.documento-checkbox4:checked');
                                    seleccionados.forEach(checkbox => checkbox.checked = false);

                                    const checkboxesSeleccionarTodo = document.querySelectorAll('.seleccionar-todo-area4');
                                    checkboxesSeleccionarTodo.forEach(chk => chk.checked = false);
                                });

                                document.querySelectorAll('.seleccionar-area4').forEach(btn => {
                                    btn.addEventListener('click', function () {
                                        const area = btn.dataset.area;
                                        const checkboxes = document.querySelectorAll(`#area_${area} .documento-checkbox4`);
                                        checkboxes.forEach(c => c.checked = true);
                                    });
                                });

                                document.querySelectorAll('.seleccionar-todo-area4').forEach(checkbox => {
                                    checkbox.addEventListener('change', function () {
                                        const area = this.dataset.area;
                                        const checkboxes = document.querySelectorAll(`#area_${area} .documento-checkbox4`);
                                        checkboxes.forEach(c => c.checked = this.checked);
                                    });
                                });
                            });
                        </script>
                        <script>
                            document.getElementById('fechabateria_select4').addEventListener('change', function () {
                                let todas = document.querySelectorAll('.grupo_fecha');
                                todas.forEach(div => div.style.display = 'none');
                                let seleccionada = this.value;
                                if (seleccionada) {
                                    let slug = seleccionada.replaceAll(/[^a-zA-Z0-9]/g, '-').toLowerCase();
                                    let grupo = document.querySelector('.grupo_fecha_' + slug);
                                    if (grupo) grupo.style.display = 'block';
                                }
                            });
                        </script>
                    </div>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0" style="font-weight: 700;">VISTA PREVIA DEL ADJUNTO/RESPUESTA</h5>
                            <a id="btnActualizarVistaLibre" class="btn btn-sm btn-verdocumento">
                                <i class="fas fa-sync-alt"></i> ACTUALIZAR
                            </a>
                        </div>
                        <iframe id="pdfPreview4" style="width:100%; height:500px; border:1px solid #ddd;"></iframe>
                    </div>
                </div>
            </div>
            <script>
                $(document).ready(function() {
                    function updatePDFPreview4() {
                        let formData4 = $('#formLibre').serialize();
                        let fontSize4 = $('#fontsize4').val() || '15px';
                        formData4 += '&fontsize4=' + encodeURIComponent(fontSize4);
                        let marginSize4 = $('#marginSize4').val() || '1.5cm 3cm 1.5cm 3cm';
                        formData4 += '&marginsize4=' + encodeURIComponent(marginSize4);
                        let clienteId4 = "{{ $cliente->id }}";
                        let nivelProcedimiento4 = $('[name="nivelprocedimiento4"]').val();
                        let tipoPdf4 = $('[name="tipo_pdf4"]').val();
                        if (!nivelProcedimiento4 || !tipoPdf4) {
                            $('#pdfPreview4').attr('src', '');
                            return;
                        }
                        $('#pdfPreview4').attr('src', '{{ url("/preview-adjunto") }}/' + clienteId4 + '?' + formData4);
                    }
                    $('[name="nivelprocedimiento4"], [name="tipo_pdf4"], #fontsize4, #marginSize4').on('change', function() {
                        updatePDFPreview4();
                    });
                    $('#btnActualizarVistaLibre').on('click', function(e) {
                        e.preventDefault();
                        updatePDFPreview4();
                    });
                    updatePDFPreview4();
                });
            </script>
        </div>
    </div>
</div> 