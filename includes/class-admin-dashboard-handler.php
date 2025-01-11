<?php
/**
 * Class Admin_Dashboard_Handler
 * Customize the WP Admin Dashboard
 *
 * @package KJR_Dev
 */

namespace KJR_Dev;

/**
 * Admin Dashboard Handler
 * Customizes the widgets on the WP Admin Dashboard
 */
class Admin_Dashboard_Handler {
	/**
	 * Admin_Dashboard_Handler constructor.
	 */
	public function __construct() {
		// add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_css' ) );

		add_action( 'admin_init', array( $this, 'additional_admin_color_schemes' ) );
		add_action( 'wp_dashboard_setup', array( $this, 'remove_dashboard_widgets' ) );
		add_action( 'wp_dashboard_setup', array( $this, 'add_dashboard_widgets' ) );
	}

	public function additional_admin_color_schemes() {
		$theme_dir = get_stylesheet_directory_uri();

		wp_admin_css_color(
			'kjr-dark',
			__( 'Dark', 'kjr-dev' ),
			$theme_dir . '/build/admin/dashboard.css',
			array( '#111', '#1d2327', '#2271b1', '#4d8fbb' )
		);
	}

	public function enqueue_css() {
		$dashboard_assets = require_once get_theme_file_path( '/build/admin/dashboard.asset.php' );
		wp_enqueue_style(
			'kjr-dark-wp-dashboard',
			get_theme_file_uri() . '/build/admin/dashboard.css',
			$dashboard_assets['dependencies'],
			$dashboard_assets['version'],
		);
	}

	/**
	 * Remove the dashboard widgets.
	 */
	public function remove_dashboard_widgets() {
		$meta_boxes = array(
			'side'   => array(
				'dashboard_quick_press',
				'dashboard_primary',
			),
			'normal' => array( 'dashboard_activity' ),
		);
		foreach ( $meta_boxes as $location => $meta_box ) {
			foreach ( $meta_box as $box ) {
				remove_meta_box( $box, 'dashboard', $location );
			}
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			$meta_boxes = array(
				'normal' => array(
					'dashboard_latest_comments',
					'dashboard_incoming_links',
					'dashboard_plugins',
					'dashboard_right_now',
				),
			);

			foreach ( $meta_boxes as $location => $meta_box ) {
				foreach ( $meta_box as $box ) {
					remove_meta_box( $box, 'dashboard', $location );
				}
			}
		}
	}

	/**
	 * Add a new widget to the dashboard using a custom function.
	 */
	public function add_dashboard_widgets() {
		$the_title = get_bloginfo( 'name' );
		wp_add_dashboard_widget(
			'kjr_dashboard_welcome_widget', // Widget slug.
			'Welcome to ' . $the_title, // Widget title.
			array( $this, 'dashboard_welcome_widget_function' ) // Function name to display the widget.
		);
	}

	/**
	 * Initialize the function to output the contents of your new dashboard widget
	 */
	public function dashboard_welcome_widget_function() {
		$first_name = get_user_meta( wp_get_current_user()->ID, 'first_name', true );
		if ( empty( $first_name ) ) {
			$first_name = 'there';
		}
		$kj_email = 'kj.roelke@gmail.com';
		echo "Hey {$first_name}! Welcome to the your site. If you have any troubles, questions, or dream features, be sure to reach out at <a href='mailto:{$kj_email}'>{$kj_email}</a>"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}
