import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './resources/views/**/*.blade.php',
    ],

    safelist: [
        'translate-x-0',
        '-translate-x-full',
        'lg:translate-x-0',
        'lg:w-20',
        'lg:w-64',
        'w-64',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', 'sans-serif', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                brand: {
                    // Light theme palette
                    bg: '#F5F7FA',       // page background — soft off-white
                    surface: '#FFFFFF',        // card / panel surfaces
                    border: '#E4E9F0',        // dividers and borders
                    muted: '#F0F4F8',        // subtle section backgrounds
                    text: '#1A202C',        // primary text — near-black
                    sub: '#64748B',        // secondary / muted text
                    accent: '#0EA5E9',        // primary accent — sky blue
                    accentd: '#0284C7',        // accent darker (hover)
                    acents: '#E0F2FE',        // accent pale wash (badge bg)
                },
                status: {
                    success: '#10B981',
                    successs: '#D1FAE5',
                    danger: '#EF4444',
                    dangers: '#FEE2E2',
                    warning: '#F59E0B',
                    warnings: '#FEF3C7',
                    info: '#6366F1',
                    infos: '#EEF2FF',
                }
            },
            boxShadow: {
                'card': '0 1px 3px 0 rgba(0,0,0,0.07), 0 1px 2px -1px rgba(0,0,0,0.04)',
                'card-md': '0 4px 12px 0 rgba(0,0,0,0.08), 0 2px 4px -1px rgba(0,0,0,0.04)',
                'card-lg': '0 8px 24px 0 rgba(0,0,0,0.10), 0 4px 8px -2px rgba(0,0,0,0.06)',
                'accent': '0 4px 14px 0 rgba(14,165,233,0.30)',
            },
        },
    },

    plugins: [forms],
};
