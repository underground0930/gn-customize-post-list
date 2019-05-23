import path from 'path';

const src = path.resolve(
  __dirname,
  'app/public/wp-content/plugins/gn-customize-post-list/admin/js/'
);

export default {
  mode: 'development',
  entry: src + '/scripts.jsx',
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
