# Layout Guide (Playbook Operacional)

Guia de execucao para evolucao visual do X-AdminPanel.
Este documento reune as regras praticas de implementacao visual e deve ser usado em conjunto com `docs-ia/COMPONENTS.md`, `docs-ia/CONVENTIONS.md` e `docs-ia/SYSTEM_GUIDE.md`.

## 1. Objetivo

- Padronizar UI/UX entre modulos.
- Reduzir divergencia visual e custo de manutencao.
- Garantir acessibilidade, performance e consistencia de interacao.

## 2. Escopo

Aplica-se a toda a camada de views:
- `resources/views/layouts/*`
- `resources/views/components/*`
- `resources/views/livewire/*`
- `resources/views/auth/*`
- `resources/views/profile/*`
- `resources/views/notifications/*`
- `resources/views/emails/*`
- `resources/views/pdf/*`

## 3. Principios de interface

As decisoes visuais do X-AdminPanel devem seguir estes principios:

### 3.1 Clareza operacional
- Priorizar leitura e decisao rapida.
- A informacao critica deve aparecer primeiro (status, risco, proxima acao).
- Evitar elementos decorativos que competem com conteudo funcional.

### 3.2 Consistencia transversal
- Componentes equivalentes devem manter o mesmo comportamento entre modulos.
- Labels, icones e estados visuais devem usar vocabulario unico.
- Acoes similares devem aparecer em posicoes previsiveis.

### 3.3 Densidade controlada
- Exibir informacao suficiente para operacao sem sobrecarga.
- Separar o que e essencial (primeira leitura) do que e complementar.
- Em listas extensas, preferir filtros + paginacao a blocos excessivamente densos.

### 3.4 Feedback imediato
- Toda acao deve produzir resposta visual clara e contextual.
- Carregamento, sucesso, erro e bloqueio temporario devem ser explicitados.
- Nunca deixar o usuario sem sinal de estado apos interagir.

### 3.5 Evolucao incremental
- Melhorias visuais devem preservar familiaridade de uso.
- Evitar mudancas abruptas de padrao sem ganho funcional evidente.
- Priorizar migracoes graduais com baixo custo cognitivo para usuario recorrente.

## 4. Arquitetura visual canonica

### 4.1 Shell autenticado
- Base: `resources/views/layouts/app.blade.php`
- Estrutura obrigatoria:
  - Sidebar (desktop + mobile)
  - Header fixo com notificacoes e perfil
  - Main content responsivo

### 4.2 Shell guest
- Base: `resources/views/layouts/guest.blade.php`
- Uso para auth e fluxos publicos sem sidebar.

### 4.3 Componentes obrigatorios de composicao
- Header de pagina: `x-page.header`
- Bloco de filtros: `x-page.filter`
- Acoes: `x-button`
- Formularios: `x-form.*`
- Modal: `x-modal`

Regra:
- Nao deve reinventar estrutura de bloco quando ja existe componente equivalente.

### 4.4 Limites de responsabilidade na camada de view

Objetivo:
- manter separacao clara entre composicao visual, padronizacao de componentes e controle de estado/interacao.

#### Blade (views de tela)
Responsavel por:
- composicao da tela;
- organizacao estrutural do layout;
- uso de componentes;
- definicao de regioes de conteudo.

Nao deve conter:
- logica de dominio;
- queries/acesso direto a dados;
- regras complexas de decisao de interface.

Uso esperado:
```blade
<x-page.header />
<x-page.filter />
<x-page.table />
```

Diretriz:
- Blade atua como orquestrador da interface, nao como camada de negocio.

#### Componentes Blade (`x-*`)
Responsaveis por:
- encapsular padroes visuais;
- padronizar markup e estilo;
- reduzir duplicacao entre telas;
- expor API simples e previsivel via props.

Nao devem:
- conter logica de dominio;
- executar operacoes de dados;
- depender de estado externo complexo para funcionar.

Exemplo:
```blade
<x-button variant="primary">
    Salvar
</x-button>
```

#### Componentes Livewire
Responsaveis por:
- estado da interface;
- interacao dinamica;
- fluxo de acoes do usuario;
- comunicacao com backend por Services.

Concentram:
- carregamento de dados da tela;
- mutacoes de estado;
- acoes assincronas.

Nao devem:
- concentrar regra de negocio que pertence aos Services;
- duplicar regras de permissao ja definidas na borda sem necessidade.

