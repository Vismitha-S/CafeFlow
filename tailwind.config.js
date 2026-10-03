import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './vendor/laravel/jetstream/**/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            // CafeFlow brand colour palette
            colors: {
                cream: {
                    50: '#FFFDF7',
                    100: '#FFF9EB',
                    200: '#FFF3D6',
                    300: '#FFE8B8',
                    400: '#FFDFA0',
                    500: '#F5E6D3',
                    600: '#E8D5BD',
                    700: '#D4BFA3',
                    800: '#C4A882',
                    900: '#A88B64',
                },
                coffee: {
                    50: '#F7F0EA',
                    100: '#EDE0D2',
                    200: '#D4BFA3',
                    300: '#B89B72',
                    400: '#8B6F47',
                    500: '#6B4F30',
                    600: '#5A3E22',
                    700: '#4A3019',
                    800: '#3C2415',
                    900: '#2A1A0E',
                },
                accent: {
                    50: '#FFF4EB',
                    100: '#FFE4CC',
                    200: '#FFD0A8',
                    300: '#FFB87A',
                    400: '#F59E4D',
                    500: '#E8722A',
                    600: '#D45A1A',
                    700: '#B84712',
                    800: '#95380E',
                    900: '#6E2A0B',
                },
            },
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
                display: ['Playfair Display', 'serif'],
            },
            // Custom animation keyframes for CafeFlow
            animation: {
                'fade-in': 'fadeIn 0.6s ease-out forwards',
                'fade-in-up': 'fadeInUp 0.6s ease-out forwards',
                'fade-in-down': 'fadeInDown 0.5s ease-out forwards',
                'slide-in-left': 'slideInLeft 0.6s ease-out forwards',
                'slide-in-right': 'slideInRight 0.6s ease-out forwards',
                'scale-in': 'scaleIn 0.4s ease-out forwards',
            },
            keyframes: {
                fadeIn: {
                    '0%': { opacity: '0' },
                    '100%': { opacity: '1' },
                },
                fadeInUp: {
                    '0%': { opacity: '0', transform: 'translateY(20px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
                fadeInDown: {
                    '0%': { opacity: '0', transform: 'translateY(-10px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
                slideInLeft: {
                    '0%': { opacity: '0', transform: 'translateX(-30px)' },
                    '100%': { opacity: '1', transform: 'translateX(0)' },
                },
                slideInRight: {
                    '0%': { opacity: '0', transform: 'translateX(30px)' },
                    '100%': { opacity: '1', transform: 'translateX(0)' },
                },
                scaleIn: {
                    '0%': { opacity: '0', transform: 'scale(0.95)' },
                    '100%': { opacity: '1', transform: 'scale(1)' },
                },
            },
        },
    },

    plugins: [forms, typography],
};
