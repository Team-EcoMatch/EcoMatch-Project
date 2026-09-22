<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restablecer Contraseña - EcoMatch</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f1f5f9; font-family: 'Segoe UI', 'Helvetica Neue', Arial, sans-serif;">
    
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f1f5f9; padding: 40px 0;">
        <tr>
            <td align="center">
                
                <table width="560" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,0.08);">
                    
                    <tr>
                        <td style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); padding: 32px 40px; text-align: center;">
                            <h1 style="margin: 0; color: #ffffff; font-size: 26px; font-weight: 700; letter-spacing: -0.5px;">
                                🌱 EcoMatch
                            </h1>
                            <p style="margin: 6px 0 0; color: #94a3b8; font-size: 13px; font-weight: 400;">
                                Intercambio de materiales empresariales
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding: 40px 40px 20px;">
                            <h2 style="margin: 0 0 16px; color: #0f172a; font-size: 20px; font-weight: 700;">
                                Restablece tu contraseña
                            </h2>
                            <p style="margin: 0 0 24px; color: #475569; font-size: 15px; line-height: 1.6;">
                                ¡Hola, <strong style="color: #0f172a;">{{ $user->name ?? 'Usuario' }}</strong>! 
                                Recibimos una solicitud para restablecer la contraseña de tu cuenta de EcoMatch.
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <td align="center" style="padding: 0 40px 24px;">
                            <table cellpadding="0" cellspacing="0">
                                <tr>
                                    <td style="border-radius: 10px; overflow: hidden;">
                                        <a href="{{ $url }}" 
                                           style="display: inline-block; padding: 14px 40px; background: linear-gradient(135deg, #3b82f6 0%, #6366f1 100%); color: #ffffff; text-decoration: none; font-size: 15px; font-weight: 600; border-radius: 10px; letter-spacing: 0.3px;">
                                            Restablecer Contraseña
                                        </a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding: 0 40px 32px;">
                            <div style="background-color: #f8fafc; border-left: 3px solid #6366f1; border-radius: 8px; padding: 16px 20px; margin-bottom: 24px;">
                                <p style="margin: 0 0 6px; color: #64748b; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">
                                    ⏱️ Enlace válido por 60 minutos
                                </p>
                                <p style="margin: 0; color: #475569; font-size: 13px; line-height: 1.5;">
                                    Si no restableces tu contraseña dentro de 60 minutos, deberás solicitar un nuevo enlace.
                                </p>
                            </div>

                            <p style="margin: 0 0 8px; color: #64748b; font-size: 13px;">
                                ¿El botón no funciona? Copia y pega este enlace:
                            </p>
                            <p style="margin: 0; padding: 12px 16px; background-color: #f1f5f9; border-radius: 8px; color: #6366f1; font-size: 12px; word-break: break-all; font-family: 'Courier New', monospace;">
                                {{ $url }}
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding: 0 40px 32px;">
                            <div style="border-top: 1px solid #e2e8f0; padding-top: 24px;">
                                <p style="margin: 0 0 8px; color: #475569; font-size: 14px; line-height: 1.6;">
                                    <strong style="color: #0f172a;">¿No solicitaste este cambio?</strong>
                                </p>
                                <p style="margin: 0; color: #64748b; font-size: 13px; line-height: 1.6;">
                                    Si no fuiste tú quien solicitó el restablecimiento, puedes ignorar este correo. Tu contraseña seguirá siendo la misma y tu cuenta está segura.
                                </p>
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <td style="background-color: #0f172a; padding: 24px 40px; text-align: center;">
                            <p style="margin: 0 0 4px; color: #94a3b8; font-size: 12px;">
                                © 2026 EcoMatch. Todos los derechos reservados.
                            </p>
                            <p style="margin: 0; color: #475569; font-size: 11px;">
                                Este es un correo automático, no respondas a este mensaje.
                            </p>
                        </td>
                    </tr>

                </table>

                <table width="560" cellpadding="0" cellspacing="0">
                    <tr>
                        <td style="padding: 20px 0; text-align: center;">
                            <p style="margin: 0; color: #94a3b8; font-size: 11px;">
                                EcoMatch · Intercambio sostenible de materiales
                            </p>
                        </td>
                    </tr>
                </table>

            </td>
        </tr>
    </table>

</body>
</html>