# Administration

## Responsabilidade

`Administration` governa o sistema autenticado. O módulo cobre usuários e os catálogos usados pelo domínio de `Task`, como status, prioridades e categorias. Ele define quem opera o sistema e parte da parametrização da execução.

## Entidades Principais

- `User`
- `Gender`
- `TaskStatus`
- `TaskStepStatus`
- `TaskCategory`
- `TaskPriority`
- `TaskStepCategory`
- `UserService`, `TaskStatusService`, `TaskStepStatusService`

## Fluxos Críticos

- Criar e atualizar usuários
- Sincronizar permissões e encerrar sessões do usuário
- Ativar ou desativar usuários
- Criar e atualizar status de tarefa
- Criar e atualizar status de etapa
- Expor catálogos ativos para `TaskPage`

## Invariantes / Regras

- `User` é a identidade autenticável do sistema
- permissões são sincronizadas via Spatie Permission
- alteração de permissão remove sessões existentes
- status usados em execução são carregados com `is_active = true`
- `App\Models\Task` e `App\Models\Administration\Task` são contextos diferentes
- `name_filter` é derivado do nome

## Integrações

- `Task`: fornece status, categorias, prioridades e responsáveis
- `Auth`: usa `User`
- `Configuration`: `User` referencia `Occupation`
- `Profile`: reutiliza `User`, `Gender` e `Occupation`

## Riscos / Armadilhas

- mudar nomes de status sem revisar `TaskService` afeta lógica por título
- misturar catálogos administrativos com execução confunde contexto
- aplicar permissão só na UI deixa a borda inconsistente
