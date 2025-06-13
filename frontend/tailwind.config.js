/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./index.html",
    "./src/**/*.{js,ts,jsx,tsx}",
  ],
  darkMode: 'class',
  theme: {
    extend: {
      colors: {
        primary: {
          50: '#e6f1ff',
          100: '#cce3ff',
          200: '#99c7ff',
          300: '#66aaff',
          400: '#338eff',
          500: '#0072ff',
          600: '#005bcc',
          700: '#004499',
          800: '#002e66',
          900: '#001733',
        },
        secondary: {
          50: '#e6f7ef',
          100: '#ccf0df',
          200: '#99e1bf',
          300: '#66d29f',
          400: '#33c37f',
          500: '#00b45f',
          600: '#00904c',
          700: '#006c39',
          800: '#004826',
          900: '#002413',
        },
      },
      fontFamily: {
        sans: ['Inter', 'system-ui', 'sans-serif'],
      },
    },
  },
  plugins: [],
}
