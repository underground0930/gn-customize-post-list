const path = require("path");
const { jsFilePath } = require("./package.json");

module.exports = function (mode) {
  return {
    mode,
    entry: {
      app: "./src/script.js",
    },
    module: {
      rules: [
        {
          test: /\.jsx|\.js$/,
          exclude: /node_modules/,
          loader: "babel-loader",
        },
        {
          test: /\.scss$/,
          use: [
            "style-loader",
            {
              loader: "css-loader",
              options: {
                importLoaders: 2,
              },
            },
            {
              loader: "sass-loader",
              options: {
                sourceMap: mode === "development",
              },
            },
          ],
        },
      ],
    },
    resolve: {
      extensions: [".js", ".jsx"],
    },
    output: {
      filename: "[name].bundle.js",
      path: path.resolve(__dirname, jsFilePath),
      clean: true,
    },
  };
};
