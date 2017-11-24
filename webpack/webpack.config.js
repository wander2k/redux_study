const path = require('path');

module.exports = {
  entry: {
    bundle: './src/app.js',
    reduxsample: './src/redux-sample.js',
    todos : './src/reduxTodos/index.js',
    rssexportermui : './src/rss_exporter_mui/index.js'
  },
  output: {
    path: path.join(__dirname, 'dist'),
    filename: '[name].js'
  },
  module: {
    loaders: [
      {
        loader: 'babel-loader',
        exclude: /node_modules/,
        test: /\.js[x]?$/,
        query: {
          cacheDirectory: true,
          presets: ['react', 'es2015', 'stage-3']
        }
      }
    ]
  }
};