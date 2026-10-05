// 2026 layout build: src-2026/ -> dist-2026/
// Kept separate from the legacy webpack.config.js (src/ -> dist/bundle.js).
const path = require('path')
const MiniCssExtractPlugin = require('mini-css-extract-plugin')
const CssMinimizerPlugin = require('css-minimizer-webpack-plugin')

module.exports = (env, argv) => {
	const isProd = argv.mode === 'production'

	return {
		name: '2026',
		entry: {
			main: ['./src-2026/js/fonts.js', './src-2026/js/main.js', './src-2026/scss/main.scss'],
		},
		output: {
			path: path.resolve(__dirname, 'dist-2026'),
			filename: '[name].js',
			chunkFilename: 'chunks/[name].[contenthash].js',
			// publicPath defaults to 'auto': lazy chunks load relative to main.js
			clean: true,
		},
		// explicit, so webpack never picks up a browserslist file for the JS target
		target: ['web', 'es2020'],
		// the theme loads jQuery globally from CDN, don't bundle a second copy
		externals: {
			jquery: 'jQuery',
		},
		module: {
			rules: [
				{
					test: /\.scss$/,
					use: [
						MiniCssExtractPlugin.loader,
						{
							loader: 'css-loader',
							// leave url() as written, relative to dist-2026/main.css
							options: { url: false, sourceMap: !isProd },
						},
						{
							loader: 'postcss-loader',
							options: {
								sourceMap: !isProd,
								postcssOptions: {
									// inline on purpose: a browserslist file would also change the legacy build target
									plugins: [['autoprefixer', { overrideBrowserslist: ['defaults'] }]],
								},
							},
						},
						{
							loader: 'sass-loader',
							options: {
								sourceMap: !isProd,
								// hide deprecation warnings from node_modules (e.g. include-media), keep ours
								sassOptions: { quietDeps: true },
							},
						},
					],
				},
				{
					// plain CSS from packages (Fontsource): url() resolved, unlike the SCSS rule
					test: /\.css$/,
					use: [MiniCssExtractPlugin.loader, 'css-loader'],
				},
				{
					test: /\.woff2?$/,
					type: 'asset/resource',
					generator: { filename: 'fonts/[name].[contenthash:8][ext]' },
				},
			],
		},
		plugins: [new MiniCssExtractPlugin({ filename: '[name].css' })],
		optimization: {
			// '...' keeps webpack's default Terser for JS
			minimizer: ['...', new CssMinimizerPlugin()],
		},
		devtool: isProd ? false : 'source-map',
		stats: 'minimal',
	}
}
