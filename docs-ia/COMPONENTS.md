# Components Guide

Catalogo operacional dos componentes Blade compartilhados em `resources/views/components`.
Atualizado em: 2026-03-13.

Este documento registra a API real dos componentes, os limites de uso e a direcao de consolidacao.
Ele deve ser lido junto com:
- `docs-ia/LAYOUT_GUIDE.md`
- `docs-ia/CONVENTIONS.md`
- `docs-ia/SYSTEM_GUIDE.md`

## 1. Proposito

Este arquivo existe para:
- documentar o inventario real dos componentes compartilhados;
- distinguir o que ja e canonico do que ainda esta em consolidacao;
- evitar proliferacao de markup e estilos paralelos;
- orientar evolucao de API sem quebrar o sistema inteiro.

Regra de ouro:
- componente compartilhado serve para padrao transversal, nao para resolver excecao local de um modulo.

## 2. Escopo

Inclui:
- `x-button`
- `x-modal`
- `x-pagination`
- `x-alert.flash`
- `x-auth-session-status`
- `x-application-logo`
- `x-page.*`
- `x-form.*`
- `x-sidebar.*`

Nao inclui:
- componentes Livewire de tela;
- layouts em `resources/views/layouts/*`;
- parciais ad-hoc de um unico modulo;
- regras de negocio que pertencem a services.

## 3. Como Ler Este Guia

Cada componente abaixo traz:
- `Uso`: papel principal no sistema;
- `API atual`: props ou slots observados no codigo;
- `Leitura operacional`: onde ele encaixa no playbook de layout;
- `Riscos`: onde o componente pode ser mal usado;
- `Direcao`: caminho recomendado para consolidacao futura.

Classificacao:
- `Canonico`: pode ser usado como base do sistema;
- `Em consolidacao`: util e reutilizavel, mas com API irregular ou excesso de variantes;
- `Legado/tatico`: manter por compatibilidade, sem expandir sem revisao.

## 4. Mapa Rapido

### 4.1 Fundacao

| Componente | Status | Papel principal |
|---|---|---|
| `x-button` | Em consolidacao | acao primaria, secundaria, destrutiva e ghost |
| `x-modal` | Em consolidacao | confirmacao, formulario curto e detalhe contextual |
| `x-alert.flash` | Canonico | feedback global |
| `x-pagination` | Canonico | paginacao padronizada |
| `x-auth-session-status` | Legado/tatico | compatibilidade com auth scaffold |
| `x-application-logo` | Canonico | branding compartilhado |

### 4.2 Pagina

| Componente | Status | Papel principal |
|---|---|---|
| `x-page.header` | Canonico | topo da tela |
| `x-page.filter` | Canonico | bloco de filtros padronizado |
| `x-page.card` | Canonico | container base de bloco |
| `x-page.table` | Em consolidacao | tabela administrativa |
| `x-page.table-th` | Canonico | cabecalho de tabela |
| `x-page.table-td` | Canonico | celula de conteudo |
| `x-page.table-status` | Canonico | badge binario simples |

### 4.3 Formulario

| Componente | Status | Papel principal |
|---|---|---|
| `x-form.label` | Canonico | label padrao |
| `x-form.error` | Canonico | erro inline |
| `x-form.input` | Em consolidacao | campo base do sistema |
| `x-form.textarea` | Em consolidacao | campo multiline alinhado a familia base |
| `x-form.select` | Em consolidacao | select para Blade puro |
| `x-form.select-livewire` | Em consolidacao | select para Livewire |

### 4.4 Navegacao

| Componente | Status | Papel principal |
|---|---|---|
| `x-sidebar.main-link` | Canonico | navegacao principal |
| `x-sidebar.main-dropdown` | Canonico | agrupamento principal |
| `x-sidebar.dropdown` | Canonico | subgrupo colapsavel |
| `x-sidebar.dropdown-link` | Canonico | link de submenu |

## 5. Regras Transversais

### 5.1 Reuso antes de criacao

Antes de criar novo componente compartilhado:
1. validar se o caso cabe em um componente existente;
2. validar se a diferenca e semantica, nao apenas estetica;
3. avaliar impacto no `LAYOUT_GUIDE.md`;
4. documentar a mudanca de API.

### 5.2 Semantica antes de variante

Novas props ou variantes devem nascer para resolver:
- papel visual diferente;
- comportamento recorrente;
- contexto transversal entre modulos.

Nao devem nascer para:
- corrigir uma unica tela;
- acomodar improviso de classe;
- duplicar componente quase igual com nome diferente.

