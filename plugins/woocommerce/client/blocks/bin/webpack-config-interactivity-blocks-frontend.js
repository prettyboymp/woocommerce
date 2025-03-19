/**
 * External dependencies
 */
const path = require( 'path' );
const fs = require( 'fs' );
const DependencyExtractionWebpackPlugin = require( '@wordpress/dependency-extraction-webpack-plugin' );
const [
	,
	moduleConfig,
] = require( '@wordpress/scripts/config/webpack.config' );

/**
 * Internal dependencies
 */
const { sharedOptimizationConfig } = require( './webpack-shared-config' );

// First look through all block.json files and find all the viewScriptModule entries.
// Then add them to the entries object.
// const entries = {};

// function findBlockJsonFilesSync( dir ) {
// 	let results = [];
// 	const ents = fs.readdirSync( dir, { withFileTypes: true } );

// 	for ( const entry of ents ) {
// 		const fullPath = path.join( dir, entry.name );
// 		if ( entry.isDirectory() ) {
// 			results = results.concat( findBlockJsonFilesSync( fullPath ) );
// 		} else if ( entry.isFile() && entry.name === 'block.json' ) {
// 			results.push( fullPath );
// 		}
// 	}
// 	return results;
// }

// const blockJsonFiles = findBlockJsonFilesSync(
// 	path.resolve( __dirname, '../assets/js' )
// );

// for ( const blockJsonFile of blockJsonFiles ) {
// 	const blockJson = JSON.parse( fs.readFileSync( blockJsonFile, 'utf8' ) );
// 	if ( blockJson.viewScriptModule ) {
// 		if ( typeof blockJson.viewScriptModule === 'string' ) {
// 			// if it's a string, try find frontend.ts then frontend.js then fail
// 			// the path will be relative to the block.json file
// 			const tsPath = path.resolve(
// 				path.dirname( blockJsonFile ),
// 				'frontend.ts'
// 			);
// 			const jsPath = path.resolve(
// 				path.dirname( blockJsonFile ),
// 				'frontend.js'
// 			);

// 			if ( fs.existsSync( tsPath ) ) {
// 				entries[ blockJson.name ] = tsPath;
// 			} else if ( fs.existsSync( jsPath ) ) {
// 				entries[ blockJson.name ] = jsPath;
// 			} else {
// 				throw new Error(
// 					`Could not find the file specified in "viewScriptModule" for block "${ blockJson.name }". Expected to find either:\n` +
// 						`- ${ tsPath }\n` +
// 						`- ${ jsPath }\n` +
// 						`Check if the file exists or ensure the "viewScriptModule" entry in block.json is correct.`
// 				);
// 			}
// 		}

// 		// if it's an array, the first element is file:./relative/path/to/file and the second is the module ID.
// 		if ( Array.isArray( blockJson.viewScriptModule ) ) {
// 			const [ fileDescriptor ] = blockJson.viewScriptModule;
// 			const filePath = fileDescriptor.replace( 'file:', '' );
// 			entries[ blockJson.name ] = path.resolve(
// 				path.dirname( blockJsonFile ),
// 				filePath
// 			);

// 			if ( ! fs.existsSync( entries[ blockJson.name ] ) ) {
// 				throw new Error(
// 					`Could not find the file specified in "viewScriptModule" for block "${ blockJson.name }". Expected to find:\n` +
// 						`${ entries[ blockJson.name ] }\n` +
// 						`Check if the file exists or ensure the "viewScriptModule" entry in block.json is correct.`
// 				);
// 			}
// 		}
// 	}
// }

