# Conventions

Padroes praticos do repositorio.

## Linguagem

- UI: PT-BR
- Documentacao e arquivos: UTF-8
- Backend (PHP, classes, metodos, regras): Ingles

Regra obrigatoria para texto de UI:
- Ao editar Blade/Livewire com acentos, validar que nao existe mojibake antes de finalizar.
- Comando de checagem: `rg -n "Ã|Â|�|â|Å" resources/views app/Livewire`.

## Organizacao Por Dominio

Pastas seguem contexto de negocio em `Models`, `Services`, `Livewire` e `Validation`.

- `Organization`
- `Task`
- `Administration`
- `Configuration`
- `Audit` e dominios de suporte

## Controllers

Controllers devem permanecer finos.

- receber request
- validar com FormRequest
- chamar service quando houver regra de negocio
- retornar `view()` ou `redirect()`

## Services

Services sao a fonte principal de consistencia.

- concentrar escrita de dominio
- encapsular transacao quando necessario
- registrar historico operacional quando o fluxo exige
- evitar espalhar regra entre Livewire e Model

## Livewire

Livewire orquestra interface, nao regra de dominio.

- `boot()` injeta services
- `mount()` carrega contexto inicial
- validacoes de entrada em classes de validacao
- mutacoes via services

Observacao atual do projeto:
- `TaskPage` ainda concentra parte da orquestracao do modulo e deve ser tratado como acoplamento conhecido.

### Alpine Em Paginas Grandes

- Evitar objetos complexos inline em `x-data="{ ... }"` quando a pagina tiver muitos binds Alpine/Livewire.
- Preferir `x-data="nomeDoEstado($wire)"` com estado/metodos definidos em funcao JS dedicada.
- Isso reduz falhas silenciosas de parsing/hidratacao em componentes extensos.

Sinal de problema tipico:
- erros no browser log como `Alpine Expression Error` e variaveis `undefined` (`tab`, `openAsideTask`, `draggedStepId`).
- efeitos colaterais: modal nao abre, select-livewire trava/interfere, aside abre sem carregar corretamente.

## Models

- `fillable` explicito
- `casts` coerente com datas/flags
- relacoes em ingles
- evitar logica de negocio pesada em eventos de model

## Rotas E Permissoes

- prefixos de URL em PT-BR quando ja padronizados no sistema
- `name()` semantico
- `middleware('can:...')` na borda
- policy para autorizacao contextual

Compatibilidade atual:
- endpoints legados `/profile` permanecem ativos para testes e integracoes existentes.

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
