# Layout Guide (Playbook Operacional)

Guia diretor para a evolucao visual do X-AdminPanel.
Atualizado em: 2026-03-13.

Este documento transforma as referencias visuais estudadas em regras praticas de design para o sistema.
Ele deve ser usado junto com:
- `docs-ia/COMPONENTS.md`
- `docs-ia/CONVENTIONS.md`
- `docs-ia/ARCHITECTURE.md`
- `docs-ia/SYSTEM_GUIDE.md`

## 1. Proposito

O X-AdminPanel nao e um dashboard generico.
Ele e uma plataforma de inteligencia organizacional e governanca estrutural.

Este guia existe para:
- transformar a interface em um sistema maduro e coerente;
- unificar criterio visual entre modulos;
- reduzir improviso de layout;
- garantir que refinamento visual nao comprometa clareza operacional;
- orientar design e implementacao no mesmo documento.

Regra de ouro:
- a interface precisa parecer um sistema institucional confiavel, rapido e inteligivel, nunca um admin pronto adaptado de ultima hora.

## 2. Escopo

Aplica-se a toda a camada visual:
- `resources/views/layouts/*`
- `resources/views/components/*`
- `resources/views/livewire/*`
- `resources/views/auth/*`
- `resources/views/profile/*`
- `resources/views/notifications/*`
- `resources/views/emails/*`
- `resources/views/pdf/*`

Nao substitui:
- regras de negocio em `Services`;
- API real dos componentes em `COMPONENTS.md`;
- leitura do dominio afetado em mudanca sensivel.

## 3. Identidade Do X-AdminPanel

### 3.1 O que o produto precisa transmitir

O sistema deve comunicar:
- confianca institucional;
- clareza estrutural;
- competencia operacional;
- rastreabilidade;
- sobriedade com alto acabamento.

Ele nao deve parecer:
- admin de e-commerce;
- painel de marketing;
- app de produtividade genérico;
- dashboard com graficos decorativos sem decisao real.

### 3.2 Tese visual

A direcao alvo do produto e:
- shell forte e organizado;
- densidade alta com boa leitura;
- superfícies claras e bem moduladas;
- cor institucional disciplinada;
- cards, tabelas e workspaces visualmente coesos;
- refinamento de produto moderno sem sacrificar operacao.

## 4. Referencias Externas E O Que Absorver

As referencias abaixo nao devem ser copiadas literalmente.
Elas servem para orientar principios de design.

### 4.1 TailAdmin

O que absorver:
- composicao de dashboard em blocos bem delimitados;
- cards de KPI com leitura imediata;
- boa relacao entre resumo numerico, lista e tabela;
- grids administrativos previsiveis;
- cards claros com sombra leve e borda suave.

O que evitar:
- visual generico de dashboard SaaS;
- graficos e widgets sem funcao real;
- excesso de modulos irrelevantes ao dominio.

Aplicacao no X-AdminPanel:
- `Dashboard`
- cards de resumo em `Assets`, `Process` e `Administration`
- blocos modulares de listagem e relatorio

### 4.2 Linear

O que absorver:
- densidade alta com pouco ruido;
- foco extremo na tarefa principal;
- workspaces operacionais sem quebra de contexto;
- boa relacao entre lista, detalhe e acao rapida;
- hierarquia silenciosa, mas precisa.

O que evitar:
- minimalismo extremo que esconda contexto institucional;
- dependencia de padroes visuais conhecidos apenas por usuarios de software.

Aplicacao no X-AdminPanel:
- `Task`
- partes de `Process`
- telas com aside e operacao recorrente

### 4.3 Stripe Dashboard

O que absorver:
- sensacao de sistema confiavel e serio;
- navegacao previsivel e profissional;
- detalhe rico sem perder prioridade visual;
- tabelas, filtros e visao de entidade com disciplina;
- clareza estrutural de areas autenticadas.

O que evitar:
- frieza excessiva;
- estetica financeira aplicada sem criterio em todos os modulos.

