const path = require("path");
const { CleanWebpackPlugin } = require("clean-webpack-plugin");
const defaultConfig = require("@wordpress/scripts/config/webpack.config");

const pluginsWithoutClean = (defaultConfig.plugins || []).filter(
    (plugin) => !(plugin instanceof CleanWebpackPlugin)
);

module.exports = {
    ...defaultConfig,

    entry: {
        "thoughts-from-team": "./src/blocks/thoughts-from-team.js",
    },

    output: {
        filename: "[name].js",
        path: path.resolve(__dirname, "inc/blocks/thoughts-from-team"),
    },

    plugins: pluginsWithoutClean,
};
