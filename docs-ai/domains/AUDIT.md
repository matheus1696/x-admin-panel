# Audit

## Responsabilidade

`Audit` registra o histórico geral do sistema em `ActivityLog` e expõe a tela de consulta desse histórico. Ele é separado do histórico operacional de tasks.

## Entidades Principais

- `ActivityLog`
- `ActivityLogHelper`
- `LogController`

## Fluxos Críticos

- Registrar eventos via `ActivityLogHelper::action()`
- Persistir usuário, IP, método, URL e descrição
- Exibir a tela de histórico em `LogController@index`

## Invariantes / Regras

- `user_id` pode ser nulo
- cada evento registra contexto da requisição
- a descrição é definida pela camada chamadora
- `ActivityLog` não substitui `TaskActivity` ou `TaskStepActivity`

## Integrações

- `Dashboard`: registra acesso
- `Profile`: registra visualização e atualização
- `Routes`: a tela é protegida por `audit.logs.view`

## Riscos / Armadilhas

- misturar `ActivityLog` com histórico operacional
- esquecer de registrar ações críticas
- mover regra de negócio para o helper transversal
