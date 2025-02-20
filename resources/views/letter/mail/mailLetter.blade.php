<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles del Turno Asignado</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333;
            background-color: rgb(255, 255, 255);
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
        }

        .email-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #235B4E;
            color: #fff;
            padding: 15px;
            border-radius: 10px 10px 0 0;
            text-align: left;
            /* Alineación a la izquierda */
        }

        /* Aplicando la fuente 'Roboto' sin negrita */
        .email-header h2 {
            margin: 0;
            font-family: 'Roboto', sans-serif;
            font-weight: 400;
            /* Peso normal */
            font-size: 30px;
            /* Tamaño más grande */
        }

        .email-header p {
            margin: 0;
            font-size: 17px;
            color: #ccc;
            /* Color gris */
        }

        .email-body {
            padding: 20px;
            line-height: 1.6;
            color: #333;
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
                    <p style="color:white">IMSS-BIENESTAR CENTRAL</p>
                </td>
            </tr>
            <tr>
                <td class="email-body">
                    <p>¡Hola, {{ $nameUser }}!</p>
                    <p>Con el fin de dar seguimiento a la correspondencia, se te ha asignado un nuevo número de turno.
                        A continuación, se detallan algunos puntos:</p>
                    <ul style="padding-left: 20px;">
                        <li style="margin-bottom: 10px;">
                            <strong style="color: #000;">Asunto:</strong> {{ $mailBody->asunto }}
                        </li>
                        <li style="margin-bottom: 10px;">
                            <strong style="color: #000;">No. Turno:</strong> {{ $mailBody->num_turno_sistema }}
                        </li>
                        <li style="margin-bottom: 10px;">
                            <strong style="color: #000;">No. Documento:</strong> {{ $mailBody->num_documento }}
                        </li>
                        <li style="margin-bottom: 10px;">
                            <strong style="color: #000;">Fecha Inicio:</strong> {{ $mailBody->fecha_inicio }}
                        </li>
                        <li style="margin-bottom: 10px;">
                            <strong style="color: #000;">Fecha Fin:</strong> {{ $mailBody->fecha_fin }}
                        </li>
                        <li style="margin-bottom: 10px;">
                            <strong style="color: #000;">Turnado a:</strong> {{ $mailBody->area_descripcion }}
                        </li>
                        <li style="margin-bottom: 10px;">
                            <strong style="color: #000;">Usuario:</strong> {{ $mailBody->usuario_area }}
                        </li>
                        <li style="margin-bottom: 10px;">
                            <strong style="color: #000;">Enlace:</strong> {{ $mailBody->usuario_enlace }}
                        </li>
                    </ul>
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