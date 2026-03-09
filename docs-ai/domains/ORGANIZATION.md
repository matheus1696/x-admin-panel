# Organization

## Responsabilidade

Dominio estrutural do sistema.
Mantem organograma (`OrganizationChart`) e template de processo (`Workflow` e `WorkflowStep`).

## Entidades Principais

- `OrganizationChart`
- `Workflow`
- `WorkflowStep`
- `OrganizationChartService`
- `WorkflowStepService`

## Fluxos Criticos

- Criar/editar setor
- Validar parent e evitar ciclo
- Reordenar hierarquia apos mutacao
- Criar/editar workflow e etapas
- Manter ordem e prazo acumulado das etapas

## Invariantes

- raiz usa `hierarchy = 0`
- no filho deve apontar para pai valido
- proibido auto-relacionamento e ciclo
- `step_order` coerente no workflow
- `total_estimated_days` coerente com etapas

## Integracoes

- `Task`: copia de workflow para task step
- `Administration`: autorizacao por permissao nas rotas

## Riscos

- quebrar reorder compromete leitura da arvore
- duplicar traversal fora do service central gera divergencia
- alterar no usado por workflow afeta execucao derivada