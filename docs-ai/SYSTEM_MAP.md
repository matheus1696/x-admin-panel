# System Map

## 1. Visão Sistêmica

O conjunto documental está organizado em dois eixos:

- `governança`: define limites, prioridade de decisão e restrições de mudança
- `leitura do código`: localiza módulos, explica a composição do sistema e aponta padrões de implementação

No sistema real, o fluxo estrutural observado no código parte de `Organization`, cruza `Task`, e é sustentado por `Administration` e `Configuration`. `Audit`, `Auth`, `Profile`, `Public` e `Dashboard` funcionam como domínios de borda ou suporte.

O conjunto de arquivos se distribui assim:

- `AGENTS.md`: contrato normativo
- `docs-ai/README.md`: índice de navegação
- `docs-ai/ARCHITECTURE.md`: mapa sistêmico
- `docs-ai/DOMAINS.md`: índice de domínios
- `docs-ai/domains/*`: detalhe por módulo
- `docs-ai/CONVENTIONS.md`: padrão de implementação

Esse arranjo separa:

- o que não deve ser quebrado
- onde cada coisa mora
- como o projeto escreve código

## 2. Relação Entre Documentos

### Precedência

1. `AGENTS.md`
2. `docs-ai/ARCHITECTURE.md`
3. `docs-ai/DOMAINS.md`
4. `docs-ai/domains/*`
5. `docs-ai/CONVENTIONS.md`
6. `docs-ai/README.md`

### Papel De Cada Camada

- `AGENTS.md` governa decisão e limites.
- `ARCHITECTURE.md` conecta módulos e camadas.
- `DOMAINS.md` escolhe o domínio correto.
- `docs-ai/domains/*` delimita escopo local de cada módulo.
- `CONVENTIONS.md` orienta forma de implementação.
- `README.md` só direciona leitura.

### Regra De Conflito

- Se houver conflito entre navegação e regra, `AGENTS.md` prevalece.
- Se houver conflito entre visão global e detalhe local, `ARCHITECTURE.md` define o contexto e `docs-ai/domains/*` define o escopo do módulo.
- Se houver conflito entre estilo e comportamento estrutural, `ARCHITECTURE.md` e o domínio afetado prevalecem sobre `CONVENTIONS.md`.

## 3. Dependência Entre Módulos

### Cadeia Principal

`Organization` -> `Task` -> `Administration`

### Dependências Diretas Observadas

- `Organization` -> `Task`
  - `WorkflowStep.organization_id` e `TaskStep.organization_id` conectam estrutura e execução
- `Task` -> `Administration`
  - tasks dependem de usuários, status, categorias e prioridades
- `Administration` -> `Configuration`
  - `User` referencia `Occupation`
- `Public` -> `Configuration`
  - contatos consultam `Department` e `Establishment`
- `Profile` -> `Administration`
  - usa `User` e `Gender`
- `Profile` -> `Configuration`
  - usa `Occupation`
- `Dashboard` -> `Audit`
  - registra acesso
- `Profile` -> `Audit`
  - registra visualização e alteração
- `Auth` -> `Administration`
  - usa `User` como identidade autenticável

### Dependências De Borda

- `Audit` depende de eventos emitidos por outros módulos
- `Dashboard` depende da malha autenticada
- `Public` é leitura de dados já mantidos por outros módulos

## 4. Fluxo De Leitura Recomendado Para Agentes

### Mudança Estrutural

1. `AGENTS.md`
2. `docs-ai/ARCHITECTURE.md`
3. `docs-ai/DOMAINS.md`
4. `docs-ai/domains/ORGANIZATION.md`
5. `docs-ai/CONVENTIONS.md`

### Mudança Em Execução

1. `AGENTS.md`
2. `docs-ai/ARCHITECTURE.md`
3. `docs-ai/domains/TASK.md`
4. `docs-ai/domains/ADMINISTRATION.md`
5. `docs-ai/CONVENTIONS.md`

### Mudança Em Acesso Ou Identidade

1. `AGENTS.md`
2. `docs-ai/DOMAINS.md`
3. `docs-ai/domains/AUTH.md`
4. `docs-ai/domains/PROFILE.md`
5. `docs-ai/domains/ADMINISTRATION.md`

### Mudança Em Cadastro Base

1. `docs-ai/DOMAINS.md`
2. `docs-ai/domains/CONFIGURATION.md`
3. `docs-ai/domains/PUBLIC.md`
4. `docs-ai/CONVENTIONS.md`

### Leitura Inicial

1. `docs-ai/README.md`
2. `docs-ai/DOMAINS.md`
3. `docs-ai/ARCHITECTURE.md`

## 5. Mapa De Risco Arquitetural

### Risco Máximo

- `Organization`
  - quebra de hierarquia propaga erro estrutural para workflow e task

### Risco Alto

- `Task`
  - concentra execução, histórico, kanban e parte da lógica hoje distribuída entre UI e service
- `Administration`
  - altera acesso e parametrização usada por task

### Risco Médio

- `Configuration`
  - afeta referências usadas por usuário, contatos e cadastros relacionados
- `Auth`
  - altera a fronteira de entrada do sistema
- `Profile`
  - afeta dados do usuário autenticado e segurança de senha

### Risco Menor

- `Audit`
  - reduz rastreabilidade se quebrado, mas não define execução principal
- `Dashboard`
  - afeta entrada e navegação
- `Public`
  - afeta leitura pública, sem papel estrutural na execução interna

### Zonas De Atenção

- `TaskPage` como ponto de acoplamento entre UI e regra
- lógica por título em status de task
- regras de hierarquia centralizadas em service único
- lookup de estabelecimento por `code`

## 6. Onde Buscar Informação Antes De Alterar Algo

### Antes De Mudar Regra

- `AGENTS.md`
- `docs-ai/ARCHITECTURE.md`
- `docs-ai/domains/<MODULO>.md`

### Antes De Mudar Local De Código

- `docs-ai/DOMAINS.md`
- `docs-ai/CONVENTIONS.md`

### Antes De Mudar Fluxo Que Cruza Módulos

- `docs-ai/ARCHITECTURE.md`
- domínio principal
- domínios dependentes

### Antes De Refatorar

- `AGENTS.md`
- `docs-ai/ARCHITECTURE.md`
- `docs-ai/CONVENTIONS.md`

### Antes De Mudar Algo Não Confirmado

- confirme no código primeiro
- se a relação não estiver explícita em model, route, Livewire ou service, trate como `Inferência`

## Inferências Explícitas

- A cadeia `Organization` -> `Task` -> `Administration` é baseada nas dependências observadas, mas a ordem de criticidade entre módulos de suporte é uma interpretação de impacto.
- A classificação de risco por módulo fora do núcleo operacional é `Inferência` quando depende de efeito indireto e não de acoplamento técnico direto.
