<?php
/**
 * Customize Login Screen
 *
 * @package KJR_Dev
 */

namespace KJR_Dev;

/**
 * Login Handler
 * Customizes the login screen
 */
class Login_Handler {
	/**
	 * Login_Handler constructor.
	 */
	public function __construct() {
		add_action( 'login_enqueue_scripts', array( $this, 'enqueue_css' ) );
		add_filter( 'login_headertitle', array( $this, 'update_title' ) );
		add_filter( 'login_headerurl', array( $this, 'update_url' ) );
	}

	/**
	 * Enqueue the custom CSS
	 */
	public function enqueue_css() {
		$login_assets = require_once get_theme_file_path( '/build/admin/login.asset.php' );
		wp_enqueue_style(
			'kjr-dark-wp-login',
			get_theme_file_uri() . '/build/admin/login.css',
			$login_assets['dependencies'],
			$login_assets['version'],
		);
	}

	/**
	 * Update the title of the login screen
	 */
	public function update_title() {
		return get_bloginfo( 'name' );
	}

	/**
	 * Update the URL of the login screen
	 */
	public function update_url() {
		return esc_url( site_url( '/' ) );
	}
}
