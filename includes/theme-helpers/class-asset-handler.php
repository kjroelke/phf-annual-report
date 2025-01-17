<?php
/**
 * Asset Handler
 * Enqueues styles and scripts needed for the theme
 *
 * @package KJR_Dev
 */

namespace KJR_Dev;

/**
 * Class Asset_Handler
 */
class Asset_Handler {
	/**
	 * Include CSS utility classes as dependency. Default false.
	 *
	 * @var bool $include_utilities
	 */
	private bool $include_utilities;

	/**
	 * The main asset file ID
	 *
	 * @var string $main_asset_id
	 */
	private string $main_asset_id;

	/**
	 * Asset_Handler constructor.
	 *
	 * @param bool $with_utilities whether to include utility classes CSS stylesheet as a dependency or not. Default false.
	 */
	public function __construct( bool $with_utilities = false ) {
		$this->include_utilities = $with_utilities;
		$this->main_asset_id     = 'kjr-global';
	}

	/**
	 * Enqueue the styles and scripts
	 */
	public function enqueue_assets() {
		if ( $this->include_utilities ) {
			$this->enqueue_utilities();
		}
		$this->enqueue_styles();
	}

	/**
	 * Enqueue the utility classes CSS stylesheet
	 */
	private function enqueue_utilities() {
		$utilities = require_once get_stylesheet_directory() . '/build/utilities/bs-utilities.asset.php';
		wp_enqueue_style(
			'bs-utilities',
			get_stylesheet_directory_uri() . '/build/utilities/bs-utilities.css',
			$utilities['dependencies'],
			$utilities['version']
		);
		$donor_utils = require_once get_stylesheet_directory() . '/build/utilities/donor-utilities.asset.php';
		wp_register_style(
			'kjr-donor-utilities',
			get_stylesheet_directory_uri() . '/build/utilities/donor-utilities.css',
			array( ...$donor_utils['dependencies'], 'bs-utilities' ),
			$donor_utils['version']
		);
	}

	/**
	 * Enqueue the theme's styles and scripts
	 */
	private function enqueue_styles() {
		$kjr_global = require_once get_stylesheet_directory() . '/build/global.asset.php';

		wp_enqueue_style(
			$this->main_asset_id,
			get_stylesheet_directory_uri() . '/build/global.css',
			$this->get_dependencies( $kjr_global, false, true ),
			$kjr_global['version']
		);
		wp_enqueue_script(
			$this->main_asset_id,
			get_stylesheet_directory_uri() . '/build/global.js',
			$this->get_dependencies( $kjr_global, true, true ),
			$kjr_global['version'],
			array( 'strategy' => 'defer' )
		);
		wp_localize_script(
			$this->main_asset_id,
			'kjrSiteData',
			array(
				'root_url' => get_site_url(),
			)
		);
	}

	/**
	 * Get the dependencies from the asset file and include any other dependencies.
	 *
	 * @param array $deps_file The asset file.
	 * @param bool  $is_script Whether the file is a script or not. Default false.
	 * @param bool  $is_main_file Whether the file is the main file (`k1-global`) or not. Default false. Prevents circular dependencies.
	 *
	 * @return array
	 */
	private function get_dependencies( array $deps_file, bool $is_script = false, bool $is_main_file = false ): array {
		$dependencies = $deps_file['dependencies'];
		if ( $is_script ) {
			return $dependencies;
		}
		if ( $this->include_utilities ) {
			$dependencies = array_unique( array( ...$deps_file['dependencies'], 'bs-utilities' ) );
		}
		$dependencies = array_unique( array( ...$dependencies, $is_main_file ? 'cs' : $this->main_asset_id ) );
		return $dependencies;
	}

	/**
	 * Dequeue the parent theme's styles
	 */
	public function dequeue_scripts() {
		wp_dequeue_style( 'x-child' );
	}
}