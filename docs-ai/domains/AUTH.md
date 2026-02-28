# Auth

## Responsabilidade

`Auth` cobre entrada e recuperação de acesso: login, logout, registro, reset de senha, confirmação de senha e verificação de e-mail.

## Entidades Principais

- `AuthenticatedSessionController`
- `RegisteredUserController`
- `PasswordResetLinkController`
- `NewPasswordController`
- `PasswordController`
- `ConfirmablePasswordController`
- `EmailVerificationPromptController`
- `EmailVerificationNotificationController`
- `VerifyEmailController`
- `LoginRequest`
- `User`

## Fluxos Críticos

- Registrar novo usuário
- Abrir e encerrar sessão
- Solicitar e executar reset de senha
- Confirmar senha em fluxo protegido
- Verificar e-mail e reenviar notificação

## Invariantes / Regras

- login, registro e reset rodam sob `guest`
- verificação, confirmação e logout rodam sob `auth`
- verificação de e-mail usa assinatura e throttle
- `User` implementa `MustVerifyEmail`

## Integrações

- `Administration`: usa `User`
- `Profile`: depende de sessão autenticada
- `Dashboard`: é acessado após autenticação

## Riscos / Armadilhas

- alterar middleware de auth muda a fronteira de todo o sistema
- customizar fluxo sem alinhar verificação de e-mail abre acesso em estado inconsistente
- acoplar regra de negócio a controllers de auth
