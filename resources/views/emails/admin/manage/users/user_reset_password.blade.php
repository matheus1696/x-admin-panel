<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Senha redefinida - {{ config('app.name') }}</title>
</head>
<body style="margin:0; padding:0; background-color:#f3f4f6; font-family:Arial, Helvetica, sans-serif; color:#111827;">

    <!-- Container principal -->
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f3f4f6; padding:40px 0;">
        <tr>
            <td align="center">

                <!-- Card principal -->
                <table width="600" cellpadding="0" cellspacing="0" style="background-color:#ffffff; border-radius:10px; box-shadow:0 2px 8px rgba(0,0,0,0.08); overflow:hidden;">
                    
                    <!-- Cabeçalho -->
                    <tr>
                        <td align="center" style="background-color:#2563eb; padding:24px;">
                            <h1 style="color:#ffffff; font-size:22px; margin:0; font-weight:600;">Senha redefinida com sucesso!</h1>
                        </td>
                    </tr>

                    <!-- Corpo -->
                    <tr>
                        <td style="padding:32px 40px;">
                            <p style="font-size:15px; color:#374151; margin:0 0 16px;">
                                Olá <strong>{{ $user->name }}</strong>,
                            </p>

                            <p style="font-size:14px; color:#4b5563; line-height:1.6; margin:0 0 20px;">
                                Sua senha foi redefinida por um administrador do sistema <strong>{{ config('app.name') }}</strong>.
                                Abaixo estão suas novas credenciais de acesso:
                            </p>

                            <!-- Bloco de credenciais -->
                            <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f9fafb; border:1px solid #e5e7eb; border-radius:6px; padding:16px;">
                                <tr>
                                    <td style="font-size:14px; color:#1f2937; padding:4px 0;">
                                        <strong>Email:</strong> {{ $user->email }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="font-size:14px; color:#1f2937; padding:4px 0;">
                                        <strong>Nova senha:</strong> {{ $password }}
                                    </td>
                                </tr>
                            </table>

                            <!-- Botão de acesso -->
                            <div style="text-align:center; margin:30px 0;">
                                <a href="{{ config('app.url') }}" 
                                   style="background-color:#2563eb; color:#ffffff; text-decoration:none; font-size:14px; font-weight:600; padding:14px 28px; border-radius:8px; display:inline-block;">
                                   Fazer Login
                                </a>
                            </div>

                            <p style="font-size:13px; color:#6b7280; line-height:1.5;">
                                Recomendamos alterar sua senha após o primeiro login para garantir sua segurança.
                            </p>
                        </td>
                    </tr>

                    <!-- Rodapé -->
                    <tr>
                        <td align="center" style="background-color:#f9fafb; padding:20px;">
                            <p style="font-size:12px; color:#9ca3af; margin:0;">
                                © {{ date('Y') }} {{ config('app.name') }}. Todos os direitos reservados.
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>

</body>
</html>
