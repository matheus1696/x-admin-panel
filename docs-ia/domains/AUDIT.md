# Audit

## Responsabilidade

`Audit` registra o historico institucional geral do sistema em `ActivityLog` e expoe a tela de consulta desse historico.
Ele nao substitui historicos operacionais especializados, como `TaskActivity`, `TaskStepActivity` ou `ProcessEvent`.

## Entidades E Controllers Principais

- `ActivityLog`
- `ActivityLogHelper`
- `LogController`

## Fluxos Criticos

- registrar eventos via `ActivityLogHelper::action()`
- persistir usuario, IP, metodo, URL e descricao
- exibir a tela de historico em `LogController@index`

## Invariantes

- `user_id` pode ser nulo
- cada evento registra contexto da requisicao
- a descricao e definida pela camada chamadora
- `ActivityLog` nao substitui historico operacional especializado

## Integracoes

- `Dashboard`: registra acesso
- `Profile`: registra visualizacao e atualizacao
- `Routes`: a tela e protegida por `audit.logs.view`

## Riscos

- misturar `ActivityLog` com historico operacional
- esquecer de registrar acoes institucionais relevantes
- mover regra de negocio para o helper transversal