Aplicacao no X-AdminPanel:
- `Assets`
- `Administration`
- `Configuration`
- paginas de detalhe com historico e blocos densos

### 4.4 Raycast

O que absorver:
- acabamento refinado;
- ritmo visual entre titulo, subtitulo e CTA;
- superfícies elegantes com sombra e contraste bem dosados;
- microcopy curta e confiante;
- rigor de spacing.

O que evitar:
- linguagem de landing page;
- estetica promocional em fluxo administrativo.

Aplicacao no X-AdminPanel:
- refinamento de componentes;
- headers;
- cards;
- asides e paineis contextuais

### 4.5 Supabase

O que absorver:
- produto tecnico com boa presenca visual;
- uso disciplinado da cor institucional;
- consistencia modular entre seções;
- composicao limpa e moderna sem excesso.

O que evitar:
- linguagem de marketing de homepage;
- estetica de dev tool aplicada cegamente em modulos administrativos.

Aplicacao no X-AdminPanel:
- consolidacao da identidade institucional;
- refinamento do uso de `emerald`;
- coesao entre shell, cards, tabelas e detail pages

### 4.6 Mosaic

O que absorver:
- aside organizado por grupos;
- seções de navegação bem escalonadas;
- sidebar com presenca, mas sem roubar foco;
- estados ativo, hover e expandido bem resolvidos;
- arquitetura visual de shell mais madura.

O que evitar:
- agrupamento artificial so para “parecer organizado”;
- aprofundamento demais da arvore de menu.

Aplicacao no X-AdminPanel:
- shell autenticado;
- sidebar desktop e mobile;
- rodape do aside;
- arquitetura de navegacao por eixo do sistema

## 5. Tecnologias E Estado Atual Do Sistema

### 5.1 Stack visual atual

O sistema usa hoje:
- Laravel 12
- Livewire 3
- Alpine.js
- Blade
- Tailwind CSS 3
- `@tailwindcss/forms`
- Vite
- Font Awesome Kit
- fonte principal `Open Sans`

### 5.2 Estado atual relevante do front

Observacoes concretas do projeto:
- `font-sans` esta mapeada para `Open Sans` no `tailwind.config.js`;
- o shell atual usa `emerald` como base institucional;
- header autenticado usa gradiente `emerald`;
- sidebar atual e expansivel no desktop;
- ha uso extenso de gradientes, bordas suaves, sombra e cards claros;
- ha ocorrencias de mojibake em views antigas;
- o projeto usa Tailwind utility-first, sem design tokens centralizados em CSS custom properties neste momento.

### 5.3 Cores observadas hoje

Cores ja institucionalizadas no codigo:
- `emerald`: shell, CTA principal, contexto positivo
- `blue`: informacao, edicao, exportacao, contexto analitico
- `yellow` ou `amber`: aviso moderado
- `red`: destrutivo, erro, risco
- `purple`: administracao e privilegio
- `gray` ou `slate`: estrutura e apoio

### 5.4 Tipografia observada hoje

Tipografia real predominante:
- familia principal: `Open Sans`
- uso recorrente de:
  - `text-xs` para metadado
  - `text-sm` para corpo administrativo
  - `text-lg` a `text-3xl` para destaque pontual

Direcao:
- manter `Open Sans` como base do sistema atual;
- melhorar hierarquia tipografica pelo uso, nao pela troca de fonte sem criterio.

## 6. Principios De Interface

### 6.1 Clareza operacional

- status, risco, responsavel, setor atual e proxima acao devem aparecer cedo;
- a leitura em 3 a 5 segundos deve funcionar;
- visual nao pode competir com o conteudo.

Aplicacao por dominio:
- `Organization`: a estrutura e o conteudo principal;
- `Process`: etapa atual, setor e historico precisam liderar;
- `Task`: ordem, status e bloqueio precisam ser obvios;
- `Assets`: estoque e operacao precisam ser visualmente separados.

### 6.2 Consistencia transversal

