// Fetch required plugins
const gulp = require('gulp');
const { src, dest, watch, series, parallel } = require('gulp');
const sourcemaps = require('gulp-sourcemaps');
const sass = require('gulp-sass')(require('sass'));
const rename = require('gulp-rename');
const terser = require('gulp-terser');
const postcss = require('gulp-postcss');
const autoprefixer = require('autoprefixer');
const cssnano = require('cssnano');
const browserSync = require('browser-sync').create();

// All paths
const paths = {
  styles: {
    src: ['src/scss/**/*.scss'],
    dest: './',
  },
  scripts: {
    src: ['src/js/**/*.js'],
    dest: './',
  }
};

// Compile styles
gulp.task('styles', function() {
  return src(paths.styles.src)
    .pipe(sourcemaps.init())
    .pipe(sass().on('error', sass.logError))
    .pipe(postcss([autoprefixer(), cssnano()]))
    .pipe(sourcemaps.write('.'))
    .pipe(dest(paths.styles.dest))
    .pipe(browserSync.reload({
        stream: true
    }));
});

// Minify scripts
gulp.task('scripts', function() {
  return src(paths.scripts.src)
    .pipe(sourcemaps.init())
    .pipe(terser().on('error', (error) => console.log(error)))
    .pipe(rename({ suffix: '.min' }))
    .pipe(sourcemaps.write('.'))
    .pipe(dest(paths.scripts.dest))
    .pipe(browserSync.reload({
        stream: true
    }));
});

gulp.task('watch', function() {   
      browserSync.init({
         proxy: "big-blue-box.local"
});
gulp.watch('src/scss/**/*.scss', gulp.series('styles'));
gulp.watch('src/js/*.js', gulp.series('scripts'));
gulp.watch('**/*.php').on('change', browserSync.reload); });
gulp.task('default', gulp.parallel('styles', 'scripts', 'watch'));