#### Alpine (quando usado)
Usar para microinteracoes locais e efemeras:
- abrir/fechar dropdown;
- controle de foco;
- estados visuais simples.

Evitar para:
- logica de dados;
- sincronizacao complexa de estado;
- fluxo de negocio.

Regra geral:
- Blade compoe.
- Componentes Blade padronizam.
- Livewire controla estado e fluxo (chamando Services).
- Alpine cuida de microinteracoes locais.

Beneficios da separacao:
- menor acoplamento entre telas;
- menor duplicacao de logica;
- manutencao mais previsivel.

## 5. Templates de tela (decisao por contexto)

### 5.1 ListPage (tabular)
Tabela deve ser utilizada quando houver:
- alta densidade de dados
- comparacao linha a linha
- acoes em massa e filtros numerosos

Base recomendada:
- `x-page.header` + `x-page.filter` + `x-page.table`

### 5.2 ListPage (cards)
Cards devem ser utilizados quando houver:
- leitura contextual por item
- destaque de estado e timeline curta
- necessidade de scan rapido em mobile

Base recomendada:
- `x-page.header` + `x-page.filter` + lista de cards clicaveis

### 5.3 DetailPage
DetailPage deve ser utilizado para:
- contexto completo de entidade
- historico/eventos
- acoes operacionais de fluxo

Base recomendada:
- header com retorno
- resumo contextual
- conteudo principal
- bloco de historico
- bloco de acoes

### 5.4 DashboardPage
DashboardPage deve ser utilizado para:
- KPIs
- indicadores de tendencia
- ultimos itens / alertas

Regra:
- evitar excesso de widgets sem decisao acionavel.

### 5.5 Quando usar tabela vs card

#### Usar tabela quando
- a comparacao entre linhas for a tarefa principal;
- houver muitas colunas e necessidade de ordenacao por campo;
- houver acao em massa ou operacao administrativa repetitiva;
- precisao visual dos dados (numero, data, codigo) for prioridade.

#### Usar card quando
- a leitura contextual por item for mais importante que comparacao tabular;
- estado visual do item (status, risco, timeline curta) for decisivo;
- a navegacao for orientada a detalhe (card inteiro clicavel);
- a experiencia mobile for prioridade de uso.

#### Regra de decisao rapida
- Tabela deve ser utilizada quando o usuario precisa comparar varios registros ao mesmo tempo.
- Card deve ser utilizado quando o usuario precisa entender rapidamente o contexto de cada registro.

#### Deve-se evitar
- misturar tabela e card na mesma listagem sem justificativa de UX;
- mudar paradigma visual por modulo sem criterio documentado.

## 6. Sistema de design (tokens e semantica)

### 6.1 Tipografia
Escala recomendada:

```css
h1: text-3xl lg:text-4xl font-light tracking-tight
h2: text-xl font-semibold
h3: text-lg font-medium
h4: text-base font-medium
body: text-sm
meta: text-xs text-gray-500
```

### 6.2 Cores semanticas
- `emerald`: sucesso/progresso/confirmacao
- `blue`: informacao/em andamento
- `amber|yellow`: alerta moderado
- `red`: erro/atraso/cancelamento
- `purple`: administracao/privilegios
- `gray|slate`: estrutura/metadado/inativo

Regra:
- uma cor, um significado por fluxo.

### 6.3 Espacamento e elevacao
- Padrao de radius: `rounded-xl` e `rounded-2xl`
- Cartoes: `shadow-sm` -> `hover:shadow-md|lg`
- Evitar empilhar sombras fortes em blocos adjacentes.

### 6.4 Estados de componentes

Todo componente interativo deve possuir estados visuais explicitos.

Estados base:
- `default`
- `hover`
- `focus`
- `active`
- `disabled`
- `loading`
- `error` (quando aplicavel)

Matriz minima:
- Botao: `default`, `hover`, `active`, `focus`, `disabled`, `loading`.
- Input/select/textarea: `default`, `focus`, `disabled`, `error`.
- Card clicavel: `default`, `hover`, `focus`.
- Tabs/pills: `default`, `hover`, `active`, `focus`.
- Modal: `open`, `closing`, `focus-trap`, `loading` em acoes assincronas.
- Linha de lista/tabela: `default`, `hover`, `selected` (quando houver selecao).

