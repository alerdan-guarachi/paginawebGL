<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FICHA MEDICA</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: center;
            font-size: 11px;
        }
        .ancho {
            width: 12.5%;
        }
        .three-rows {
            height: 4.5em; /* Ajusta esta altura según tus necesidades */
            line-height: 1.5em; /* Ajusta la altura de línea según tus necesidades */
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            white-space: normal;
            word-wrap: break-word;
        }
    </style>
</head>
<body>
    <table>
        <tr>
            <th colspan="8" style="text-align: center; background: yellow;">FICHA MEDICA</th>
        </tr>
        <tr>
            <th class="ancho">Fecha de aten.</th>
            <td class="ancho">{{ Session::get('fechaatencion') }}</td>
            <th class="ancho">Empresa</th>
            <td class="ancho" colspan="2">{{ $cliente->empresa }}</td>
            <th class="ancho">Regional</th>
            <td class="ancho" colspan="2">{{ $cliente->lugarnacimiento }}</td>
        </tr>
        <tr>
            <th class="ancho" colspan="2">Antecedentes patologicos</th>
            <td class="ancho three-rows" colspan="6">{{ Session::get('antecedentespatologicos') }}</td>
        </tr>
        <tr>
            <th class="ancho" colspan="2">Nombres y Apellidos</th>
            <td class="ancho" colspan="4">{{ $cliente->nombrecompleto }}</td>
            <th class="ancho">Codigo</th>
            <td class="ancho">{{ $cliente->id }}</td>
        </tr>
        <tr>
            <th class="ancho">Genero</th>
            <td class="ancho">{{ $cliente->genero }}</td>
            <th class="ancho">Fecha de nac.</th>
            <td class="ancho">{{ $cliente->fechanacimiento }}</td>
            <th class="ancho">Edad</th>
            <td class="ancho">{{ $cliente->edad }}</td>
            <th class="ancho">Lugar de nac.</th>
            <td class="ancho">{{ $cliente->lugarnacimiento }}</td>
        </tr>
        <tr>
            <th class="ancho" colspan="2">Residencia</th>
            <td class="ancho" colspan="2">{{ $cliente->lugarnacimiento }}</td>
            <th class="ancho" colspan="2">Grado de instruccion</th>
            <td class="ancho" colspan="2">{{ $cliente->gradoinstruccion }}</td>
        </tr>
        <tr>
            <th class="ancho" colspan="2">Estado civil</th>
            <td class="ancho" colspan="2">{{ $cliente->estadocivil }}</td>
            <th class="ancho" colspan="2">Telefono del paciente</th>
            <td class="ancho" colspan="2">{{ $cliente->telefono }}</td>
        </tr>
        <tr>
            <th class="ancho" colspan="2">Cedula de identidad</th>
            <td class="ancho" colspan="2">{{ $cliente->ci }}</td>
            <th class="ancho" colspan="2">Motivo de consulta</th>
            <td class="ancho" colspan="2"></td>
        </tr>
        <tr>
            <th class="ancho" colspan="2">Direccion domiciliaria</th>
            <td class="ancho" colspan="2">{{ $cliente->direccion }}</td>
            <th class="ancho" colspan="2">Profesion/Ocupacion</th>
            <td class="ancho" colspan="2">{{  $cliente->ocupacion }}</td>
        </tr>
        <tr>
            <th class="ancho" colspan="2">Ente gestor de salud</th>
            <td class="ancho" colspan="2">{{ $cliente->entgestorsalud }}</td>
            <th class="ancho" colspan="2">Actividad laboral</th>
            <td class="ancho" colspan="2">{{  $cliente->estadolaboral }}</td>
        </tr>
        <tr>
            <th colspan="8" style="text-align: center; background: yellow;">DATOS OCUPACIONALES</th>
        </tr>
        <tr>
            <th class="ancho" colspan="2">Periodo de tiempo laboral</th>
            <td class="ancho" colspan="2"></td>
            <th class="ancho" colspan="2">Cargo que desempeña o ultimo cargo desempeñado</th>
            <td class="ancho" colspan="2"></td>
        </tr>
        <tr>
            <th colspan="8" style="text-align: center; background: yellow;">IDENTIFICACION DE PELIGROS</th>
        </tr>
        <tr>
            <th class="ancho" colspan="2">Tipo de peligro</th>
            <th class="ancho">Si / No</th>
            <th class="ancho" colspan="5">Descripcion del peligro</th>
        </tr>
        <tr>
            <th class="ancho" colspan="2">Peligros fisicos</th>
            <td class="ancho">{{ Session::get('peligrosfisicos') }}</td>
            <td class="ancho" colspan="5">{{ Session::get('descripcionpeligrosfisicos') }}</td>
        </tr>
        <tr>
            <th class="ancho" colspan="2">Peligros quimicos</th>
            <td class="ancho">{{ Session::get('peligrosquimicos') }}</td>
            <td class="ancho" colspan="5">{{ Session::get('descripcionpeligrosquimicos') }}</td>
        </tr>
        <tr>
            <th class="ancho" colspan="2">Peligros ergonomicos</th>
            <td class="ancho">{{ Session::get('peligrosergonomicos') }}</td>
            <td class="ancho" colspan="5">{{ Session::get('descripcionpeligrosergonomicos') }}</td>
        </tr>
        <tr>
            <th class="ancho" colspan="2">EPP'S</th>
            <td class="ancho">{{ Session::get('peligrosepps') }}</td>
            <td class="ancho" colspan="5">{{ Session::get('descripcionpeligrosepps') }}</td>
        </tr>
        <tr>
            <th class="ancho" colspan="2">Peligros biologicos</th>
            <td class="ancho">{{ Session::get('peligrosbiologicos') }}</td>
            <td class="ancho" colspan="5">{{ Session::get('descripcionpeligrosbiologicos') }}</td>
        </tr>
        <tr>
            <th class="ancho" colspan="2">Peligros mecanicos</th>
            <td class="ancho">{{ Session::get('peligrosmecanicos') }}</td>
            <td class="ancho" colspan="5">{{ Session::get('descripcionpeligrosmecanicos') }}</td>
        </tr>
        <tr>
            <th class="ancho" colspan="2">Peligros ambientales</th>
            <td class="ancho">{{ Session::get('peligrosambientales') }}</td>
            <td class="ancho" colspan="5">{{ Session::get('descripcionpeligrosambientales') }}</td>
        </tr>
        <tr>
            <th class="ancho" colspan="2">Peligros psicosociales</th>
            <td class="ancho">{{ Session::get('peligrospsicosociales') }}</td>
            <td class="ancho" colspan="5">{{ Session::get('descripcionpeligrospsicosociales') }}</td>
        </tr>
        <tr>
            <th class="ancho" colspan="2">Otros</th>
            <td class="ancho" colspan="6">{{ Session::get('otros') }}</td>
        </tr>
        <tr>
            <th colspan="8" style="text-align: center; background: yellow;">ANTECEDENTES PATOLOGICOS</th>
        </tr>
        <tr>
            <th class="ancho" >Patologia</th>
            <th class="ancho">Si / No</th>
            <th class="ancho" colspan="2">Hace cuanto</th>
            <th class="ancho">Patologia</th>
            <th class="ancho">Si / No</th>
            <th class="ancho" colspan="2">Hace cuanto</th>
        </tr>
        <tr>
            <th colspan="8" style="text-align: center;">OFTALMOLOGIA</th>
        </tr>
        <tr>
            <th class="ancho">Cefalea</th>
            <td class="ancho">{{ Session::get('cefalea') }}</td>
            <td class="ancho" colspan="2">{{ Session::get('hacecuanto001'). ' ' .Session::get('periodotipo001') }}</td>
            <th class="ancho">Defecto visual</th>
            <td class="ancho">{{ Session::get('defectovisual') }}</td>
            <td class="ancho" colspan="2">{{ Session::get('hacecuanto002'). ' ' .Session::get('periodotipo002') }}</td>
        </tr>
        <tr>
            <th class="ancho">Irritacion ocular</th>
            <td class="ancho">{{ Session::get('irritacionocular') }}</td>
            <td class="ancho" colspan="2">{{ Session::get('hacecuanto003'). ' ' .Session::get('periodotipo003') }}</td>
            <th class="ancho">Sequedad ocular</th>
            <td class="ancho">{{ Session::get('sequedadocular') }}</td>
            <td class="ancho" colspan="2">{{ Session::get('hacecuanto004'). ' ' .Session::get('periodotipo004') }}</td>
        </tr>
        <tr>
            <th class="ancho">Lagrimeo</th>
            <td class="ancho">{{ Session::get('lagrimeo') }}</td>
            <td class="ancho" colspan="2">{{ Session::get('hacecuanto005'). ' ' .Session::get('periodotipo005') }}</td>
            <th class="ancho">Vision borrosa</th>
            <td class="ancho">{{ Session::get('visionborrosa') }}</td>
            <td class="ancho" colspan="2">{{ Session::get('hacecuanto006'). ' ' .Session::get('periodotipo006') }}</td>
        </tr>
        <tr>
            <th colspan="8" style="text-align: center;">OTORRINOLARINGOLOGIA</th>
        </tr>
        <tr>
            <th class="ancho">Hipoacuasia</th>
            <td class="ancho">{{ Session::get('hipoacuasia') }}</td>
            <td class="ancho" colspan="2">{{ Session::get('hacecuanto007'). ' ' .Session::get('periodotipo007') }}</td>
            <th class="ancho">Otitis media</th>
            <td class="ancho">{{ Session::get('otitismedia') }}</td>
            <td class="ancho" colspan="2">{{ Session::get('hacecuanto008'). ' ' .Session::get('periodotipo008') }}</td>
        </tr>
        <tr>
            <th class="ancho">Sinusitis</th>
            <td class="ancho">{{ Session::get('sinusitis') }}</td>
            <td class="ancho" colspan="2">{{ Session::get('hacecuanto009'). ' ' .Session::get('periodotipo009') }}</td>
            <th class="ancho">Tinitus</th>
            <td class="ancho">{{ Session::get('tinitus') }}</td>
            <td class="ancho" colspan="2">{{ Session::get('hacecuanto010'). ' ' .Session::get('periodotipo010') }}</td>
        </tr>
        <tr>
            <th colspan="8" style="text-align: center;">NEUROLOGIA</th>
        </tr>
        <tr>
            <th class="ancho">Convulsiones</th>
            <td class="ancho">{{ Session::get('convulsiones') }}</td>
            <td class="ancho" colspan="2">{{ Session::get('hacecuanto011'). ' ' .Session::get('periodotipo011') }}</td>
            <th class="ancho">Epilepsia</th>
            <td class="ancho">{{ Session::get('epilepsia') }}</td>
            <td class="ancho" colspan="2">{{ Session::get('hacecuanto012'). ' ' .Session::get('periodotipo012') }}</td>
        </tr>
        <tr>
            <th class="ancho">Lumbalgia</th>
            <td class="ancho">{{ Session::get('lumbalgia') }}</td>
            <td class="ancho" colspan="2">{{ Session::get('hacecuanto013'). ' ' .Session::get('periodotipo013') }}</td>
            <th class="ancho">Neuropatia</th>
            <td class="ancho">{{ Session::get('neuropatia') }}</td>
            <td class="ancho" colspan="2">{{ Session::get('hacecuanto014'). ' ' .Session::get('periodotipo014') }}</td>
        </tr>
        <tr>
            <th class="ancho">ACV</th>
            <td class="ancho">{{ Session::get('acv') }}</td>
            <td class="ancho" colspan="2">{{ Session::get('hacecuanto015'). ' ' .Session::get('periodotipo015') }}</td>
            <th class="ancho">Cefaleas</th>
            <td class="ancho">{{ Session::get('cefaleaneurologia') }}</td>
            <td class="ancho" colspan="2">{{ Session::get('hacecuanto016'). ' ' .Session::get('periodotipo016') }}</td>
        </tr>
        <tr>
            <th class="ancho">Dismorfia muscular</th>
            <td class="ancho">{{ Session::get('dismorfiamuscular') }}</td>
            <td class="ancho" colspan="2">{{ Session::get('hacecuanto017'). ' ' .Session::get('periodotipo017') }}</td>
            <th class="ancho">Lesion en medula espinal</th>
            <td class="ancho">{{ Session::get('lesionmedulaespinal') }}</td>
            <td class="ancho" colspan="2">{{ Session::get('hacecuanto018'). ' ' .Session::get('periodotipo018') }}</td>
        </tr>
        <tr>
            <th colspan="8" style="text-align: center;">CARDIOLOGIA</th>
        </tr>
        <tr>
            <th class="ancho">HTA</th>
            <td class="ancho">{{ Session::get('hta') }}</td>
            <td class="ancho" colspan="2">{{ Session::get('hacecuanto019'). ' ' .Session::get('periodotipo019') }}</td>
            <th class="ancho">Arritmia</th>
            <td class="ancho">{{ Session::get('arritmia') }}</td>
            <td class="ancho" colspan="2">{{ Session::get('hacecuanto020'). ' ' .Session::get('periodotipo020') }}</td>
        </tr>
        <tr>
            <th class="ancho">Chagas</th>
            <td class="ancho">{{ Session::get('chagas') }}</td>
            <td class="ancho" colspan="2">{{ Session::get('hacecuanto021'). ' ' .Session::get('periodotipo021') }}</td>
            <th class="ancho">Taquicardia</th>
            <td class="ancho">{{ Session::get('taquicardia') }}</td>
            <td class="ancho" colspan="2">{{ Session::get('hacecuanto022'). ' ' .Session::get('periodotipo022') }}</td>
        </tr>
        <tr>
            <th class="ancho">Bradicardia</th>
            <td class="ancho">{{ Session::get('bradicardia') }}</td>
            <td class="ancho" colspan="2">{{ Session::get('hacecuanto023'). ' ' .Session::get('periodotipo023') }}</td>
            <th class="ancho">Bloqueo de rama</th>
            <td class="ancho">{{ Session::get('bloqueoderama') }}</td>
            <td class="ancho" colspan="2">{{ Session::get('hacecuanto024'). ' ' .Session::get('periodotipo024') }}</td>
        </tr>
        <tr>
            <th class="ancho">Stent coronario</th>
            <td class="ancho">{{ Session::get('bradicardia') }}</td>
            <td class="ancho" colspan="2">{{ Session::get('hacecuanto025'). ' ' .Session::get('periodotipo025') }}</td>
            <th class="ancho">Marcapaso</th>
            <td class="ancho">{{ Session::get('bloqueoderama') }}</td>
            <td class="ancho" colspan="2">{{ Session::get('hacecuanto026'). ' ' .Session::get('periodotipo026') }}</td>
        </tr>
        <tr>
            <th colspan="8" style="text-align: center;">ENDICRONOLOGIA</th>
        </tr>
        <tr>
            <th class="ancho">DMT2</th>
            <td class="ancho">{{ Session::get('dmt2') }}</td>
            <td class="ancho" colspan="2">{{ Session::get('hacecuanto027'). ' ' .Session::get('periodotipo027') }}</td>
            <th class="ancho">Lupus eritematoso</th>
            <td class="ancho">{{ Session::get('lupuseritematoso') }}</td>
            <td class="ancho" colspan="2">{{ Session::get('hacecuanto028'). ' ' .Session::get('periodotipo028') }}</td>
        </tr>
        <tr>
            <th class="ancho">Colesterol elevado</th>
            <td class="ancho">{{ Session::get('colesterolelevado') }}</td>
            <td class="ancho" colspan="2">{{ Session::get('hacecuanto029'). ' ' .Session::get('periodotipo029') }}</td>
            <th class="ancho">Hipotiroidismo</th>
            <td class="ancho">{{ Session::get('hipotiroidismo') }}</td>
            <td class="ancho" colspan="2">{{ Session::get('hacecuanto030'). ' ' .Session::get('periodotipo030') }}</td>
        </tr>
        <tr>
            <th class="ancho">Hipertiroidismo</th>
            <td class="ancho">{{ Session::get('hipertiroidismo') }}</td>
            <td class="ancho" colspan="2">{{ Session::get('hacecuanto031'). ' ' .Session::get('periodotipo031') }}</td>
            <th class="ancho"></th>
            <td class="ancho"></td>
            <td class="ancho" colspan="2"></td>
        </tr>
        <tr>
            <th colspan="8" style="text-align: center;">TRAUMATOLOGIA</th>
        </tr>
        <tr>
            <th class="ancho">Artritis</th>
            <td class="ancho">{{ Session::get('artritis') }}</td>
            <td class="ancho" colspan="2">{{ Session::get('hacecuanto032'). ' ' .Session::get('periodotipo032') }}</td>
            <th class="ancho">Dolores articulares</th>
            <td class="ancho">{{ Session::get('doloresarticulares') }}</td>
            <td class="ancho" colspan="2">{{ Session::get('hacecuanto033'). ' ' .Session::get('periodotipo033') }}</td>
        </tr>
        <tr>
            <th class="ancho">Lumbalgia</th>
            <td class="ancho">{{ Session::get('lumbalgia') }}</td>
            <td class="ancho" colspan="2">{{ Session::get('hacecuanto034'). ' ' .Session::get('periodotipo034') }}</td>
            <th class="ancho">Cervicalgia</th>
            <td class="ancho">{{ Session::get('cervicalgia') }}</td>
            <td class="ancho" colspan="2">{{ Session::get('hacecuanto035'). ' ' .Session::get('periodotipo035') }}</td>
        </tr>
        <tr>
            <th class="ancho">Dorsalgia</th>
            <td class="ancho">{{ Session::get('dorsalgia') }}</td>
            <td class="ancho" colspan="2">{{ Session::get('hacecuanto036'). ' ' .Session::get('periodotipo036') }}</td>
            <th class="ancho">Silicosis</th>
            <td class="ancho">{{ Session::get('silicosis') }}</td>
            <td class="ancho" colspan="2">{{ Session::get('hacecuanto037'). ' ' .Session::get('periodotipo037') }}</td>
        </tr>
        <tr>
            <th colspan="8" style="text-align: center;">NEUMOLOGIA</th>
        </tr>
        <tr>
            <th class="ancho">Bronquitis</th>
            <td class="ancho">{{ Session::get('bronquitis') }}</td>
            <td class="ancho" colspan="2">{{ Session::get('hacecuanto038'). ' ' .Session::get('periodotipo038') }}</td>
            <th class="ancho">Asma</th>
            <td class="ancho">{{ Session::get('asma') }}</td>
            <td class="ancho" colspan="2">{{ Session::get('hacecuanto039'). ' ' .Session::get('periodotipo039') }}</td>
        </tr>
        <tr>
            <th class="ancho">Tuberculosis</th>
            <td class="ancho">{{ Session::get('tuberculosis') }}</td>
            <td class="ancho" colspan="2">{{ Session::get('hacecuanto040'). ' ' .Session::get('periodotipo040') }}</td>
            <th class="ancho">EPOC</th>
            <td class="ancho">{{ Session::get('epoc') }}</td>
            <td class="ancho" colspan="2">{{ Session::get('hacecuanto041'). ' ' .Session::get('periodotipo041') }}</td>
        </tr>
        <tr>
            <th class="ancho">Enfisema pulmonar</th>
            <td class="ancho">{{ Session::get('enfisemapulmonar') }}</td>
            <td class="ancho" colspan="2">{{ Session::get('hacecuanto042'). ' ' .Session::get('periodotipo042') }}</td>
            <th class="ancho"></th>
            <td class="ancho"></td>
            <td class="ancho" colspan="2"></td>
        </tr>
        <tr>
            <th colspan="8" style="text-align: center;">GASTROENTEROLOGIA</th>
        </tr>
        <tr>
            <th class="ancho">Gastritis</th>
            <td class="ancho">{{ Session::get('gastritis') }}</td>
            <td class="ancho" colspan="2">{{ Session::get('hacecuanto043'). ' ' .Session::get('periodotipo043') }}</td>
            <th class="ancho">Enf. acido peptica</th>
            <td class="ancho">{{ Session::get('enfacidopeptica') }}</td>
            <td class="ancho" colspan="2">{{ Session::get('hacecuanto044'). ' ' .Session::get('periodotipo044') }}</td>
        </tr>
        <tr>
            <th class="ancho">Colon irritable</th>
            <td class="ancho">{{ Session::get('colonirritable') }}</td>
            <td class="ancho" colspan="2">{{ Session::get('hacecuanto045'). ' ' .Session::get('periodotipo045') }}</td>
            <th class="ancho">Cololetiasis</th>
            <td class="ancho">{{ Session::get('cololetiasis') }}</td>
            <td class="ancho" colspan="2">{{ Session::get('hacecuanto046'). ' ' .Session::get('periodotipo046') }}</td>
        </tr>
        <tr>
            <th class="ancho">Distension</th>
            <td class="ancho">{{ Session::get('distencion') }}</td>
            <td class="ancho" colspan="2">{{ Session::get('hacecuanto047'). ' ' .Session::get('periodotipo047') }}</td>
            <th class="ancho">Calculos biliares</th>
            <td class="ancho">{{ Session::get('calculosbiliares') }}</td>
            <td class="ancho" colspan="2">{{ Session::get('hacecuanto048'). ' ' .Session::get('periodotipo048') }}</td>
        </tr>
        <tr>
            <th class="ancho">Ulcera intestinal</th>
            <td class="ancho">{{ Session::get('ulceraintestinal') }}</td>
            <td class="ancho" colspan="2">{{ Session::get('hacecuanto049'). ' ' .Session::get('periodotipo049') }}</td>
            <th class="ancho">Hepatitis</th>
            <td class="ancho">{{ Session::get('hepatitis') }}</td>
            <td class="ancho" colspan="2">{{ Session::get('hacecuanto050'). ' ' .Session::get('periodotipo050') }}</td>
        </tr>
        <tr>
            <th colspan="8" style="text-align: center;">UROLOGIA / NEFROLOGIA</th>
        </tr>
        <tr>
            <th class="ancho">Urolitiasis</th>
            <td class="ancho">{{ Session::get('urolitiasis') }}</td>
            <td class="ancho" colspan="2">{{ Session::get('hacecuanto051'). ' ' .Session::get('periodotipo051') }}</td>
            <th class="ancho">Infeccion urinaria</th>
            <td class="ancho">{{ Session::get('infeccionurinaria') }}</td>
            <td class="ancho" colspan="2">{{ Session::get('hacecuanto052'). ' ' .Session::get('periodotipo052') }}</td>
        </tr>
        <tr>
            <th class="ancho">Prostatitis</th>
            <td class="ancho">{{ Session::get('prostatitis') }}</td>
            <td class="ancho" colspan="2">{{ Session::get('hacecuanto053'). ' ' .Session::get('periodotipo053') }}</td>
            <th class="ancho">Varicocele</th>
            <td class="ancho">{{ Session::get('varicocele') }}</td>
            <td class="ancho" colspan="2">{{ Session::get('hacecuanto054'). ' ' .Session::get('periodotipo054') }}</td>
        </tr>
        <tr>
            <th colspan="8" style="text-align: center;">DERMATOLOGIA</th>
        </tr>
        <tr>
            <th class="ancho">Dermatitis</th>
            <td class="ancho">{{ Session::get('dermatitis') }}</td>
            <td class="ancho" colspan="2">{{ Session::get('hacecuanto055'). ' ' .Session::get('periodotipo055') }}</td>
            <th class="ancho">Lupus eritematoso</th>
            <td class="ancho">{{ Session::get('lupuseritematosoder') }}</td>
            <td class="ancho" colspan="2">{{ Session::get('hacecuanto056'). ' ' .Session::get('periodotipo056') }}</td>
        </tr>
        <tr>
            <th class="ancho">Vitiligo</th>
            <td class="ancho">{{ Session::get('vitiligo') }}</td>
            <td class="ancho" colspan="2">{{ Session::get('hacecuanto057'). ' ' .Session::get('periodotipo057') }}</td>
            <th class="ancho">Eccema</th>
            <td class="ancho">{{ Session::get('eccema') }}</td>
            <td class="ancho" colspan="2">{{ Session::get('hacecuanto058'). ' ' .Session::get('periodotipo058') }}</td>
        </tr>
        <tr>
            <th class="ancho">Impetigo</th>
            <td class="ancho">{{ Session::get('impetigo') }}</td>
            <td class="ancho" colspan="2">{{ Session::get('hacecuanto059'). ' ' .Session::get('periodotipo059') }}</td>
            <th class="ancho">Psoriasis</th>
            <td class="ancho">{{ Session::get('psoriasis') }}</td>
            <td class="ancho" colspan="2">{{ Session::get('hacecuanto060'). ' ' .Session::get('periodotipo060') }}</td>
        </tr>
        <tr>
            <th colspan="8" style="text-align: center;">CIRUGIA VASCULAR</th>
        </tr>
        <tr>
            <th class="ancho">Varices en piernas</th>
            <td class="ancho">{{ Session::get('varicesenpiernas') }}</td>
            <td class="ancho" colspan="2">{{ Session::get('hacecuanto061'). ' ' .Session::get('periodotipo061') }}</td>
            <th class="ancho">Celulitis en MMII</th>
            <td class="ancho">{{ Session::get('celulitisenmmii') }}</td>
            <td class="ancho" colspan="2">{{ Session::get('hacecuanto062'). ' ' .Session::get('periodotipo062') }}</td>
        </tr>
        <tr>
            <th class="ancho">Trombosis</th>
            <td class="ancho">{{ Session::get('trombosis') }}</td>
            <td class="ancho" colspan="2">{{ Session::get('hacecuanto063'). ' ' .Session::get('periodotipo063') }}</td>
            <th class="ancho"></th>
            <td class="ancho"></td>
            <td class="ancho" colspan="2"></td>
        </tr>
        <tr>
            <th colspan="8" style="text-align: center;">REUMATOLOGIA</th>
        </tr>
        <tr>
            <th class="ancho">Artritis reumatoidea</th>
            <td class="ancho">{{ Session::get('artritisreumatoidea') }}</td>
            <td class="ancho" colspan="2">{{ Session::get('hacecuanto064'). ' ' .Session::get('periodotipo064') }}</td>
            <th class="ancho">Artrosis</th>
            <td class="ancho">{{ Session::get('artrosisreu') }}</td>
            <td class="ancho" colspan="2">{{ Session::get('hacecuanto065'). ' ' .Session::get('periodotipo065') }}</td>
        </tr>
        <tr>
            <th class="ancho">Psoriasis</th>
            <td class="ancho">{{ Session::get('psoriasisreu') }}</td>
            <td class="ancho" colspan="2">{{ Session::get('hacecuanto066'). ' ' .Session::get('periodotipo066') }}</td>
            <th class="ancho">Lupus eritematoso</th>
            <td class="ancho">{{ Session::get('lupuseritematosoreu') }}</td>
            <td class="ancho" colspan="2">{{ Session::get('hacecuanto067'). ' ' .Session::get('periodotipo067') }}</td>
        </tr>
        <tr>
            <th class="ancho">Gota</th>
            <td class="ancho">{{ Session::get('gota') }}</td>
            <td class="ancho" colspan="2">{{ Session::get('hacecuanto068'). ' ' .Session::get('periodotipo068') }}</td>
            <th class="ancho">Espondilitis anquilosante</th>
            <td class="ancho">{{ Session::get('espondilitisanquilosante') }}</td>
            <td class="ancho" colspan="2">{{ Session::get('hacecuanto069'). ' ' .Session::get('periodotipo069') }}</td>
        </tr>
        <tr>
            <th class="ancho">Fibromialgia</th>
            <td class="ancho">{{ Session::get('fibromialgia') }}</td>
            <td class="ancho" colspan="2">{{ Session::get('hacecuanto070'). ' ' .Session::get('periodotipo070') }}</td>
            <th class="ancho">Reumatismo</th>
            <td class="ancho">{{ Session::get('reumatismo') }}</td>
            <td class="ancho" colspan="2">{{ Session::get('hacecuanto071'). ' ' .Session::get('periodotipo071') }}</td>
        </tr>
        <tr>
            <th colspan="8" style="text-align: center;">ONCOLOGIA</th>
        </tr>
        <tr>
            <th class="ancho">Cancer</th>
            <td class="ancho">{{ Session::get('cancer') }}</td>
            <td class="ancho" colspan="2">{{ Session::get('hacecuanto072'). ' ' .Session::get('periodotipo072') }}</td>
            <th class="ancho"></th>
            <td class="ancho"></td>
            <td class="ancho" colspan="2"></td>
        </tr>
        <tr>
            <th colspan="8" style="text-align: center;">CIRUGIA GENERAL</th>
        </tr>
        <tr>
            <th class="ancho">Hernia inguinal</th>
            <td class="ancho">{{ Session::get('herniainguinal') }}</td>
            <td class="ancho" colspan="2">{{ Session::get('hacecuanto073'). ' ' .Session::get('periodotipo073') }}</td>
            <th class="ancho">Hernia umbilical</th>
            <td class="ancho">{{ Session::get('herniaumbilical') }}</td>
            <td class="ancho" colspan="2">{{ Session::get('hacecuanto074'). ' ' .Session::get('periodotipo074') }}</td>
        </tr>
        <tr>
            <th colspan="8" style="text-align: center;">GINECOLOGIA</th>
        </tr>
        <tr>
            <th class="ancho">Endometriosis</th>
            <td class="ancho">{{ Session::get('endometriosis') }}</td>
            <td class="ancho" colspan="2">{{ Session::get('hacecuanto075'). ' ' .Session::get('periodotipo075') }}</td>
            <th class="ancho">Miomas uterinos</th>
            <td class="ancho">{{ Session::get('miomasuterinos') }}</td>
            <td class="ancho" colspan="2">{{ Session::get('hacecuanto076'). ' ' .Session::get('periodotipo076') }}</td>
        </tr>
        <tr>
            <th class="ancho">Polipos uterinos</th>
            <td class="ancho">{{ Session::get('poliposuterinos') }}</td>
            <td class="ancho" colspan="2">{{ Session::get('hacecuanto077'). ' ' .Session::get('periodotipo077') }}</td>
            <th class="ancho">Quistes de ovario</th>
            <td class="ancho">{{ Session::get('quistesdeovarios') }}</td>
            <td class="ancho" colspan="2">{{ Session::get('hacecuanto078'). ' ' .Session::get('periodotipo078') }}</td>
        </tr>
        <tr>
            <th class="ancho">Prolapso genital</th>
            <td class="ancho">{{ Session::get('prolapsogenital') }}</td>
            <td class="ancho" colspan="2">{{ Session::get('hacecuanto079'). ' ' .Session::get('periodotipo079') }}</td>
            <th class="ancho"></th>
            <td class="ancho"></td>
            <td class="ancho" colspan="2"></td>
        </tr>


        <tr>
            <th class="ancho" colspan="2">Fracturas</th>
            <td class="ancho" colspan="2">{{ Session::get('fracturas') }}</td>
            <th class="ancho" colspan="2">Alergias</th>
            <td class="ancho" colspan="2">{{ Session::get('alergias') }}</td>
        </tr>
        <tr>
            <th class="ancho" colspan="2">Transfusiones</th>
            <td class="ancho" colspan="2">{{ Session::get('transfusiones') }}</td>
            <th class="ancho" colspan="2">Intoxicaciones</th>
            <td class="ancho" colspan="2">{{ Session::get('intoxicaciones') }}</td>
        </tr>
        <tr>
            <th class="ancho" colspan="2">Enfermedades de transmision sexual</th>
            <td class="ancho" colspan="2">{{ Session::get('enfermedadessexual') }}</td>
            <th class="ancho" colspan="2">Alteraciones en vision</th>
            <td class="ancho" colspan="2">{{ Session::get('alteracionvision') }}</td>
        </tr>
        <tr>
            <th class="ancho" colspan="2">Alteraciones en oido</th>
            <td class="ancho" colspan="2">{{ Session::get('alteracionoido') }}</td>
            <th class="ancho" colspan="2">Enfermedades del aparato digestivo</th>
            <td class="ancho" colspan="2">{{ Session::get('enfermedaddigestivo') }}</td>
        </tr>
        <tr>
            <th class="ancho" colspan="2">Enfermedades del aparato urogenital</th>
            <td class="ancho" colspan="2">{{ Session::get('enfermedadurogenital') }}</td>
            <th class="ancho" colspan="4"></th>
        </tr>
        <tr>
            <th colspan="8" style="text-align: center; background: yellow;">HISTORIA DE LA ENFERMEDAD ACTUAL</th>
        </tr>
        <tr>
            <td class="ancho" colspan="8">{{ Session::get('historiaenfermedad') }}</td>
        </tr>
        <tr>
            <th colspan="8" style="text-align: center; background: yellow;">ANTECEDENTES PERSONALES NO PATOLOGICOS</th>
        </tr>
        <tr>
            <th colspan="8" style="text-align: center; background: yellow;">HABITOS TOXICOS</th>
        </tr>
        <tr>
            <th class="ancho" colspan="4">CIGARRILLOS</th>
            <th class="ancho" colspan="2">Estado</th>
            <td class="ancho" colspan="2">{{ Session::get('estadocigarrillos') }}</td>
        </tr>
        <tr>
            <th class="ancho">Tiempo de suspension</th>
            <td class="ancho">{{ Session::get('suspcigarillos'). ' ' .Session::get('tiemposuspcigarillos') }}</td>
            <th class="ancho">Frecuencia</th>
            <td class="ancho">{{ Session::get('freccigarillos'). ' ' .Session::get('tiempofreccigarillos') }}</td>
            <th class="ancho">Tiempo de consumo</th>
            <td class="ancho">{{ Session::get('consumocigarillos'). ' ' .Session::get('tiempoconscigarillos') }}</td>
            <th class="ancho">Nro. cigarrillos/dia</th>
            <td class="ancho">{{ Session::get('numerocigarrillos') }}</td>
        </tr>
        <tr>
            <th class="ancho" colspan="4">BEBIDAS ALCOHOLICAS</th>
            <th class="ancho" colspan="2">Estado</th>
            <td class="ancho" colspan="2">{{ Session::get('estadoalcoholismo') }}</td>
        </tr>
        <tr>
            <th class="ancho">Tiempo de suspension</th>
            <td class="ancho">{{ Session::get('suspensionalcohol'). ' ' .Session::get('tiemposuspalcohol') }}</td>
            <th class="ancho">Frecuencia</th>
            <td class="ancho">{{ Session::get('frecuenciaalcohol'). ' ' .Session::get('tiempofrecalcohol') }}</td>
            <th class="ancho">Tiempo de consumo</th>
            <td class="ancho">{{ Session::get('consumoalcohol'). ' ' .Session::get('tiempoconsalcohol') }}</td>
            <th class="ancho">Tipo de bebida</th>
            <td class="ancho">{{ Session::get('tipobebida') }}</td>
        </tr>
        <tr>
            <th class="ancho" colspan="4">MASTICA COCA</th>
            <th class="ancho" colspan="2">Estado</th>
            <td class="ancho" colspan="2">{{ Session::get('estadococa') }}</td>
        </tr>
        <tr>
            <th class="ancho" colspan="2">Tiempo de suspension</th>
            <td class="ancho" colspan="2">{{ Session::get('consumococa'). ' ' .Session::get('tiempoconscoca') }}</td>
            <th class="ancho" colspan="2">Frecuencia</th>
            <td class="ancho" colspan="2">{{ Session::get('frecuenciacoca'). ' ' .Session::get('tiempofreccoca') }}</td>
        </tr>
        <tr>
            <th class="ancho" colspan="4">CONSUME MEDICAMENTOS</th>
            <th class="ancho" colspan="2">Estado</th>
            <td class="ancho" colspan="2">{{ Session::get('estadomedicamento') }}</td>
        </tr>
        <tr>
            <th class="ancho" colspan="2">Cuales</th>
            <td class="ancho" colspan="6">{{ Session::get('cualesmedicamentos') }}</td>
        </tr>
        <tr>
            <th colspan="8" style="text-align: center; background: yellow;">ADICIONAL</th>
        </tr>
        <tr>
            <th class="ancho">Vivienda</th>
            <td class="ancho" colspan="3">{{ Session::get('vivienda') }}</td>
            <th class="ancho">Alimentacion</th>
            <td class="ancho" colspan="3">{{ Session::get('alimentacion') }}</td>
        </tr>
        <tr>
            <th class="ancho">Drogas</th>
            <td class="ancho" colspan="3">{{ Session::get('drogas') }}</td>
            <th class="ancho">Deporte</th>
            <td class="ancho" colspan="3">{{ Session::get('deporte') }}</td>
        </tr>
        <tr>
            <th class="ancho">Catarsis</th>
            <td class="ancho" colspan="3">{{ Session::get('catarsis') }}</td>
            <th class="ancho">Diuresis</th>
            <td class="ancho" colspan="3">{{ Session::get('diuresis') }}</td>
        </tr>
        <tr>
            <th class="ancho">Combe</th>
            <td class="ancho" colspan="3">{{ Session::get('combe') }}</td>
            <th class="ancho"></th>
            <td class="ancho" colspan="3"></td>
        </tr>
        <tr>
            <th colspan="8" style="text-align: center;">ANTECEDENTES QUIRURGICOS</th>
        </tr>
        <tr>
            <th class="ancho" colspan="5">Antecedente</th>
            <th class="ancho" colspan="3">Periodo de tiempo</th>
        </tr>
        <tr>
            <td class="ancho" colspan="5">{{ Session::get('atcquirurgico1') }}</td>
            <td class="ancho" colspan="3">{{ Session::get('atcperiodo1') }}</td>
        </tr>
        <tr>
            <td class="ancho" colspan="5">{{ Session::get('atcquirurgico2') }}</td>
            <td class="ancho" colspan="3">{{ Session::get('atcperiodo2') }}</td>
        </tr>
        <tr>
            <td class="ancho" colspan="5">{{ Session::get('atcquirurgico3') }}</td>
            <td class="ancho" colspan="3">{{ Session::get('atcperiodo3') }}</td>
        </tr>
        <tr>
            <th colspan="8" style="text-align: center;">ANTECEDENTES TRAUMATICOS</th>
        </tr>
        <tr>
            <th class="ancho" colspan="5">Antecedente</th>
            <th class="ancho" colspan="3">Periodo de tiempo</th>
        </tr>
        <tr>
            <td class="ancho" colspan="5">{{ Session::get('atctrau1') }}</td>
            <td class="ancho" colspan="3">{{ Session::get('atctrauperiodo1') }}</td>
        </tr>
        <tr>
            <td class="ancho" colspan="5">{{ Session::get('atctrau2') }}</td>
            <td class="ancho" colspan="3">{{ Session::get('atctrauperiodo2') }}</td>
        </tr>
        <tr>
            <td class="ancho" colspan="5">{{ Session::get('atctrau3') }}</td>
            <td class="ancho" colspan="3">{{ Session::get('atctrauperiodo3') }}</td>
        </tr>
        <tr>
            <th colspan="8" style="text-align: center; background: yellow;">ANTECEDENTES FAMILIARES</th>
        </tr>
        <tr>
            <th class="ancho">Familiar</th>
            <th class="ancho">Estado de salud</th>
            <th class="ancho">Edad vivo</th>
            <th class="ancho">Edad al fallecer</th>
            <th class="ancho" colspan="2">Causa de fallecimiento</th>
            <th class="ancho" colspan="2">Observaciones/Enfermedades</th>
        </tr>
        <tr>
            <th class="ancho">Padre</th>
            <td class="ancho">{{ Session::get('estadosaludpadre') }}</td>
            <td class="ancho">{{ Session::get('edadvivopadre') }}</td>
            <td class="ancho">{{ Session::get('edadfallecidopadre') }}</td>
            <td class="ancho" colspan="2">{{ Session::get('causafallecidopadre') }}</td>
            <td class="ancho" colspan="2">{{ Session::get('enfermedadespadre') }}</td>
        </tr>
        <tr>
            <th class="ancho">Madre</th>
            <td class="ancho">{{ Session::get('estadosaludmadre') }}</td>
            <td class="ancho">{{ Session::get('edadvivomadre') }}</td>
            <td class="ancho">{{ Session::get('edadfallecemadre') }}</td>
            <td class="ancho" colspan="2">{{ Session::get('causafallecemadre') }}</td>
            <td class="ancho" colspan="2">{{ Session::get('enfermedadesmadre') }}</td>
        </tr>
        <tr>
            <th class="ancho">Hermanos/as</th>
            <td class="ancho">{{ Session::get('cantidadhermanos') }}</td>
            <td class="ancho">{{ Session::get('hermanovivo') }}</td>
            <td class="ancho">{{ Session::get('hermanofallece') }}</td>
            <td class="ancho" colspan="2">{{ Session::get('caudafallecehermano') }}</td>
            <td class="ancho" colspan="2">{{ Session::get('enfermedadeshermano') }}</td>
        </tr>
        <tr>
            <th class="ancho">Esposo/a</th>
            <td class="ancho">{{ Session::get('estadosaludesposo') }}</td>
            <td class="ancho">{{ Session::get('edadvivoesposo') }}</td>
            <td class="ancho">{{ Session::get('edadfalleceesposo') }}</td>
            <td class="ancho" colspan="2">{{ Session::get('causafalleceesposo') }}</td>
            <td class="ancho" colspan="2">{{ Session::get('enfermedadesesposo') }}</td>
        </tr>
        <tr>
            <th class="ancho">Hijos/as</th>
            <td class="ancho">{{ Session::get('cantidadhijos') }}</td>
            <td class="ancho">{{ Session::get('hijosvivo') }}</td>
            <td class="ancho">{{ Session::get('hijosfallece') }}</td>
            <td class="ancho" colspan="2">{{ Session::get('causafallecehijos') }}</td>
            <td class="ancho" colspan="2">{{ Session::get('enfermedadeshijos') }}</td>
        </tr>
        <tr>
            <th colspan="8" style="text-align: center; background: yellow;">ANTECEDENTES FAMILIARES ADICIONALES</th>
        </tr>
        <tr>
            <th class="ancho">Antecedente</th>
            <th class="ancho">SI / NO</th>
            <th class="ancho" colspan="2">Hace cuanto</th>
            <th class="ancho">Antecedente</th>
            <th class="ancho">SI / NO</th>
            <th class="ancho" colspan="2">Hace cuanto</th>
        </tr>
        <tr>
            <td class="ancho">HTA</td>
            <td class="ancho">{{ Session::get('afhta') }}</td>
            <td class="ancho" colspan="2">{{ Session::get('hacecuanto30'). ' ' .Session::get('periodotipo30') }}</td>
            <td class="ancho">Infarto</td>
            <td class="ancho">{{ Session::get('afinfarto') }}</td>
            <td class="ancho" colspan="2">{{ Session::get('hacecuanto31'). ' ' .Session::get('periodotipo31') }}</td>
        </tr>
        <tr>
            <td class="ancho">ACV</td>
            <td class="ancho">{{ Session::get('afacv') }}</td>
            <td class="ancho" colspan="2">{{ Session::get('hacecuanto32'). ' ' .Session::get('periodotipo32') }}</td>
            <td class="ancho">Alergias</td>
            <td class="ancho">{{ Session::get('afalergias') }}</td>
            <td class="ancho" colspan="2">{{ Session::get('hacecuanto33'). ' ' .Session::get('periodotipo33') }}</td>
        </tr>
        <tr>
            <td class="ancho">Ulcera peptica</td>
            <td class="ancho">{{ Session::get('afulcerapeptica') }}</td>
            <td class="ancho" colspan="2">{{ Session::get('hacecuanto34'). ' ' .Session::get('periodotipo34') }}</td>
            <td class="ancho">Diabetes</td>
            <td class="ancho">{{ Session::get('afdiabetes') }}</td>
            <td class="ancho" colspan="2">{{ Session::get('hacecuanto35'). ' ' .Session::get('periodotipo35') }}</td>
        </tr>
        <tr>
            <td class="ancho">Asma</td>
            <td class="ancho">{{ Session::get('afasma') }}</td>
            <td class="ancho" colspan="2">{{ Session::get('hacecuanto36'). ' ' .Session::get('periodotipo36') }}</td>
            <td class="ancho">TBC</td>
            <td class="ancho">{{ Session::get('aftbc') }}</td>
            <td class="ancho" colspan="2">{{ Session::get('hacecuanto37'). ' ' .Session::get('periodotipo37') }}</td>
        </tr>
        <tr>
            <td class="ancho">Artritis</td>
            <td class="ancho">{{ Session::get('afartritis') }}</td>
            <td class="ancho" colspan="2">{{ Session::get('hacecuanto38'). ' ' .Session::get('periodotipo38') }}</td>
            <td class="ancho">Enfermedad mental</td>
            <td class="ancho">{{ Session::get('afenfermedadmental') }}</td>
            <td class="ancho" colspan="2">{{ Session::get('hacecuanto39'). ' ' .Session::get('periodotipo39') }}</td>
        </tr>
        <tr>
            <td class="ancho">Cancer</td>
            <td class="ancho">{{ Session::get('afcancer') }}</td>
            <td class="ancho" colspan="2">{{ Session::get('hacecuanto40'). ' ' .Session::get('periodotipo40') }}</td>
            <td class="ancho">Otros</td>
            <td class="ancho">{{ Session::get('afotros') }}</td>
            <td class="ancho" colspan="2">{{ Session::get('hacecuanto41'). ' ' .Session::get('periodotipo41') }}</td>
        </tr>
        <tr>
            <th colspan="8" style="text-align: center; background: yellow;">ANTECEDENTES LABORALES</th>
        </tr>
        <tr>
            <th class="ancho" colspan="2">Fecha inicial</th>
            <td class="ancho" colspan="2">{{ Session::get('fechainicioatclab') }}</td>
            <th class="ancho" colspan="2">Fecha final</th>
            <td class="ancho" colspan="2">{{ Session::get('fechafinalatclab') }}</td>
        </tr>
        <tr>
            <th class="ancho">Numero</th>
            <th class="ancho" colspan="3">Caracteristicas importantes y exigencias</th>
            <th class="ancho" colspan="2">Denuncia de accidente</th>
            <th class="ancho" colspan="2">Afeccion a la capacidad laboral2</th>
        </tr>
        <tr>
            <th class="ancho">1</th>
            <td class="ancho" colspan="3">{{ Session::get('caracatclaboral1') }}</td>
            <td class="ancho" colspan="2">{{ Session::get('denunatclaboral1') }}</td>
            <td class="ancho" colspan="2">{{ Session::get('atenatclaboral1') }}</td>
        </tr>
        <tr>
            <th class="ancho">2</th>
            <th class="ancho" colspan="3">{{ Session::get('atclaboral2') }}</th>
            <td class="ancho" colspan="2">{{ Session::get('denunatclaboral2') }}</td>
            <td class="ancho" colspan="2">{{ Session::get('atenatclaboral2') }}</td>
        </tr>
        <tr>
            <th class="ancho">3</th>
            <td class="ancho" colspan="3">{{ Session::get('atclaboral3') }}</td>
            <td class="ancho" colspan="2">{{ Session::get('denunatclaboral3') }}</td>
            <td class="ancho" colspan="2">{{ Session::get('atenatclaboral3') }}</td>
        </tr>
        <tr>
            <th colspan="8" style="text-align: center; background: yellow;">EXAMEN FISICO</th>
        </tr>
        <tr>
            <th class="ancho" colspan="2">Examen fisico general</th>
            <td class="ancho" colspan="6">{{ Session::get('examenfisicogeneral') }}</td>
        </tr>
        <tr>
            <th class="ancho" colspan="2">Llenado capilar</th>
            <td class="ancho" colspan="6">{{ Session::get('llenadocapilar') }}</td>
        </tr>
        <tr>
            <th class="ancho" colspan="2">Lateralidad</th>
            <td class="ancho" colspan="6">{{ Session::get('lateralidad') }}</td>
        </tr>
        <tr>
            <th colspan="8" style="text-align: center;">SIGNOS VITALES</th>
        </tr>
        <tr>
            <th class="ancho">Pulso</th>
            <td class="ancho">{{ Session::get('pulso') }} lpm</td>
            <th class="ancho">satO2</th>
            <td class="ancho">{{ Session::get('satO2') }} %</td>
            <th class="ancho">F. respiracion</th>
            <td class="ancho">{{ Session::get('frespiracion') }} rpm</td>
            <th class="ancho">Temperatura</th>
            <td class="ancho">{{ Session::get('temperatura') }} °C</td>
        </tr>
        <tr>
            <th class="ancho">Presion arterial</th>
            <td class="ancho">{{ Session::get('presionarterial') }} mmHg</td>
            <th class="ancho" colspan="6"></th>
        </tr>
        <tr>
            <th colspan="8" style="text-align: center;">VISION</th>
        </tr>
        <tr>
            {{-- <th class="ancho" colspan="2">Agudeza visual</th>
            <td class="ancho" colspan="2">{{ Session::get('agudezavisual') }}</td> --}}
            <th class="ancho" colspan="4">Uso de lentes</th>
            <td class="ancho" colspan="4">{{ Session::get('usalentes') }}</td>
        </tr>
        <tr>
            <th colspan="8" style="text-align: center;">DATOS ANTROPOMETRICOS</th>
        </tr>
        <tr>
            <th class="ancho">Peso</th>
            <td class="ancho" colspan="2">{{ Session::get('peso') }} kg</td>
            <th class="ancho">Estatura</th>
            <td class="ancho" colspan="2">{{ Session::get('estatura') }} cm</td>
            <th class="ancho">IMC</th>
            <td class="ancho">{{ Session::get('imc') }}</td>
        </tr>
        <tr>
            <th colspan="8" style="text-align: center; background: yellow;">EXAMEN FISICO SEGMENTADO</th>
        </tr>
        <tr>
            <th class="ancho" colspan="2">Cabeza</th>
            <td class="ancho" colspan="6">{{ Session::get('exficabeza') }}</td>
        </tr>
        <tr>
            <th class="ancho" colspan="2">Ojos</th>
            <td class="ancho" colspan="6">{{ Session::get('exfiojos') }}</td>
        </tr>
        <tr>
            <th class="ancho" colspan="2">Nariz</th>
            <td class="ancho" colspan="6">{{ Session::get('exfinariz') }}</td>
        </tr>
        <tr>
            <th class="ancho" colspan="2">Oido</th>
            <td class="ancho" colspan="6">{{ Session::get('exfioidos') }}</td>
        </tr>
        <tr>
            <th class="ancho" colspan="2">Boca</th>
            <td class="ancho" colspan="6">{{ Session::get('exfiboca') }}</td>
        </tr>
        <tr>
            <th class="ancho" colspan="2">Cuello</th>
            <td class="ancho" colspan="6">{{ Session::get('exficuello') }}</td>
        </tr>
        <tr>
            <th class="ancho" colspan="2">Torax</th>
            <td class="ancho" colspan="6">{{ Session::get('exfitorax') }}</td>
        </tr>
        <tr>
            <th class="ancho" colspan="2">Corazon</th>
            <td class="ancho" colspan="6">{{ Session::get('exficorazon') }}</td>
        </tr>
        <tr>
            <th class="ancho" colspan="2">Pulmones</th>
            <td class="ancho" colspan="6">{{ Session::get('exfipulmones') }}</td>
        </tr>
        <tr>
            <th class="ancho" colspan="2">Abdomen</th>
            <td class="ancho" colspan="6">{{ Session::get('exfiabdomen') }}</td>
        </tr>
        <tr>
            <th class="ancho" colspan="2">Extremidades MMSS</th>
            <td class="ancho" colspan="6">{{ Session::get('exfiextremidadesmmss') }}</td>
        </tr>
        <tr>
            <th class="ancho" colspan="2">Extremidades MMII</th>
            <td class="ancho" colspan="6">{{ Session::get('exfiextremidadesmmii') }}</td>
        </tr>
        <tr>
            <th class="ancho" colspan="2">Neurologico</th>
            <td class="ancho" colspan="6">{{ Session::get('exfineurologico') }}</td>
        </tr>
        <tr>
            <th class="ancho" colspan="2">Vestibulo/Cerebeloso</th>
            <td class="ancho" colspan="6">{{ Session::get('exfivestibulocereboloso') }}</td>
        </tr>
        <tr>
            <th class="ancho" colspan="2">Marcha</th>
            <td class="ancho" colspan="6">{{ Session::get('exfimarcha') }}</td>
        </tr>
        <tr>
            <th class="ancho" colspan="2">Craneo y Columna</th>
            <td class="ancho" colspan="6">{{ Session::get('exficraneoycolumna') }}</td>
        </tr>
        <tr>
            <th class="ancho" colspan="2">Exploracion neurovascular</th>
            <td class="ancho" colspan="6">{{ Session::get('exfiexploracionneuro') }}</td>
        </tr>
    </table>
</body>
</html>
