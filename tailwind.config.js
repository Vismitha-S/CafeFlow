import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';

// Tailwind configuration for CafeFlow vintage cafe theme
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './vendor/laravel/jetstream/**/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './app/Services/**/*.php',
        './app/Http/Controllers/**/*.php',
    ],

    theme: {
        extend: {
            colors: {
                cream: {
                    50: '#FAF7F2',
                    100: '#F5EFE6',
                    200: '#EFE7DC',
                    300: '#E4D8C8',
                    400: '#D5C4AF',
                    500: '#C4AE95',
                    600: '#AE957B',
                    700: '#947B62',
                    800: '#735E49',
                    900: '#544333',
                },
                coffee: {
                    50: '#F8F5F2',
                    100: '#EDE4DC',
                    200: '#DCCBBD',
                    300: '#BF9E83',
                    400: '#9E7A5D',
                    500: '#7D5C42',
                    600: '#644732',
                    700: '#4D3525',
                    800: '#352317',
                    900: '#20150D',
                    950: '#150D08',
                },
                accent: {
                    50: '#FDF6F0',
                    100: '#FAECE0',
                    200: '#F5D7C2',
                    300: '#EDB997',
                    400: '#E09265',
                    500: '#C8632B',
                    600: '#B55320',
                    700: '#954117',
                    800: '#783516',
                    900: '#5F2D14',
                },
                sage: {
                    50: '#F4F7F4',
                    100: '#E7EFE8',
                    200: '#D0DFD2',
                    300: '#ADC6B1',
                    400: '#83A689',
                    500: '#5E8565',
                    600: '#4A6D51',
                    700: '#3A543E',
                    800: '#2F4333',
                    900: '#27372B',
                },
                brass: {
                    50: '#FDFBF7',
                    100: '#F9F5EC',
                    200: '#F2E8D3',
                    300: '#E7D5B0',
                    400: '#D7BC84',
                    500: '#BF9E58',
                    600: '#A38141',
                    700: '#7F6231',
                    800: '#644D27',
                    900: '#4D3B20',
                },
                taupe: {
                    50: '#F9F8F6',
                    100: '#F1EFEA',
                    200: '#E2DED6',
                    300: '#CCC5BA',
                    400: '#B2A99B',
                    500: '#948979',
                    600: '#786C5D',
                    700: '#5E5448',
                    800: '#473F37',
                    900: '#332D28',
                },
            },
            fontFamily: {
                sans: ['Plus Jakarta Sans', 'Figtree', ...defaultTheme.fontFamily.sans],
                serif: ['Playfair Display', 'Georgia', 'serif'],
                display: ['Playfair Display', 'Georgia', 'serif'],
            },
            boxShadow: {
                'subtle': '0 2px 10px -2px rgba(43, 30, 22, 0.04), 0 1px 3px -1px rgba(43, 30, 22, 0.03)',
                'card': '0 4px 20px -2px rgba(43, 30, 22, 0.06), 0 2px 6px -2px rgba(43, 30, 22, 0.04)',
                'card-hover': '0 12px 30px -4px rgba(43, 30, 22, 0.10), 0 4px 10px -2px rgba(43, 30, 22, 0.05)',
                'glass': '0 8px 32px 0 rgba(43, 30, 22, 0.06)',
            },
        },
    },

    plugins: [forms, typography],
};
