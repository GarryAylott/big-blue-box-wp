const path = require("path");
const { CleanWebpackPlugin } = require("clean-webpack-plugin");
const defaultConfig = require("@wordpress/scripts/config/webpack.config");

const pluginsWithoutClean = (defaultConfig.plugins || []).filter(
    (plugin) => !(plugin instanceof CleanWebpackPlugin)
);

const blocks = ["thoughts-from-team", "info-block"];

module.exports = blocks.map((block) => ({
    ...defaultConfig,

    entry: {
        [block]: `./src/blocks/${block}.js`,
    },

    output: {
        filename: "[name].js",
        path: path.resolve(__dirname, `inc/blocks/${block}`),
    },

    plugins: pluginsWithoutClean,
}));