### 5.3 Relação com templates de tela

Os componentes deste catalogo sustentam os templates do `LAYOUT_GUIDE.md`:
- `ListPage`: `x-page.header`, `x-page.filter`, `x-page.table`, `x-pagination`
- `DetailPage`: `x-page.header`, `x-page.card`, `x-button`, `x-modal`
- `Workspace operacional`: `x-page.header`, `x-button`, `x-modal`, `x-form.*`
- `DashboardPage`: `x-page.card`, `x-button`, `x-alert.flash`

Regra:
- se um template depende sempre do mesmo markup repetido, o proximo passo costuma ser evoluir um componente, nao copiar a estrutura.

## 6. Fundacao

### 6.1 `x-button`

Status:
- `Em consolidacao`

Uso:
- CTA primaria;
- CTA secundaria;
- acao destrutiva;
- acao textual discreta;
- link com aparencia de botao.

API atual:
- props: `href`, `type`, `variant`, `icon`, `text`, `loading`, `disabled`
- props: `fullWidth`, `size`, `pill`, `shadow`, `withIconRight`
- props: `loadingText`, `spinner`, `preventSubmit`

Modos observados:
- link (`href`);
- button comum;
- button com `preventSubmit` controlado em Alpine.

Tamanhos:
- `xs`, `sm`, `md`, `lg`

Familias canonicas normalizadas internamente:
- `primary`
- `secondary`
- `destructive`
- `ghost`
- `link`
- `success`
- `warning`
- `info`

Variantes implementadas no componente:
- solid: `green_solid`, `gray_solid`, `yellow_solid`, `red_solid`, `blue_solid`, `sky_solid`, `indigo_solid`, `purple_solid`
- outline: `gray_outline`, `green_outline`, `blue_outline`, `red_outline`, `yellow_outline`, `purple_outline`
- text: `white_text`, `gray_text`, `green_text`, `blue_text`, `red_text`, `yellow_text`, `purple_text`, `sky_text`, `indigo_text`
- light: `gray_light`, `green_light`, `blue_light`
- especiais: `purple_premium`, `blue_gradient`, `green_gradient`, `gray_dark`, `white_dark`

Aliases aceitos por compatibilidade:
- `green`, `gray`, `blue`, `red`, `yellow`, `sky`
- `default`, `filled`, `inline`, `minimal`, `pills`

Leitura operacional:
- `primary` deve ser a acao dominante da tela;
- `secondary` deve apoiar sem competir;
- acoes de `Editar` devem usar `blue` ou o papel `info` como semantica padrao;
- `destructive` deve ficar restrito a risco real;
- `ghost` e `link` servem para toolbars, filtros e acoes de apoio.

Riscos:
- usar cor por gosto e nao por semantica;
- espalhar aliases legados novos;
- usar `xs` em CTA principal;
- depender de `preventSubmit` como solucao padrao para formularios.

Direcao:
- em novos usos, preferir sempre familias canonicas;
- reduzir gradualmente variantes especiais sem papel transversal;
- tratar `primary`, `secondary`, `destructive` e `ghost` como lingua franca do sistema.

### 6.2 `x-modal`

Status:
- `Em consolidacao`

Uso:
- confirmacao destrutiva;
- formulario curto;
- detalhe contextual;
- acao pontual sem nova pagina.

API atual:
- props: `show`, `size`, `maxWidth`, `closeable`, `closeMethod`
- props: `title`, `description`, `panelClass`, `bodyClass`, `footerClass`
- slots: `header`, `default`, `footer`

Tamanhos:
- `sm`, `md`, `lg`, `xl`, `2xl`, `full`

Leitura operacional:
- e o modal canonico do sistema;
- deve ser preferido a modais ad-hoc;
- cabe bem em confirmacao, formulario rapido e detalhe complementar.

Riscos:
- usar modal para fluxo que precisa de pagina dedicada;
- acoplar fechamento a um metodo unico sem considerar o caso;
- abrir modal com muito contexto sem hierarquia.

Direcao:
- preferir `size` em vez de `maxWidth` em novos usos;
- consolidar um padrao de confirmacao destrutiva com foco inicial seguro em `Cancelar`;
- manter `title` e `description` como trilha simples para modais comuns.

### 6.3 `x-alert.flash`

Status:
- `Canonico`

Uso:
- feedback global por sessao ou evento de browser.

API atual:
- sem props declaradas;
- trabalha sobre eventos e sessao.

