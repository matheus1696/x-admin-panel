# Organization

## Responsabilidade

Dominio estrutural do sistema.
Mantem organograma (`OrganizationChart`) e templates de processo (`Workflow` e `WorkflowStep`).

## Entidades E Services Principais

- `OrganizationChart`
- `Workflow`
- `WorkflowStep`
- `OrganizationChartService`
- `WorkflowService`
- `WorkflowStepService`

## Interfaces Principais

- `chart.index` e `chart.full.index`: leitura do organograma
- `organization.manage.chart`: configuracao estrutural
- `organization.manage.workflow`: manutencao de workflows

## Fluxos Criticos

- criar e editar setor
- validar parent, auto-relacionamento e ciclo
- reordenar hierarquia apos mutacao
- sincronizar usuarios por setor
- criar e editar workflow e etapas
- manter `step_order` e `total_estimated_days`

## Invariantes

- raiz usa `hierarchy = 0`
- no filho deve apontar para pai valido
- proibido auto-relacionamento e ciclo
- `order` e `number_hierarchy` dependem de `reorder()`
- `step_order` deve permanecer ordenado no workflow
- soma de `deadline_days` precisa refletir `total_estimated_days`

## Integracoes

- `Process`: usa workflow como template de processo
- `Task`: pode copiar etapas de workflow para execucao operacional
- `Administration`: usuarios e permissao entram pela borda

## Riscos

- quebrar `reorder()` compromete leitura da arvore
- duplicar traversal fora de `OrganizationChartService` gera divergencia
- alterar regra de etapa sem alinhar `WorkflowStepService` quebra prazo acumulado
- mudar setor usado por workflow afeta `Process` e `Task`