- componentes equivalentes se comportam da mesma forma;
- labels de acao usam o mesmo verbo para o mesmo papel;
- estados vazio, loading e erro seguem o mesmo criterio visual.

Padrao de linguagem:
- `Salvar`
- `Editar`
- `Cancelar`
- `Excluir`
- `Avancar etapa`
- `Retornar etapa`

### 6.3 Densidade controlada

- o sistema deve ser denso o suficiente para operacao;
- mas essa densidade precisa ser ordenada;
- o usuario deve ver muito, sem sentir caos.

Ferramentas para isso:
- filtros;
- tabs;
- cards de contexto;
- listas com truncamento controlado;
- detail pages em blocos;
- asides para acoes frequentes.

### 6.4 Feedback imediato

- clique responde na hora;
- submit bloqueia repeticao;
- erro aponta problema claro;
- sucesso confirma a acao;
- hover e focus precisam ser distinguiveis.

### 6.5 Evolucao incremental

- preservar familiaridade do usuario recorrente;
- melhorar por componente e por modulo;
- evitar “redesign total” sem plano.

## 7. Direcao Visual Oficial

### 7.1 Look and feel

O X-AdminPanel deve combinar:
- sobriedade institucional da Stripe;
- modularidade de dashboard do TailAdmin;
- foco operacional da Linear;
- acabamento de produto do Raycast;
- disciplina visual do Supabase;
- arquitetura de aside do Mosaic.

Isso produz uma direcao unica:
- clara;
- moderna;
- robusta;
- orientada a trabalho real.

### 7.2 Materiais visuais

Superficies:
- fundo geral claro, neutro e pouco intrusivo;
- cards e paineis em branco ou branco levemente tonalizado;
- borda suave em cinza claro;
- blur pontual so em overlays ou componentes especiais;
- gradiente forte reservado para shell, destaque institucional e alguns headers.

Sombras:
- `shadow-sm` ou `shadow` como base;
- `shadow-lg` apenas em elementos destacados ou overlays;
- evitar “colchao de sombra” em toda a tela.

Radius:
- `rounded-xl` e `rounded-2xl` como padrao;
- `rounded-full` para pills e elementos de avatar/badge especificos.

### 7.3 Ritmo de tela

Toda tela deve ter esta cadencia:
1. orientacao: titulo, subtitulo, local da tela;
2. decisao: filtros, tabs, CTA principal;
3. operacao: tabela, cards, kanban, detalhe;
4. suporte: historico, notas, metadado, acoes secundarias.

## 8. Sistema De Cor

### 8.1 Paleta semantica

| Cor | Papel semantico | Uso tipico |
|---|---|---|
| `emerald` | identidade, confirmacao, CTA principal | salvar, concluir, seguir fluxo, shell |
| `blue` | edicao, informacao, exportacao, acao secundaria forte | editar, ver detalhes, exportar, contextualizar |
| `amber` ou `yellow` | aviso moderado | pendencia, senha padrao, cuidado |
| `red` | erro, destruicao, risco, atraso | excluir, cancelar, falha, bloqueio |
| `purple` | privilegio, administracao, configuracao sensivel | RBAC, areas administrativas |
| `gray` ou `slate` | neutro, estrutura, metadado | borda, texto auxiliar, estado inativo |

### 8.2 Regras obrigatorias

- editar deve usar `blue` como semantica padrao;
- acao primaria institucional continua em `emerald`;
- destruir, excluir e cancelar usam `red`;
- `purple` nao deve vazar para operacao comum;
- nao depender apenas de cor: combinar com label e, quando fizer sentido, icone.

### 8.3 Uso do emerald

`emerald` e a identidade do sistema, mas deve ser disciplinado.

Usar em:
- shell;
- CTA principal;
- estados positivos;
- realce institucional;
- notificação positiva;
- pontos de continuidade do fluxo.

Evitar:
- espalhar `emerald` em todas as superficies;
- transformar a cor institucional em ruído.

## 9. Tipografia E Texto

### 9.1 Escala recomendada

