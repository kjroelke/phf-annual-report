<?php
/**
 * The Donor List Markup
 *
 * @package KJR_Dev
 */

$donor_names = $args['donor_names'];
$list_id     = $args['list_id'] ?? 'donor-list';
?>
<ul class="d-grid gap-3 ps-0 my-0" id="<?php echo esc_attr( $list_id ); ?>">
	<?php foreach ( $donor_names as $name ) : ?>
	<li id="<?php echo esc_html( sanitize_title( $name ) ); ?>">
		<?php echo esc_html( $name ); ?>
	</li>
	<?php endforeach; ?>
</ul>
