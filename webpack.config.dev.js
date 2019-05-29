const path = require('path');

const packageJson = require('./package');
const src = path.resolve(__dirname, packageJson.jsFilePath);

module.exports = {
  mode: 'development',
  entry: src + '/App.jsx',
  devtool: 'inline-source-map',
  watch: true,
  output: {
    path: src,
    filename: 'scripts.js'
  },
  module: {
    rules: [
      {
        test: /\.jsx$/,
        exclude: /node_modules/,
        loader: 'babel-loader'
      },
      {
        test: /\.css$/,
        use: ['style-loader', 'css-loader']
      }
    ]
  },
  resolve: {
    extensions: ['.js', '.jsx']
  },
  plugins: []
};
