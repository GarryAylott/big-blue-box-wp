// rollup.config.js
import resolve from "rollup-plugin-node-resolve";
import { terser } from "rollup-plugin-terser";

export default {
    input: "src/scripts/bbb-scripts.js", // your source JS
    output: {
        file: "scripts/bbb-scripts.min.js", // your theme output
        format: "esm",
        sourcemap: true,
    },
    plugins: [resolve(), terser()],
};