```css
h1: text-3xl lg:text-4xl font-light tracking-tight
h2: text-xl font-semibold
h3: text-lg font-medium
h4: text-base font-medium
body: text-sm
meta: text-xs text-gray-500
label: text-xs font-medium
```

### 9.2 Regras

- titulo explica o que a tela governa;
- subtitulo contextualiza a operacao;
- labels de formulario devem ser curtas;
- texto de apoio deve existir apenas quando realmente ajuda;
- uppercase so em metadados curtos, tabs auxiliares ou badges pequenos.

### 9.3 Microcopy

O texto deve ser:
- claro;
- direto;
- acionavel;
- consistente em PT-BR;
- sem tecnicismo desnecessario.

Exemplos corretos:
- `Nenhum processo encontrado`
- `Ajuste os filtros`
- `Editar usuario`
- `Liberar ativo`
- `Alterar senha`

## 10. Shell E Navegacao

### 10.1 Shell autenticado

Base:
- `resources/views/layouts/app.blade.php`

Papel:
- estabelecer identidade do sistema;
- orientar o usuario no espaco institucional;
- sustentar navegacao e contexto persistente.

### 10.2 Aside oficial

Inspiracao principal:
- Mosaic, com apoio do rigor estrutural da Stripe.

Direcao alvo:
- aside com grupos claros de navegacao;
- grupos discretos, nao chamativos;
- itens com forte leitura de estado ativo;
- menu colapsavel ou expansivel sem perder coerencia;
- rodape com contexto do usuario e atalhos utilitarios.

Arquitetura recomendada por eixos:
- `Dashboard`
- `Estrutura`: `Organization`, `Process`
- `Operacao`: `Task`, `TimeClock`
- `Patrimonio`: `Assets`
- `Administracao`: `Administration`, `Configuration`
- `Suporte`: `Audit`, `Profile`

Regras:
- nao misturar itens sem hierarquia;
- nao aprofundar mais niveis do que o necessario;
- estado ativo precisa ser obvio mesmo em leitura rapida;
- hover deve reforcar, nao competir com o ativo;
- mobile e desktop devem compartilhar a mesma logica de grupos.

### 10.3 Header autenticado

Papel:
- navegacao utilitaria;
- notificacoes;
- perfil;
- controle de aside;
- contexto persistente do usuario.

Regras:
- header nao e lugar para excesso de CTA de dominio;
- deve ser visualmente forte, mas estavel;
- area utilitaria deve ser compacta.

### 10.4 Guest shell

Papel:
- autenticacao e recuperacao de acesso com baixo atrito.

Regras:
- simplicidade;
- clareza;
- foco em completar a tarefa;
- manter marca visual sem virar tela promocional.

## 11. Templates De Tela

### 11.1 DashboardPage

Inspiracao:
- TailAdmin para composicao;
- Stripe para credibilidade;
- Raycast para acabamento.

Estrutura:
1. bloco de boas-vindas ou situacao
2. cards de resumo
3. blocos de lista e alertas
4. atalhos operacionais

Regras:
- cada widget responde a uma pergunta;
- grafico sem decisao concreta nao entra;
- cards de KPI usam numero, label e contexto.

### 11.2 ListPage tabular

Inspiracao:
- Stripe para disciplina;
- TailAdmin para composicao.

Quando usar:
- comparacao;
- ordenacao;
- operacao administrativa;
- dados numericos, codigos, datas e statuses.

Estrutura:
1. `x-page.header`
2. `x-page.filter`
3. `x-page.table`
4. paginacao

### 11.3 ListPage em cards

Inspiracao:
- TailAdmin para blocos;
- Supabase e Raycast para acabamento.

Quando usar:
- contexto do item e mais importante que comparacao tabular;
- status visual ajuda decisao;
- mobile tem peso maior.

### 11.4 DetailPage

Inspiracao:
- Stripe para estrutura rica;
- Supabase para modularidade;
- Raycast para polimento.

Estrutura:
1. header com retorno e CTA
2. resumo executivo
3. blocos de detalhe
4. historico/eventos
5. acoes secundarias

