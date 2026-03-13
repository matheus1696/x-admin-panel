# Components Guide

Catalogo operacional dos componentes Blade compartilhados em `resources/views/components`.
Baseado no codigo real em 2026-03-12.
Este arquivo deve ser lido em conjunto com `LAYOUT_GUIDE.md`, `CONVENTIONS.md` e `SYSTEM_GUIDE.md`.

Objetivos:
- documentar a API atual dos componentes;
- explicitar variacoes suportadas hoje;
- registrar inconsistencias ja presentes no repositorio;
- definir uma padronizacao alvo para alinhamento posterior.

## 1. Escopo

Inventario atual:
- `x-button`
- `x-modal`
- `x-pagination`
- `x-alert.flash`
- `x-auth-session-status`
- `x-application-logo`
- `x-page.*`
- `x-form.*`
- `x-sidebar.*`

Nao entram aqui:
- componentes Livewire de tela;
- layouts (`resources/views/layouts/*`);
- slots ad-hoc locais de um modulo especifico.

## 2. Regras De Leitura

Cada componente abaixo possui:
- `Uso`: responsabilidade principal.
- `Props atuais`: API real observada no arquivo.
- `Variacoes atuais`: variantes e estados hoje suportados.
- `Padrao alvo`: direcao para consolidacao futura.

Classificacao de padronizacao:
- `Canonico`: componente ja pode ser reutilizado como base do sistema.
- `Em consolidacao`: componente util, mas com API/estilo ainda irregular.
- `Legado/tatico`: manter por compatibilidade, evitar expandir sem revisao.

## 3. Mapa Rapido

### 3.1 Fundacao

| Componente | Status | Observacao |
|---|---|---|
| `x-button` | Em consolidacao | Muitas variantes e alguns aliases usados nao existem na implementacao |
| `x-modal` | Em consolidacao | Boa base visual, mas acoplado a `closeModal()` |
| `x-alert.flash` | Canonico | Boa base para feedback global |
| `x-pagination` | Canonico | Focado em Livewire paginado |
| `x-auth-session-status` | Legado/tatico | Hoje fica oculto |
| `x-application-logo` | Canonico | Componente simples de branding |

### 3.2 Pagina

| Componente | Status | Observacao |
|---|---|---|
| `x-page.header` | Canonico | Header principal de tela |
| `x-page.filter` | Canonico | Bloco padrao de filtros expansivel |
| `x-page.card` | Canonico | Container simples de bloco |
| `x-page.table` | Em consolidacao | Base util, mas com props nao utilizadas |
| `x-page.table-th` | Canonico | Celula de cabecalho padronizada |
| `x-page.table-td` | Canonico | Celula padronizada |
| `x-page.table-status` | Canonico | Badge booleano simples |

### 3.3 Formulario

| Componente | Status | Observacao |
|---|---|---|
| `x-form.label` | Canonico | Simples e consistente |
| `x-form.error` | Canonico | Responsabilidade unica |
| `x-form.input` | Em consolidacao | API rica e flexivel |
| `x-form.textarea` | Em consolidacao | API mais pobre que `input` |
| `x-form.select` | Em consolidacao | Select customizado para Blade puro |
| `x-form.select-livewire` | Em consolidacao | Select customizado para Livewire |

### 3.4 Navegacao

| Componente | Status | Observacao |
|---|---|---|
| `x-sidebar.main-link` | Canonico | Link principal da sidebar |
| `x-sidebar.main-dropdown` | Canonico | Grupo principal colapsavel |
| `x-sidebar.dropdown` | Canonico | Subgrupo colapsavel |
| `x-sidebar.dropdown-link` | Canonico | Link de subnivel |

## 4. Fundacao

### 4.1 `x-button`

Uso:
- CTA primaria, secundaria, destrutiva e acao iconografica.

Props atuais:
- `href`, `type`, `variant`, `icon`, `text`, `loading`, `disabled`
- `fullWidth`, `size`, `pill`, `shadow`, `withIconRight`
- `loadingText`, `spinner`, `preventSubmit`

