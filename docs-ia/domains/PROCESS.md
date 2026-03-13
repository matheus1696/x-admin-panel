# Process

## Responsabilidade

`Process` governa o fluxo administrativo institucional orientado por etapas, setor responsavel e despacho.

## Entidades E Services Principais

- `Process`
- `ProcessStep`
- `ProcessEvent`
- `ProcessStatus`
- `ProcessUserView`
- `ProcessService`
- `ProcessEventService`

## Interfaces Principais

- `process.index`
- `process.dashboard`
- `process.show`

## Fluxos Criticos

- abrir processo com ou sem workflow
- criar etapas do processo a partir do workflow
- sincronizar setores relacionados ao processo
- avancar etapa com comentario obrigatorio
- retornar etapa com comentario obrigatorio
- comentar despacho
- atribuir owner dentro do setor da etapa atual
- marcar visualizacao e detectar atualizacoes nao lidas
- gerar indicadores por janela temporal e setor

## Invariantes

- owner ou usuarios de setores vinculados podem visualizar
- apenas usuarios do setor da etapa atual executam acoes de despacho
- deve existir apenas uma etapa corrente
- avancar e retornar nao podem ocorrer em processo `CLOSED` ou `CANCELLED`
- `ProcessEvent` e append-only com `event_number` crescente
- `organization_id` do processo acompanha a etapa corrente quando aplicavel

## Integracoes

- `Organization`: workflow e setores
- `Administration`: owner e usuarios por setor
- `Dashboard`: resumo e entradas recentes
- `Notification`: notificacoes por interacao do processo

## Riscos

- permitir despacho fora do setor corrente quebra governanca
- perder sincronizacao entre etapa corrente e `organization_id` distorce filtros
- criar etapa corrente duplicada invalida analytics e permissoes
- mover notificacao para a UI tira rastreabilidade do fluxo