### 11.5 Workspace operacional

Inspiracao:
- Linear.

Quando usar:
- operacao frequente;
- contexto persistente;
- acoes sequenciais;
- quadro, lista viva ou painel lateral.

Aplicacao:
- `Task`
- partes de `Process`
- manutencao estrutural de `Organization`

Regras:
- nao esconder regra critica em hover;
- aside deve preservar contexto;
- acoes frequentes precisam estar ao alcance sem excesso de clique.

## 12. Regras Por Dominio

### 12.1 Organization

- a arvore e o conteudo principal;
- hierarquia nao pode parecer lista plana;
- mudancas estruturais precisam de contexto claro de pai, filho e impacto;
- destaque visual deve servir a leitura da estrutura.

### 12.2 Process

- etapa atual, setor atual, owner e status lideram a leitura;
- historico deve parecer trilha institucional;
- avancar e retornar exigem contexto de impacto;
- detail page deve equilibrar fluxo, historico e metadata.

### 12.3 Task

- o modulo deve se aproximar do foco operacional da Linear;
- cards precisam mostrar status, prioridade, responsavel e bloqueio sem poluicao;
- asides precisam ser fortes, mas nao opacos;
- o kanban precisa manter leitura de ordem real.

### 12.4 Assets

- estoque e operacao nao podem parecer o mesmo contexto;
- tabelas e detalhe devem seguir rigor proximo da Stripe;
- historico patrimonial precisa de destaque institucional;
- auditoria deve parecer trilha confiavel, nao anotacao solta.

### 12.5 Administration e Configuration

- produtividade administrativa;
- filtros previsiveis;
- formulários compactos e claros;
- densidade alta com leitura controlada;
- uso de `purple` apenas quando o contexto justificar privilegio ou configuracao sensivel.

### 12.6 Dashboard, Auth, Profile e Public

- `Dashboard`: sintese e orientacao
- `Auth`: foco total em completar o acesso
- `Profile`: seguranca, clareza e baixo atrito
- `Public`: leitura e busca, sem semantica de admin

## 13. Componentes E Composicao

### 13.1 Componentes-base

Prioridade de reuso:
- `x-page.header`
- `x-page.filter`
- `x-page.card`
- `x-page.table`
- `x-button`
- `x-form.*`
- `x-modal`
- `x-alert.flash`

### 13.2 Header de pagina

Deve conter:
- titulo;
- subtitulo;
- CTA principal;
- contexto visual discreto.

Nao deve:
- virar hero section;
- competir com a area principal da tela.

### 13.3 Filtros

Regras:
- busca principal primeiro;
- filtros mais usados primeiro;
- limpar filtros de forma previsivel;
- nao misturar filtros com acoes estruturais pesadas.

### 13.4 Tabelas

Regras:
- coluna de acoes a direita;
- numeros a direita;
- status compactos;
- textos longos com truncamento controlado;
- no maximo 7 ou 8 colunas realmente legiveis sem degradar a operacao.

### 13.5 Cards

Papeis validos:
- resumo;
- entidade clicavel;
- bloco de detalhe;
- bloco de formulario;
- alerta contextual.

### 13.6 Forms

Regras:
- label sempre visivel;
- erro abaixo do campo;
- variantes semanticas, nao decorativas;
- a familia `x-form.*` deve se comportar como uma linguagem unica.

### 13.7 Modais e asides

Modal:
- confirmacao;
- detalhe rapido;
- formulario curto;
- acao tatica.

Aside:
- fluxo frequente;
- edicao contextual;
- operacao com necessidade de manter a tela principal viva.

Regra:
- o aside deve ser uma extensao do workspace, nao um pop-up improvisado.

## 14. Estados E Interacao

### 14.1 Estados obrigatorios

Todo interativo deve ter:
- `default`
- `hover`
- `focus`
- `active`
- `disabled`
- `loading`

Quando aplicavel:
- `error`
- `selected`
- `empty`

