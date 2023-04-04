const colors = require("tailwindcss/colors");

module.exports = {
  content: ["./resources/**/*.blade.php", "./vendor/filament/**/*.blade.php"],
  darkMode: "class",
  theme: {
    extend: {
      colors: {
        danger: colors.pink,
        primary: colors.sky,
        success: colors.teal,
        warning: colors.yellow
      }
    }
  },
  plugins: [require("@tailwindcss/forms"), require("@tailwindcss/typography")]
};
