@props(['disabled' => false, 'name' => null, 'default' => 'Selecione uma Opção'])

<select name="{{ $name }}" {{ $attributes->merge([
        'class' => 'w-full rounded-md border px-2.5 py-2 text-xs shadow-sm transition-all duration-200 disabled:bg-gray-300 disabled:text-gray-700 disabled:cursor-not-allowed ' . ($errors->has($name) && $disabled === false
                ? 'border-red-500 bg-red-50 text-red-700 placeholder-red-400 focus:border-red-500 focus:ring-red-500'
                : 'border-gray-300 bg-gray-50 text-gray-700 placeholder-gray-400 focus:border-indigo-700')
    ]) }}>
    
    <option disabled selected>{{ $default }}</option>
    {{ $slot }}
</select>