const path = require('path');

module.exports = {  
  entry: {
    redux_study : './src/redux_study/index.js'
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
  },
  watchOptions: {
    poll: true
  }
};