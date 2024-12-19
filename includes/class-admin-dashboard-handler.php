<?php
/**
 * Class Admin_Dashboard_Handler
 * Customize the WP Admin Dashboard
 *
 * @package KingdomOne
 */

namespace KingdomOne;

/**
 * Admin Dashboard Handler
 * Customizes the widgets on the WP Admin Dashboard
 */
class Admin_Dashboard_Handler {
	/**
	 * Admin_Dashboard_Handler constructor.
	 */
	public function __construct() {
		add_action( 'wp_dashboard_setup', array( $this, 'remove_dashboard_widgets' ) );
		add_action( 'wp_dashboard_setup', array( $this, 'add_dashboard_widgets' ) );
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
			'k1_dashboard_welcome_widget', // Widget slug.
			'Welcome to ' . $the_title, // Widget title.
			array( $this, 'k1_dashboard_welcome_widget_function' ) // Function name to display the widget.
		);
	}

	/**
	 * Initialize the function to output the contents of your new dashboard widget
	 */
	public function k1_dashboard_welcome_widget_function() {
		$first_name = get_user_meta( wp_get_current_user()->ID, 'first_name', true );
		$k1_email   = 'webdev@kingdomone.co';
		echo "Hey {$first_name}! Welcome to the your site. If you have any troubles, questions, or dream features, be sure to reach out to us at <a href='mailto:{$k1_email}'>{$k1_email}</a>"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}
