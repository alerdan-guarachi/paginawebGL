@php
                            $documento43 = $cliente->tramites()->where('subprocedimiento', 'NOTIFICACIÓN DE DICTAMEN')->where('estadodictamen', 'ACEPTADO')->where('tramite', 'PENSIÓN POR MUERTE CON DERIVACIÓN A RETIRO DE APORTES')->first();
                            $documentoRechazado = $cliente->tramites()->where('subprocedimiento', 'NOTIFICACIÓN DE DICTAMEN')->where('estadodictamen', 'RECHAZADO')->where('tramite', 'PENSIÓN POR MUERTE CON DERIVACIÓN A RETIRO DE APORTES')->first();
                        @endphp
                        <div class="col-12 col-md-6 mb-3">
                            @if ($documentoRechazado)
                                <button type="button" class="btn btn-custom btn-block text-center" data-toggle="modal" data-target="#modalDictamenRechazado" disabled>
                                    <div class="d-flex flex-column align-items-center justify-content-center">
                                        <i class="fas fa-balance-scale-left fa-5x mr-2"></i>
                                        <span class="h6 mb-0 btn-block text-center">DICTAMEN ACEPTADO</span>
                                    </div>
                                </button>
                            @elseif ($documento43)
                                <button type="button" class="btn btn-custom btn-block text-center" data-toggle="modal" data-target="#modalDictamenAceptado">
                                    <div class="d-flex flex-column align-items-center justify-content-center">
                                        <i class="fas fa-balance-scale-left fa-5x mr-2"></i>
                                        <span class="h6 mb-0 btn-block text-center">DICTAMEN ACEPTADO</span>
                                    </div>
                                </button>
                                <br>
                                @php
                                    $documento43 = $cliente->tramites()->where('nivelprocedimiento', 'DICTAMEN')->where('subprocedimiento', 'ACEPTACIÓN DE DICTAMEN')->where('tramite', 'PENSIÓN POR MUERTE CON DERIVACIÓN A RETIRO DE APORTES')->first();
                                    $documento44 = $cliente->tramites()->where('nivelprocedimiento', 'DICTAMEN')->where('subprocedimiento', 'FIRMA DE FORMULARIO')->where('tramite', 'PENSIÓN POR MUERTE CON DERIVACIÓN A RETIRO DE APORTES')->first();
                                    $documento45 = $cliente->tramites()->where('nivelprocedimiento', 'DICTAMEN')->where('subprocedimiento', 'FIRMA DE CONTRATO')->where('tramite', 'PENSIÓN POR MUERTE CON DERIVACIÓN A RETIRO DE APORTES')->first();
                                    $documento46 = $cliente->tramites()->where('nivelprocedimiento', 'DICTAMEN')->where('subprocedimiento', 'COBRO DE PENSIÓN')->where('tramite', 'PENSIÓN POR MUERTE CON DERIVACIÓN A RETIRO DE APORTES')->first();
                                    $documento47 = $cliente->tramites()->where('nivelprocedimiento', 'DICTAMEN')->where('subprocedimiento', 'NOTA DE RECHAZO DE TRÁMITE')->where('tramite', 'PENSIÓN POR MUERTE CON DERIVACIÓN A RETIRO DE APORTES')->first();

                                    $accedepensiondictamen = $cliente->tramites()->where('nivelprocedimiento', 'DICTAMEN')->where('subprocedimiento', 'RENUNCIA A REVISIÓN DE DICTAMEN')->where('accesopension', 'SI')->where('tramite', 'PENSIÓN POR MUERTE CON DERIVACIÓN A RETIRO DE APORTES')->first();
                                    $noaccedepensiondictamene6 = $cliente->tramites()->where('nivelprocedimiento', 'DICTAMEN')->where('subprocedimiento', 'RENUNCIA A REVISIÓN DE DICTAMEN (EXCESO DE 6 MESES)')->where('accesopension', 'NO')->where('motivonopension', 'EXCESO DE 6 MESES')->where('tramite', 'PENSIÓN POR MUERTE CON DERIVACIÓN A RETIRO DE APORTES')->first();
                                    $noaccedepensiondictamenfc = $cliente->tramites()->where('nivelprocedimiento', 'DICTAMEN')->where('subprocedimiento', 'RENUNCIA A REVISIÓN DE DICTAMEN (FALTA DE COBERTURA)')->where('accesopension', 'NO')->where('motivonopension', 'FALTA DE COBERTURA')->where('tramite', 'PENSIÓN POR MUERTE CON DERIVACIÓN A RETIRO DE APORTES')->first();
                                    
                                @endphp
                                <div class="text-center">
                                    @if (!$accedepensiondictamen && !$noaccedepensiondictamene6 && !$noaccedepensiondictamenfc)
                                        <span class="mb-0 checkamarillo">
                                            <i class="fas fa-exclamation-triangle"></i> INCOMPLETO
                                        </span>
                                    @elseif ($accedepensiondictamen)
                                        <span class="mb-0 checkverde">
                                            <i class="fas fa-check-circle"></i> SI ACCEDE A PENSIÓN
                                        </span>
                                    @elseif ($noaccedepensiondictamene6)
                                        <span class="mb-0 checkverde">
                                            <i class="fas fa-check-circle"></i> NO ACCEDE A PENSIÓN - EXCESO DE 6 MESES
                                        </span>
                                    @elseif ($noaccedepensiondictamenfc)
                                        <span class="mb-0 checkverde">
                                            <i class="fas fa-check-circle"></i> NO ACCEDE A PENSIÓN - FALTA DE COBERTURA
                                        </span>
                                    @endif
                                </div>
                                @else
                                <button type="button" class="btn btn-custom btn-block text-center" data-toggle="modal" data-target="#modalDictamenAceptado" disabled>
                                    <div class="d-flex flex-column align-items-center justify-content-center">
                                        <i class="fas fa-balance-scale-left fa-5x mr-2"></i>
                                        <span class="h6 mb-0 btn-block text-center">ESTADO DE DICTAMEN PENDIENTE</span>
                                    </div>
                                </button>
                            @endif
                        </div>