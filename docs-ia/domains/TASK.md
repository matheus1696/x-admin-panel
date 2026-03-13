# Task

## Responsabilidade

Dominio de execucao operacional.
Controla hubs, tarefas, etapas, kanban e historico operacional.

## Entidades E Services Principais

- `TaskHub`
- `TaskHubMember`
- `Task`
- `TaskStep`
- `TaskActivity`
- `TaskStepActivity`
- `TaskService`
- `TaskCategoryService`

## Interfaces Principais

- `tasks.index`: hubs disponiveis
- `tasks.show`: quadro operacional do hub

## Fluxos Criticos

- criar hub e associar owner
- controlar acesso por ownership, membership ou compartilhamento
- criar tarefa via `TaskService`
- criar etapa manual ou por copia de workflow
- mover task e step no kanban com reorder e log
- completar, cancelar e reabrir etapa com motivo
- registrar historico de cada mutacao relevante

## Invariantes

- `Task` pertence a um `TaskHub`
- `TaskStep` pertence a `Task` e ao mesmo hub
- `kanban_order` deve permanecer consistente por coluna
- transicoes terminais mantem coerencia de datas
- status e categorias sao contextualizados por hub
- acesso ao hub nao deve ignorar ownership e membership

## Regras Kanban

- mover etapa sempre atualiza status, datas, ordem e historico
- etapa obrigatoria anterior precisa estar concluida antes do avancar
- colunas terminais ficam ao final do kanban
- transicao para `Concluida` ou `Cancelada` exige motivo
- reabertura saindo de status terminal exige motivo
- e proibido mover diretamente `Concluida -> Cancelada` ou `Cancelada -> Concluida`
- filtros visuais nao podem corromper a ordenacao real

## Estado Atual

- escritas de `TaskAside` e `TaskStepAside` foram consolidadas em `TaskService`
- `TaskPage` ainda concentra parte da orquestracao do modulo
- `TaskService` segue como ponto principal de consistencia

## Integracoes

- `Organization`: workflow e `organization_id` das etapas
- `Administration`: usuarios, status, categorias e prioridades
- `Auth`: usuario autenticado para acesso e auditoria

## Riscos

- mutacao fora de service quebra consistencia
- reorder incorreto quebra leitura do kanban
- logica terminal baseada em titulo exige cuidado ao renomear status
- permissao divergente entre rota, componente e service gera acesso inconsistente

## Estabilidade Livewire No Task Page

- asides de task e step devem ser controlados por estado Livewire
- fechamento de aside deve ocorrer por evento Livewire
- `wire:key` estavel evita erro de snapshot
- em UI PT-BR, revisar mojibake antes de concluir edicao de view
