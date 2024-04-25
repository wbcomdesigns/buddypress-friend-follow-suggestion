const defaultConfig = require('@wordpress/scripts/config/webpack.config');
const path = require('path');

module.exports = {
    ...defaultConfig,
    entry: {
        'swiper': './public/js/src/index.js', // Adjust path to match the actual location of index.js
    },
    output: {
        path: path.resolve(__dirname, 'public/js/build'), // Specify the output directory path
        filename: '[name].js', // Define the output filename pattern
    },
    mode: process.env.NODE_ENV || 'development', // Set webpack mode based on NODE_ENV or default to 'development'
    optimization: {
        minimize: process.env.NODE_ENV === 'production', // Minimize JavaScript output in production mode
    },
    devtool: process.env.NODE_ENV === 'production' ? 'source-map' : 'eval-source-map', // Generate source maps for better debugging (optional)
};
