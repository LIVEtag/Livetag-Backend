/**
 * Browser Sync & webpack middlewares
 */
const browserSync = require('browser-sync');
const gulp = require('gulp');
const webpack = require('webpack');
const webpackDevMiddleware = require('webpack-dev-middleware');
const webpackHotMiddleware = require('webpack-hot-middleware');
const path = require('path');

const config = require('./config');
const mode = require('./lib/mode');
const webpackConfig = require('./webpack.config');

const webpackCompiler = webpack(webpackConfig);

const browserSyncConfig = {
  logPrefix: 'HDRPrint',
  port: config.browserSync.port,
  ui: {
    port: config.browserSync.port + 1,
  },
};

/**
 * Use Proxy
 * else create Server
 */
if (config.browserSync.proxy.target) {
  browserSyncConfig.proxy = {
    target: config.browserSync.proxy.target,
  };
  browserSyncConfig.files = config.browserSync.proxy.files;
} else {
  browserSyncConfig.server = {
    baseDir: config.root.dist,
  };
}

if (!mode.production) {
  browserSyncConfig.middleware = [
    webpackDevMiddleware(webpackCompiler, {
      publicPath: path.resolve(webpackConfig.output.publicPath),
      noInfo: true,
      stats: {
        colors: true,
      },
    }),
    webpackHotMiddleware(webpackCompiler),
  ];
}

gulp.task('liveReload', () => {
  browserSync.init(browserSyncConfig);
});