Variacoes observadas:
- `success`
- `error`
- `warning`
- `info`

Leitura operacional:
- deve ser a superficie padrao de sucesso e erro global;
- fluxos assincronos devem convergir para ela quando o feedback nao for apenas inline.

Riscos:
- criar segundo sistema de toast paralelo;
- usar microcopy vaga;
- depender so de toast em fluxo critico sem sinal inline.

Direcao:
- manter como superficie canonica de feedback global;
- padronizar microcopy e duracao por tipo.

### 6.4 `x-pagination`

Status:
- `Canonico`

Uso:
- paginacao de listas Livewire.

API atual:
- `paginator`

Direcao:
- manter como base unica de paginacao administrativa;
- evoluir dentro da mesma familia, sem componente alternativo paralelo.

### 6.5 `x-auth-session-status`

Status:
- `Legado/tatico`

Uso:
- compatibilidade com fluxos de autenticacao do scaffold.

Observacao:
- hoje fica oculto.

Direcao:
- manter por compatibilidade;
- evitar expandir;
- se voltar a ter protagonismo, reavaliar se deve migrar para padrao alinhado a `x-alert.flash`.

### 6.6 `x-application-logo`

Status:
- `Canonico`

Uso:
- identidade visual em auth e shell.

Direcao:
- manter simples;
- continuar aceitando `class` externa.

## 7. Pagina

### 7.1 `x-page.header`

Status:
- `Canonico`

Uso:
- topo padronizado de tela.

API atual:
- props: `icon`, `color`, `title`, `subtitle`, `button`, `badge`

Cores implementadas:
- `green`, `blue`, `purple`, `amber`, `red`, `gray`

Leitura operacional:
- e o header padrao de `ListPage`, `DetailPage` e workspaces;
- deve concentrar titulo, subtitulo e CTA principal;
- badge deve ser contextual e comedida.

Observacoes reais:
- o componente apresenta sinais de mojibake no texto default do arquivo;
- o repositorio usa majoritariamente slot/prop `button` para acao.

Riscos:
- transformar o header em bloco decorativo;
- usar cores fora da semantica do sistema;
- expandir a API sem papel claro.

Direcao:
- manter como header canonico;
- oficializar `button` como trilha principal de acao;
- alinhar texto default e encoding do arquivo quando houver manutencao do componente.

### 7.2 `x-page.filter`

Status:
- `Canonico`

Uso:
- bloco padronizado de filtros com expansao local.

API atual:
- props: `accordionOpen`, `title`, `icon`, `description`, `showClear`, `clearAction`
- props: `gridClass`, `panelClass`, `headerClass`
- slots: `default`, `actions`, `showBasic` legado

Leitura operacional:
- e o bloco canonico de filtros para listagens e dashboards com recorte;
- ajuda a controlar densidade sem criar outro formulario.

Riscos:
- usar o componente como container generico de qualquer toolbar;
- misturar filtro e acao estrutural no mesmo bloco;
- esconder filtros fundamentais em accordion sempre fechado sem criterio.

Direcao:
- manter `description` para orientar recortes mais complexos;
- manter `actions` para CTA auxiliar de filtro;
- tratar `showBasic` como compatibilidade, evitando novos usos.

### 7.3 `x-page.card`

Status:
- `Canonico`

Uso:
- bloco base para resumo, secao, formulario ou detalhe.

API atual:
- sem props declaradas.

Leitura operacional:
- e o container mais seguro para reduzir repeticao de caixa, borda e espacamento;
- combina bem com `DetailPage` e `DashboardPage`.

Riscos:
- explodir variacoes locais por classes repetidas;
- usar card para resolver qualquer problema de layout sem semantica.

Direcao:
- manter como base simples;
- futuras variacoes devem nascer por props semanticas como `tone`, `padding`, `interactive`, se a necessidade for recorrente.

### 7.4 `x-page.table`

Status:
- `Em consolidacao`

Uso:
- tabela administrativa base.

API atual:
- props: `pagination`, `striped`, `hover`, `bordered`, `compact`, `stickyHeader`, `emptyMessage`
- slots: `thead`, `tbody`, `tfoot`, `emptyAction`

Leitura operacional:
- e a base de `ListPage` tabular;
- deve sustentar listagens administrativas, auditorias e relatorios operacionais.

Inconsistencias reais:
- o arquivo possui sinais de mojibake em comentarios e textos.

Estado atual consolidado:
- `compact` ajusta densidade de `th` e `td` por seletor descendente;
- `stickyHeader` aplica `sticky top-0 z-10` no `thead`;
- a tabela continua aceitando `striped`, `hover`, `bordered` e `pagination`.

