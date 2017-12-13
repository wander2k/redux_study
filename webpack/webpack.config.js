const path = require('path');

module.exports = {
  entry: {
<<<<<<< HEAD
    rssexportermui : './src/rss_exporter_mui/index.js'
=======
    redux_study : './src/redux_study/index.js'
>>>>>>> e4067aaeac7444ea3a9e074a543f817e00723b38
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