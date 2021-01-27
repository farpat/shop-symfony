const Encore = require('@symfony/webpack-encore')

// Manually configure the runtime environment if not already configured yet by the "encore" command.
// It's useful when you use tools that rely on webpack.config.js file.
if (!Encore.isRuntimeEnvironmentConfigured()) {
  Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev')
}

Encore
  // directory where compiled assets will be stored
  .setOutputPath('public/assets/')
  // public path used by the web server to access the output path
  .setPublicPath('/assets')

  .addEntry('app', './assets/js/app.js')

  // When enabled, Webpack "splits" your files into smaller pieces for greater optimization.
  .splitEntryChunks()

  // will require an extra script tag for runtime.js
  // but, you probably want this, unless you're building a single-page app
  .enableSingleRuntimeChunk()
  .cleanupOutputBeforeBuild()
  .enableBuildNotifications()
  .enableSourceMaps(!Encore.isProduction())
  // enables hashed filenames (e.g. app.abc123.css)
  .enableVersioning(Encore.isProduction())

  .configureBabel((config) => {
    config.plugins.push('@babel/plugin-proposal-class-properties')
  })

  // enables @babel/preset-env polyfills
  .configureBabelPresetEnv((config) => {
    config.useBuiltIns = 'usage'
    config.corejs = 3
  })

  // enables Sass/SCSS support
  .enableSassLoader()

  // uncomment if you use React
  .enableReactPreset()

if (Encore.isDevServer()) {
  const Chokidar = require('chokidar')
  const ReactRefreshWebpackPlugin = require('@pmmmwh/react-refresh-webpack-plugin')

  Encore.disableCssExtraction()

  Encore.configureDevServerOptions(options => {
    options.before = (app, server) => {
      Chokidar.watch('templates/**/*.twig').on('change', () => server.sockWrite(server.sockets, 'content-changed'))
    }
  })

  Encore.addPlugin(new ReactRefreshWebpackPlugin())

  Encore.configureBabel(config => {
    config.plugins.push(require('react-refresh/babel'))
  })
} else if (Encore.isProduction()) {
  const BundleAnalyzerPlugin = require('webpack-bundle-analyzer').BundleAnalyzerPlugin

  Encore.addPlugin(new BundleAnalyzerPlugin({
    analyzerMode: 'static',
    openAnalyzer: false
  }))
}

module.exports = Encore.getWebpackConfig()
