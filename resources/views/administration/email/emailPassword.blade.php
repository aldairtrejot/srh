<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIRH - Actualización de Contraseña</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333;
            background-color: #f7f7f7;
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .email-container {
            width: 100%;
            max-width: 800px;
            background-color: #fff;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 10px;
            box-sizing: border-box;
            /* Aquí va tu imagen codificada en base64 */
            background-size: cover;
            /* Hace que la imagen se ajuste al tamaño del contenedor */
            background-position: center;
            /* Centra la imagen */
            background-repeat: no-repeat;
            /* No repite la imagen */
        }

        .email-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #235B4E;
            color: #fff;
            padding: 20px;
            border-radius: 10px 10px 0 0;
            text-align: left;
        }

        .email-header h2 {
            margin: 0;
            font-family: 'Roboto', sans-serif;
            font-weight: 400;
            font-size: 30px;
        }

        .email-header p {
            margin: 0;
            font-size: 17px;
            color: #ccc;
        }

        .email-body {
            padding: 20px;
            line-height: 1.6;
            color: #333;
            font-size: 16px;
        }

        .email-body p {
            margin: 10px 0;
        }

        .email-body a {
            /*color:rgb(137, 194, 255);*/
            /* Color azul para el enlace */
            /*text-decoration: none;*/
            /*font-weight: bold;*/
        }

        .email-body a:hover {
            text-decoration: underline;
            /* Subraya al pasar el mouse */
        }

        .password {
            font-size: 24px;
            font-weight: bold;
            color: #235B4E;
            background-color: #f1f1f1;
            padding: 10px;
            border-radius: 5px;
            display: inline-block;
            margin-top: 10px;
            letter-spacing: 1px;
            text-align: center;
            width: 100%;
            box-sizing: border-box;
        }

        .email-footer {
            text-align: center;
            font-size: 12px;
            color: #888;
            padding-top: 10px;
            margin-top: 20px;
            background-color: rgb(255, 255, 255);
            padding-bottom: 20px;
        }

        .email-footer p {
            margin: 5px 0;
        }
    </style>
</head>

<body>

    <div class="email-container">
        <table role="presentation" cellpadding="0" cellspacing="0" width="100%">
            <tr>
                <td class="email-header">
                    <p style="font-size: 18px; font-weight: 500;">IMSS-BIENESTAR CENTRAL</p>
                </td>
            </tr>
            <tr>
                <td class="email-body">
                    <p><strong>ESTIMADO/A {{ $name }},</strong></p>

                    <p>Te informamos que hemos recibido una solicitud para actualizar tu contraseña en el Sistema
                        Integral para Recursos Humanos (SIRH) el día <strong>{{ $fecha }}</strong> a las
                        <strong>{{ $hora }}</strong> hrs.
                    </p>

                    <p>Tu nueva contraseña es la siguiente:</p>

                    <div class="password">{{ $password }}</div>

                    <p>Para acceder al sistema, por favor <a href="http://172.16.17.11/srh/public/login"
                            target="_blank">haz clic aquí</a>.</p>

                    <p>Saludos cordiales,</p>
                </td>
            </tr>
            <tr>
                <td class="email-footer">
                    <p>Este es un correo de notificación automática. Por favor, no respondas a este mensaje.</p>
                    <p><strong>Sistema Integral para Recursos Humanos</strong></p>
                </td>
            </tr>
        </table>
    </div>
</body>

</html>