// const manualEntries = {
// 	// Blocks
// 	'woocommerce/product-gallery':
// 		'./assets/js/blocks/product-gallery/frontend.ts',
// 	'woocommerce/product-gallery-large-image':
// 		'./assets/js/blocks/product-gallery/inner-blocks/product-gallery-large-image/frontend.ts',
// 	'woocommerce/product-collection':
// 		'./assets/js/blocks/product-collection/frontend.ts',
// 	'woocommerce/product-filters':
// 		'./assets/js/blocks/product-filters/frontend.ts',
// 	'woocommerce/product-filter-active':
// 		'./assets/js/blocks/product-filters/inner-blocks/active-filters/frontend.ts',
// 	'woocommerce/product-filter-attribute':
// 		'./assets/js/blocks/product-filters/inner-blocks/attribute-filter/frontend.ts',
// 	'woocommerce/product-filter-checkbox-list':
// 		'./assets/js/blocks/product-filters/inner-blocks/checkbox-list/frontend.ts',
// 	'woocommerce/product-filter-chips':
// 		'./assets/js/blocks/product-filters/inner-blocks/chips/frontend.ts',
// 	'woocommerce/product-filter-price':
// 		'./assets/js/blocks/product-filters/inner-blocks/price-filter/frontend.ts',
// 	'woocommerce/product-filter-price-slider':
// 		'./assets/js/blocks/product-filters/inner-blocks/price-slider/frontend.ts',
// 	'woocommerce/product-filter-rating':
// 		'./assets/js/blocks/product-filters/inner-blocks/rating-filter/frontend.ts',
// 	'woocommerce/product-filter-removable-chips':
// 		'./assets/js/blocks/product-filters/inner-blocks/removable-chips/frontend.ts',
// 	'woocommerce/product-filter-status':
// 		'./assets/js/blocks/product-filters/inner-blocks/status-filter/frontend.ts',
// 	'woocommerce/accordion-group':
// 		'./assets/js/blocks/accordion/accordion-group/frontend.js',
// 	'woocommerce/add-to-cart-form':
// 		'./assets/js/blocks/product-elements/add-to-cart-form/frontend.ts',
// 	'woocommerce/add-to-cart-with-options':
// 		'./assets/js/blocks/add-to-cart-with-options/frontend.ts',
// 	'woocommerce/add-to-cart-with-options-grouped-product-selector':
// 		'./assets/js/blocks/add-to-cart-with-options/grouped-product-selector/frontend.ts',
// 	'woocommerce/add-to-cart-with-options-quantity-selector':
// 		'./assets/js/blocks/add-to-cart-with-options/quantity-selector/frontend.ts',
// 	'woocommerce/add-to-cart-with-options-variation-selector':
// 		'./assets/js/blocks/add-to-cart-with-options/variation-selector/frontend.ts',

// 	// Other
// 	'@woocommerce/stores/woocommerce/cart':
// 		'./assets/js/base/stores/woocommerce/cart.ts',
// 	'@woocommerce/stores/store-notices':
// 		'./assets/js/base/stores/store-notices.ts',
// };

module.exports = {
	...moduleConfig,
	// entry: {
	// 	...entries,
	// 	...manualEntries,
	// },
	optimization: sharedOptimizationConfig,
	name: 'interactivity-blocks-modules',
	// experiments: {
	// 	outputModule: true,
	// },
	output: {
		devtoolNamespace: 'wc',
		filename: '[name].js',
		library: {
			type: 'module',
		},
		path: path.resolve( __dirname, '../build/' ),
		asyncChunks: false,
		chunkFormat: 'module',
		environment: { module: true },
		module: true,
	},
	// resolve: {
	// 	extensions: [ '.js', '.ts', '.tsx' ],
	// },
	plugins: [
		new DependencyExtractionWebpackPlugin( {
			combineAssets: true,
			combinedOutputFile: './interactivity-blocks-frontend-assets.php',
			requestToExternalModule( request ) {
				if ( request.startsWith( '@woocommerce/stores/' ) ) {
					return `import ${ request }`;
				}
			},
		} ),
	],
	// module: {
	// 	rules: [
	// 		{
	// 			test: /\.(j|t)sx?$/,
	// 			exclude: [ /[\/\\](node_modules|build|docs|vendor)[\/\\]/ ],
	// 			use: {
	// 				loader: 'babel-loader',
	// 				options: {
	// 					presets: [
	// 						[
	// 							'@wordpress/babel-preset-default',
	// 							{
	// 								modules: false,
	// 								targets: {
	// 									browsers: [
	// 										'extends @wordpress/browserslist-config',
	// 									],
	// 								},
	// 							},
	// 						],
	// 					],
	// 					cacheDirectory: path.resolve(
	// 						__dirname,
	// 						'../../../node_modules/.cache/babel-loader'
	// 					),
	// 					cacheCompression: false,
	// 				},
	// 			},
	// 		},
	// 		{
	// 			test: /\.s[c|a]ss$/,
	// 			use: {
	// 				loader: 'ignore-loader',
	// 			},
	// 		},
	// 	],
	// },
};
