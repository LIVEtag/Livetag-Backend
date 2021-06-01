var gulp = require('gulp'),
    include = require('gulp-include'),
    postcss = require('gulp-postcss'),
    sass = require('gulp-sass'),
    cssnano = require('gulp-cssnano'),
    clean = require('gulp-clean'),
    plumber = require('gulp-plumber'),
    fonts = require('postcss-font-magician'),
    uglify = require('gulp-uglify'),
    gutil = require('gulp-util'),
    babel = require('gulp-babel'),
    sourcemaps = require('gulp-sourcemaps');

var pathToWeb = './backend/web/';
var pathToResources = './backend/resources/';

/* Options */
var inputs = {
    sass: [
        pathToResources + 'sass/*.scss',
        pathToResources + 'sass/**/*.scss'
    ],
    js: [
        pathToResources + 'js/*.js',
        pathToResources + 'js/**/*.js'
    ]
};
var outputs = {
    css: pathToWeb + 'css',
    js: pathToWeb + 'js'
};

/* Task for clean css */
gulp.task('clean-css', function () {
    gulp
        .src(pathToWeb + 'css/', {read: false})
        .pipe(clean());
});

/* Task for CSS */
var supported = [
    'last 2 versions',
    'safari >= 8',
    'ie >= 10',
    'ff >= 20',
    'ios 6',
    'android 4'
];
gulp.task('css', function () {
    gulp
        .src(inputs.sass)
        .pipe(sourcemaps.init())
        .pipe(plumber())
        .pipe(sass.sync({
          importer: require("node-sass-tilde-importer")
        }))
        .pipe(sass({
            includePaths: require('node-normalize-scss').includePaths
        }))
        .pipe(postcss([
            fonts({
              display: 'swap'
            })
        ]))
        .pipe(sourcemaps.write())
        .pipe(cssnano({
            zindex: false,
            reduceIdents: false,
            autoprefixer: {
                browsers: supported,
                add: true,
                grid: true,
                zindex: false
            }
        }))
        .pipe(gulp.dest(outputs.css));
});

/* Task for JS */
gulp.task('js', function () {
    gulp
        .src(inputs.js)
        .pipe(sourcemaps.init())
        .pipe(include({
            includePaths: [__dirname + "/node_modules"]
        }))
        .pipe(plumber())
        .pipe(babel({
            presets: ['env']
        }))
        // .pipe(browserify())
        .pipe(uglify(undefined, {
            outSourceMap: true
        }))
        .on('error', function (err) {
            gutil.log(gutil.colors.red('[Error]'), err.toString());
        })
        .pipe(gulp.dest(outputs.js));
});

/* Task for JS */
gulp.task('js:copy', function () {
    gulp.src(pathToResources + 'js/scripts/*.js')
        .pipe(include({
            includePaths: [
                __dirname + '/node_modules/'
            ]
      }))
      .pipe(gulp.dest(outputs.js));
});

/* Task Clean (delete folder dist) */
gulp.task('clean', function () {
  gulp
    .src('dist', {read: false})
    .pipe(clean());
});

/* Watch tasks */
gulp.task('watch', function () {
  gulp.watch(inputs.sass, ['css']);
  gulp.watch(inputs.js, ['js']);
});

/* Task Build */
gulp.task('deploy', ['css', 'js']);

gulp.task('rebuild', ['clean-css', 'css', 'js']);

/* Task Default */
gulp.task('default', ['rebuild', 'watch']);
