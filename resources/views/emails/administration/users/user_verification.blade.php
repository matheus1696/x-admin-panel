<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifique sua conta - {{ config('app.name') }}</title>
    <style>
        /* Reset styles */
        body, table, td, p, a {
            font-family: 'Segoe UI', 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
        }
        
        /* Responsive button */
        @media screen and (max-width: 600px) {
            .button {
                width: 100% !important;
                display: block !important;
                padding: 16px !important;
                font-size: 16px !important;
            }
            .content {
                padding: 24px 20px !important;
            }
        }
    </style>
</head>
<body style="margin:0; padding:0; background-color:#ffffff; font-family:'Segoe UI', 'Helvetica Neue', Arial, sans-serif; color:#111827;">

    <!-- Container principal -->
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#ffffff; padding:40px 0;">
        <tr>
            <td align="center">

                <!-- Card principal -->
                <table width="600" cellpadding="0" cellspacing="0" style="background-color:#ffffff; border-radius:16px; box-shadow:0 4px 6px -1px rgba(0,0,0,0.1), 0 2px 4px -1px rgba(0,0,0,0.06); overflow:hidden; max-width:100%; border:1px solid #e5e7eb;">
                    
                    <!-- Cabeçalho -->
                    <tr>
                        <td align="center" style="background: linear-gradient(135deg, #065f46 0%, #047857 100%); padding:40px 24px;">
                            <!-- Logo/Ícone -->
                            <div style="width:72px; height:72px; background-color:rgba(255,255,255,0.1); border-radius:50%; margin:0 auto 16px; display:flex; align-items:center; justify-content:center; border:2px solid rgba(255,255,255,0.2);">
                                <svg width="36" height="36" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="color:#ffffff;">
                                    <path d="M20 4H4C2.9 4 2 4.9 2 6V18C2 19.1 2.9 20 4 20H20C21.1 20 22 19.1 22 18V6C22 4.9 21.1 4 20 4ZM20 8L12 13L4 8V6L12 11L20 6V8Z" fill="currentColor"/>
                                </svg>
                            </div>
                            <h1 style="color:#ffffff; font-size:24px; margin:0 0 8px; font-weight:600;">
                                Olá, {{ $user->name }}!
                            </h1>
                            <p style="color:rgba(255,255,255,0.9); font-size:15px; margin:0;">
                                Seja bem-vindo ao {{ config('app.name') }}
                            </p>
                        </td>
                    </tr>

                    <!-- Corpo -->
                    <tr>
                        <td class="content" style="padding:40px 48px;">
                            
                            <!-- Mensagem de boas-vindas -->
                            <p style="font-size:16px; color:#374151; margin:0 0 24px;">
                                Estamos muito felizes em tê-lo conosco! Para começar a utilizar todos os recursos do sistema, 
                                precisamos verificar seu endereço de e-mail.
                            </p>

                            <!-- Box de verificação -->
                            <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f0fdf4; border:1px solid #d1fae5; border-radius:12px; padding:24px; margin-bottom:32px;">
                                <tr>
                                    <td align="center" style="padding:24px;">
                                        <div style="width:56px; height:56px; background: linear-gradient(135deg, #047857 0%, #065f46 100%); border-radius:50%; margin:0 auto 16px; display:flex; align-items:center; justify-content:center;">
                                            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="color:#ffffff;">
                                                <path d="M12 2C6.48 2 2 6.48 2 12C2 17.52 6.48 22 12 22C17.52 22 22 17.52 22 12C22 6.48 17.52 2 12 2ZM10 17L5 12L6.41 10.59L10 14.17L17.59 6.58L19 8L10 17Z" fill="currentColor"/>
                                            </svg>
                                        </div>
                                        <h2 style="font-size:18px; color:#065f46; margin:0 0 8px; font-weight:600;">
                                            Verificação de e-mail
                                        </h2>
                                        <p style="font-size:14px; color:#047857; margin:0;">
                                            Clique no botão abaixo para confirmar sua conta
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <!-- Botão de verificação -->
                            <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:32px;">
                                <tr>
                                    <td align="center">
                                        <a href="{{ $verificationUrl }}" 
                                           class="button"
                                           style="background: linear-gradient(135deg, #065f46 0%, #047857 100%); 
                                                  color:#ffffff; 
                                                  text-decoration:none; 
                                                  font-size:15px; 
                                                  font-weight:600; 
                                                  padding:14px 32px; 
                                                  border-radius:8px; 
                                                  display:inline-block;
                                                  box-shadow:0 4px 6px -1px rgba(5, 150, 105, 0.3);">
                                            Verificar minha conta
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <!-- Informações úteis -->
                            <div style="background-color:#f9fafb; border-radius:8px; padding:20px; margin-bottom:32px; border-left:4px solid #047857;">
                                <p style="font-size:14px; color:#065f46; margin:0 0 12px; font-weight:600;">
                                    🔒 Informações importantes:
                                </p>
                                <ul style="font-size:13px; color:#4b5563; margin:0; padding-left:20px;">
                                    <li style="margin-bottom:8px;">Este link expira em <strong>24 horas</strong></li>
                                    <li style="margin-bottom:8px;">Se você não solicitou esta conta, ignore este e-mail</li>
                                    <li>Em caso de dúvidas, contate nosso suporte</li>
                                </ul>
                            </div>

                            <!-- Link alternativo -->
                            <div style="border-top:1px solid #e5e7eb; padding-top:24px;">
                                <p style="font-size:12px; color:#6b7280; text-align:center; margin:0 0 8px;">
                                    Se o botão não funcionar, copie e cole este link no seu navegador:
                                </p>
                                <p style="font-size:11px; color:#047857; text-align:center; word-break:break-all; margin:0; background-color:#f9fafb; padding:8px; border-radius:4px; font-family:monospace;">
                                    {{ $verificationUrl }}
                                </p>
                            </div>

                        </td>
                    </tr>

                    <!-- Rodapé -->
                    <tr>
                        <td align="center" style="background-color:#f9fafb; padding:24px; border-top:1px solid #e5e7eb;">
                            <p style="font-size:12px; color:#6b7280; margin:0 0 4px;">
                                © {{ date('Y') }} {{ config('app.name') }}. Todos os direitos reservados.
                            </p>
                            <p style="font-size:11px; color:#9ca3af; margin:0;">
                                Este é um e-mail automático, por favor não responda.
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>

</body>
</html>