Riscos:
- tratar a tabela como componente fechado demais e voltar a criar tabelas paralelas;
- manter props fantasmas por tempo indefinido;
- exagerar em colunas sem revisar legibilidade.

Direcao:
- manter como base unica de tabela administrativa;
- limpar ou implementar props sem efeito;
- reforcar empty state orientativo e paginação consistente.

### 7.5 `x-page.table-th`

Status:
- `Canonico`

Uso:
- cabecalho padronizado de coluna.

API atual:
- `value`

Direcao:
- manter simples;
- usar classes locais para alinhamento e largura.

### 7.6 `x-page.table-td`

Status:
- `Canonico`

Uso:
- celula padronizada de conteudo.

API atual:
- `value`

Direcao:
- manter simples;
- complementar com classes locais para numero, truncamento e destaque.

### 7.7 `x-page.table-status`

Status:
- `Canonico`

Uso:
- badge binario simples.

API atual:
- `condition`, `trueText`, `falseText`

Leitura operacional:
- adequado apenas para estados realmente binarios.

Direcao:
- se o dominio exigir mais que verdadeiro/falso, criar ou usar badge semantico mais apropriado, em vez de estender este componente indefinidamente.

## 8. Formulario

### 8.1 `x-form.label`

Status:
- `Canonico`

Uso:
- label padrao de campo.

API atual:
- `value`
- `icon`

Direcao:
- manter simples;
- seguir regra de label acima do campo.

### 8.2 `x-form.error`

Status:
- `Canonico`

Uso:
- erro inline por campo.

API atual:
- `for`

Direcao:
- manter como superficie padrao de erro de campo;
- continuar alinhado ao bag de erros.

### 8.3 `x-form.input`

Status:
- `Em consolidacao`

Uso:
- campo base para texto, busca e entradas simples.

API atual:
- props: `disabled`, `name`, `variant`, `size`, `withIcon`, `icon`
- props: `iconPosition`, `borderColor`, `rounded`, `shadow`, `loading`

Variantes observadas:
- `default`, `outline`, `filled`, `minimal`, `glass`, `pills`

Tamanhos:
- `xs`, `sm`, `md`, `lg`

Cores:
- `green`, `blue`, `purple`, `red`, `yellow`, `gray`, `sky`, `indigo`

Leitura operacional:
- deve sustentar a maioria dos inputs do sistema;
- uso mais frequente em busca, filtros e formularios administrativos.

Riscos:
- explodir variantes visuais sem papel semantico;
- usar cores e estilos especiais em campos comuns;
- divergir de `textarea` e `select` ate criar familia incoerente.

Direcao:
- tratar `default`, `outline` e `minimal` como trilha principal;
- reservar `glass` e `pills` para casos realmente especiais;
- usar `md` como tamanho padrao de formulario e `sm` para filtros.

### 8.4 `x-form.textarea`

Status:
- `Em consolidacao`

Uso:
- entrada multiline.

API atual:
- `disabled`, `name`, `rows`, `variant`, `size`, `withIcon`, `icon`
- `iconPosition`, `borderColor`, `rounded`, `shadow`, `loading`

Estado atual consolidado:
- segue a mesma matriz principal de variantes de `x-form.input`;
- suporta tamanho, cor, rounded, loading e icones;
- preserva `rows` como diferenca natural do campo multiline.

Direcao:
- manter alinhado a `x-form.input` em semantica e comportamento;
- evitar abrir uma segunda familia de textarea;
- usar `rows` e `resize-y` como diferencas naturais do componente.

### 8.5 `x-form.select`

Status:
- `Em consolidacao`

Uso:
- select pesquisavel em Blade puro.

API atual:
- props: `name`, `options`, `collection`, `classes`
- props: `labelField`, `labelAcronym`, `valueField`
- props: `selected`, `placeholder`, `default`
- props: `disabled`, `variant`, `size`, `withIcon`, `icon`
- props: `borderColor`, `searchable`, `rounded`, `shadow`

Variantes observadas:
- `default`, `inline`, `outline`, `filled`, `pills`, `minimal`

Leitura operacional:
- adequado para formularios e filtros sem `wire:model`.

Direcao:
- preferir `collection` quando a origem vier de modelos/listas estruturadas;
- usar `placeholder` como API canonica;
- reservar `inline` para filtros compactos e toolbars;
- manter `default` apenas como alias de compatibilidade para `placeholder`.

