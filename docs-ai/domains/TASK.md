# Task

## Responsabilidade

Dominio de execucao operacional.
Controla ambientes (`TaskHub`), tarefas, etapas, kanban e historico operacional.

## Entidades Principais

- `TaskHub`
- `TaskHubMember`
- `Task`
- `TaskStep`
- `TaskActivity`
- `TaskStepActivity`
- `TaskService`

## Fluxos Criticos

- Criar hub e associar owner
- Controlar acesso por ownership/membership
- Criar tarefa via `TaskService`
- Criar etapa manual ou por copia de workflow
- Mover task e step no kanban com reorder e log
- Completar, cancelar e reabrir etapa com motivo

## Invariantes

- `Task` pertence a um `TaskHub`
- `TaskStep` pertence a `Task` e ao mesmo `TaskHub`
- `kanban_order` deve permanecer consistente por coluna
- transicoes terminais devem manter coerencia de datas
- status e categorias sao contextualizados por hub

## Regras Kanban (Task + Step)

- mover etapa no kanban sempre atualiza `task_status_id`, `started_at`/`finished_at`, reorder e historico
- colunas terminais (`Concluida` e `Cancelada`) ficam no final do kanban, mesmo com novos status
- transicao para `Concluida` ou `Cancelada` exige motivo obrigatorio
- motivo obrigatorio deve gerar comentario e atividade de auditoria (`kanban_move` + evento de dominio)
- reabertura (saindo de status terminal para nao terminal) exige motivo obrigatorio
- e proibido mover diretamente `Concluida -> Cancelada` ou `Cancelada -> Concluida`
- os motivos de conclusao/cancelamento/reabertura devem aparecer com destaque visual por cor no historico
- as mesmas regras devem ser aplicadas em `TaskPage`, `TaskAside` e `TaskStepAside`

## Estado Atual

- Escritas de `TaskAside` e `TaskStepAside` foram movidas para `TaskService`.
- `TaskPage` ainda concentra parte da orquestracao do modulo (acoplamento conhecido).
- `TaskService` permanece o ponto principal de consistencia de execucao.

## Integracoes

- `Organization`: workflow e `organization_id` das etapas
- `Administration`: usuarios, status, categorias e prioridades
- `Auth`: usuario autenticado para acesso e auditoria

## Riscos

- mutacao fora de service pode quebrar consistencia
- reorder incorreto quebra leitura do kanban
- logica terminal por titulo de status exige cuidado em renomeacoes

## Estabilidade Livewire No Task Page

- asides de task e step devem ser controlados por estado Livewire (`selectedTaskId` e `selectedStepId`), evitando estado de abertura em Alpine para esses paines
- fechamento de `task-aside` e `task-step-aside` deve ocorrer por eventos Livewire (`task-aside-close` e `task-step-aside-close`)
- manter `wire:key` estavel nos componentes filhos de aside para evitar erros de snapshot (`Could not find Livewire component in DOM tree`)
- em UI PT-BR, evitar caracteres mojibake em views (`Ã`, `â€¢`, `â€”`, `Â`)
