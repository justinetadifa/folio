module.exports = {
    content: ["./resources/views/**/*.blade.php", "./resources/js/**/*.js"],
    theme: {
        extend: {
            colors: {
                ink: {
                    DEFAULT: "#121915",
                    deep: "#0d1310",
                    surface: "#18231d",
                    elevated: "#223129",
                },
                forest: {
                    DEFAULT: "#1e372b",
                    dark: "#14261e",
                    light: "#2b4d3d",
                },
                moss: {
                    DEFAULT: "#3e5e4b",
                    light: "#507560",
                },
                sage: {
                    DEFAULT: "#6b8b78",
                    light: "#8ea999",
                    subtle: "#e3ebe5",
                },
                paper: {
                    DEFAULT: "#f7f5ee",
                    warm: "#fdfbf7",
                    dark: "#ede8db",
                },
                parchment: "#f4efe2",
                cream: "#fffdf9",
                line: {
                    DEFAULT: "#e4dfd2",
                    dark: "#26362d",
                },
                brass: {
                    DEFAULT: "#b88e42",
                    light: "#d8ad5a",
                    dark: "#8f6c2d",
                    subtle: "#f8f0dc",
                },
                muted: {
                    DEFAULT: "#6f7d75",
                    light: "#9aa79f",
                    dark: "#455149",
                },
                brand: "#204637",
            },
            fontFamily: {
                sans: ["DM Sans", "sans-serif"],
                serif: ["Lora", "Georgia", "serif"],
            },
        },
    },
    plugins: [],
};
