/** @type {import('tailwindcss').Config} */
module.exports = {
  content: ["./inc/redux_custom_fields/**/*.php"],
  theme: {
    extend: {},
  },
  plugins: [],
  prefix: "tw-",
  important: ".tailwindcss",
  corePlugins: {
    preflight: false,
  },
};
