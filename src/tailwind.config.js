/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
  ],
  theme: {
    extend: {
      colors: {
        'pos-primary': '#6366f1', // Indigo modern
        'pos-dark': '#1e293b',
      }
    },
  },
  plugins: [],
}