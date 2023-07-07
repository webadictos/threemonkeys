const path = require('path'),
  webpack = require('webpack');
const TerserPlugin = require('terser-webpack-plugin');
const themeFolder = path.basename(__dirname);

console.log(`\nCompilando el tema ubicado en ${themeFolder}\n`);

module.exports = {
  mode: 'production',
  context: path.resolve(__dirname, 'assets'),
  entry: {
    main: ['./main.js'],
    home: ['./home.js'],
  },
  output: {
    path: path.resolve(__dirname, 'assets/dist'),
    filename: '[name].bundle.js',
    publicPath: `/wp-content/themes/${themeFolder}/assets/dist/`,
    chunkFilename: '[name].bundle.[chunkhash].js',
  },
  // Uncomment if jQuery support is needed
  externals: {
    jquery: 'jQuery',
  },
  plugins: [
    new webpack.ProvidePlugin({
      $: 'jquery',
      jQuery: 'jquery',
      'window.jQuery': 'jquery',
    }),
  ],
  optimization: {
    minimize: true,
    chunkIds: 'named',
    moduleIds: 'named',
    minimizer: [
      new TerserPlugin({
        minify: TerserPlugin.uglifyJsMinify,
        // `terserOptions` options will be passed to `uglify-js`
        // Link to options - https://github.com/mishoo/UglifyJS#minify-options
        terserOptions: {},
      }),
    ],
  },
  devtool: false,
  watch: true,
};
