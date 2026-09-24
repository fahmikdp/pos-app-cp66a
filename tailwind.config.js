/** @type {import('tailwindcss').Config} */
const colors = require('tailwindcss/colors')

export default {
  darkMode: 'class',
  content: [
    './resources/**/*.blade.php',
    './resources/**/*.js',
    './app/Livewire/**/*.php',
    './vendor/wireui/wireui/src/**/*.php',
    './vendor/wireui/wireui/ts/**/*.ts',
    './vendor/wireui/wireui/resources/views/**/*.blade.php',
    './vendor/power-components/livewire-powergrid/resources/**/*.blade.php',
    './vendor/power-components/livewire-powergrid/resources/**/*.js',
  ],
  theme: {
    extend: {
      colors: {
        primary: colors.red,
        secondary: colors.slate,
        positive:  colors.emerald,
        negative:  colors.orange,
        warning:   colors.amber,
        info:      colors.blue,
      },
    },
  },
  plugins: [],
}
