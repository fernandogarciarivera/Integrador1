import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            spacing: {
                xs: '0.25rem',
                sm: '0.75rem',
                md: '1.5rem',
                lg: '2.5rem',
                xl: '4rem',
            },
            colors: {
                brand: 'var(--theme-primary)',
                'brand-soft': 'var(--theme-primary-soft)',
                'theme-border': 'var(--theme-border)',
            },
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [forms],
};
