const Encore = require('@symfony/webpack-encore');

Encore
    .setOutputPath('public/')
    .setPublicPath('/bundles/markocupicsaceventfeedback')
    .setManifestKeyPrefix('')

    .copyFiles({
      from: './assets/css',
      to: 'css/[path][name].[hash:8].[ext]',
    })
    .copyFiles({
      from: './assets/images',
      to: 'images/[path][name].[ext]',
    })

    // Typescripts
    .enableTypeScriptLoader()

    .disableSingleRuntimeChunk()
    .cleanupOutputBeforeBuild()
    .enableSourceMaps()
    .enableVersioning()

    // enables @babel/preset-env polyfills
    .configureBabelPresetEnv((config) => {
      config.useBuiltIns = 'usage';
      config.corejs = 3;
    })

    .enablePostCssLoader()
;

module.exports = Encore.getWebpackConfig();
