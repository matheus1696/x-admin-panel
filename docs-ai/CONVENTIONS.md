# Conventions

Padrões observados neste repositório.

## Organização

O código é agrupado por contexto de negócio:

- `Organization`
- `Task`
- `Administration`
- `Configuration`
- `Audit`

Esse recorte aparece em `Models`, `Services`, `Livewire`, `Validation` e parte de `Http/Requests`.

## Controllers

Controllers são usados em fluxos HTTP clássicos:

- auth
- profile
- dashboard
- audit

Padrão recorrente:

- métodos curtos
- `view()` ou `redirect()` direto
- `FormRequest` para validação
- logging explícito quando o fluxo é auditável

Exemplo:

```php
public function update(ProfileUpdateRequest $request): RedirectResponse
{
    $user = User::find(Auth::user()->id);
    $user->update($request->validated());

    return redirect()->route('profile.edit');
}
```

## Services

Services concentram mutação e consistência.

Padrão recorrente:

- uma classe por subdomínio
- métodos como `find`, `index`, `create/store`, `update`, `status`, `delete`
- filtros simples em `index`
- transação quando há reorder ou atualização coordenada

Exemplo:

```php
public function update(int $id, array $data): void
{
    $organizationChart = OrganizationChart::findOrFail($id);
    $organizationChart->update($data);
    $this->reorder();
}
```

O projeto não usa `Actions`, DTOs ou repositories como padrão.

## Livewire

Livewire é a interface principal dos módulos internos.

Padrão recorrente:

- propriedades públicas para filtros e formulário
- `boot()` para injeção de service
- `mount()` para contexto inicial
- `updatedFilters()` chama `resetPage()`
- `create`, `edit`, `store`, `update`, `status`
- `render()` retorna view com `layouts.app`
- traits `Modal` e `WithFlashMessage`

Exemplo:

```php
public function store(): void
{
    $data = $this->validate(UserRules::store());
    $this->userService->store($data);
}
```

Padrão de consulta:

- dataset principal via service
- listas auxiliares podem vir direto do model

## Validação

Dois formatos aparecem no projeto:

- `FormRequest` em controllers
- classes em `app/Validation/...` para Livewire

Exemplos:

```php
$data = $this->validate(UserRules::store());
```

```php
public function update(ProfileUpdateRequest $request)
```

## Models

Padrão recorrente:

- `fillable` explícito
- `casts` para datas e flags
- relacionamentos em inglês
- traits internas como `HasUuid`, `HasActive`, `HasTitleFilter`
- `booted()` para campos derivados simples

Exemplo:

```php
static::created(function ($task) {
    $taskCount = $task->taskHub->tasks()->count();
    $task->update(['code' => $task->taskHub->acronym . str_pad($taskCount, 5, '0', STR_PAD_LEFT)]);
});
```

## Rotas E Permissões

Padrão recorrente:

- prefixos em PT-BR
- agrupamento por contexto
- `name()` semântico
- `middleware('can:...')` na borda
- páginas Livewire ligadas direto na rota

Exemplo:

```php
Route::get('/organizacao/workflow', WorkflowProcessesPage::class)
    ->middleware('can:organization.manage.workflow');
```

## Pest

Padrão observado:

- `tests/Pest.php` aplica `Tests\TestCase`
- `RefreshDatabase` entra automaticamente em `Feature`
- testes usam `test('...', function () {})`
- helpers locais por arquivo são comuns
- `actingAs()` para HTTP
- `Auth::login()` para service
- `Livewire::test()` para componentes
- `expect(...)` para asserts

Exemplo:

```php
test('users cannot access task hubs they do not own or share', function () {
    $this->actingAs($user)
        ->get('/tarefas/' . $privateHub->uuid)
        ->assertNotFound();
});
```

## Observações Práticas

- UI usa PT-BR; classes e métodos usam inglês.
- Mensagens de sucesso e erro costumam sair da camada de UI.
- O projeto favorece nomes diretos, como `store`, `update` e `status`.
- Em `Task`, parte da regra ainda está em `TaskPage`; não assuma service puro em todos os fluxos.
