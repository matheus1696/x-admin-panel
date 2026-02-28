# Dashboard

## Responsabilidade

`Dashboard` é a página inicial autenticada. No código atual, ele registra o acesso e entrega a view principal; não concentra regra de negócio.

## Entidades Principais

- `DashboardController`
- view `dashboard`
- `ActivityLogHelper`

## Fluxos Críticos

- Acessar `/dashboard`
- Registrar o acesso em `ActivityLog`
- Renderizar a view

## Invariantes / Regras

- a rota fica dentro do grupo `auth` + `verified`
- o controller atual apenas registra e retorna a view
- o acesso gera evento de auditoria

## Integrações

- `Audit`: registra a entrada
- `Auth`: depende de sessão autenticada e e-mail verificado

## Riscos / Armadilhas

- mover regra de negócio pesada para o dashboard
- transformar a página em agregador de queries sem service
