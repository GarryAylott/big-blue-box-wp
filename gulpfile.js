import gulp from "gulp";
import * as dartSass from "sass";
import gulpSass from "gulp-sass";
import sourcemaps from "gulp-sourcemaps";
import postcss from "gulp-postcss";
import autoprefixer from "autoprefixer";
import cssnano from "cssnano";
import browserSync from "browser-sync";
import terser from "@rollup/plugin-terser";
import { rollup } from "rollup";
import resolve from "@rollup/plugin-node-resolve";
import fs from "fs";
import path from "path";

const sass = gulpSass(dartSass);
const bs = browserSync.create();

// File paths
const paths = {
    styles: {
        src: "src/scss/**/*.scss",
        dest: "./",
    },
    scripts: {
        src: "src/scripts/**/*.js",
        dest: "./scripts/",
    },
    php: "**/*.php",
};

// Sass task
export function styles() {
    return gulp
        .src(paths.styles.src)
        .pipe(sourcemaps.init())
        .pipe(sass({ outputStyle: "compressed" }).on("error", sass.logError))
        .pipe(postcss([autoprefixer(), cssnano()]))
        .pipe(sourcemaps.write("."))
        .pipe(gulp.dest(paths.styles.dest))
        .pipe(bs.stream());
}

// Scripts task
export async function scripts() {
    const bundle = await rollup({
        input: "src/scripts/bbb-scripts.js",
        plugins: [resolve(), terser()],
    });

    const { output } = await bundle.generate({
        format: "esm",
        sourcemap: true,
    });

    // Write JS output manually
    const jsFile = path.resolve("scripts/bbb-scripts.min.js");
    const mapFile = `${jsFile}.map`;

    for (const chunkOrAsset of output) {
        if (chunkOrAsset.type === "chunk") {
            fs.writeFileSync(jsFile, chunkOrAsset.code);
            if (chunkOrAsset.map) {
                fs.writeFileSync(mapFile, chunkOrAsset.map.toString());
            }
        }
    }

    bs.reload();
}

// BrowserSync
export function serve(done) {
    bs.init({
        proxy: "https://big-blue-box.local",
        host: "big-blue-box.local",
        https: {
            key: `${process.env.HOME}/Library/Application Support/Local/run/router/nginx/certs/big-blue-box.local.key`,
            cert: `${process.env.HOME}/Library/Application Support/Local/run/router/nginx/certs/big-blue-box.local.crt`,
        },
        open: "external",
        notify: false,
        ghostMode: false,
    });
    done();
}

// Watch files
export function watchFiles() {
    gulp.watch(paths.styles.src, styles);
    gulp.watch(paths.scripts.src, scripts);
    gulp.watch(paths.php).on("change", bs.reload);
}

// Default task
export const dev = gulp.series(
    gulp.parallel(styles, scripts),
    serve,
    watchFiles
);
export default dev;
