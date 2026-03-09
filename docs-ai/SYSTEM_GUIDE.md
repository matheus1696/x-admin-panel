# SYSTEM GUIDE

Guia operacional consolidado para manutencao segura do X-AdminPanel.
Atualizado em: 2026-03-08.

## 1. Invariantes

### 1.1 Camadas

- Fluxo obrigatorio: `Services -> Controllers/Livewire -> Blade`
- Regra de negocio e consistencia ficam em `Services`
- `Livewire` gerencia estado e entrada
- `Blade` nao decide regra de dominio

### 1.2 Linguagem E Codificacao

- UI do usuario final: PT-BR
- Arquivos e docs: UTF-8
- Backend: Ingles

### 1.3 Hierarquia (Organization)

- `hierarchy = 0` representa raiz
- `hierarchy > 0` deve apontar para `organization_charts.id` valido
- proibido pai igual ao proprio no
- proibido ciclo
- mutacao estrutural exige `reorder()`

### 1.4 Workflow

- `WorkflowStep` sempre pertence a `Workflow`
- `step_order` coerente dentro do workflow
- `total_estimated_days` coerente com as etapas
- `organization_id`, quando informado, deve existir

### 1.5 Task

- `Task` pertence a `TaskHub`
- `TaskStep` pertence a `Task` e ao mesmo hub
- mover cards/steps preserva `kanban_order`
- transicoes terminais ajustam `finished_at`
- acesso ao hub somente owner, membro ou acesso compartilhado valido

### 1.6 Catalogos De Task

- `task_statuses` e `task_step_statuses` sao por hub (`task_hub_id`)
- `task_categories` sao por hub
- `task_step_categories` sao catalogo de etapa

### 1.7 Auth/Profile

- `User` e identidade autenticavel
- perfil atua sobre usuario autenticado
- mudanca de email invalida verificacao (`email_verified_at = null`)
- rotas legadas `/profile` mantidas por compatibilidade
- autorizacao de roles/permissoes baseada em Spatie Permission

### 1.8 Assets

- `Asset` em estoque usa estado `IN_STOCK`
- ativos em estoque nao aparecem na lista operacional
- finalizacao de nota gera ativos pendentes em estoque
- nota finalizada bloqueia alteracao de itens
- mudanca de estado/unidade/setor registra `asset_event`

## 2. Fluxos Criticos

### 2.1 Reordenacao Do Organograma

1. Validar pai e ciclo
2. Persistir alteracao
3. Executar `OrganizationChartService::reorder()`
4. Abortar operacao em inconsistencias

### 2.2 Copia De Workflow Para Task

1. Validar tarefa sem etapas existentes
2. Carregar workflow e ordenar por `step_order`
3. Criar `TaskStep` preservando `organization_id`
4. Calcular prazo acumulado e registrar historico

### 2.3 Kanban De Task E Step

1. Validar origem/destino
2. Aplicar regras de bloqueio de etapa obrigatoria
3. Atualizar status e ordem em transacao
4. Registrar atividade com motivo quando aplicavel

### 2.4 Compartilhamento De Hub

1. Apenas owner gerencia membros/setores
2. Evitar duplicacao de membro no pivot
3. Preservar ownership

### 2.5 Entrada E Liberacao De Ativos

1. Cadastrar nota e itens
2. Finalizar nota com item e valor total > 0
3. Gerar ativos em estoque para saldo pendente
4. Liberar unitario ou por pedido em lote
5. Registrar evento de movimentacao para cada mutacao

## 3. Limites Por Modulo

- `Organization`: dono da hierarquia e estrutura
- `Workflow`: dono do template de execucao
- `Task`: dono da execucao operacional
- `Assets`: dono do patrimonio operacional
- `Administration`: dono de usuarios, permissoes e catalogos
- `Configuration`: dono de referencias base
- `Auth/Profile/Public/Dashboard/Audit`: borda e suporte

## 4. Acoplamentos Sensiveis

- `Organization -> Task` por `organization_id`
- `Task -> Administration` por usuarios e catalogos
- `Assets -> Administration` por usuarios, fornecedores e produtos
- `Assets -> Configuration` por unidade, setor e bloco financeiro
- `Profile/Auth -> Administration` por identidade
- `Profile/Public -> Configuration`

## 5. Checklist De Revisao Estrutural

### Services

- [ ] Regra de negocio esta em service?
- [ ] Fluxo critico usa transacao quando necessario?
- [ ] Historico operacional foi registrado?

### Livewire/Controllers

- [ ] Camada apenas orquestra entrada e estado?
- [ ] Sem escrita direta de dominio com regra sensivel?
- [ ] Autorizacao consistente entre rota e contexto?

### Models

- [ ] Relacoes, `fillable` e `casts` coerentes?
- [ ] Sem logica de negocio pesada em eventos?

### Migrations/Seeders

- [ ] FK e unique sustentam invariantes?
- [ ] Seeder compativel com schema atual?

### Views

- [ ] Blade declarativa, sem query de dominio?
- [ ] Texto da UI em PT-BR?
- [ ] Em pagina Livewire grande, estado Alpine extraido para funcao dedicada (evitar `x-data` inline extenso)?
- [ ] Browser log sem `Alpine Expression Error`/variaveis `undefined` antes de validar bug de backend?
- [ ] Sem mojibake em textos de UI (`rg -n "Ã|Â|�|â|Å" resources/views app/Livewire`)?

## 6. Criterio De Pronto

Uma mudanca estrutural so esta pronta quando:

- integridade da hierarquia esta preservada
- limites de modulo continuam claros
- regras nao estao duplicadas sem necessidade
- autorizacao esta consistente
- impacto de performance foi avaliado
- risco residual esta documentado
