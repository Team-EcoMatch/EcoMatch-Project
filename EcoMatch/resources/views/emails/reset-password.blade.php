<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restablecer Contraseña</title>
</head>
<body style="margin: 0; padding: 0; background-color: #0B1110; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #0B1110; padding: 40px 0;">
        <tr>
            <td align="center">
                <table role="presentation" width="600" cellspacing="0" cellpadding="0" style="background-color: #14221D; border-radius: 16px; overflow: hidden; max-width: 600px; width: 100%; border: 1px solid #263A32;">
                    
                    <tr>
                        <td style="padding: 40px 40px 20px 40px; text-align: center;">
                            <h1 style="color: #10B981; font-size: 28px; font-weight: 800; margin: 0; letter-spacing: -0.5px;">EcoMatch</h1>
                            <p style="color: #6B7280; font-size: 14px; margin: 5px 0 0 0;">Economía Circular</p>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding: 20px 40px 40px 40px;">
                            <h2 style="color: #F1F5F3; font-size: 22px; font-weight: 700; margin: 0 0 20px 0;">¡Hola, {{ $user->name ?? 'Usuario' }}! 👋</h2>
                            <p style="color: #94A3B8; font-size: 16px; line-height: 1.6; margin: 0 0 30px 0;">
                                Has recibido este correo electrónico porque solicitaste restablecer la contraseña de tu cuenta en EcoMatch.
                            </p>
                            
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td align="center" style="padding-bottom: 30px;">
                                        <a href="{{ $url }}" style="background-color: #10B981; color: #ffffff; text-decoration: none; padding: 16px 40px; border-radius: 8px; font-weight: 600; font-size: 16px; display: inline-block;">
                                            Restablecer Contraseña
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <p style="color: #6B7280; font-size: 14px; line-height: 1.6; margin: 0 0 20px 0;">
                                Este enlace de restablecimiento de contraseña caducará en 60 minutos.
                            </p>
                            <p style="color: #6B7280; font-size: 14px; line-height: 1.6; margin: 0 0 30px 0;">
                                Si no solicitaste un restablecimiento de contraseña, no se requiere ninguna otra acción.
                            </p>
                            
                            <p style="color: #4B5563; font-size: 12px; line-height: 1.5; margin: 0; border-top: 1px solid #263A32; padding-top: 20px;">
                                Si tienes problemas para hacer clic en el botón, copia y pega la siguiente URL en tu navegador:<br>
                                <a href="{{ $url }}" style="color: #10B981; word-break: break-all;">{{ $url }}</a>
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding: 20px 40px; background-color: #111A17; text-align: center;">
                            <p style="color: #4B5563; font-size: 12px; margin: 0;">
                                &copy; {{ date('Y') }} EcoMatch. Todos los derechos reservados.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>