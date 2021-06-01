/**
 * Build CSS
 */
const gulp = require('gulp'),
    {reload} = require('browser-sync'),
    autoprefixer = require('gulp-autoprefixer'),
    changed = require('gulp-changed'),
    glob = require('glob'),
    gulpif = require('gulp-if'),
    minify = require('gulp-clean-css'),
    notify = require('gulp-notify'),
    path = require('path'),
    plumber = require('gulp-plumber'),
    scss = require('gulp-sass'),
    sourcemaps = require('gulp-sourcemaps'),
    uncss = require('gulp-uncss'),
    config = require('./config'),
    argv = require('yargs').argv,
    mode = require('./lib/mode');

// Configuration for gulp-uncss plugin.
const unCssIgnore = [
    /(#|\.)fancybox(-[a-zA-Z]+)?/,
    /tooltip/,
    '.modal',
    '.panel',
    '.active',
    '.hide',
    '.show',
    '.fade',
    '.fade.in',
    '.collapse',
    '.collapse.in',
    '.navbar-collapse',
    '.navbar-collapse.in',
    '.collapsing'
];

const supported = [
    'last 2 versions',
    'safari >= 8',
    'ie >= 10',
    'ff >= 20',
    'ios 6',
    'android 4'
];

gulp.task('css', () =>
    gulp
        .src((argv.page === undefined) ? path.join(config.root.dev, config.css.dev) : argv.page)
        .pipe(gulpif(!mode.production, sourcemaps.init()))
        .pipe(plumber({errorHandler: notify.onError('Error: <%= error.message %>')}))
        .pipe(changed(path.join(config.root.dev, 'sass/home/main.scss'), {hasChanged: changed.compareContents}))
        .pipe(scss())

        .pipe(gulpif(
            mode.production,
            minify({
                keepSpecialComments: 0,
            }),
        ))
        .pipe(gulpif(!mode.production, sourcemaps.write()))
        .pipe(gulp.dest(path.join(config.root.dist, config.css.dist)))
        .pipe(reload({stream: true})));