Variacoes atuais:
- modos:
  - link (`href`)
  - button simples
  - button com auto-submit protegido (`preventSubmit`)
- tamanhos:
  - `xs`, `sm`, `md`, `lg`
- variantes implementadas:
  - solid: `green_solid`, `gray_solid`, `yellow_solid`, `red_solid`, `blue_solid`, `sky_solid`, `indigo_solid`, `purple_solid`
  - outline: `gray_outline`, `green_outline`, `blue_outline`, `red_outline`, `yellow_outline`, `purple_outline`
  - text: `white_text`, `gray_text`, `green_text`, `blue_text`, `red_text`, `yellow_text`, `purple_text`, `sky_text`, `indigo_text`
  - light: `gray_light`, `green_light`, `blue_light`
  - especiais: `purple_premium`, `blue_gradient`, `green_gradient`, `gray_dark`, `white_dark`

Padrao atual consolidado:
- variante default: `primary`
- familias canonicas:
  - `primary`
  - `secondary`
  - `destructive`
  - `ghost`
  - `link`
  - `success`
  - `warning`
  - `info`
- aliases legados continuam aceitos por compatibilidade:
  - `green`, `gray`, `blue`, `red`, `yellow`, `sky`
  - `default`, `filled`, `inline`, `minimal`, `pills`
- aliases curtos agora sao normalizados internamente para variantes implementadas.

Padrao alvo:
- preferir sempre as familias canonicas em novos usos;
- manter cores especiais (`blue`, `purple`, `sky`) apenas para contexto semantico de modulo;
- reduzir gradualmente o uso de aliases legados nas views;
- manter `preventSubmit` apenas como compatibilidade, evitando expandir essa responsabilidade.

### 4.2 `x-modal`

Uso:
- modal base para confirmacao, formulario curto e detalhes contextuais.

Props atuais:
- `show`, `size`, `maxWidth`, `closeable`, `closeMethod`
- `title`, `description`, `panelClass`, `bodyClass`, `footerClass`

Slots atuais:
- `header`
- `default`
- `footer`

Variacoes atuais:
- exibicao condicional por `show`;
- larguras semanticas por `size` com fallback para `maxWidth`;
- fechamento por overlay, clique fora e `Esc` quando `closeable=true`.

Estado atual consolidado:
- `size` suporta `sm`, `md`, `lg`, `xl`, `2xl` e `full`;
- `maxWidth` continua aceito para compatibilidade;
- fechamento continua integrado a Livewire, mas agora pode usar outro metodo via `closeMethod`;
- `header` e `footer` seguem como slots oficiais;
- `title` e `description` permitem uso sem slot de cabecalho quando o modal e simples.

Padrao alvo:
- manter como modal canonico do sistema;
- preferir `size` em novos usos e deixar `maxWidth` apenas como escape hatch;
- manter `header` e `footer` como slots oficiais;
- criar variante obrigatoria de confirmacao destrutiva com foco inicial seguro em `Cancelar`.

### 4.3 `x-alert.flash`

Uso:
- toasts globais disparados por sessao ou evento `app-flash`.

Props atuais:
- sem props declaradas; trabalha com sessao e evento de browser.

Variacoes atuais:
- `success`
- `error`
- `warning`
- `info`

Padrao alvo:
- componente canonico de feedback global;
- todos os fluxos assincronos devem convergir para esta superficie;
- padronizar duracao default em `4s` e microcopy objetiva.

### 4.4 `x-pagination`

Uso:
- paginacao customizada para listas Livewire.

Props atuais:
- `paginator`

Padrao alvo:
- manter como base canonica de paginacao administrativa;
- evoluir apenas dentro do mesmo markup, evitando nova familia paralela.

### 4.5 `x-auth-session-status`

Uso:
- compatibilidade com fluxos de autenticacao do Breeze.