Regras:
- Nao depender apenas de cor para diferenciar estado.
- `focus` deve ser sempre visivel.
- `loading` deve bloquear duplicidade de acao.

### 6.5 Grid e layout responsivo

Padrao base:
- grid principal: `max-w-screen-xl`
- gutter: `24px` desktop / `16px` mobile
- padding lateral: `px-4` mobile / `px-6` desktop

Breakpoints:
- `sm`: `640px`
- `md`: `768px`
- `lg`: `1024px`
- `xl`: `1280px`

Regras:
- evitar conteudo com largura total em telas grandes;
- manter linhas de texto entre `60` e `90` caracteres, quando aplicavel;
- evitar mais de `3` colunas funcionais em telas administrativas;
- priorizar leitura vertical no mobile antes de aumentar densidade em desktop.

### 6.6 Padrao de tabelas

Tabelas administrativas devem seguir modelo unico para evitar divergencia entre modulos.

Estrutura:
- cabecalho fixo;
- cabecalho fixo quando a tabela exceder o viewport vertical;
- ordenacao por coluna quando aplicavel;
- coluna de acoes posicionada a direita.

Regras:
- alinhar numeros a direita;
- alinhar texto a esquerda;
- evitar mais de `8` colunas visiveis simultaneamente;
- truncar textos longos com suporte a tooltip/contexto completo.

Estados obrigatorios:
- `loading`;
- `empty`;
- `erro de carregamento`.

Diretriz de consistencia:
- usar componentes de tabela compartilhados (`x-page.table*`) como base, evoluindo variacoes dentro do mesmo padrao.

### 6.7 Padrao de formularios

A base de formularios deve reutilizar `x-form.*` para padronizar experiencia e manutencao.

Estrutura recomendada:
- agrupamento logico de campos por contexto;
- labels sempre visiveis;
- ajuda contextual quando necessario.

Regras:
- label acima do campo;
- mensagens de erro abaixo do campo;
- campos obrigatorios claramente indicados;
- acao de submit sempre no final do formulario.

Deve-se evitar:
- labels dentro do input como unica referencia;
- formularios extensos sem agrupamento/separacao por blocos.

### 6.8 Hierarquia de acoes

Cada tela deve explicitar prioridade de decisao:
- `1` acao primaria;
- ate `2` acoes secundarias;
- acoes destrutivas visualmente destacadas.

Regras:
- CTA primaria deve ser visualmente dominante;
- evitar mais de `3` botoes relevantes lado a lado;
- acoes destrutivas exigem confirmacao explicita antes de executar.
- acao primaria deve aparecer no canto superior direito do header da pagina, quando aplicavel.

### 6.8.1 Confirmacao de acao destrutiva (padrao obrigatorio)

Toda acao destrutiva deve seguir fluxo de confirmacao antes da execucao final.

Fluxo minimo:
1. Usuario aciona acao destrutiva.
2. Sistema abre modal de confirmacao.
3. Modal explica impacto da acao.
4. Usuario confirma ou cancela.
5. Sistema executa e retorna feedback de sucesso/erro.

Regras:
- botao destrutivo com cor semantica (`red`) e label objetiva;
- botao secundario de cancelamento sempre presente;
- opcao padrao de foco inicial deve ser segura (`Cancelar`);
- em operacoes irreversiveis, reforcar texto de impacto no modal.

Microcopy recomendada:
- titulo: `Confirmar exclusao` / `Confirmar cancelamento`
- descricao: `Esta acao nao pode ser desfeita.`
- acoes: `Cancelar` e `Excluir` (ou verbo destrutivo equivalente)

### 6.9 Estados de sistema

#### Loading
- indicar claramente carregamento de dados;
- evitar tela vazia sem feedback durante processamento;
- quando possivel, usar placeholder/skeleton coerente com o layout final.

#### Empty state
- explicar ausencia de dados em linguagem objetiva;
- sugerir proxima acao (ex.: ajustar filtro, criar novo item);
- manter consistencia visual entre modulos para estados vazios.

#### Error state
- explicar o que falhou sem mensagem tecnica desnecessaria;
- oferecer opcao clara de tentar novamente;
- preservar contexto da tela para evitar perda de fluxo do usuario.

### 6.10 Evolucao do layout system

Mudancas em componentes base devem seguir este fluxo:

