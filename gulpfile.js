import gulp from "gulp";
import * as dartSass from "sass";
import gulpSass from "gulp-sass";
import sourcemaps from "gulp-sourcemaps";
import postcss from "gulp-postcss";
import autoprefixer from "autoprefixer";
import cssnano from "cssnano";
import browserSync from "browser-sync";
import header from "gulp-header";
import terser from "@rollup/plugin-terser";
import { rollup } from "rollup";
import resolve from "@rollup/plugin-node-resolve";
import fs from "fs";
import path from "path";
import { deleteAsync } from "del";

const sass = gulpSass(dartSass);
const bs = browserSync.create();

const themeHeader = `/*\nTheme Name: Big Blue Box Theme\nTheme URI: https://www.bigblueboxpodcast.co.uk\nAuthor: Garry Aylott\nDescription: A custom theme for The Big Blue Box Podcast website.\nVersion: 2\nText Domain: bigbluebox\n*/\n`;

const runtimeGlobs = [
    "**/*.php",
    "inc/**/*",
    "template-parts/**/*",
    "page-templates/**/*",
    "data/**/*",
    "fonts/**/*",
    "images/**/*",
    "scripts/bbb-scripts.min.js",
    "scripts/bbb-scripts.min.js.map",
    "style.css",
    "style.css.map",
    "screenshot.png",
    "!node_modules{,/**}",
    "!src{,/**}",
    "!dist{,/**}",
    "!.git{,/**}",
    "!.vscode{,/**}",
    "!gulpfile.js",
    "!package.json",
    "!package-lock.json",
    "!README.md",
    "!AGENTS.md",
];

const distDir = "dist";

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

function compileStyles({
    sourcemapsEnabled = true,
    streamToBrowser = false,
} = {}) {
    let pipeline = gulp.src(paths.styles.src);

    if (sourcemapsEnabled) {
        pipeline = pipeline.pipe(sourcemaps.init());
    }

    pipeline = pipeline
        .pipe(
            sass({ outputStyle: "compressed", charset: false }).on(
                "error",
                sass.logError
            )
        )
        .pipe(postcss([autoprefixer(), cssnano()]))
        .pipe(header(themeHeader));

    if (sourcemapsEnabled) {
        pipeline = pipeline.pipe(sourcemaps.write("."));
    }

    pipeline = pipeline.pipe(gulp.dest(paths.styles.dest));

    if (streamToBrowser) {
        pipeline = pipeline.pipe(bs.stream({ match: "**/*.css" }));
    }

    return pipeline;
}

async function bundleScripts({
    production = false,
    reloadBrowser = false,
} = {}) {
    const bundle = await rollup({
        input: "src/scripts/bbb-scripts.js",
        plugins: [
            resolve(),
            terser({
                compress: {
                    drop_console: production,
                },
                format: {
                    comments: false,
                },
            }),
        ],
    });

    const jsFile = path.resolve("scripts/bbb-scripts.min.js");
    const mapFile = `${jsFile}.map`;

    await bundle.write({
        file: jsFile,
        format: "esm",
        sourcemap: production ? false : true,
    });

    await bundle.close();

    if (production && fs.existsSync(mapFile)) {
        fs.rmSync(mapFile, { force: true });
    }

    if (reloadBrowser) {
        bs.reload();
    }
}

export const stylesDev = () =>
    compileStyles({ sourcemapsEnabled: true, streamToBrowser: true });
export const stylesBuild = () => compileStyles({ sourcemapsEnabled: false });

export const scriptsDev = () =>
    bundleScripts({ production: false, reloadBrowser: true });
export const scriptsBuild = () =>
    bundleScripts({ production: true, reloadBrowser: false });

export const cleanDist = () => deleteAsync([`${distDir}/**`, `!${distDir}`]);

export const copyRuntime = () =>
    gulp
        .src(runtimeGlobs, { base: ".", nodir: true, allowEmpty: true })
        .pipe(gulp.dest(distDir));

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
export const dev = gulp.series(
    gulp.parallel(stylesDev, scriptsDev),
    serve,
    watchFiles
);
export const build = gulp.series(gulp.parallel(stylesBuild, scriptsBuild));
export const dist = gulp.series(build, cleanDist, copyRuntime);
export const prod = dist;

export default dev;