Observacao:
- hoje renderiza conteudo com classe `hidden`.

Padrao alvo:
- manter apenas por compatibilidade;
- se voltar a ser exibido, usar `x-alert.flash` ou um alert inline canonico.

### 4.6 `x-application-logo`

Uso:
- logo compartilhada em layouts e auth.

Padrao alvo:
- manter simples;
- qualquer evolucao deve continuar aceitando `class` externa.

## 5. Pagina

### 5.1 `x-page.header`

Uso:
- header padrao de tela com titulo, subtitulo, icone e slot de acao.

Props atuais:
- `icon`, `color`, `title`, `subtitle`, `button`, `badge`, `accordionOpen`

Variacoes atuais:
- cores implementadas:
  - `green`, `blue`, `purple`, `amber`, `red`, `gray`
- aceita badge numerico;
- aceita slot/prop `button`.

Observacoes:
- `accordionOpen` esta declarado, mas nao participa da renderizacao do header;
- na pratica, o repositorio usa majoritariamente slot nomeado `button`.

Padrao alvo:
- header canonico de pagina;
- restringir cores de contexto aos significados semanticos definidos em `LAYOUT_GUIDE.md`;
- oficializar `button` como slot preferencial e evitar prop textual paralela.

### 5.2 `x-page.filter`

Uso:
- container de filtros com accordion local.

Props atuais:
- `accordionOpen`, `title`, `icon`, `description`, `showClear`, `clearAction`
- `gridClass`, `panelClass`, `headerClass`

Variacoes atuais:
- aberto/fechado por `accordionOpen`;
- CTA opcional de limpar por `showClear + clearAction`;
- aceita slot padrao para o conteudo dos filtros;
- aceita slot legado `showBasic` por compatibilidade;
- aceita slot `actions` para acoes auxiliares no header.

Padrao alvo:
- bloco canonico para filtros;
- usar `description` quando o contexto do filtro precisar orientar o usuario;
- usar slot `actions` quando houver CTA auxiliar alem de `Limpar`;
- evitar inserir regra de dominio no slot.

### 5.3 `x-page.card`

Uso:
- bloco visual simples para formularios, resumos e secoes.

Props atuais:
- sem props declaradas.

Padrao alvo:
- card canonico base;
- futuras variacoes devem surgir por props semanticas (`tone`, `padding`, `interactive`) e nao por classes ad-hoc repetidas.

### 5.4 `x-page.table`

Uso:
- tabela administrativa com slots para cabecalho, corpo, rodape e empty state.

Props atuais:
- `pagination`, `striped`, `hover`, `bordered`, `compact`, `stickyHeader`, `emptyMessage`

Slots atuais:
- `thead`
- `tbody`
- `tfoot`
- `emptyAction`

Variacoes atuais:
- linhas listradas;
- hover de linha;
- borda externa;
- empty state customizavel;
- bloco de paginacao integrado.

Inconsistencias atuais:
- `compact` e `stickyHeader` estao expostos, mas nao afetam o markup atual;
- o comentario menciona sticky header, mas o `thead` nao esta sticky.

Padrao alvo:
- manter como base unica de tabela administrativa;
- remover props sem efeito ou implementa-las de fato;
- padronizar coluna de acoes a direita e empty state orientativo.

### 5.5 `x-page.table-th`

Uso:
- celula de cabecalho padronizada.

Props atuais:
- `value`

Padrao alvo:
- manter simples;
- permitir classes locais para alinhamento e largura.

### 5.6 `x-page.table-td`

Uso:
- celula de conteudo padronizada.

Props atuais:
- `value`

Padrao alvo:
- manter simples;
- usar classes locais para alinhamento numerico, truncamento e destaque.

### 5.7 `x-page.table-status`

Uso:
- badge booleano simples dentro de tabela.

Props atuais:
- `condition`, `trueText`, `falseText`

Variacoes atuais:
- verdadeiro: verde
- falso: vermelho

