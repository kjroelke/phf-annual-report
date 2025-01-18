<?php
/**
 * The Donor List Template
 *
 * @package KJR_Dev
 */

// TODO: Add the names to be rendered with JS as JSON Object with localized script.
// TODO: Add non-JS fallback that only shows potential matches based on $_GET['name'] parameter.

$donor_names = $args['donor_names'];
?>
<section class="container">
	<?php if ( is_wp_error( $donor_names ) ) : ?>
	<p class="text-danger">There was an error reading the file. Please try again later.</p>
	<?php else : ?>
	<ul class="d-grid gap-3 ps-0" style="list-style-type: none;grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));" id="donor-list">
		<?php foreach ( $donor_names as $name ) : ?>
		<li id="<?php echo esc_html( sanitize_title( $name ) ); ?>">
			<?php echo esc_html( $name ); ?>
		</li>
		<?php endforeach; ?>
	</ul>
	<?php endif; ?>
</section>