1. documentacao da mudanca;
2. avaliacao de impacto (modulos, telas, comportamento);
3. migracao progressiva com compatibilidade;
4. remocao controlada do padrao antigo.

Regra de governanca:
- evitar breaking changes sem plano explicito de migracao.

## 7. Padroes de interacao

| Acao | Feedback | Timing |
|---|---|---|
| Click | estado visual imediato | `< 100ms` |
| Submit | loading + disable | durante processamento |
| Erro | campo destacado + mensagem | imediato |
| Sucesso | toast | ~3s |
| Hover | transicao suave | ~200ms |

Regras:
- sem loading silencioso em acao assincrona;
- sem duplo submit;
- feedback sempre contextual ao elemento afetado.

## 8. Acessibilidade (WCAG 2.1 AA)

Checklist minimo:
- Navegacao completa por teclado (`Tab`, `Shift+Tab`, `Enter`, `Esc`).
- Focus visivel em todos os interativos.
- ARIA para controles sem texto.
- Contraste: `4.5:1` (texto normal), `3:1` (texto grande).
- Touch target minimo: `44x44px`.
- Hierarquia semantica de titulos (`h1 -> h2 -> h3`).

## 9. Performance UX

Metas:
- LCP `< 2.5s`
- INP `< 200ms`
- CLS `< 0.1`

Praticas:
- lazy loading para conteudo pesado;
- debounce em busca em tempo real;
- reduzir JS inline em telas densas;
- evitar shifts de layout (reservar espaco para blocos dinamicos).

## 10. Priorizacao de melhorias (acordadas)

### 10.1 Imediatas
- Padronizar componente de card em todos os modulos.
- Unificar sistema de toasts (feedback global consistente).
- Revisar contraste de textos cinza claro.

### 10.2 Estrategicas
- Sistema de temas claro/escuro com tokens.
- Analytics de UX para pontos de atrito.
- Feedback integrado (pesquisas rapidas).
- Testes automatizados de acessibilidade no CI/CD.
- Migracao gradual de listagens para padrao moderno (cards/tabelas modernas).
- Padronizacao completa de componentes para todos os modulos.

### 10.3 Padronizacao completa de componentes (futuro)

Objetivo:
- consolidar comportamento, estilo e API dos componentes compartilhados.

Escopo inicial:
- `x-button`
- `x-form.*`
- `x-page.*`
- `x-modal`
- componentes de sidebar e navegacao

Diretrizes:
- reduzir variacoes redundantes de estilo.
- manter nomenclatura e props previsiveis.
- criar versoes canônicas por tipo (primary/secondary/destructive, etc).
- aplicar migracao progressiva por modulo (sem ruptura global).

## 11. Fluxo de trabalho para mudancas de layout

1. Registrar ideia em `docs-ia/LAYOUT_GUIDE.md` e, se afetar API de componente, tambem em `docs-ia/COMPONENTS.md`.
2. Definir escopo e criterio de aceite.
3. Implementar em componentes base primeiro (quando aplicavel).
4. Propagar para telas do modulo.
5. Validar responsividade + acessibilidade + performance.
6. Atualizar documentacao final (`COMPONENTS.md` e este arquivo, quando aplicavel).

## 12. Checklist de PR (obrigatorio)

### 12.1 Estrutural
- Usa template de tela apropriado.
- Reusa componentes base.
- Componentes existentes foram avaliados antes de criar novo componente.
- Responsabilidades Blade / componente / Livewire foram respeitadas.
- Nao move regra de negocio para Blade.

### 12.2 Visual
- Hierarquia tipografica correta.
- Semantica de cores preservada.
- Espacamento consistente.
- Empty states claros.

### 12.3 Interacao
- Loading, erro e sucesso implementados.
- Estados hover/focus/disabled visiveis.
- Navegacao de teclado funcional.

### 12.4 Performance
- Debounce em busca.
- Paginacao/infinite scroll em listas longas.
- Sem regressao perceptivel de carregamento.

## 13. Referencias rapidas

- Catalogo de componentes: `docs-ia/COMPONENTS.md`
- Conventions: `docs-ia/CONVENTIONS.md`
- Architecture: `docs-ia/ARCHITECTURE.md`
- Domain map: `docs-ia/DOMAINS.md`

## 13.1 Relacao com o catalogo de componentes

Este guia define:
- principios de interface;
- semantica visual;
- estrutura de pagina;
- regras de interacao e acessibilidade.

