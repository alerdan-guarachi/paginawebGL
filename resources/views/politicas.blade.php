@extends('adminlte::page')

@section('title', 'Políticas de Privacidad')

@section('content_header')
    <h3 class="text-center"><strong>POLÍTICAS DE PRIVACIDAD</strong></h3>
@stop

@section('content')
<div class="container mt-6 mb-5">
    <div class="card shadow-sm">
        <div class="card-body">

            <p class="text-muted text-right">
                Última actualización: {{ date('d/m/Y') }}
            </p>

            <hr>

            <p>
                En GOOD LIFE S.R.L., protegemos su privacidad y la confidencialidad de su información, especialmente sus datos de salud. Esta Política de Privacidad explica de manera clara cómo recopilamos, utilizamos, almacenamos y protegemos su información cuando utiliza nuestra aplicación móvil.
            </p>

            <h4>1. RESPONSABLE DEL TRATAMIENTO DE DATOS</h4>
            <p>
                El responsable del tratamiento de sus datos es:<br>
                <strong>GOOD LIFE S.R.L.</strong>
            </p>

            <ul>
                <li>
                    📍 <strong>Santa Cruz de la Sierra, Bolivia</strong><br>
                    Av. René Moreno N° 484 Esq. Ana Barba (Barrio Sur)
                </li>

                <li>
                    📍 <strong>Cochabamba, Bolivia</strong><br>
                    Calle Lanza entre R. Rivero y Oruro, Edif. Shashelly piso 2, of. 2B
                </li>
            </ul>

            <p>
                📧 soporte@goodlife.com.bo<br>
                📞 77427900
            </p>

            <h4>2. INFORMACIÓN QUE RECOPILAMOS</h4>

            <p>Recopilamos únicamente la información necesaria para brindarle nuestros servicios:</p>

            <p><strong>a) Datos de Identificación</strong></p>
            <ul>
                <li>Nombre completo</li>
                <li>Correo electrónico</li>
                <li>Identificador de usuario</li>
            </ul>

            <p><strong>b) Datos de Salud (Datos Sensibles)</strong></p>
            <ul>
                <li>Informes médicos</li>
                <li>Diagnósticos</li>
                <li>Resultados de estudios</li>
                <li>Historial clínico</li>
            </ul>

            <p>⚠️ Estos datos son proporcionados por profesionales y centros médicos autorizados y están disponibles exclusivamente para su consulta.</p>

            <p><strong>c) Datos de Gestión</strong></p>
            <ul>
                <li>Estado de trámites</li>
                <li>Programaciones médicas</li>
                <li>Historial de atenciones</li>
            </ul>
            
            <hr>

            <h4>3. FINALIDAD DEL TRATAMIENTO</h4>

            <p>Utilizamos su información para:</p>
            <ul>
                <li>Brindarle acceso seguro a sus informes médicos</li>
                <li>Gestionar y mostrar el avance de sus trámites</li>
                <li>Enviarle notificaciones importantes (citas, resultados, avisos)</li>
                <li>Garantizar la seguridad y confidencialidad de su información</li>
                <li>Mejorar la calidad de nuestros servicios</li>
            </ul>

            <hr>

            <h4>4. BASE LEGAL DEL TRATAMIENTO</h4>

            <p>Tratamos sus datos en base a:</p>
            <ul>
                <li>Su consentimiento al registrarse en la aplicación</li>
                <li>La necesidad de ejecutar los servicios contratados</li>
                <li>Cumplimiento de obligaciones legales en el sector salud</li>
            </ul>

            <hr>

            <h4>5. PROTECCIÓN Y SEGURIDAD DE LOS DATOS</h4>

            <ul>
                <li>🔐 <strong>Cifrado en tránsito:</strong> Toda la información se transmite mediante protocolos seguros HTTPS (SSL/TLS).</li>
                <li>📵 <strong>Protección de contenido sensible:</strong> Se bloquean capturas y grabaciones en secciones con datos sensibles.</li>
                <li>🔑 <strong>Control de acceso:</strong> Solo el titular accede mediante autenticación segura.</li>
                <li>📱 <strong>Visualización segura:</strong> Los datos se muestran en tiempo real y no se almacenan en el dispositivo.</li>
            </ul>

            <h4>6. USO DE DATOS Y PUBLICIDAD</h4>

            <ul>
                <li>❌ No vendemos ni compartimos sus datos personales o de salud</li>
                <li>❌ No utilizamos su información con fines publicitarios</li>
                <li>✔️ Uso exclusivo para la prestación del servicio</li>
            </ul>

            <hr>

            <h4>7. CONSERVACIÓN DE LOS DATOS</h4>

            <p>Sus datos serán almacenados:</p>
            <ul>
                <li>Mientras su cuenta esté activa</li>
                <li>O el tiempo requerido por la normativa legal del sector salud</li>
            </ul>

            <p>Una vez finalizado este plazo, serán eliminados o anonimizados.</p>

            <hr>

            <h4>8. DERECHOS DEL USUARIO</h4>

            <p>Usted tiene derecho a:</p>
            <ul>
                <li>Acceder a sus datos personales y de trámites</li>
                <li>Solicitar correcciones de cualquier dato inexacto</li>
                <li>Solicitar la eliminación de su cuenta de usuario. Sin embargo, algunos datos de evaluación o trámites podrían conservarse según nuestras políticas internas, si son necesarios para fines operativos.</li>
            </ul>

            <p>
            Para ejercer estos derechos, puede escribir a:<br>
                📧 soporte@goodlife.com.bo
            </p>

            <hr>

            <h4>9. ELIMINACIÓN DE CUENTA DE USUARIO</h4>

            <p>
            Puede solicitar la eliminación de su cuenta en cualquier momento enviando un correo a soporte@goodlife.com.bo. Una vez eliminada la cuenta, los datos asociados serán eliminados, salvo aquellos que deban conservarse conforme a nuestras políticas internas para fines operativos puntuales.
            </p>

            <hr>

            <h4>10. PERMISOS DE LA APLICACIÓN</h4>

            <p>La aplicación puede solicitar:</p>
            <ul>
                <li>🌐 Acceso a Internet (para el funcionamiento del servicio)</li>
                <li>🔔 Notificaciones (avisos importantes sobre su salud y trámites)</li>
            </ul>

            <hr>

            <h4>11. CAMBIOS EN LA POLÍTICA</h4>

            <p>
            Podremos actualizar esta política para reflejar mejoras o cambios legales.
            Le notificaremos cualquier cambio relevante a través de la aplicación.
            </p>

            <hr>

            <h4>12. CONTACTO</h4>

            <p>Si tiene consultas sobre esta política:</p>

            <p>
                📧 soporte@goodlife.com.bo<br>
                📞 77427900
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