@props([ 'value' => null, ])

<td {{ $attributes->merge([ 'class' => 'px-6 py-3 break-words whitespace-nowrap' ]) }}> {{ $value ?? $slot }} </td>