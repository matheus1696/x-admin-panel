<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }}</title>
</head>
<body style="margin:0; padding:24px; background:#f8fafc; color:#111827; font-family:Arial, sans-serif;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:640px; margin:0 auto; background:#ffffff; border-radius:12px; border:1px solid #e5e7eb;">
        <tr>
            <td style="padding:24px;">
                {{ $slot }}
            </td>
        </tr>
    </table>
</body>
</html>

