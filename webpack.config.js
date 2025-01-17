const defaultConfig = require( '@wordpress/scripts/config/webpack.config' );
const RemoveEmptyScriptsPlugin = require( 'webpack-remove-empty-scripts' );
module.exports = {
	...defaultConfig,
	...{
		entry: {
			global: `./src/index.ts`,
			'utilities/bs-utilities': `./src/styles/utilities/bootstrap-utilities.scss`,
			'utilities/donor-utilities': `./src/styles/utilities/donor-utilities.scss`,
		},
		plugins: [
			...defaultConfig.plugins,
			new RemoveEmptyScriptsPlugin( {
				stage: RemoveEmptyScriptsPlugin.STAGE_AFTER_PROCESS_PLUGINS,
			} ),
		],
	},
};