Padrao alvo:
- manter como badge booleano canonico;
- se estados deixarem de ser binarios, migrar para badge semantico dedicado, nao estender este componente indefinidamente.

## 6. Formulario

### 6.1 `x-form.label`

Uso:
- label padrao de campo.

Props atuais:
- `value`
- `icon`

Padrao alvo:
- manter canonico;
- label sempre acima do campo.

### 6.2 `x-form.error`

Uso:
- mensagem inline de erro por campo.

Props atuais:
- `for`

Padrao alvo:
- manter canonico;
- continuar acoplado ao bag de erros padrao.

### 6.3 `x-form.input`

Uso:
- input base do sistema.

Props atuais:
- `disabled`, `name`, `variant`, `size`, `withIcon`, `icon`
- `iconPosition`, `borderColor`, `rounded`, `shadow`, `loading`

Variacoes atuais:
- variantes:
  - `default`, `outline`, `filled`, `minimal`, `glass`, `pills`
- tamanhos:
  - `xs`, `sm`, `md`, `lg`
- cores:
  - `green`, `blue`, `purple`, `red`, `yellow`, `gray`, `sky`, `indigo`
- icone:
  - esquerda ou direita

Padrao alvo:
- input canonico para a maior parte dos campos;
- limitar variantes de uso comum a:
  - `default`
  - `outline`
  - `minimal`
- reservar `glass` e `pills` para casos muito especificos, sem proliferar.

### 6.4 `x-form.textarea`

Uso:
- textarea simples.

Props atuais:
- `disabled`, `name`, `rows`

Inconsistencias atuais:
- nao acompanha a mesma API de `x-form.input` para tamanho, variante, cor ou loading;
- usa estilo proprio, mais simples.

Padrao alvo:
- alinhar visualmente com `x-form.input`;
- acrescentar ao menos `variant`, `size` e `borderColor` se a equipe quiser consistencia total;
- ate la, evitar criar uma segunda familia de textarea.

### 6.5 `x-form.select`

Uso:
- select pesquisavel para Blade puro.

Props atuais:
- `name`, `options`, `collection`, `classes`
- `labelField`, `labelAcronym`, `valueField`
- `selected`, `placeholder`
- `disabled`, `variant`, `size`, `withIcon`, `icon`
- `borderColor`, `searchable`, `rounded`

Variacoes atuais:
- variantes:
  - `default`, `inline`, `outline`, `filled`, `pills`, `minimal`
- tamanhos:
  - `xs`, `sm`, `md`, `lg`
- cores:
  - `green`, `blue`, `purple`, `red`, `yellow`, `gray`
- dados:
  - `options`
  - `collection`

Padrao alvo:
- manter como select canonico para Blade puro;
- preferir `collection` quando os dados vierem de modelos/listas estruturadas;
- usar `default` como variante base e `inline` apenas em filtros compactos.

### 6.6 `x-form.select-livewire`

Uso:
- select pesquisavel integrado ao `wire:model`.

Props atuais:
- mesma familia de `x-form.select`, com exigencia de `wire:model`

Variacoes atuais:
- variantes:
  - `default`, `inline`, `outline`, `filled`, `pills`, `minimal`, `glass`
- tamanhos:
  - `xs`, `sm`, `md`, `lg`
- cores:
  - `green`, `blue`, `purple`, `red`, `yellow`, `gray`, `sky`, `indigo`

Inconsistencias atuais:
- a familia Livewire aceita mais cores e a variante `glass`, enquanto `x-form.select` nao;
- isso cria uma API semelhante, mas nao identica.

Padrao alvo:
- convergir `x-form.select` e `x-form.select-livewire` para a mesma matriz de variantes;
- manter `default`, `inline`, `outline` como trilha principal;
- tratar `glass` como experimental ate haver uso recorrente justificado.

## 7. Navegacao

### 7.1 `x-sidebar.main-link`

Uso:
- item principal de navegacao lateral.

Props atuais:
- `icon`, `title`, `active`, `href`

