# Auth

## Responsabilidade

Entrada e recuperacao de acesso:

- login
- logout
- registro
- reset de senha
- confirmacao de senha
- verificacao de email

## Entidades E Controllers Principais

- `AuthenticatedSessionController`
- `RegisteredUserController`
- `PasswordResetLinkController`
- `NewPasswordController`
- `VerifyEmailController`
- `EmailVerificationPromptController`
- `EmailVerificationNotificationController`
- `PasswordController`
- `LoginRequest`
- `User`

## Invariantes

- fluxos de login, registro e reset sob middleware `guest`
- fluxos autenticados sob middleware `auth`
- verificacao de email com assinatura e throttle
- `User` implementa `MustVerifyEmail`

## Estado Atual

- registro tenta vincular a role `user` quando disponivel
- se a role nao existir no ambiente, o fluxo de registro nao deve quebrar
- o grupo autenticado principal do sistema usa `auth` + `verified`

## Integracoes

- `Administration`: entidade `User` e permissoes
- `Profile`: depende de sessao autenticada
- `Dashboard`: rota pos-login

## Riscos

- alterar middleware de auth muda a fronteira do sistema inteiro
- acoplar regra de negocio em controllers de auth
- assumir role fixa sem fallback quebra bootstrap do ambiente
