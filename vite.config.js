// vite.config.js
import { defineConfig } from "vite";
import { fileURLToPath, URL } from "node:url";

export default defineConfig({
    // your source lives here
    root: "src",

    // nice, stable aliases for JS and SCSS
    resolve: {
        alias: {
            "@": fileURLToPath(new URL("./src", import.meta.url)),
            "@scss": fileURLToPath(new URL("./src/scss", import.meta.url)),
        },
    },

    // sass config so bare imports like "utilities" resolve under src/scss
    css: {
        preprocessorOptions: {
            scss: {
                includePaths: [
                    fileURLToPath(new URL("./src/scss", import.meta.url)),
                ],
            },
        },
    },

    // production build
    build: {
        outDir: "../dist", // relative to root ('src')
        emptyOutDir: true,
        manifest: true,
        rollupOptions: {
            // entries are relative to root
            input: {
                "bbb-scripts": fileURLToPath(
                    new URL("./src/scripts/bbb-scripts.js", import.meta.url)
                ),
                style: fileURLToPath(
                    new URL("./src/scss/style.scss", import.meta.url)
                ),
            },
            output: {
                entryFileNames: "scripts/[name]-[hash].js",
                chunkFileNames: "scripts/chunks/[name]-[hash].js",
                assetFileNames: "assets/[name]-[hash][extname]",
            },
        },
    },

    // dev server that proxies WP through LocalWP
    server: {
        host: "localhost",
        port: 5173,
        strictPort: true,
        proxy: {
            "/": {
                target: "https://big-blue-box.local",
                changeOrigin: true,
                secure: false, // tolerate LocalWP's self-signed cert
            },
        },
    },
});