Padrao alvo:
- componente canonico de primeiro nivel;
- nao criar outro link principal paralelo.

### 7.2 `x-sidebar.main-dropdown`

Uso:
- grupo principal colapsavel da sidebar.

Props atuais:
- `icon`, `title`, `active`

Padrao alvo:
- componente canonico de agrupamento principal;
- manter expansao baseada em estado de contexto da sidebar.

### 7.3 `x-sidebar.dropdown`

Uso:
- subgrupo colapsavel dentro da navegacao.

Props atuais:
- `icon`, `title`, `active`

Padrao alvo:
- usar apenas quando houver necessidade real de terceiro nivel;
- evitar aprofundar demais a arvore visual.

### 7.4 `x-sidebar.dropdown-link`

Uso:
- link interno de submenu.

Props atuais:
- `title`, `href`, `active`, `icon`

Padrao alvo:
- manter simples e previsivel;
- preservar semantica visual entre niveis.

## 8. Inconsistencias Reais Encontradas

### 8.1 Variantes em uso que nao existem no componente

`x-button` possui uso com aliases legados que devem ser migrados aos poucos:
- `green`
- `gray`
- `sky`

Efeito atual:
- esses casos caem no fallback `green_solid`, o que mascara a intencao visual da tela.

Direcao:
- substituir gradualmente por variantes reais;
- se a equipe quiser manter aliases curtos, documentar e implementar explicitamente.

### 8.2 Props expostas sem efeito claro

Casos atuais:
- `x-page.header.accordionOpen`
- `x-page.table.compact`
- `x-page.table.stickyHeader`

Direcao:
- remover da API ou implementar comportamento real antes de ampliar uso.

### 8.3 Assimetria na familia de formularios

Casos atuais:
- `x-form.input` e `x-form.select-livewire` tem API rica;
- `x-form.textarea` ficou para tras;
- `x-form.select` e `x-form.select-livewire` nao compartilham exatamente a mesma matriz de cores/variantes.

Direcao:
- alinhar primeiro a semantica, depois o estilo.

## 9. Padronizacao Proposta Para Alinhamento

### 9.1 Escala canonica de variantes

Para consolidacao futura, a documentacao deve trabalhar com cinco papeis visuais:
- `primary`
- `secondary`
- `destructive`
- `ghost`
- `inline`

Mapeamento inicial sugerido:
- `primary`: verde solido
- `secondary`: cinza outline
- `destructive`: vermelho solido ou outline destrutivo conforme contexto
- `ghost`: texto/acao discreta
- `inline`: controles compactos de filtro e toolbar

### 9.2 Escala canonica de tamanhos

Padrao recomendado:
- `sm`: listas, filtros e toolbars
- `md`: formularios e CTA padrao
- `lg`: destaque pontual

Direcao:
- evitar `xs` fora de contextos de alta densidade administrativa.

### 9.3 Escala canonica de cores

Uso semantico recomendado:
- `green`: confirmacao, salvar, seguir fluxo
- `blue` ou `sky`: informacao, exportacao, navegacao secundaria
- `amber` ou `yellow`: alerta moderado
- `red`: destruir, cancelar, risco
- `gray`: apoio, metadado, neutralidade
- `purple`: administracao/privilegios, quando houver motivo claro

### 9.4 Regra de governanca

Antes de criar nova variante:
1. validar se um papel canonico ja resolve;
2. validar se a diferenca e semantica, nao apenas estetica;
3. documentar no componente e no `LAYOUT_GUIDE.md`;
4. migrar usos antigos gradualmente.

## 10. Proximos Passos Recomendados

Ordem sugerida de alinhamento:
1. normalizar aliases invalidos de `x-button`;
2. alinhar `x-form.textarea` com a familia de campos;
3. limpar props sem efeito em `x-page.header` e `x-page.table`;
4. definir a matriz canonica final de variantes;
5. depois migrar telas por modulo, sem ruptura global.
