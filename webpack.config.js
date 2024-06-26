const Encore = require('@symfony/webpack-encore');
//let dotenv = require('dotenv');

//const env = dotenv.config();
//let theme = env.parsed.APP_THEME || 'tailwind';
// ${theme}

// Manually configure the runtime environment if not already configured yet by the "encore" command.
// It's useful when you use tools that rely on webpack.config.js file.
if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}
Encore
    .setOutputPath('public/build/')
    .setPublicPath('/build')
    .addEntry('app', `./assets/tailwind/app.js`)
    .enableStimulusBridge(`./assets/tailwind/controllers.json`)
    .addEntry('coupon-js', `./assets/tailwind/js/coupon.js`)
    .addEntry('message-js', `./assets/tailwind/js/message.js`)
    .addEntry('market-js', `./assets/tailwind/js/market.js`)
    .enablePostCssLoader((options) => {
        options.postcssOptions = {
            config: './postcss.config.js',
        }
    })
    .splitEntryChunks()
    .enableSingleRuntimeChunk()
    .cleanupOutputBeforeBuild()
    .enableBuildNotifications()
    .enableSourceMaps(!Encore.isProduction())
    .enableVersioning(Encore.isProduction())
    .configureBabel((config) => {
        config.plugins.push('@babel/plugin-proposal-class-properties');
    })
    .configureBabelPresetEnv((config) => {
        config.useBuiltIns = 'usage';
        config.corejs = 3;
    })
;

module.exports = Encore.getWebpackConfig();