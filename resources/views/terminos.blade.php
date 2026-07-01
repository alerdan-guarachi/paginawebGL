@extends('adminlte::page')

@section('title', 'Términos y Condiciones de Servicio')

@section('content_header')
    <h3 class="text-center"><strong>TÉRMINOS Y CONDICIONES DE SERVICIO</strong></h3>
@stop

@section('content')
<div class="container-fluid px-2 px-md-4 mt-3 mb-4">
    <div class="card shadow-sm">
        <div class="card-body">

            <p class="text-muted text-right">
                Última actualización: 10/04/2026
            </p>

            <hr>

            <p>
                Bienvenido a la aplicación móvil oficial de GOOD LIFE S.R.L. Al descargar, instalar o utilizar nuestra aplicación, usted acepta cumplir y estar sujeto a los siguientes términos y condiciones. Si no está de acuerdo con alguna parte de estos términos, le solicitamos que no utilice la aplicación.
            </p>

            <h4>1. ACEPTACIÓN DE LOS TÉRMINOS</h4>
            <p>
                El acceso y uso de esta aplicación atribuye la condición de usuario e implica la aceptación total de todas las disposiciones incluidas en este documento. GOOD LIFE S.R.L. se reserva el derecho de modificar estos términos en cualquier momento.
            </p>

            <h4 class="mt-4">2.	NATURALEZA DEL SERVICIO</h4>
            <p>
                La aplicación de GOOD LIFE S.R.L. es una herramienta tecnológica diseñada para:
                <ul>
                    <li>La visualización de informes médicos y estudios.</li>
                    <li>El seguimiento de trámites administrativos ante la empresa.</li>
                    <li>La consulta de programaciones y ausencias del personal médico.</li>
                    <li>Recepción de notificaciones informativas.</li>
                </ul>
                <strong>DESCARGO DE RESPONSABILIDAD MÉDICA:</strong> El contenido de esta aplicación tiene fines exclusivamente informativos. La visualización de informes médicos en la app no sustituye una consulta médica presencial ni constituye un servicio de urgencias. En caso de una emergencia médica, el usuario debe acudir inmediatamente a un centro de salud.
            </p>

            <h4 class="mt-4">3.	REGISTRO Y SEGURIDAD DE LA CUENTA</h4>
            <p>
                <ul>
                    <li>El usuario es responsable de mantener la confidencialidad de sus credenciales de acceso (usuario y contraseña).</li>
                    <li>Toda actividad realizada bajo su cuenta se considerará responsabilidad del usuario titular.</li>
                    <li>En caso de sospecha de uso no autorizado de su cuenta, el usuario debe notificar inmediatamente a GOOD LIFE S.R.L.</li>
                </ul>
            </p>

            <h4 class="mt-4">4.	USO PERMITIDO Y PROHIBICIONES</h4>
            <p>
                El usuario se compromete a hacer un uso lícito y adecuado de la aplicación. Queda estrictamente prohibido:
                <ul>
                    <li>Intentar vulnerar las medidas de seguridad de la aplicación, incluyendo los sistemas de bloqueo de capturas de pantalla y grabaciones.</li>
                    <li>Utilizar la aplicación para fines fraudulentos o ilícitos.</li>
                    <li>Realizar ingeniería inversa o intentar extraer el código fuente de la aplicación.</li>
                    <li>Acceder o intentar acceder a datos de otros usuarios.</li>
                </ul>
            </p>

            <h4 class="mt-4">5.	PROPIEDAD INTELECTUAL</h4>
            <p>
                Todos los contenidos, marcas, logos, gráficos y códigos de programación son propiedad exclusiva de GOOD LIFE S.R.L. o de sus licenciantes. Queda prohibida su reproducción, distribución o modificación sin autorización expresa por escrito.
            </p>

            <h4 class="mt-4">6.	PRIVACIDAD Y PROTECCIÓN DE DATOS</h4>
            <p>
                El uso de sus datos personales y de salud se rige por nuestra Política de Privacidad, la cual está disponible para su consulta dentro del menú de la aplicación y en nuestro sitio web oficial.
            </p>

            <h4 class="mt-4">7.	LIMITACIÓN DE RESPONSABILIDAD</h4>
            <p>
                GOOD LIFE S.R.L. no será responsable por:
                <ul>
                    <li>Interrupciones en el servicio debidas a fallos técnicos, mantenimiento o problemas de conectividad del usuario.</li>
                    <li>Daños derivados del mal uso de la información visualizada por parte del usuario o de terceros autorizados por él.</li>
                    <li>Pérdida de datos si el usuario no sigue las recomendaciones de seguridad de su dispositivo móvil.</li>
                </ul>
            </p>

            <h4 class="mt-4">8.	ELIMINACIÓN DE CUENTA Y SUSPENSIÓN</h4>
            <p>
                Usted puede solicitar la eliminación de su cuenta de usuario en cualquier momento a través de los canales de soporte indicados en la aplicación móvil. GOOD LIFE S.R.L. se reserva el derecho de suspender o cancelar el acceso a cualquier usuario que infrinja estos términos.
            </p>

            <h4 class="mt-4">9.	LEY APLICABLE Y JURISDICCIÓN</h4>
            <p>
                Estos términos se rigen por las leyes de Bolivia. Cualquier controversia derivada del uso de la aplicación será sometida a los tribunales competentes de dicha jurisdicción.
            </p>

            <h4 class="mt-4">10. CONTACTO</h4>
            <p>
                Para cualquier duda o comentario respecto a estos términos, puede contactarnos a través de:<br>
                📧 soporte@goodlife.com.bo
            </p>
        </div>
    </div>
</div>
@stop

@section('css')
<style>
    .card {
        border-radius: 50px;
    }
    .card-body {
        margin-right: 20px;
        margin-left: 20px;
        margin-top: 20px;
        margin-bottom: 30px;
    }
    h4 {
        font-weight: 800;
        font-size: 18px;
    }
    p {
        text-align: justify;
    }
    
</style>
@stop