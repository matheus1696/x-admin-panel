# Organization

## Responsabilidade

`Organization` mantém a estrutura organizacional (`OrganizationChart`) e o modelo de processo (`Workflow` e `WorkflowStep`). É o domínio estrutural do sistema: fornece a hierarquia usada na visualização e também o vínculo organizacional consumido por workflows e etapas de tarefas.

## Entidades Principais

- `OrganizationChart`
- `Workflow`
- `WorkflowStep`
- Regras: `OrganizationChartRules`, `WorkflowStoreRequest`, `WorkflowUpdateRequest`, `WorkflowStepRules`

## Fluxos Críticos

- Criar e editar setores via `OrganizationChartConfigPage` e `OrganizationChartService`
- Recalcular a hierarquia via `OrganizationChartService::reorder()`
- Criar workflows em `WorkflowProcessesPage`
- Manter etapas, ordem e prazo em `WorkflowSteps` e `WorkflowStepService`
- Propagar `organization_id` para execução quando um workflow é copiado para task

## Invariantes / Regras

- `hierarchy` aponta para o pai; raízes usam `0`
- mudanças estruturais exigem recálculo de `order` e `number_hierarchy`
- `order` é derivado, não manual
- `children()` retorna apenas nós ativos
- `WorkflowStep` sempre pertence a `Workflow`
- `WorkflowStep.organization_id`, quando preenchido, referencia `OrganizationChart`
- `Workflow.total_estimated_days` acompanha as etapas
- `step_order` precisa ser coerente dentro do workflow

## Integrações

- `Task`: `TaskPage` copia `WorkflowStep` para `TaskStep`
- `Administration`: rotas do módulo são protegidas por permissão

## Riscos / Armadilhas

- alterar `hierarchy` sem `reorder()` quebra a leitura da árvore
- duplicar lógica de árvore fora do service cria resultados diferentes
- mover ou desativar nós usados por workflows afeta a execução derivada
- tratar workflow como cadastro simples ignora seu papel de template operacional
