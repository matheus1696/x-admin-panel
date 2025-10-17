@props(['buttonText' => null])

<a {{ $attributes->merge([ 'class' => 'w-full px-6 py-2 bg-gradient-to-r from-blue-600 to-blue-700 text-white font-medium rounded-xl shadow-lg hover:shadow-xl transform transition-all duration-300 ease-in-out focus:outline-none focus:ring focus:ring-blue-500/30 border-0 text-sm']) }}>{{ $buttonText }}</a>