### 8.6 `x-form.select-livewire`

Status:
- `Em consolidacao`

Uso:
- select pesquisavel integrado a `wire:model`.

API atual:
- mesma familia de `x-form.select`, exigindo `wire:model`

Variantes observadas:
- `default`, `inline`, `outline`, `filled`, `pills`, `minimal`, `glass`

Cores observadas:
- `green`, `blue`, `purple`, `red`, `yellow`, `gray`, `sky`, `indigo`

Estado atual consolidado:
- aceita a mesma superficie principal de props de `x-form.select`;
- suporta alias `default` como compatibilidade para `placeholder`;
- `classes` agora afeta o wrapper, assim como no select Blade puro;
- continua com implementacao propria por depender de `wire:model`.

Direcao:
- manter `default`, `inline` e `outline` como trilha principal;
- tratar `glass` como experimental.

## 9. Navegacao

### 9.1 `x-sidebar.main-link`

Status:
- `Canonico`

Uso:
- item principal da sidebar.

API atual:
- `icon`, `title`, `active`, `href`

Direcao:
- manter como entrada canonica de primeiro nivel;
- nao criar outro link principal paralelo.

### 9.2 `x-sidebar.main-dropdown`

Status:
- `Canonico`

Uso:
- grupo principal colapsavel.

API atual:
- `icon`, `title`, `active`

Direcao:
- manter como agrupador principal;
- preservar coerencia com estado da sidebar.

### 9.3 `x-sidebar.dropdown`

Status:
- `Canonico`

Uso:
- subgrupo colapsavel.

API atual:
- `icon`, `title`, `active`

Direcao:
- usar apenas quando o terceiro nivel for realmente necessario;
- evitar aprofundar demais a navegacao.

### 9.4 `x-sidebar.dropdown-link`

Status:
- `Canonico`

Uso:
- link interno de submenu.

API atual:
- `title`, `href`, `active`, `icon`

Direcao:
- manter previsivel;
- preservar semantica visual entre niveis.

## 10. Inconsistencias Reais Do Estado Atual

### 10.1 Props expostas sem efeito

Status atual:
- `x-page.header.accordionOpen` foi removida da API publica por nao ter uso real;
- props compartilhadas restantes devem manter comportamento observavel no markup.

Direcao:
- qualquer nova prop compartilhada deve nascer com uso real e documentado;
- evitar acumular compatibilidade silenciosa sem efeito.

### 10.2 Assimetria da familia de campos

Casos identificados:
- `x-form.input` e `x-form.select-livewire` tem API extensa;
- a familia de select ainda tem diferencas visuais internas, apesar da superficie de props estar alinhada.

Direcao:
- alinhar primeiro semantica e comportamento;
- depois alinhar visual e variantes.

### 10.3 Encoding e manutencao de texto

Status atual:
- `x-page.header` e `x-page.table` ja foram normalizados;
- a validacao de mojibake continua obrigatoria em qualquer edicao de view com acentuacao.

Direcao:
- manter arquivos de view em UTF-8;
- validar sempre com a checagem recomendada em `CONVENTIONS.md`.

## 11. Padronizacao Recomendada

### 11.1 Papeis visuais canonicos

Para novas APIs, trabalhar preferencialmente com estes papeis:
- `primary`
- `secondary`
- `destructive`
- `ghost`
- `inline`

Mapeamento sugerido:
- `primary`: confirmacao, salvar, seguir fluxo
- `secondary`: apoio neutro
- `info`: editar, detalhes contextuais e navegacao secundaria orientada a acao
- `destructive`: cancelar, excluir, risco
- `ghost`: acao discreta e toolbar
- `inline`: filtro compacto e acao de contexto

### 11.2 Tamanhos

Escala recomendada:
- `sm`: filtros e toolbars
- `md`: formulario e CTA padrao
- `lg`: destaque pontual

Regra:
- evitar `xs` fora de contextos muito densos.

### 11.3 Governanca de API

Antes de expandir um componente compartilhado:
1. confirmar recorrencia em mais de uma tela;
2. validar semantica com o `LAYOUT_GUIDE.md`;
3. avaliar impacto nos modulos sensiveis;
4. atualizar documentacao junto com a mudanca.

## 12. Proximos Passos Recomendados

Ordem sugerida:
1. reduzir aliases legados de `x-button` em views novas;
2. consolidar o padrao de modal destrutivo;
3. revisar se os dois selects ja podem compartilhar uma base interna sem aumentar risco.
