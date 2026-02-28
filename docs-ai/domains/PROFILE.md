# Profile

## Responsabilidade

`Profile` mantém os dados do próprio usuário autenticado: edição cadastral e troca de senha. É um fluxo de autoatendimento com auditoria explícita.

## Entidades Principais

- `ProfileController`
- `ProfileUpdateRequest`
- `ProfilePasswordUpdateRequest`
- `User`
- `Gender`
- `Occupation`
- `UserPasswordResetedMail`

## Fluxos Críticos

- Exibir a tela de edição
- Atualizar os dados do usuário autenticado
- Exibir a tela de senha
- Trocar senha e marcar `password_default = false`
- Enviar e-mail após troca
- Registrar auditoria das ações

## Invariantes / Regras

- o módulo sempre atua sobre `Auth::user()`
- atualização cadastral usa `ProfileUpdateRequest`
- atualização de senha usa `ProfilePasswordUpdateRequest`
- troca de senha ajusta `password_default`
- troca de senha envia e-mail

## Integrações

- `Administration`: usa `User` e `Gender`
- `Configuration`: usa `Occupation`
- `Audit`: registra as ações
- `Auth`: depende de sessão válida

## Riscos / Armadilhas

- usar um `id` arbitrário em vez do usuário autenticado
- trocar senha sem atualizar `password_default`
- remover o e-mail de aviso
