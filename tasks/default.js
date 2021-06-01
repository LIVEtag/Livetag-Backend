/**
 * Default Tasks
 */
const gulp = require('gulp'),
    runSequence = require('run-sequence'),
    mode = require('./lib/mode'),
    config = require('./config'),
    assets = ['img'];

/**
 * Enable/Disable html build using config
 * Usually when we use proxy this task became unused
 */

gulp.task('default', (cb) => {
    mode.production
        ? runSequence('clean', assets, ['css', 'js'], 'size', cb)
        : runSequence(assets, 'css', 'watch', cb)
});
