import gulp from "gulp";
import * as dartSass from "sass";
import gulpSass from "gulp-sass";
import sourcemaps from "gulp-sourcemaps";
import postcss from "gulp-postcss";
import autoprefixer from "autoprefixer";
import cssnano from "cssnano";
import browserSync from "browser-sync";
import header from "gulp-header";
import { rollup } from "rollup";
import resolve from "@rollup/plugin-node-resolve";
import fs from "fs";
import path from "path";
import { minify } from "terser";

const sass = gulpSass(dartSass);
const bs = browserSync.create();

const themeHeader = `/*\nTheme Name: Big Blue Box Theme\nTheme URI: https://www.bigblueboxpodcast.co.uk\nAuthor: Garry Aylott\nDescription: A custom theme for The Big Blue Box Podcast website.\nVersion: 2\nText Domain: bigbluebox\n*/\n`;

// -----------------------------------------------------------------------------
// File paths
// -----------------------------------------------------------------------------
const paths = {
    styles: {
        src: "src/scss/style.scss",
        dest: "./",
    },
    editorStyles: {
        src: "src/scss/editor.scss",
        dest: "./",
    },
    scripts: {
        src: "src/scripts/**/*.js",
        dest: "./scripts/",
    },
    php: "**/*.php",
};

// -----------------------------------------------------------------------------
// Compile main theme styles (style.css)
// -----------------------------------------------------------------------------
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

// -----------------------------------------------------------------------------
// Compile block editor styles (editor.css)
// -----------------------------------------------------------------------------
function compileEditorStyles() {
    return gulp
        .src(paths.editorStyles.src)
        .pipe(sourcemaps.init())
        .pipe(
            sass({ outputStyle: "compressed", charset: false }).on(
                "error",
                sass.logError
            )
        )
        .pipe(postcss([autoprefixer(), cssnano()]))
        .pipe(sourcemaps.write("."))
        .pipe(gulp.dest(paths.editorStyles.dest))
        .pipe(bs.stream({ match: "**/*.css" }));
}

// -----------------------------------------------------------------------------
// Bundle scripts using Rollup
// -----------------------------------------------------------------------------
async function bundleScripts({
    production = false,
    reloadBrowser = false,
} = {}) {
    const jsFile = path.resolve("scripts/bbb-scripts.min.js");
    const mapFile = `${jsFile}.map`;

    const bundle = await rollup({
        input: "src/scripts/bbb-scripts.js",
        plugins: [resolve()],
    });

    const { output } = await bundle.generate({
        file: jsFile,
        format: "esm",
        sourcemap: production ? false : true,
    });

    const chunk = output.find((item) => item.type === "chunk") || output[0];
    const originalCode = chunk?.code || "";
    const originalMap = chunk?.map || null;

    const minified = await minify(originalCode, {
        module: true,
        compress: { drop_console: production },
        mangle: true,
        format: { comments: false },
        sourceMap: production
            ? false
            : {
                  content: originalMap || undefined,
                  filename: path.basename(jsFile),
                  url: path.basename(mapFile),
              },
    });

    await fs.promises.mkdir(path.dirname(jsFile), { recursive: true });
    await fs.promises.writeFile(jsFile, minified.code || originalCode, "utf8");

    await bundle.close();

    if (!production && minified.map) {
        await fs.promises.writeFile(mapFile, minified.map, "utf8");
    } else if (production && fs.existsSync(mapFile)) {
        fs.rmSync(mapFile, { force: true });
    }

    if (reloadBrowser) {
        bs.reload();
    }
}

// -----------------------------------------------------------------------------
// Exports for different build contexts
// -----------------------------------------------------------------------------
export const stylesDev = () =>
    compileStyles({ sourcemapsEnabled: true, streamToBrowser: true });
export const stylesBuild = () => compileStyles({ sourcemapsEnabled: false });

export const editorStylesDev = compileEditorStyles;
export const editorStylesBuild = compileEditorStyles;

export const scriptsDev = () =>
    bundleScripts({ production: false, reloadBrowser: true });
export const scriptsDevConditional = async () => {
    const jsFile = path.resolve("scripts/bbb-scripts.min.js");
    if (!fs.existsSync(jsFile)) {
        return bundleScripts({ production: false, reloadBrowser: false });
    }
};
export const scriptsBuild = () =>
    bundleScripts({ production: true, reloadBrowser: false });

// -----------------------------------------------------------------------------
// BrowserSync
// -----------------------------------------------------------------------------
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

// -----------------------------------------------------------------------------
// Watch files
// -----------------------------------------------------------------------------
export function watchFiles() {
    gulp.watch("src/scss/**/*.scss", stylesDev);
    gulp.watch("src/scss/wp-blocks/**/*.scss", editorStylesDev);
    gulp.watch(paths.editorStyles.src, editorStylesDev);
    gulp.watch(paths.scripts.src, scriptsDev);
    gulp.watch(paths.php).on("change", bs.reload);
}

// -----------------------------------------------------------------------------
// Combined tasks
// -----------------------------------------------------------------------------
export const dev = gulp.series(
    gulp.parallel(stylesDev, scriptsDevConditional, editorStylesDev),
    serve,
    watchFiles
);

export const build = gulp.series(
    gulp.parallel(stylesBuild, scriptsBuild, editorStylesBuild)
);

export const prod = build;

export default dev;
