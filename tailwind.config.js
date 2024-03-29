/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        "./assets/**/*.js",
        "./templates/**/*.html.twig",
        "./node_modules/flowbite/**/*.js",
        "./node_modules/tw-elements/js/**/*.js" // set up the path to the flowbite package
    ],
    theme: {
        extend: {},
    },
    plugins: [
        require("tw-elements/plugin.cjs"),
        require('flowbite/plugin')
    ],
    darkMode: "class"
}