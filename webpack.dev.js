const { merge } = require("webpack-merge");
const common = require("./webpack.common");
const { jsFilePath } = require("./package.json");

module.exports = merge(common("development"), {
  devtool: "inline-source-map",
  watch: true,
  devServer: {
    static: jsFilePath,
  },
});
