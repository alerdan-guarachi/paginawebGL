<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="{{ asset('css/prestacionescartas.css') }}">
    <style>
        body {
            margin: {{ $marginSize ?? '1.5cm 3cm 1.5cm 3cm' }};
            background: transparent;
        }
        main {
            font-size: {{ $fontSize ?? '15px' }};
        }
        .tipo1, .tipo2, .tipo3, .tipo5, .tipo6, .tipo7, .tipo8, .tipo9, .tipo10 {
            font-size: {{ $fontSize ?? '15px' }};
        }
    </style>
</head>

<body> 
    <main>
        <div class="tipo1">
            @if ($cliente->sucursal === 'SANTA CRUZ')
                Santa Cruz de la Sierra, {{ $fechaactual }}
            @elseif ($cliente->sucursal === 'COCHABAMBA')
                Cochabamba, {{ $fechaactual }}
            @else
                {{ $cliente->sucursal }}, {{ $fechaactual }}
            @endif
        </div>
        <div class="tipo2">Señores:</div>
        <div class="tipo3"><strong>{{ $nombremedico }}</strong></div>
        <div class="tipo3"><strong>{{ $cargomedico }}</strong></div>
        <div class="tipo3"><strong>Autoridad de Fiscalización y Control de Pensiones y Seguros</strong></div>
        <div class="tipo9" style="margin-top: -10px;">Presente. -</div>
        <div class="tipo5"><strong>REF.- CARTA DE RECLAMO DE TRÁMITE DE</strong></div>
        <div class="tipo5">
            
            @php
                $tramite = strtoupper($nombretramite);
            @endphp
            @if ($tramite === 'RECALIFICACIÓN' || $tramite === 'APELACIÓN DE RECALIFICACIÓN' || $tramite === 'RECALIFICACIÓN SEGUNDA SOLICITUD' || $tramite === 'APELACIÓN DE RECALIFICACIÓN SEGUNDA SOLICITUD')
                <strong>PENSIÓN POR INVALIDEZ (RECALIFICACIÓN)</strong>
            @elseif ($tramite === 'INVALIDEZ' || $tramite === 'APELACIÓN' || $tramite === 'SEGUNDA SOLICITUD' || $tramite === 'APELACIÓN SEGUNDA SOLICITUD' || $tramite === 'TERCERA SOLICITUD' || $tramite === 'APELACIÓN TERCERA SOLICITUD')
                <strong>PENSIÓN POR INVALIDEZ</strong>
            @else
                <strong>{{ $nombretramite }}</strong>
            @endif
            
        </div>
        <div class="tipo2" style="margin-bottom: -5px;">Distinguidos Señores:</div>
        <div class="tipo6">
            Yo, @if ($sexo === 'masculino')el Sr.@elseif ($sexo === 'femenino')la Sra.@endif <strong>{{ $nombre }}</strong>, con documento de Identidad <strong>{{ $ci }}{{ $ciexp }}</strong>. En Calidad de Apoderado con N.º de poder 
            <strong>{{ $numeropoder }}</strong>, {{ $afiliadoTexto }} <strong>{{ $cliente->nombrecompleto }}</strong> con <strong>CUA N.º {{ $cliente->nuacua }}</strong>, 
            con <strong>C.I. {{ $cliente->ci }} {{ $cliente->ciexp }}</strong>
        </div>
        @if ($nombretramite !== 'APELACIÓN')
            <div class="tipo6">
                Me permito dirigirme a su Autoridad con la finalidad de presentar mi Reclamo, ya que a la fecha no llega ninguna respuesta de la Gestora 
                Pública de la Seguridad Social de Largo Plazo. Dentro del Trámite de
                
                @if ($tramite === 'RECALIFICACIÓN' || $tramite === 'APELACIÓN DE RECALIFICACIÓN' || $tramite === 'RECALIFICACIÓN SEGUNDA SOLICITUD' || $tramite === 'APELACIÓN DE RECALIFICACIÓN SEGUNDA SOLICITUD')
                    <strong>PENSIÓN POR INVALIDEZ (RECALIFICACIÓN)</strong>
                @elseif ($tramite === 'INVALIDEZ' || $tramite === 'APELACIÓN' || $tramite === 'SEGUNDA SOLICITUD' || $tramite === 'APELACIÓN SEGUNDA SOLICITUD' || $tramite === 'TERCERA SOLICITUD' || $tramite === 'APELACIÓN TERCERA SOLICITUD')
                    <strong>PENSIÓN POR INVALIDEZ</strong>
                @else
                    <strong>{{ $nombretramite }}</strong>
                @endif
                
                @if ($subProcedimiento === 'INGRESO DE TRÁMITE' && $nombretramite !== 'APELACIÓN')
                        en fecha <strong>{!! $fechaingresotramite ?? '<span class="textoedita">FECHA INGRESO TRÁMITE</span>' !!}</strong> 
                        se realizó el Ingresó del Trámite, con fecha de retorno para consultar el avance del Trámite en fecha 
                        <strong>{!! $fecharetornoingresotramite ?? '<span class="textoedita">FECHA RETORNO INGRESO TRÁMITE</span>' !!}</strong>.
                @elseif ($subProcedimiento === 'VALIDACIÓN DE TRÁMITE' && $nombretramite !== 'APELACIÓN')
                        en fecha <strong>{!! $fechaingresotramite ?? '<span class="textoedita">FECHA INGRESO TRÁMITE</span>' !!}</strong> 
                        se realizó el Ingresó del Trámite, con fecha de retorno para consultar por la Validación del Poder en fecha
                        <strong>{!! $fecharetornovalidacion ?? '<span class="textoedita">FECHA RETORNO VALIDACIÓN</span>' !!}</strong>.
                @elseif ($subProcedimiento === 'FIRMA EAP' && $nombretramite !== 'APELACIÓN')
                        en fecha <strong>{!! $fechaingresotramite ?? '<span class="textoedita">FECHA INGRESO TRÁMITE</span>' !!}</strong> 
                        se realizó el Ingresó del Trámite, y en fecha <strong>{!! $fechafirmaeap ?? '<span class="textoedita">FECHA FIRMA EAP</span>' !!}</strong> 
                        se firmó el Certificado de la Verificación del Estado de Ahorro Previsional (Extracto), en el cual se indicó que la fecha 
                        para consultar sobre el avance del trámite será el <strong>{!! $fecharetornofirmaeap ?? '<span class="textoedita">FECHA RETORNO FIRMA EAP</span>' !!}</strong>
                @elseif (($subProcedimiento === 'ADJUNTO DE DOCUMENTOS' || $subProcedimiento === 'ADJUNTO DE DOCUMENTACIÓN MÉDICA') && $nombretramite !== 'APELACIÓN')
                        en fecha <strong>{!! $fechaadjunto ?? '<span class="textoedita">FECHA ADJUNTO</span>' !!}</strong> 
                        se presentó adjunto de documento / documentación médica (<strong>{!! $tipoadjunto ?? '<span class="textoedita">TIPO ADJUNTO</span>' !!}</strong>) 
                        para complementar con el trámite iniciado en fecha <strong>{!! $fechaingresotramite ?? '<span class="textoedita">FECHA INGRESO TRÁMITE</span>' !!}</strong>
                @elseif ($subProcedimiento === 'SOLICITUD DE MODIFICACIÓN DE CITE' && $nombretramite !== 'APELACIÓN')
                        en fecha <strong>{!! $fechamodificacioncite ?? '<span class="textoedita">FECHA MODIFICACIÓN DE CITE</span>' !!}</strong> 
                        se solicito la modificación de la solicitud de Información <strong>{!! $solmodificar3 ?? '<span class="textoedita">SOLICITUD MODIFICAR</span>' !!}</strong> 
                        bajo <strong>NOTA CITE {!! $nronota3 ?? '<span class="textoedita">NOTA CITE</span>' !!}</strong> con fecha 
                        <strong>{!! $fechanota3 ?? '<span class="textoedita">FECHA NOTA CITE</span>' !!}</strong>, ya que esta dirigido a la 
                        <strong>{!! $dirigidoa3 ?? '<span class="textoedita">DIRIGIDO A: CAJA/EMPRESA</span>' !!}</strong> puesto que el afiliado esta 
                        <strong>{!! $estadolab3 ?? '<span class="textoedita">ESTADO LABORAL: ASEGURADO/TRABAJANDO/TRABAJÓ</span>' !!}</strong> en 
                        <strong>{!! $afiliadoa3 ?? '<span class="textoedita">AFILIADO A: CAJA/EMPRESA</span>' !!}</strong>. {{ $textocomplementario3 }}
                @elseif (($subProcedimiento === 'ADJUNTO Y RESPUESTA AL TÉCNICO MÉDICO' || $subProcedimiento === 'ADJUNTO Y RESPUESTA A NOTIFICACIÓN TMC' || $subProcedimiento === 'ADJUNTO Y RESPUESTA DE INFORME DEL EMPLEADOR' || $subProcedimiento === 'ADJUNTO Y RESPUESTA AL COMPLEMENTARIO') && ($nombretramite !== 'APELACIÓN' && $nombretramite !== 'PENSIÓN POR MUERTE'))
                        en fecha <strong>{!! $fechaadjuntodocumento ?? '<span class="textoedita">FECHA ADJUNTO DOCUMENTACIÓN</span>' !!}</strong> 
                        se presentó y adjuntó documentación médica solicitada, en respuesta a la solicitud de Información 
                        <strong>{!! $solmodificar3 ?? '<span class="textoedita">SOLICITUD MODIFICAR</span>' !!}</strong> 
                        bajo <strong>NOTA CITE {!! $nronota3 ?? '<span class="textoedita">NOTA CITE</span>' !!}</strong> con fecha 
                        <strong>{!! $fechanota3 ?? '<span class="textoedita">FECHA NOTA CITE</span>' !!}</strong>.
                @elseif ($subProcedimiento === 'ADJUNTO Y RESPUESTA AL COMPLEMENTARIO' && $nombretramite === 'PENSIÓN POR MUERTE')
                        en fecha <strong>{!! $fechaadjuntodocumento ?? '<span class="textoedita">FECHA ADJUNTO DOCUMENTACIÓN</span>' !!}</strong> 
                        se presentó y adjuntó documentación médica solicitada, en respuesta a la solicitud de Información 
                        <strong>{!! $solmodificar3 ?? '<span class="textoedita">SOLICITUD MODIFICAR</span>' !!}</strong> 
                        bajo <strong>NOTA CITE {!! $nronota3 ?? '<span class="textoedita">NOTA CITE</span>' !!}</strong> con fecha 
                        <strong>{!! $fechanota3 ?? '<span class="textoedita">FECHA NOTA CITE</span>' !!}</strong>.
                @elseif ($subProcedimiento === 'RESPUESTA A TRÁMITE' && $nombretramite === 'RETIRO DE APORTES TOTAL')
                        en fecha <strong>{!! $fechaadjuntodocumento ?? '<span class="textoedita">FECHA ADJUNTO DOCUMENTACIÓN</span>' !!}</strong> 
                        se presentó y adjuntó documentación médica solicitada, en respuesta a la solicitud de Información 
                        <strong>{!! $solmodificar3 ?? '<span class="textoedita">SOLICITUD MODIFICAR</span>' !!}</strong> 
                        bajo <strong>NOTA CITE {!! $nronota3 ?? '<span class="textoedita">NOTA CITE</span>' !!}</strong> con fecha 
                        <strong>{!! $fechanota3 ?? '<span class="textoedita">FECHA NOTA CITE</span>' !!}</strong>.
                @elseif ($subProcedimiento === 'RESPUESTA A TRÁMITE' && $nombretramite === 'RETIRO DE APORTES PARCIAL')
                        en fecha <strong>{!! $fechaadjuntodocumento ?? '<span class="textoedita">FECHA ADJUNTO DOCUMENTACIÓN</span>' !!}</strong> 
                        se presentó y adjuntó documentación médica solicitada, en respuesta a la solicitud de Información 
                        <strong>{!! $solmodificar3 ?? '<span class="textoedita">SOLICITUD MODIFICAR</span>' !!}</strong> 
                        bajo <strong>NOTA CITE {!! $nronota3 ?? '<span class="textoedita">NOTA CITE</span>' !!}</strong> con fecha 
                        <strong>{!! $fechanota3 ?? '<span class="textoedita">FECHA NOTA CITE</span>' !!}</strong>.
                @elseif ($subProcedimiento === 'COMPRA DE SERVICIOS' && $nombretramite !== 'APELACIÓN')
                        en fecha <strong>{!! $fechaconclusionprog3 ?? '<span class="textoedita">FECHA CONCLUSIÓN VALORACIONES</span>' !!}</strong> 
                        el afiliado completó todas las valoraciones médicas solicitadas por el Tribunal Medico Calificador (TMC), 
                        lo que permitió dar curso y respuesta al requerimiento emitido en la
                        <strong>NOTA CITE {!! $nronota3 ?? '<span class="textoedita">NOTA CITE</span>' !!}</strong> con fecha 
                        <strong>{!! $fechanota3 ?? '<span class="textoedita">FECHA NOTA CITE</span>' !!}</strong>, relacionado 
                        con la comunicación de presencia del asegurado dentro de la Compra de Servicios. 
                @elseif ($subProcedimiento === 'SOLICITUD DE COMPRA DE SERVICIOS' && $nombretramite !== 'APELACIÓN')
                        en fecha <strong>{!! $fechasolcompraservicios ?? '<span class="textoedita">FECHA COMPRA DE SERVICIOS</span>' !!}</strong> 
                        se solicito una compra de servicios en respuesta a la Solicitud de Información 
                        <strong>{!! $solmodificar3 ?? '<span class="textoedita">SOLICITUD MODIFICAR</span>' !!}</strong> 
                        con <strong>NOTA CITE {!! $nronota3 ?? '<span class="textoedita">NOTA CITE</span>' !!}</strong> con fecha 
                        <strong>{!! $fechanota3 ?? '<span class="textoedita">FECHA NOTA CITE</span>' !!}</strong>, 
                        {!! $textocomplementario3 ?? '<span class="textoedita">TEXTO COMPLEMENTARIO</span>' !!}
                @endif
            </div>
        @endif
        @if ($nombretramite === 'APELACIÓN')
            <div class="tipo6">
                Me permito dirigirme a su Autoridad con la finalidad de presentar mi Reclamo, ya que a la fecha no llega ninguna respuesta de la Gestora 
                Pública de la Seguridad Social de Largo Plazo. Dentro del Trámite de
                @if ($tramite === 'RECALIFICACIÓN' || $tramite === 'APELACIÓN DE RECALIFICACIÓN' || $tramite === 'RECALIFICACIÓN SEGUNDA SOLICITUD' || $tramite === 'APELACIÓN DE RECALIFICACIÓN SEGUNDA SOLICITUD')
                    <strong>PENSIÓN POR INVALIDEZ (RECALIFICACIÓN)</strong>
                @elseif ($tramite === 'INVALIDEZ' || $tramite === 'APELACIÓN' || $tramite === 'SEGUNDA SOLICITUD' || $tramite === 'APELACIÓN SEGUNDA SOLICITUD' || $tramite === 'TERCERA SOLICITUD' || $tramite === 'APELACIÓN TERCERA SOLICITUD')
                    <strong>PENSIÓN POR INVALIDEZ</strong>
                @else
                    <strong>{{ $nombretramite }}</strong>
                @endif
                en fecha <strong>{!! $fechasolrevdictamen ?? '<span class="textoedita">FECHA SOL. REV. DICTAMEN</span>' !!}</strong>
                @if ($subProcedimiento === 'VALIDACIÓN DE TRÁMITE' || $subProcedimiento === 'SOLICITUD DE REVISIÓN DE DICTAMEN' || $subProcedimiento === 'AUTO DE ADMISIÓN' || $subProcedimiento === 'AUTO DE RECHAZO')
                    se Solicitó la 
                @else
                    se nos notificó con el Auto de Admisión emitido por la APS, con referencia a la 
                @endif
                Revisión del Dictamen <strong>{!! $nrorevisiondictamen ?? '<span class="textoedita">NRO. REVISIÓN DICTAMEN</span>' !!}</strong> 
                de fecha <strong>{!! $fecharevdictamen ?? '<span class="textoedita">FECHA REVISIÓN DICTAMEN</span>' !!}</strong>, 
                al no estar de acuerdo con la Calificación Obtenida del <strong>{!! $porcentajedictamen ?? '<span class="textoedita">PORCENTAJE DICTAMEN</span>' !!}</strong> 
                de Perdida de la Capacidad laboral de origen <strong>{!! $origendictamen ?? '<span class="textoedita">ORIGEN DICTAMEN</span>' !!}</strong> 
                por <strong>{!! $motivoorigendictamen ?? '<span class="textoedita">MOTIVO ORIGEN DICTAMEN</span>' !!}</strong>.
            </div>
        @endif

        <div class="tipo6">
            Con relación al avance del trámite de
            @if ($tramite === 'RECALIFICACIÓN' || $tramite === 'APELACIÓN DE RECALIFICACIÓN' || $tramite === 'RECALIFICACIÓN SEGUNDA SOLICITUD' || $tramite === 'APELACIÓN DE RECALIFICACIÓN SEGUNDA SOLICITUD')
                <strong>PENSIÓN POR INVALIDEZ (RECALIFICACIÓN)</strong>
            @elseif ($tramite === 'INVALIDEZ' || $tramite === 'APELACIÓN' || $tramite === 'SEGUNDA SOLICITUD' || $tramite === 'APELACIÓN SEGUNDA SOLICITUD' || $tramite === 'TERCERA SOLICITUD' || $tramite === 'APELACIÓN TERCERA SOLICITUD')
                <strong>PENSIÓN POR INVALIDEZ</strong>
            @else
                <strong>{{ $nombretramite }}</strong>
            @endif
            se informa que dentro de la Gestora Publica se realizó lo siguiente:
        </div>
        <div class="tipo6" style="margin-top: -15px;">
            <ul style="list-style-type: disc; padding-left: 20px;">
                @if($fecha1sit)
                    <li>
                        En fecha <strong>{!! $fecha1sit ?? '<span class="textoedita">FECHA CARTA</span>' !!}</strong>, se presentó una Carta.
                        @if($fecharesp1sit)
                            Esta fue respondida mediante
                            <strong>NOTA CITE {!! $cite1sit ?? '<span class="textoedita">NOTA CITE</span>' !!}</strong> con fecha 
                            <strong>{!! $fechacite1sit ?? '<span class="textoedita">FECHA NOTA CITE</span>' !!}</strong>, la cual fue recogida el 
                            <strong>{!! $fecharesp1sit ?? '<span class="textoedita">FECHA RESPUESTA</span>' !!}</strong>, 
                            en donde se indicó: {!! $texto1sit ?? '<span class="textoedita">TEXTO COMP.</span>' !!}
                        @endif
                    </li>
                @endif
                @if($fecha2sit)
                    <li>
                        En fecha <strong>{!! $fecha2sit ?? '<span class="textoedita">FECHA</span>' !!}</strong>, se presentó una Segunda Carta.
                        @if($fecharesp2sit)
                            Esta fue respondida mediante
                            <strong>NOTA CITE {!! $cite2sit ?? '<span class="textoedita">NOTA CITE</span>' !!}</strong> con fecha 
                            <strong>{!! $fechacite2sit ?? '<span class="textoedita">FECHA NOTA CITE</span>' !!}</strong>, la cual fue recogida el 
                            <strong>{!! $fecharesp2sit ?? '<span class="textoedita">FECHA RESPUESTA</span>' !!}</strong>, 
                            en donde se indicó: {!! $texto2sit ?? '<span class="textoedita">TEXTO COMP.</span>' !!}
                        @endif
                    </li>
                @endif
                @if($fecha3sit)
                    <li>
                        En fecha <strong>{!! $fecha3sit ?? '<span class="textoedita">FECHA CARTA</span>' !!}</strong>, se presentó una Tercera Carta.
                        @if($fecharesp3sit)
                            Esta fue respondida mediante
                            <strong>NOTA CITE {!! $cite3sit ?? '<span class="textoedita">NOTA CITE</span>' !!}</strong> con fecha 
                            <strong>{!! $fechacite3sit ?? '<span class="textoedita">FECHA NOTA CITE</span>' !!}</strong>, la cual fue recogida el 
                            <strong>{!! $fecharesp3sit ?? '<span class="textoedita">FECHA RESPUESTA</span>' !!}</strong>, 
                            en donde se indicó: {!! $texto3sit ?? '<span class="textoedita">TEXTO COMP.</span>' !!}
                        @endif
                    </li>
                @endif
                @if($fecha1reclamogp)
                    <li>
                        En fecha <strong>{!! $fecha1reclamogp ?? '<span class="textoedita">FECHA CARTA</span>' !!}</strong>, se presentó una Carta de Reclamo.
                        @if($fecharesp1reclamogp)
                            Esta fue respondida mediante
                            <strong>NOTA CITE {!! $cite1reclamogp ?? '<span class="textoedita">NOTA CITE</span>' !!}</strong> con fecha 
                            <strong>{!! $fechacite1reclamogp ?? '<span class="textoedita">FECHA NOTA CITE</span>' !!}</strong>, la cual fue recogida el 
                            <strong>{!! $fecharesp1reclamogp ?? '<span class="textoedita">FECHA RESPUESTA</span>' !!}</strong>, 
                            en donde se indicó: {!! $texto1reclamogp ?? '<span class="textoedita">TEXTO COMP.</span>' !!}
                        @endif
                    </li>
                @endif
                @if($fecha2reclamogp)
                    <li>
                        En fecha <strong>{!! $fecha2reclamogp ?? '<span class="textoedita">FECHA CARTA</span>' !!}</strong>, se presentó una Segunda Carta de Reclamo.
                        @if($fecharesp2reclamogp)
                            Esta fue respondida mediante
                            <strong>NOTA CITE {!! $cite2reclamogp ?? '<span class="textoedita">NOTA CITE</span>' !!}</strong> con fecha 
                            <strong>{!! $fechacite2reclamogp ?? '<span class="textoedita">FECHA NOTA CITE</span>' !!}</strong>, la cual fue recogida el 
                            <strong>{!! $fecharesp2reclamogp ?? '<span class="textoedita">FECHA RESPUESTA</span>' !!}</strong>, 
                            en donde se indicó: {!! $texto2reclamogp ?? '<span class="textoedita">TEXTO COMP.</span>' !!}
                        @endif
                    </li>
                @endif
                @if($fecha3reclamogp)
                    <li>
                        En fecha <strong>{!! $fecha3reclamogp ?? '<span class="textoedita">FECHA CARTA</span>' !!}</strong>, se presentó una Tercera Carta de Reclamo.
                        @if($fecharesp3reclamogp)
                            Esta fue respondida mediante
                            <strong>NOTA CITE {!! $cite3reclamogp ?? '<span class="textoedita">NOTA CITE</span>' !!}</strong> con fecha 
                            <strong>{!! $fechacite3reclamogp ?? '<span class="textoedita">FECHA NOTA CITE</span>' !!}</strong>, la cual fue recogida el 
                            <strong>{!! $fecharesp3reclamogp ?? '<span class="textoedita">FECHA RESPUESTA</span>' !!}</strong>, 
                            en donde se indicó: {!! $texto3reclamogp ?? '<span class="textoedita">TEXTO COMP.</span>' !!}
                        @endif
                    </li>
                @endif
            </ul>
        </div>
        <div class="tipo6">
            Por lo expuesto, y en virtud de la urgencia y la gravedad del caso, solicito a su Autoridad, de la manera más respetuosa y humanitaria, que se realice el seguimiento necesario y se proporcione una respuesta pronta y adecuada al presente caso, dada la angustia y preocupación del afiliado, quien se encuentra en una situación de salud delicada y con un legítimo interés en poder acceder a su pensión.
        </div>
        <div class="tipo6">
            Atte.
        </div>
        <div class="tipo7" style="margin-top: 70px;"><strong>{{ $nombre }}</strong></div>
        <div class="tipo8">C.I. {{ $ci }}{{ $ciexp }}</div>
        <div class="tipo7"><strong>APODERADO</strong></div>
        <div class="tipo8">Teléfono: {{ $telefono }}</div>
    </main>
</body>

</html>
