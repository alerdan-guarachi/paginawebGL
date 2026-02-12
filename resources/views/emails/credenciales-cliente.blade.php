<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
</head>
<body>
    <h3>
        Estimad{{ $cliente->genero === 'FEMENINO' ? 'a' : 'o' }}:<br>
        {{ $cliente->nombrecompleto }}
    </h3>

    <p>Su acceso a la "Good Life App" ha sido creado correctamente.</p>

    <p><strong>Usuario:</strong> {{ $cliente->email }}</p>
    <p><strong>Contraseña:</strong> {{ $password }}</p>

    <hr>

    <p style="margin-top: 20px;">
        Descargue nuestra app:
    </p>

    <table cellpadding="10" cellspacing="0">
        <tr>
            <td>
                <a href="https://play.google.com/store/apps/details?id=com.duolingo" target="_blank"
                style="display:inline-block;padding:10px 15px;background:#94c93b;color:#ffffff;text-decoration:none;border-radius:5px;">
                    DESCARGAR PARA ANDROID
                </a>
            </td>
            <td>
                <a href="https://www.whatsapp.com/download/ios" target="_blank"
                style="display:inline-block;padding:10px 15px;background:#faa625;color:#ffffff;text-decoration:none;border-radius:5px;">
                    DESCARGAR PARA IOS
                </a>
            </td>
        </tr>
    </table>

    <br>
    <p>Atentamente,<br>
    <strong>Good Life S.R.L.</strong></p>
</body>
</html>
