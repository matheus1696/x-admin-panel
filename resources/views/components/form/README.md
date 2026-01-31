# Estrutura Componentização do Formulário

resources/views/components/form/
├── select.blade.php
└── select-livewire.blade.php

## Componente Select Search

Componente de select customizado utilizando Alpine.js, compatível com Blade puro e Livewire.

### Recursos

- Busca integrada
- Navegação por teclado
- Acessibilidade básica (ARIA)
- Variantes visuais
- Compatível com formulários tradicionais e Livewire
- Sem dependências externas de JavaScript

##  Atributos

| Propriedade   | Tipo             | Descrição                    |
|---------------|------------------|------------------------------|
| `name`        | `string`         | Nome do campo (obrigatório)  |
| `wire:model`  | `string`         | Model Livewire (obrigatório) |
| `collection`  | `Collection`     | Coleção Eloquent             |
| `options`     | `array`          | Array manual de opções       |
| `labelField`  | `string`         | Campo usado como label       |
| `labelAcronym`| `string \| null` | Campo adicional para prefixo |
| `valueField`  | `string`         | Campo usado como value       |
| `placeholder` | `string`         | Texto padrão                 |
| `variant`     | `string`         | `default` ou `inline`        |
| `disabled`    | `boolean`        | Desativa o select            |


### Uso Básico (Blade)

```blade
<x-form.select
    name="status"
    :options="[
        ['value' => 'open', 'label' => 'Aberto'],
        ['value' => 'closed', 'label' => 'Fechado'],
    ]"
/>
```

### Uso Básico (Livewire)

```blade
<x-form.select-livewire
    name="user_id"
    wire:model.live="user_id"
    :collection="$users"
    label-field="name"
    placeholder="Selecione um usuário"
/>
```

### Uso com Collection (Blade)

```blade
<x-form.select
    name="user_id"
    :collection="$users"
    label-field="name"
    value-field="id"
    placeholder="Selecione um usuário"
/>
```

### Valor Selecionado (Blade)

```blade
<x-form.select
    name="status"
    :options="$statusOptions"
    :selected="$db->is_active"
/>
```

### Uso de Laytous Variantes (Blade)

```blade
<x-form.select
    name="priority"
    :options="$priorities"
    variant="inline"
/>
```

### Navegação por Teclado

| Tecla | Ação                 |
|-------|----------------------|
| Tab   | Foca e abre o select |
| ↑ / ↓ | Navega entre opções  |
| Enter | Seleciona a opção    |
| Esc   | Fecha o dropdown     |

- Enter **não** submete o formulário
- Foco automático no campo de busca ao abrir
- Fecha ao perder o foco ou ao clicar fora

### Boas Práticas

- Utilize `collection` sempre que possível
- Não misture `select` com `select-livewire`
- Indicado para formulários administrativos
