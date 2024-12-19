const defaultConfig = require( '@wordpress/scripts/config/webpack.config.js' );

module.exports = {
	...defaultConfig,
	...{
		entry: {
			global: `./src/index.ts`,
			'utilities/bs-utilities': `./src/styles/utilities/bootstrap-utilities.scss`,
		},
	},
};
