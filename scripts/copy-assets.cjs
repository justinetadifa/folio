const fs = require("node:fs");
const path = require("node:path");
fs.mkdirSync("public/fonts", { recursive: true });
fs.mkdirSync("public/js", { recursive: true });
fs.copyFileSync("resources/js/app.js", "public/js/app.js");
for (const family of ["dm-sans", "lora"]) {
    for (const weight of family === "dm-sans"
        ? [400, 500, 600, 700]
        : [400, 500]) {
        const name = `${family}-latin-${weight}-normal.woff2`;
        fs.copyFileSync(
            path.join("node_modules", "@fontsource", family, "files", name),
            path.join("public/fonts", name),
        );
    }
    fs.copyFileSync(
        path.join("node_modules", "@fontsource", family, "LICENSE"),
        `public/fonts/${family}-LICENSE.txt`,
    );
}
console.log("Local JavaScript and licensed fonts copied.");
