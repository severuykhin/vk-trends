const path = require('path');
const webpack = require('webpack');
const Uglify = require('uglifyjs-webpack-plugin');

let MODE = process.argv[process.argv.length - 1].substr(1) === 'prod' ? 'production' : 'development';

const config = {
    entry  : {
      // main: path.resolve(__dirname, './blocks/index.js'),
      admin : path.resolve(__dirname, './admin/index.js'),
    },
    mode   : MODE,
    devtool: MODE === 'development' ? 'inline-source-map' : false, // Инициализируем sourcemaps в зависимости от окружения
    output : {
      filename: '[name].js',
      path: path.resolve(__dirname, '../public')
    },
    module: {

      rules: [
        {
          test: /\.(js|ts)$/,
          loader: 'babel-loader',
          exclude: /node_modules/,
            query: {
              presets: ['@babel/env'],
              "plugins": [
                '@babel/plugin-proposal-object-rest-spread',
                "transform-class-properties",
                "transform-regenerator",
              ]
            }
        },
        {
          test: /\.css$/i,
          use: ['style-loader', 'css-loader'],
        }
      ]
    },
    resolve: {
      extensions: [ '.ts', '.js' ],
    },
};

module.exports = config;