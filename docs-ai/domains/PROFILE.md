# Profile

## Responsabilidade

Autoatendimento do usuario autenticado: dados cadastrais e senha.

## Entidades Principais

- `ProfileController`
- `ProfileUpdateRequest`
- `ProfilePasswordUpdateRequest`
- `User`
- `Gender`
- `Occupation`

## Fluxos Criticos

- Exibir tela de edicao
- Atualizar perfil do usuario autenticado
- Exibir tela de senha
- Atualizar senha e remover `password_default`
- Enviar email de aviso apos troca de senha

## Invariantes

- operacao sempre no usuario autenticado
- update de perfil usa `ProfileUpdateRequest`
- update de senha usa `ProfilePasswordUpdateRequest`
- alteracao de email invalida verificacao (`email_verified_at = null`)

## Compatibilidade

- endpoints legados `/profile` (GET/PATCH/DELETE) estao mantidos para compatibilidade de testes e fluxos existentes.

## Integracoes

- `Administration`: `User`, `Gender`
- `Configuration`: `Occupation`
- `Audit`: registro de acoes
- `Auth`: sessao valida

## Riscos

- atualizar perfil por id arbitrario
- alterar senha sem ajustar flags de seguranca
- remover notificacao de troca de senha