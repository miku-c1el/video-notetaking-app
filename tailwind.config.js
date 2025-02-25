import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
                montserrat: ['Montserrat', 'sans-serif'],
            },
            colors: {
                'primary': '#b2a6d9',
                'primary-dark': '#2d2c38',
                'primary-light': '#ede8F5',
                'background': '#f6f5f4',
            }
        },
    },

    plugins: [
        forms,
        require('@tailwindcss/aspect-ratio'),
    ],
};
