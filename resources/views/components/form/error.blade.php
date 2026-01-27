@props(['messages'])

@if (!empty($messages))
    <p {{ $attributes->merge(['class' => 'text-xs text-red-600 dark:text-red-400 pt-1 pl-1']) }}>
        {{ is_array($messages) ? $messages[0] : $messages }}
    </p>
@endif
