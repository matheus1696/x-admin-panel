@props([ 'value' => null, ])

<th {{ $attributes->merge([ 'class' => 'px-6 py-4 tracking-wider' ]) }}> {{ $value ?? $slot }} </th>