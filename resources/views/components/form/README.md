# Form Components

Guia rapido da familia `x-form.*`.

## Componentes

- `x-form.label`
- `x-form.error`
- `x-form.input`
- `x-form.textarea`
- `x-form.select`
- `x-form.select-livewire`

## Matriz Padrao

Variantes compartilhadas:
- `default`
- `outline`
- `filled`
- `minimal`
- `glass`
- `pills`

Tamanhos compartilhados:
- `xs`
- `sm`
- `md`
- `lg`

Cores compartilhadas:
- `green`
- `blue`
- `purple`
- `red`
- `yellow`
- `gray`
- `sky`
- `indigo`

## Regras De Uso

- `x-form.label` deve ficar acima do campo.
- `x-form.error` deve ficar imediatamente abaixo do campo.
- `x-form.select` deve ser usado em Blade puro.
- `x-form.select-livewire` deve ser usado quando houver `wire:model`.
- `variant="default"` e `borderColor="green"` sao o baseline do sistema.
- `variant="minimal"` deve ficar restrito a filtros compactos e toolbars.
- `glass` e `pills` devem ser usados com criterio, nao como padrao geral.

## Exemplos

### Input

```blade
<x-form.label for="title" value="Titulo" required />
<x-form.input
    name="title"
    placeholder="Informe o titulo"
/>
<x-form.error for="title" />
```

### Textarea

```blade
<x-form.label for="description" value="Descricao" />
<x-form.textarea
    name="description"
    rows="4"
    variant="filled"
/>
<x-form.error for="description" />
```

### Select Blade

```blade
<x-form.select
    name="status"
    :options="[
        ['value' => 'open', 'label' => 'Aberto'],
        ['value' => 'closed', 'label' => 'Fechado'],
    ]"
    placeholder="Selecione um status"
/>
```

### Select Livewire

```blade
<x-form.select-livewire
    name="user_id"
    wire:model.live="userId"
    :collection="$users"
    label-field="name"
    placeholder="Selecione um usuario"
/>
```

## Observacoes

- `x-form.select-livewire` exige `wire:model`.
- `x-form.input` e `x-form.textarea` aceitam `loading` para bloquear interacao.
- `x-form.label` aceita `required` e `size`.
- `x-form.error` aceita `icon` para customizacao leve.
