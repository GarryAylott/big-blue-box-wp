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

// Sass tasks
export function stylesDev() {
    return gulp
        .src(paths.styles.src)
        .pipe(sourcemaps.init())
        .pipe(sass({ outputStyle: "expanded" }).on("error", sass.logError))
        .pipe(postcss([autoprefixer()]))
        .pipe(sourcemaps.write("."))
        .pipe(gulp.dest(paths.styles.dest))
        .pipe(bs.stream());
}

export function stylesProd() {
    return gulp
        .src(paths.styles.src)
        .pipe(sass({ outputStyle: "compressed" }).on("error", sass.logError))
        .pipe(postcss([autoprefixer(), cssnano()]))
        .pipe(gulp.dest(paths.styles.dest));
}

// Scripts task
export async function scriptsDev() {
    const bundle = await rollup({
        input: "src/scripts/bbb-scripts.js",
        plugins: [resolve()],
    });

    const { output } = await bundle.generate({
        format: "esm",
        sourcemap: true,
    });

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

export async function scriptsProd() {
    const bundle = await rollup({
        input: "src/scripts/bbb-scripts.js",
        plugins: [resolve(), terser()],
    });

    const { output } = await bundle.generate({
        format: "esm",
        sourcemap: false,
    });

    const jsFile = path.resolve("scripts/bbb-scripts.min.js");

    for (const chunkOrAsset of output) {
        if (chunkOrAsset.type === "chunk") {
            fs.writeFileSync(jsFile, chunkOrAsset.code);
        }
    }
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
    gulp.watch(paths.styles.src, stylesDev);
    gulp.watch(paths.scripts.src, scriptsDev);
    gulp.watch(paths.php).on("change", bs.reload);
}

// Default task
export const dev = gulp.series(gulp.parallel(stylesDev, scriptsDev), serve, watchFiles);

// Production build (no server/watch)
export const build = gulp.series(gulp.parallel(stylesProd, scriptsProd));

export default dev;
