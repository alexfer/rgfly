const Encore = require('@symfony/webpack-encore');
let dotenv = require('dotenv');

const env = dotenv.config();
const theme = env.parsed.APP_THEME;

// Manually configure the runtime environment if not already configured yet by the "encore" command.
// It's useful when you use tools that rely on webpack.config.js file.
if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}
Encore
    .setOutputPath('public/build/')
    .setPublicPath('/build')
    .addEntry('app', `./assets/${theme}/app.js`)
    .enableStimulusBridge(`./assets/${theme}/controllers.json`)
    .addEntry('market-js', `./assets/${theme}/js/market.js`);
if (theme === 'bootstrap') {
    Encore
        .addEntry('layout', `./assets/${theme}/js/layout.js`)
        .addEntry('image', `./assets/${theme}/js/image.js`)
        .addEntry('responsive', `./assets/${theme}/js/responsive.js`)
        .addStyleEntry('sidebar', `./assets/${theme}/styles/sidebar.css`)
        .addStyleEntry('market', `./assets/${theme}/styles/market.css`);
}
Encore
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