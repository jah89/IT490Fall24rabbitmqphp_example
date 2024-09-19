/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    './js/.js',
    '../**/*.{html, php}',
  ],
  theme: {
    extend: {},
  },
  plugins: [
    require('@tailwindcss/forms'),
    require('@tailwindcss/typography'),
  ],
}

