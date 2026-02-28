# Task

## Responsabilidade

`Task` é o domínio de execução. Ele organiza trabalho em `TaskHub`, mantém tarefas e etapas, controla listas e kanban e registra histórico operacional. Também faz a ponte entre workflow e execução concreta.

## Entidades Principais

- `TaskHub`
- `TaskHubMember`
- `Task`
- `TaskStep`
- `TaskActivity`
- `TaskStepActivity`
- `TaskService`

## Fluxos Críticos

- Criar `TaskHub` e adicionar o owner como membro
- Liberar acesso ao hub apenas para owner ou membro
- Criar `Task` via `TaskService::create()`
- Criar `TaskStep` manualmente ou a partir de workflow
- Mover tarefas no kanban com reorder e histórico
- Mover etapas no kanban com reorder, finalização e histórico

## Invariantes / Regras

- `Task` pertence a `TaskHub`
- `TaskStep` pertence a `Task` e ao mesmo `TaskHub`
- apenas owner e membros acessam o hub
- inclusão e remoção de membros passam pelo owner
- `kanban_order` precisa ser consistente por coluna e hub
- status terminais ajustam `finished_at`
- entrada em execução pode preencher `started_at`
- conclusão, cancelamento e reabertura pedem motivo na UI
- cópia de workflow preserva sequência, `organization_id` e prazo acumulado
- `code` depende do `acronym` do hub

## Integrações

- `Organization`: usa `Workflow` e `OrganizationChart`
- `Administration`: consome status, categorias, prioridades e usuários
- `Auth`: usa o usuário autenticado em acesso e histórico

## Riscos / Armadilhas

- criar `TaskStep` fora das regras pode desalinhar tarefa e hub
- mover cards sem atualizar `kanban_order` quebra a coluna
- ignorar motivo em mudança terminal enfraquece o histórico
- mudar títulos de status sem revisar `TaskService` pode quebrar lógica terminal
