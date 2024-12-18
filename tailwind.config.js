/** @type {import('tailwindcss').Config} */
module.exports = {
    mode: 'jit',
    content: [
        './assets/**/*.js',
        './templates/theme/tailwind/**/*.{html.twig,js}',
        './node_modules/flowbite/**/*.js',
    ],
    theme: {
        fontFamily: {
            'oswald': ['Oswald'],
            'roboto': ['Roboto'],
            'sans': ['Helvetica', 'Arial', 'sans-serif'],
        },
        extend: {
            height: {
                '100': '100px',
                '150': '150px',
                '200': '200px',
                '250': '250px',
                '300': '300px',
                '400': '400px',
            },
            width: {
                '100': '100px',
                '150': '150px',
                '295': '295px'
            },
            gridTemplateColumns: {
                'auto-fill-200': 'repeat(auto-fill, minmax(200px, 1fr))',
                'auto-fit-200': 'repeat(auto-fit, minmax(200px, 1fr))',
                'auto-fill-150': 'repeat(auto-fill, minmax(150px, 1fr))',
                'auto-fit-150': 'repeat(auto-fit, minmax(150px, 1fr))',
                'auto-fill-300': 'repeat(auto-fill, minmax(300px, 1fr))',
                'auto-fit-300': 'repeat(auto-fit, minmax(300px, 1fr))',
            },
        },
    },
    plugins: [
        'tw-elements/plugin.cjs',
        'flowbite/plugin',
        '@tailwindcss/typography',
        '@tailwindcss/forms',
        '@tailwindcss/aspect-ratio',
        'tailwindcss/inset',
        'tailwindcss/plugin',
        '@tailwindcss/container-queries',
    ],
    darkMode: "class"
}