/**
 * Build font assets
 */
const gulp = require('gulp'),
    path = require('path'),
    config = require('./config');

gulp.task('ie', () =>
    gulp
        .src(path.join(config.root.dev, config.js.dev, 'ie8/', '*.js'))
        .pipe(gulp.dest(path.join(config.root.dist, config.js.dist))));
