# Profile

## Responsabilidade

Autoatendimento do usuario autenticado:

- dados cadastrais
- senha
- compatibilidade com endpoints legados de perfil

## Entidades E Controllers Principais

- `ProfileController`
- `ProfileUpdateRequest`
- `ProfilePasswordUpdateRequest`
- `User`
- `Gender`
- `Occupation`

## Fluxos Criticos

- exibir tela de edicao
- atualizar perfil do usuario autenticado
- exibir tela de senha
- atualizar senha e remover `password_default`
- enviar email de aviso apos troca de senha

## Invariantes

- operacao sempre sobre o usuario autenticado
- update de perfil usa `ProfileUpdateRequest`
- update de senha usa `ProfilePasswordUpdateRequest`
- alteracao de email invalida verificacao (`email_verified_at = null`)

## Compatibilidade

- endpoints `/perfil/*` sao os oficiais de UI atual
- endpoints legados `/profile` continuam ativos para testes e fluxos antigos
- rota `DELETE /profile` ainda existe para compatibilidade do scaffold

## Integracoes

- `Administration`: `User` e `Gender`
- `Configuration`: `Occupation`
- `Audit`: registro de acoes
- `Auth`: sessao valida

## Riscos

- atualizar perfil por id arbitrario
- alterar senha sem ajustar flags de seguranca
- remover notificacao de troca de senha
