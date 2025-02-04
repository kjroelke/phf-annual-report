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
		$this->disable_discussion();
		add_action( 'wp_enqueue_scripts', array( $this->asset_handler, 'enqueue_assets' ) );
		add_action( 'wp_enqueue_scripts', array( $this->asset_handler, 'dequeue_scripts' ), 40 );
		add_action( 'init', array( $this->block_handler, 'register_patterns_category' ) );
		add_action( 'init', array( $this, 'alter_post_types' ) );
		add_action( 'after_setup_theme', array( $this, 'theme_setup' ) );
		add_filter( 'category_link', array( $this, 'remove_category_prefix' ), 10, 1 );
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
			'csv-handler'             => null,
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

	/** Remove post type support from posts types. */
	public function alter_post_types() {
		$post_types = array(
			'post',
			'page',
		);
		foreach ( $post_types as $post_type ) {
			$this->disable_post_type_support( $post_type );
		}
	}

	/**
	 * Disable post-type-supports from posts
	 *
	 * @param string $post_type the post type to remove supports from.
	 */
	private function disable_post_type_support( string $post_type ) {
		$supports = array(
			'comments',
			'trackbacks',
			'author',
		);
		foreach ( $supports as $support ) {
			if ( post_type_supports( $post_type, $support ) ) {
				remove_post_type_support( $post_type, $support );
			}
		}
		$this->remove_editor_support();
	}

	/**
	 * Remove Editor Support for Donors children
	 */
	private function remove_editor_support() {
		if ( is_admin() ) {
			$post_id = isset( $_GET['post'] ) ? $_GET['post'] : null; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			if ( $post_id ) {
				$post = get_post( $post_id );
				if ( $post && $post->post_parent ) {
					$parent = get_post( $post->post_parent );
					if ( $parent && 'donors' === $parent->post_name ) {
						remove_post_type_support( 'page', 'editor' );
					}
				}
			}
		}
	}

	/** Remove comments, pings and trackbacks support from posts types. */
	private function disable_discussion() {
		// Close comments on the front-end.
		add_filter( 'comments_open', '__return_false', 20, 2 );
		add_filter( 'pings_open', '__return_false', 20, 2 );

		// Hide existing comments.
		add_filter( 'comments_array', '__return_empty_array', 10, 2 );

		// Remove comments page in menu.
		add_action(
			'admin_menu',
			function () {
				remove_menu_page( 'edit-comments.php' );
			}
		);

		// Remove comments links from admin bar.
		add_action(
			'init',
			function () {
				if ( is_admin_bar_showing() ) {
					remove_action( 'admin_bar_menu', 'wp_admin_bar_comments_menu', 60 );
				}
			}
		);
	}

	/**
	 * Remove the '/category/' prefix from category archive permalinks.
	 *
	 * @param string $category_link The category archive permalink.
	 * @return string The modified category archive permalink.
	 */
	public function remove_category_prefix( $category_link ) {
		$category_base = get_option( 'category_base' );
		if ( $category_base ) {
			$category_link = str_replace( '/' . $category_base . '/', '/', $category_link );
		} else {
			$category_link = str_replace( '/category/', '/', $category_link );
		}
		return $category_link;
	}
}