import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    safelist: [
        'bg-gray-100', 'text-gray-700', 'hover:bg-gray-200',
        'bg-blue-100', 'text-blue-700', 'hover:bg-blue-200',
        'bg-green-100', 'text-green-700', 'hover:bg-green-200',
        'bg-yellow-100', 'text-yellow-800', 'hover:bg-yellow-200',
        'bg-red-100', 'text-red-700', 'hover:bg-red-200',
        'bg-purple-100', 'text-purple-700', 'hover:bg-purple-200',
    ],
    
    theme: {
        extend: {
            fontFamily: {
                sans: ['Open Sans', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [forms],
};