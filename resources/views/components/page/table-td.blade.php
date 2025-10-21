@props([ 'value' => null, ])

<td {{ $attributes->merge([ 'class' => 'px-6 py-4 tracking-nowrap' ]) }}> {{ $value ?? $slot }} </td>