<?php
/**
 * Customize Login Screen
 *
 * @package KingdomOne
 */

namespace KingdomOne;

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
		wp_enqueue_style(
			'pro-child',
			get_theme_file_uri() . '/css/dist/login.min.css',
			array(),
			null, // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion
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
