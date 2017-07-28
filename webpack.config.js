const path = require('path');
const webpack = require('webpack');
const ExtractTextPlugin = require('extract-text-webpack-plugin');
module.exports = {
    context: path.resolve(__dirname, './assets/js'),
    entry: {
        admin: './admin/index.js',
        frontend: './frontend/index.js'
    },
    output: {
        path: path.resolve(__dirname, './dist'),
        filename: '[name].bundle.js',
    },
    module: {
        rules: [
            {
                test: /\.css$/,
                exclude: /node_modules/,
                loader: ExtractTextPlugin.extract({
                    loader: 'css-loader?importLoaders=1!postcss-loader'
                }),
            },
            {
                test: /\.js$/,
                exclude: /(node_modules|bower_components)/,
                loader: 'babel-loader',
                query: {
                    presets: ['es2015', 'es2016', 'react']
                }
            },
            {
                test: /\.svg(\?v=\d+\.\d+\.\d+)?$/,
                exclude: /(node_modules|bower_components)/,
                loader: 'url-loader?limit=10000&mimetype=image/svg+xml'
            },
            {
                test: /\.(eot|svg|ttf|WOFF|woff2)$/,
                loader: 'file-loader?name=fonts/[name].[ext]'
            }
        ]
    },
    devtool: 'source-map',
    plugins: [
        new ExtractTextPlugin({ filename: '[name].bundle.css', disable: false, allChunks: true })
    ]
};
