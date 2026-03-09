# Auth

## Responsabilidade

Entrada e recuperacao de acesso: login, logout, registro, reset, confirmacao e verificacao de email.

## Entidades Principais

- `AuthenticatedSessionController`
- `RegisteredUserController`
- `PasswordResetLinkController`
- `NewPasswordController`
- `VerifyEmailController`
- `LoginRequest`
- `User`

## Invariantes

- fluxos de login/registro/reset sob middleware `guest`
- fluxos autenticados sob middleware `auth`
- verificacao de email com assinatura e throttle
- `User` implementa `MustVerifyEmail`

## Estado Atual

- Registro tenta vincular role `user` quando disponivel.
- Se role ainda nao existir no ambiente, o fluxo de registro nao deve quebrar.

## Integracoes

- `Administration`: entidade `User` e permissoes
- `Profile`: depende de sessao autenticada
- `Dashboard`: rota pos-login

## Riscos

- alterar middleware de auth muda fronteira de todo sistema
- acoplar regra de negocio em controllers de auth