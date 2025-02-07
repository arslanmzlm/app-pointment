import colors from 'tailwindcss/colors';
import defaultTheme from 'tailwindcss/defaultTheme';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],
    darkMode: 'class',
    theme: {
        extend: {
            fontFamily: {
                sans: ['Satoshi', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                primary: {
                    DEFAULT: '#45d2b0',
                    50: '#f6fdfb',
                    100: '#d2f4ec',
                    200: '#afecdd',
                    300: '#8ce3ce',
                    400: '#68dbbf',
                    500: '#45d2b0',
                    600: '#3bb396',
                    700: '#30937b',
                    800: '#267461',
                    900: '#1c5446',
                    950: '#11352c',
                },
                surface: {
                    0: colors.white,
                    100: colors.slate['100'],
                    200: colors.slate['200'],
                    300: colors.slate['300'],
                    400: colors.slate['400'],
                    500: colors.slate['500'],
                    600: colors.slate['600'],
                    700: colors.slate['700'],
                    800: colors.slate['800'],
                    900: colors.slate['900'],
                    950: colors.slate['950'],
                },
                success: 'var(--p-green-500)',
                danger: 'var(--p-red-500)',
                warn: 'var(--p-orange-500)',
                info: 'var(--p-blue-500)',
            },
        },
    },

    plugins: [require('tailwindcss-primeui')],
};
