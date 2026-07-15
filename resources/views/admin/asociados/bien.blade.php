{{-- MODAL SUBIR INFORMES auditoria --}}
                        <div class="modal fade" id="subirinformeauditoriaModal" tabindex="-1" role="dialog">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h3 class="modal-title"
                                            style="color:#94c93b;font-weight:bold;">
                                            SUBIR INFORME
                                        </h3>
                                        <button type="button"
                                            class="close"
                                            data-dismiss="modal">
                                            <span>&times;</span>
                                        </button>
                                    </div>
                                    <div class="info-box-modal">
                                        <p>
                                            <strong>CLIENTE:</strong>
                                            <span id="clienteauditorianombre-texto"></span>
                                        </p>
                                        <p>
                                            <strong>EST. / ESP.:</strong>
                                            <span id="accionnombre-texto2"></span>
                                        </p>
                                    </div>

                                    {!! Form::open(['route' => 'procesar.informeauditoria', 'method' => 'POST', 'files' => true, 'id' => 'formtres']) !!}
                                        {!! Form::text('usuarioid', auth()->user()->id) !!}
                                        {!! Form::text('usuarioregistro', auth()->user()->name) !!}
                                        {!! Form::text('clienteauditoriaid', '', ['id' => 'modal-clienteauditoriaid']) !!}
                                        {!! Form::text('clienteauditorianombre', '', ['id' => 'modal-clienteauditorianombre']) !!}
                                        {!! Form::text('fechabateria', '', ['id' => 'modal-fechabateria3']) !!}
                                        {!! Form::text('accion', '', ['id' => 'modal-accion3']) !!}
                                        {!! Form::text('programacionid', '', ['id' => 'modal-id3']) !!}

                                        @php
                                            $usuariosAdjuntarImagenes = [
                                                'CARLOS ALEJANDRO GUARACHI SANDOVAL',
                                                'PROMED S.R.L.',
                                                'SERRANO PORSTENDOERFER VIVIAN YANETH',
                                                'MARIA RENEE MONTENEGRO ORELLANA'
                                            ];
                                        @endphp

                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-12 mb-3">
                                                    <label><strong>INFORME PDF (Obligatorio)</strong></label>
                                                    <input type="file" name="archivo" id="archivoauditoria" class="form-control" accept=".pdf" required>
                                                </div>
                                                <div class="col-12 mb-3">
                                                    <label><strong>INFORME WORD (Se guardará sin firma)</strong></label>
                                                    <input type="file" name="archivo3" id="archivoauditoriaword" class="form-control" accept=".doc,.docx">
                                                </div>

                                                @if(in_array($nombreusuario, $usuariosAdjuntarImagenes))
                                                    <div class="col-12 mb-3">
                                                        <label><strong>IMAGEN 1</strong></label>
                                                        <input type="file" name="picture" id="picture" class="form-control" accept="image/*">
                                                    </div>
                                                    <div class="col-12 mb-3">
                                                        <label><strong>IMAGEN 2</strong></label>
                                                        <input type="file" name="picture2" id="picture2" class="form-control" accept="image/*">
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <div class="text-center w-100">
                                                {!! Form::submit('SUBIR INFORME', ['class'=>'btn btn-sm btn-crear', 'id'=>'btnSubirInforme3']) !!}
                                            </div>
                                        </div>
                                    {!! Form::close() !!}
                                </div>
                            </div>
                        </div>
                        <script>
                            $(document).on('click', '.btn-subir-informeauditoria', function () {
                                let clienteauditoriaid = $(this).attr('data-clienteauditoriaid');
                                let clienteauditorianombre = $(this).attr('data-clienteauditorianombre');
                                let fechabateria = $(this).attr('data-fechabateria');
                                let accion = $(this).attr('data-accion');
                                let id = $(this).attr('data-id');

                                console.log("DATOS EN BOTON", {
                                    clienteauditoriaid,
                                    clienteauditorianombre,
                                    fechabateria,
                                    accion,
                                    id
                                });

                                $('#modal-clienteauditoriaid').val(clienteauditoriaid);
                                $('#modal-clienteauditorianombre').val(clienteauditorianombre);
                                $('#modal-fechabateria3').val(fechabateria);
                                $('#modal-accion3').val(accion);
                                $('#modal-id3').val(id);
                                $('#clienteauditorianombre-texto').text(clienteauditorianombre);
                                $('#accionnombre-texto2').text(accion);
                            });

                            $('#subirinformeauditoriaModal').on('hidden.bs.modal', function () {
                                if($('#formtres').length){
                                    $('#formtres')[0].reset();
                                }

                                $('#clienteauditorianombre-texto').text('');
                                $('#accionnombre-texto2').text('');

                                $('#modal-clienteauditoriaid').val('');
                                $('#modal-clienteauditorianombre').val('');
                                $('#modal-fechabateria3').val('');
                                $('#modal-accion3').val('');
                                $('#modal-id3').val('');
                            });

                            document.addEventListener('DOMContentLoaded', function () {
                                const form = document.querySelector('#formtres');
                                const btn = document.getElementById('btnSubirInforme3');

                                if(form){
                                    form.addEventListener('submit', function(e){
                                        const archivo = document.getElementById('archivoauditoria').files.length;
                                        if(archivo === 0){
                                            e.preventDefault();
                                            alert('Debes subir el INFORME PDF');
                                            return;
                                        }
                                        btn.value = 'GUARDANDO...';
                                        btn.disabled = true;
                                    });
                                }
                            });
                        </script>

                        <!-- MODAL SUBIR INFORME MULTIPLE auditoria -->
                        <div class="modal fade" id="subirinformemultipleauditoriaModal" tabindex="-1" role="dialog">
                            <div class="modal-dialog modal-lg" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <div class="container text-center">
                                            <div class="row">
                                                <div class="col-12">
                                                    <h3 class="modal-title" id="subirinformemultipleauditoriaModalLabel" style="color: #94c93b; font-weight: bold;">
                                                        SUBIR INFORME
                                                    </h3>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-12">
                                                    <p><strong>CLIENTE:</strong> <span id="clienteauditorianombre2-texto"></span></p>
                                                </div>
                                            </div>
                                        </div>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    {!! Form::open(['route' => 'procesar.informe.multiple.auditoria', 'method' => 'POST', 'files' => true, 'id' => 'formcuatro']) !!}
                                        {!! Form::text('usuarioid2', auth()->user()->id) !!}
                                        {!! Form::text('usuarioregistro2', auth()->user()->name) !!}
                                        {!! Form::text('clienteauditoriaid2', null, ['id' => 'modal2-clienteauditoriaid']) !!}
                                        {!! Form::text('clienteauditorianombre2', null, ['id' => 'modal2-clienteauditorianombre']) !!}
                                        {!! Form::text('fechabateria2', null, ['id' => 'modal2-fechabateria']) !!}
                                        {!! Form::text('acciones_seleccionadas4', null, ['id' => 'acciones_seleccionadas4']) !!}
                                        
                                        @php
                                            $usuariosAdjuntarImagenes = [
                                                'CARLOS ALEJANDRO GUARACHI SANDOVAL',
                                                'PROMED S.R.L.',
                                                'SERRANO PORSTENDOERFER VIVIAN YANETH',
                                                'MARIA RENEE MONTENEGRO ORELLANA'
                                            ];
                                        @endphp

                                        <div class="modal-body">
                                            <div class="row"> 
                                                <div class="col-lg-6">
                                                    <div id="acciones-checkboxes4">
                                                    </div>
                                                </div>
                                                <script>
                                                    $(document).on('submit', '#formcuatro', function () {
                                                        let seleccionadas = $('input[name="acciones[]"]:checked').map(function () {
                                                            return $(this).val();
                                                        }).get();

                                                        $('#acciones_seleccionadas4').val(JSON.stringify(seleccionadas));

                                                        console.log('Acciones seleccionadas:', seleccionadas);
                                                    });
                                                </script>
                                                <div class="col-lg-6">
                                                    <div class="card">
                                                        <div class="card-body">
                                                            <div class="row"> 
                                                                <div class="col-lg-12 mb-3">
                                                                    <label><strong>INFORME PDF (Obligatorio)</strong></label>
                                                                    <input type="file" name="archivo" id="archivoauditoria" class="form-control" accept=".pdf" required/>
                                                                </div>
                                                                @if(in_array($nombreusuario, $usuariosAdjuntarImagenes))
                                                                    <div class="col-12 mb-3">
                                                                        <label><strong>IMAGEN 1</strong></label>
                                                                        <input type="file" name="picture" class="form-control" accept="image/*">
                                                                    </div>
                                                                    <div class="col-12 mb-3">
                                                                        <label><strong>IMAGEN 2</strong></label>
                                                                        <input type="file" name="picture2" class="form-control" accept="image/*">
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <div class="text-center w-100">
                                                {!! Form::submit('SUBIR INFORME', ['class' => 'btn btn-crear', 'id' => 'btnSubirInforme4']) !!}
                                            </div>
                                        </div>
                                    {!! Form::close() !!}
                                </div>
                            </div>
                        </div>
                        <script>
                            $(document).on('click', '.btn-subir-btn-subirinforme-multipleauditoria', function () {

                                let clienteauditoriaid = $(this).data('clienteauditoriaid2');
                                let clienteauditorianombre = $(this).data('clienteauditorianombre2');
                                let fechabateria = $(this).data('fechabateria2');
                                let id = $(this).data('id');

                                $('#clienteauditorianombre2-texto').text(clienteauditorianombre);
                                $('#modal2-clienteauditoriaid').val(clienteauditoriaid);
                                $('#modal2-clienteauditorianombre').val(clienteauditorianombre);
                                $('#modal2-fechabateria').val(fechabateria);
                                $('#acciones-checkboxes4').html('<strong>ESTUDIOS / ESPECIALIDADES</strong>');

                                let selectAll = `
                                    <div>
                                        <input type="checkbox" id="select-all2">
                                        <label for="select-all2">Seleccionar todo</label>
                                    </div>
                                `;
                                $('#acciones-checkboxes4').append(selectAll);

                                @foreach($reservasmedicas as $reserva)
                                    @if(!$reserva->documentacionDisponible)
                                        if ({{ $reserva->clienteauditoriaid }} == clienteauditoriaid && '{{ $reserva->fechabateria }}' == fechabateria) {

                                            let checkbox = `
                                                <div>
                                                    <input type="checkbox" name="acciones[]" value="{{ $reserva->accionnombre }}">
                                                    <label>{{ $reserva->accionnombre }}</label>
                                                </div>
                                            `;
                                            $('#acciones-checkboxes4').append(checkbox);
                                        }
                                    @endif
                                @endforeach

                                $('#select-all2').on('change', function () {
                                    let checked = $(this).prop('checked');
                                    $('#acciones-checkboxes4 input[type="checkbox"]').not(this).prop('checked', checked);
                                });

                            });

                            document.addEventListener('DOMContentLoaded', function () {
                                const form = document.querySelector('#formcuatro');
                                const btn = document.getElementById('btnSubirInforme4');
                                form.addEventListener('submit', function(e) {
                                    btn.value = 'GUARDANDO...';
                                    btn.disabled = true;
                                });
                            });

                            $('#subirinformemultipleauditoriaModal').on('hidden.bs.modal', function () {
                                $('#formcuatro')[0].reset();
                                $('#acciones-checkboxes4').html('');
                                $('#clienteauditorianombre2-texto').text('');
                                $('#modal2-clienteauditoriaid').val('');
                                $('#modal2-clienteauditorianombre').val('');
                                $('#modal2-fechabateria').val('');
                            });
                        </script>