const path = require("path");
const webpack_configurator = require("../../../../tools/utils/scripts/webpack-configurator.js");

const assets_dir_path = path.resolve(
    __dirname,
    "../../../../src/www/assets/pull-requests/scripts/"
);
const manifest_plugin = webpack_configurator.getManifestPlugin();

const webpack_config_for_create_button = {
    entry: {
        "create-pullrequest-button": "./create-pullrequest-button/src/index.js"
    },
    externals: {
        tlp: "tlp"
    },
    resolve: {
        alias: webpack_configurator.extendAliases(webpack_configurator.tlp_mocks_alias)
    },
    context: path.resolve(__dirname),
    output: webpack_configurator.configureOutput(assets_dir_path),
    module: {
        rules: [
            webpack_configurator.configureBabelRule(webpack_configurator.babel_options_karma),
            webpack_configurator.rule_easygettext_loader,
            webpack_configurator.rule_vue_loader
        ]
    },
    plugins: [manifest_plugin, webpack_configurator.getVueLoaderPlugin()],
    resolveLoader: {
        alias: webpack_configurator.extendAliases(webpack_configurator.easygettext_loader_alias)
    }
};

const webpack_config = {
    entry: {
        "tuleap-pullrequest": "./src/app/app.js",
        "move-button-back": "./move-button-back.js"
    },
    context: path.resolve(__dirname),
    output: webpack_configurator.configureOutput(assets_dir_path),
    externals: {
        jquery: "jQuery",
        tlp: "tlp"
    },
    resolve: {
        alias: webpack_configurator.extendAliases(
            {
                "tuleap-pullrequest-module": path.resolve(__dirname, "./src/app/app.js"),
                angular$: path.resolve(__dirname, "node_modules/angular")
            },
            webpack_configurator.tlp_mocks_alias,
            webpack_configurator.tuleap_core_alias,
            webpack_configurator.angular_tlp_alias
        )
    },
    module: {
        rules: [
            webpack_configurator.configureBabelRule(webpack_configurator.babel_options_karma),
            webpack_configurator.rule_ng_cache_loader,
            webpack_configurator.rule_angular_gettext_loader
        ]
    },
    plugins: [manifest_plugin, webpack_configurator.getMomentLocalePlugin()]
};

if (process.env.NODE_ENV === "watch") {
    webpack_config.plugins.push(webpack_configurator.getAngularGettextPlugin());
}

module.exports = [webpack_config_for_create_button, webpack_config];
