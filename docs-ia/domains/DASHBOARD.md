# Dashboard

## Responsabilidade

`Dashboard` e a pagina inicial autenticada.
Ele agrega um resumo leve do sistema, sem assumir ownership de regra de negocio.

## Entidades E Services Principais

- `DashboardController`
- view `dashboard`
- `ActivityLogHelper`
- `TaskService`
- `ProcessService`
- `NotificationService`

## Fluxos Criticos

- acessar `/dashboard`
- registrar o acesso no `ActivityLog`
- carregar resumo de tasks do usuario
- carregar entradas recentes de processos quando o usuario pode visualizar processos
- carregar resumo de notificacoes

## Invariantes

- a rota fica dentro do grupo `auth` + `verified`
- dashboard nao deve escrever negocio alem do log de acesso
- consultas devem permanecer enxutas e delegadas a services

## Integracoes

- `Audit`: registra a entrada
- `Task`: overview pessoal
- `Process`: entradas recentes e statuses
- `Notification`: resumo de notificacoes
- `Auth`: depende de sessao autenticada e email verificado

## Riscos

- mover regra de negocio pesada para o dashboard
- transformar a pagina em agregador de queries de dominio sem service
- esconder checagem de permissao dentro da view em vez da borda e controller