O arquivo `docs-ia/COMPONENTS.md` define:
- inventario real dos componentes Blade compartilhados;
- props e variacoes suportadas hoje;
- inconsistencias atuais da API;
- padronizacao alvo para alinhamento gradual.

Regra:
- ao evoluir um componente base, atualizar os dois arquivos quando a mudanca impactar layout e API.

## 14. Guia de microcopy

### 14.1 Principios
- Clareza antes de criatividade.
- Frases curtas, objetivas e acionaveis.
- Linguagem consistente em PT-BR.
- Evitar termos tecnicos para o usuario final quando houver alternativa simples.

### 14.2 Padroes por contexto
- Botoes:
  - usar verbo no infinitivo com objetivo claro (`Salvar`, `Avancar etapa`, `Marcar como lida`).
- Toast de sucesso:
  - confirmar acao concluida (`Processo criado com sucesso.`).
- Toast de erro:
  - informar falha sem culpar usuario e, quando possivel, orientar proximo passo.
- Validacao de campo:
  - apontar o problema exato no campo (`Informe o titulo do processo.`).
- Empty state:
  - explicar ausencia de dados e sugerir acao (`Nenhum processo encontrado. Ajuste os filtros ou crie um novo processo.`).

### 14.3 Regras de consistencia
- Nao alternar sinonimos para a mesma acao entre telas (`Salvar` vs `Gravar`) sem motivo.
- Manter o mesmo termo para o mesmo conceito de dominio:
  - `Processo`, `Etapa`, `Setor`, `Responsavel`, `Notificacao`.
- Em textos de risco, priorizar semantica clara:
  - `Atrasado`, `Cancelado`, `Pendente`.

### 14.4 Checklist rapido de microcopy
- O texto explica claramente o que aconteceu?
- O usuario entende o que fazer em seguida?
- O termo utilizado esta consistente com o resto do sistema?
- A frase cabe bem no componente sem comprometer legibilidade?

## 15. Criterios de qualidade visual

### 15.1 Criterios obrigatorios
- Hierarquia visual clara:
  - titulo, subtitulo, conteudo e metadados com pesos distintos.
- Consistencia de espacamento:
  - manter ritmo entre blocos e evitar distancias irregulares.
- Semantica de cores:
  - cores refletem estado funcional (sucesso, alerta, erro, informacao).
- Legibilidade:
  - contraste adequado e tamanhos de fonte compatíveis com contexto.
- Componentizacao:
  - priorizar componentes base do sistema antes de estilos ad-hoc.
- Responsividade:
  - layout funcional em mobile, tablet e desktop sem quebra de fluxo.

### 15.2 Sinais de baixa qualidade visual
- Excesso de estilos isolados para resolver casos pontuais.
- Cores sem relacao com significado de dominio.
- Blocos com densidade visual desigual na mesma tela.
- Acoes importantes sem destaque adequado.
- Estados vazios sem orientacao clara para o usuario.

### 15.3 Gate de aprovacao visual
- O design segue padrao do modulo e do shell global?
- A leitura da tela funciona em varredura rapida (3-5 segundos)?
- A acao primaria esta clara e proxima do contexto?
- Ha coerencia visual com telas equivalentes do sistema?

## 16. Glossario rapido

### 16.1 Termos de UI/UX
- `CTA`:
  - Call To Action. Acao principal esperada na tela.
- `Empty state`:
  - estado exibido quando nao ha dados para mostrar.
- `Feedback`:
  - resposta visual do sistema apos interacao (loading, erro, sucesso).
- `Token visual`:
  - valor reutilizavel de design (cor, espacamento, tipografia).
- `Scanability`:
  - facilidade de leitura rapida da tela.
- `Affordance`:
  - pista visual de que um elemento e clicavel/interativo.

### 16.2 Termos de dominio do X-AdminPanel
- `Processo`:
  - entidade operacional que percorre etapas de workflow.
- `Etapa`:
  - fase do processo com setor e prazo associados.
- `Setor`:
  - unidade organizacional vinculada ao organograma.
- `Workflow`:
  - sequencia de etapas que orienta execucao de processos.
- `Responsavel`:
  - usuario atribuido ao processo no momento atual.
- `Notificacao`:
  - aviso gerado por interacoes relevantes do sistema.
- `Dashboard`:
  - tela de consolidacao com indicadores e atalhos de acao.
