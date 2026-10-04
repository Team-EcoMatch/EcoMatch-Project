<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restablecer Contraseña - EcoMatch</title>
</head>
<body style="margin:0;padding:0;background-color:#0a0a0a;font-family:'Segoe UI','Helvetica Neue',Arial,sans-serif;">

<table width="100%" cellpadding="0" cellspacing="0" style="background-color:#0a0a0a;padding:40px 16px;">
    <tr>
        <td align="center">

<table width="580" cellpadding="0" cellspacing="0" style="max-width:580px;width:100%;">

    <tr>
        <td style="padding:0 0 20px;text-align:center;">
            <table cellpadding="0" cellspacing="0" style="margin:0 auto;">
                <tr>
                    <td style="background-color:#18181b;border:1px solid #27272a;border-radius:14px;padding:10px 20px;">
                        <span style="font-size:22px;font-weight:800;color:#10b981;letter-spacing:-0.5px;">🌱 EcoMatch</span>
                    </td>
                </tr>
            </table>
        </td>
    </tr>

    <tr>
        <td style="padding:0 0 8px;">
            <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#18181b;border-radius:24px;overflow:hidden;border:1px solid #27272a;">

                <tr>
                    <td style="padding:48px 40px 0;text-align:center;">
                        <table cellpadding="0" cellspacing="0" style="margin:0 auto;">
                            <tr>
                                <td style="width:72px;height:72px;border-radius:50%;background-color:#10b981;text-align:center;vertical-align:middle;">
                                    <span style="font-size:36px;line-height:72px;display:block;">🔐</span>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <tr>
                    <td style="padding:20px 40px 8px;text-align:center;">
                        <p style="margin:0 0 12px;color:#10b981;font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:2px;">
                            Recuperación de cuenta
                        </p>
                        <h1 style="margin:0;color:#fafafa;font-size:26px;font-weight:800;letter-spacing:-0.5px;line-height:1.25;">
                            Restablece tu contraseña
                        </h1>
                    </td>
                </tr>

                <tr>
                    <td style="padding:12px 40px 36px;text-align:center;">
                        <p style="margin:0;color:#a1a1aa;font-size:15px;line-height:1.7;">
                            Hola <strong style="color:#10b981;">{{ $user->name ?? 'Usuario' }}</strong>, hemos recibido una solicitud para restablecer la contraseña de tu cuenta.
                        </p>
                    </td>
                </tr>

                <tr>
                    <td style="padding:0 40px 36px;" align="center">
                        <table cellpadding="0" cellspacing="0">
                            <tr>
                                <td style="border-radius:12px;overflow:hidden;">
                                    <a href="{{ $url }}" style="display:inline-block;padding:16px 52px;background-color:#10b981;color:#0a0a0a;text-decoration:none;font-size:16px;font-weight:700;border-radius:12px;letter-spacing:0.3px;">
                                        Restablecer contraseña →
                                    </a>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <tr>
                    <td style="padding:0 40px 32px;">
                        <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#0a0a0a;border:1px solid #27272a;border-radius:16px;">
                            <tr>
                                <td style="padding:18px 20px 8px;">
                                    <p style="margin:0;color:#10b981;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:1.5px;">
                                        ⏱️ Enlace válido por 60 minutos
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding:0 20px 16px;">
                                    <p style="margin:0;color:#a1a1aa;font-size:13px;line-height:1.6;">
                                        Si no restableces tu contraseña dentro de 60 minutos, deberás solicitar un nuevo enlace de recuperación.
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding:0 20px 20px;border-top:1px solid #27272a;">
                                    <p style="margin:14px 0 6px;color:#71717a;font-size:12px;">
                                        ¿El botón no funciona? Copia este enlace:
                                    </p>
                                    <p style="margin:0;padding:10px 14px;background-color:#18181b;border-radius:8px;color:#10b981;font-size:11px;word-break:break-all;font-family:'Courier New',monospace;border:1px solid #27272a;">
                                        {{ $url }}
                                    </p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <tr>
                    <td style="padding:0 40px 32px;">
                        <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#0a0a0a;border:1px solid #27272a;border-radius:16px;">
                            <tr>
                                <td style="padding:18px 20px;">
                                    <p style="margin:0 0 6px;color:#71717a;font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;">
                                        💡 ¿No solicitaste este cambio?
                                    </p>
                                    <p style="margin:0;color:#a1a1aa;font-size:13px;line-height:1.6;">
                                        Si no fuiste tú, ignora este correo. Tu contraseña seguirá siendo la misma y tu cuenta está protegida.
                                    </p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <tr>
                    <td style="padding:0 40px 40px;">
                        <table width="100%" cellpadding="0" cellspacing="0">
                            <tr>
                                <td style="padding:24px 0 0;border-top:1px solid #27272a;text-align:center;">
                                    <p style="margin:0 0 14px;color:#71717a;font-size:12px;">
                                        🔒 Este enlace es personal e intransferible. No lo compartas.
                                    </p>
                                    <table cellpadding="0" cellspacing="0" style="margin:0 auto;">
                                        <tr>
                                            <td style="padding:0 6px;"><span style="display:inline-block;width:6px;height:6px;border-radius:50%;background:#10b981;"></span></td>
                                            <td style="padding:0 6px;"><span style="display:inline-block;width:6px;height:6px;border-radius:50%;background:#3b82f6;"></span></td>
                                            <td style="padding:0 6px;"><span style="display:inline-block;width:6px;height:6px;border-radius:50%;background:#8b5cf6;"></span></td>
                                            <td style="padding:0 6px;"><span style="display:inline-block;width:6px;height:6px;border-radius:50%;background:#f59e0b;"></span></td>
                                            <td style="padding:0 6px;"><span style="display:inline-block;width:6px;height:6px;border-radius:50%;background:#ec4899;"></span></td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

            </table>
        </td>
    </tr>

    <tr>
        <td style="padding:28px 40px;text-align:center;">
            <p style="margin:0 0 4px;color:#52525b;font-size:13px;font-weight:600;">
                EcoMatch · Economía Circular Empresarial
            </p>
            <p style="margin:0 0 4px;color:#3f3f46;font-size:11px;">
                © 2026 EcoMatch. Todos los derechos reservados.
            </p>
            <p style="margin:8px 0 0;color:#3f3f46;font-size:11px;">
                Correo automático · No responder a este mensaje
            </p>
        </td>
    </tr>

</table>

        </td>
    </tr>
</table>

</body>
</html>