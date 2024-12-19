<?php
/**
 * Block Handler
 * Helper class for handling WordPress Blocks
 *
 * @package KingdomOne
 */

namespace KingdomOne;

/**
 * Class Block_Handler
 */
class Block_Handler {
	/**
	 * Block_Handler constructor.
	 */
	public function __construct() {
		add_filter(
			'block_editor_settings_all',
			array( $this, 'hide_block_locking_ui' ),
			10,
			2
		);
		add_filter( 'should_load_remote_block_patterns', '__return_false' );
	}


	/**
	 * Registers a "Kingdom One" Category to place all custom patterns inside of.
	 */
	public function register_patterns_category() {
		register_block_pattern_category(
			'kingdomone',
			array(
				'label'       => esc_html__( 'Kingdom One', 'kingdomone' ),
				'description' => esc_html__( 'Custom Patterns built by Kingdom One', 'kingdomone' ),
			)
		);
	}

	/**
	 * Hides the Block Lock settings for non-admin users on Tour post type.
	 *
	 * @param array                    $settings Default editor settings.
	 * @param \WP_Block_Editor_Context $context the block context object.
	 *
	 * @return array
	 */
	public function hide_block_locking_ui( $settings, $context ): array { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed
		$is_admin = current_user_can( 'edit_files' );
		if ( ! $is_admin ) {
			$settings['canLockBlocks']      = false;
			$settings['codeEditingEnabled'] = false;
		}
		return $settings;
	}
}
