<?php
/**
 * Class Theme_Init
 *
 * @package KJR_Dev
 */

namespace KJR_Dev;

/**
 * Theme Initialization
 * Enqueues styles and scripts
 */
class Theme_Init {
	/**
	 * Asset Handler
	 *
	 * @var Asset_Handler $asset_handler
	 */
	private Asset_Handler $asset_handler;

	/**
	 * Block Handler
	 *
	 * @var Block_Handler $block_handler
	 */
	private Block_Handler $block_handler;

	/**
	 * Theme_Init constructor.
	 */
	public function __construct() {
		$this->load_required_files();
		$this->asset_handler = new Asset_Handler( true );
		$this->block_handler = new Block_Handler();
		add_action( 'wp_enqueue_scripts', array( $this->asset_handler, 'enqueue_assets' ) );
		add_action( 'wp_enqueue_scripts', array( $this->asset_handler, 'dequeue_scripts' ), 40 );
		add_action( 'init', array( $this->block_handler, 'register_patterns_category' ) );
		add_action( 'after_setup_theme', array( $this, 'theme_setup' ) );
	}

	/**
	 * Load the required files
	 */
	private function load_required_files() {
		$helpers = array( 'asset-handler', 'block-handler' );
		foreach ( $helpers as $file ) {
			require_once get_theme_file_path( "/includes/theme-helpers/class-{$file}.php" );
		}

		$files = array(
			'login-handler'           => 'Login_Handler',
			'admin-dashboard-handler' => 'Admin_Dashboard_Handler',
		);

		foreach ( $files as $file => $class ) {
			require_once get_theme_file_path( "/includes/class-{$file}.php" );
			if ( $class ) {
				$class = "KJR_Dev\\{$class}";
				new $class();
			}
		}
	}

	/**
	 * Theme Setup
	 */
	public function theme_setup() {
		remove_theme_support( 'core-block-patterns' );
	}
}
