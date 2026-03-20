const path = require("path");
const defaultConfig = require("@wordpress/scripts/config/webpack.config");

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
}));
