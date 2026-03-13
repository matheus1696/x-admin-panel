# Conventions

Padroes praticos do repositorio.
Atualizado em: 2026-03-12.

## Linguagem

- UI: PT-BR
- documentacao e arquivos: UTF-8
- backend (PHP, classes, metodos e regras): Ingles

Regra obrigatoria para texto de UI:

- ao editar Blade ou Livewire com acentos, validar se nao existe mojibake antes de finalizar
- comando de checagem: `rg -n "Ãƒ|Ã‚|ï¿½|Ã¢|Ã…" resources/views app/Livewire`

## Organizacao Por Dominio

Pastas seguem contexto de negocio em `Models`, `Services`, `Livewire`, `Validation`, `DTOs` e `Policies`.

Dominios ativos hoje:

- `Organization`
- `Process`
- `Task`
- `Assets`
- `TimeClock`
- `Administration`
- `Configuration`
- `Audit` e dominios de suporte

## Controllers

Controllers devem permanecer finos.

- receber request
- validar com `FormRequest` quando o fluxo for HTTP classico
- delegar regra de negocio a service
- retornar `view()` ou `redirect()`

## Services

Services sao a fonte principal de consistencia.

- concentrar escrita de dominio
- encapsular transacao quando necessario
- registrar historico operacional ou institucional quando o fluxo exigir
- usar DTOs quando o input for mais sensivel ou multi-campo
- evitar espalhar regra entre Livewire, Model e Controller

## Validacao

- `FormRequest` para controllers classicos
- classes em `app/Validation/*` para regras reutilizaveis em Livewire e services
- exceptions de validacao de dominio devem carregar mensagem de negocio clara

## Livewire

Livewire orquestra interface, nao regra de dominio.

- `boot()` injeta services quando isso melhora legibilidade
- `mount()` carrega contexto inicial
- validacoes de entrada ficam em classes dedicadas quando reaproveitaveis
- mutacoes criticas passam por service

Observacao atual do projeto:

- `TaskPage` ainda concentra parte da orquestracao do modulo e deve ser tratado como acoplamento conhecido

### Alpine Em Paginas Grandes

- evitar objetos complexos inline em `x-data="{ ... }"` quando a pagina tiver muitos binds Alpine/Livewire
- preferir `x-data="nomeDoEstado($wire)"` com funcao JS dedicada
- isso reduz falhas silenciosas de parsing e hidratacao

Sinais de problema tipico:

- `Alpine Expression Error`
- variaveis `undefined`
- modal nao abre, aside nao sincroniza ou componente Livewire perde snapshot

## Models

- `fillable` explicito
- `casts` coerente com datas, enums e flags
- relacoes em Ingles
- evitar logica de negocio pesada em eventos de model

## Rotas E Permissoes

- prefixos de URL em PT-BR quando isso ja for padrao do modulo
- `name()` semantico e consistente
- `middleware('can:...')` na borda
- policy para autorizacao contextual
- fonte de verdade para RBAC: Spatie Permission (`roles` e `permissions`)

Compatibilidade atual:

- endpoints legados `/profile` permanecem ativos para testes e integracoes existentes

## Notificacoes

- notificacoes de sistema usam `NotificationService`
- a notificacao deve nascer no fechamento do fluxo de negocio, nao na view
- payload deve carregar `url`, `icon`, `level` e `meta` apenas quando agregam contexto real

## Testes

Padrao Pest com `RefreshDatabase` em `Feature`.

- HTTP: `actingAs()`
- Service: login explicito quando necessario
- Livewire: `Livewire::test()`
- asserts com `expect()` ou asserts HTTP

## Anti-Padroes

- regra de negocio em Blade
- escrita direta de dominio em Livewire quando ja existe service
- duplicacao de permissao com comportamento divergente
- logica de hierarquia fora do service central
- dashboard virando hub de negocio
- modulo de suporte absorvendo responsabilidade de modulo central