### 14.2 Hierarquia de acoes

Cada tela deve ter:
1. uma acao primaria;
2. ate duas secundarias;
3. destrutivas separadas visualmente.

Regras:
- `Salvar`, `Concluir`, `Avancar`: `emerald`
- `Editar`: `blue`
- `Cancelar`, `Excluir`: `red`
- acoes auxiliares neutras: `gray` ou `ghost`

### 14.3 Feedback

| Gatilho | Feedback | Timing |
|---|---|---|
| clique | resposta imediata | `< 100ms` |
| submit | loading + bloqueio | durante processamento |
| erro | mensagem clara | imediato |
| sucesso | toast ou confirmacao inline | ate `~3s` |
| hover | transicao suave | `~200ms` |

### 14.4 Empty states

Devem:
- explicar o motivo da ausencia;
- sugerir proximo passo;
- diferenciar vazio real, filtro excessivo e falta de permissao.

## 15. Acessibilidade E Legibilidade

Checklist minimo:
- navegacao completa por teclado;
- focus visivel em todos os interativos;
- contraste minimo `4.5:1`;
- area clicavel minima `44x44px`;
- hierarquia semantica de titulos;
- ARIA quando faltar texto explicito.

Regras adicionais:
- icone nunca e semantica suficiente sozinho;
- modal precisa respeitar `Esc` e fluxo de foco;
- dropdown precisa permitir teclado.

## 16. Performance De Interface

Metas referenciais:
- LCP `< 2.5s`
- INP `< 200ms`
- CLS `< 0.1`

Praticas:
- debounce em busca Livewire;
- paginacao em listas longas;
- evitar JS inline excessivo em telas densas;
- reservar espaco para blocos dinamicos;
- evitar layout shifts em cards e tabelas.

## 17. Checklist Visual De PR

### 17.1 Estrutural

- usa o template correto;
- reusa componente base;
- nao move regra de dominio para view;
- respeita fronteira entre Blade, Livewire, Alpine e Service.

### 17.2 Visual

- semantica de cor correta;
- `Editar` em `blue`;
- CTA primaria coerente com `emerald`;
- espacamento consistente;
- cards e tabelas integrados ao sistema;
- shell e aside preservados.

### 17.3 Interacao

- loading implementado;
- erro e sucesso claros;
- foco e hover visiveis;
- mobile e desktop coerentes.

### 17.4 Qualidade

- a tela parece pertencer ao mesmo sistema;
- leitura principal acontece rapido;
- a acao dominante esta clara;
- a interface parece mais madura, nao apenas mais “bonita”.

## 18. Fluxo De Evolucao

1. identificar a necessidade real;
2. localizar o template ou componente correspondente;
3. verificar impacto no dominio;
4. implementar no componente base quando fizer sentido;
5. propagar por modulo;
6. validar responsividade, acessibilidade, performance e mojibake;
7. atualizar documentacao.

Regra:
- se virar padrao, precisa entrar neste guia.

## 19. Relacao Com O Estado Atual

Este guia nao ignora o sistema atual.
Ele parte do que o projeto ja tem:
- `Open Sans` como fonte principal;
- Tailwind como linguagem de implementacao;
- `emerald` como identidade institucional;
- gradientes no shell;
- componentes compartilhados em Blade;
- Livewire como camada de interacao;
- Alpine para microinteracao;
- sidebar expansivel;
- cards, tabelas e modais ja consolidados.

Direcao pratica:
- manter o que ja expressa identidade;
- elevar acabamento, hierarquia e consistencia;
- corrigir encoding, props mortas e divergencia de API;
- transformar o sistema em uma plataforma com presenca de produto real.

## 20. Glossario Rapido

- `Scanability`: rapidez de encontrar o que importa.
- `Affordance`: pista visual de interacao.
- `Workspace operacional`: tela com contexto persistente e operacao frequente.
- `Empty state`: tela ou bloco sem dados.
- `Token visual`: valor reutilizavel de cor, spacing ou tipografia.
