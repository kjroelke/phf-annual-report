<?php
/**
 * Multi-List Donor Content
 *
 * @package KJR_Dev
 */

$donor_names   = $args['donor_names'];
$list_settings = get_field( 'lists' );
?>
<?php foreach ( $donor_names as $index => $donor_list ) : ?>
	<?php
	$is_even         = 0 === $index % 2;
	$section_classes = $is_even ? 'bg-primary text-white py-5' : 'container';
	$heading_classes = ( $is_even ? 'text-white' : 'text-primary' ) . ' text-center';
	?>
<section class="<?php echo esc_attr( $section_classes ); ?>">
	<?php if ( $is_even ) : ?>
	<div class="container">
		<?php endif; ?>
		<div class="row justify-content-center text-center mb-5">
			<div class="col-lg-8">
				<h2 class="<?php echo esc_attr( $heading_classes ); ?>">
					<?php echo esc_textarea( $list_settings[ $index ]['list_label'] ); ?>
				</h2>
				<?php
				if ( $list_settings[ $index ]['list_description'] ) {
					echo '<p class="m-0 fs-5">' . esc_textarea( $list_settings[ $index ]['list_description'] ) . '</p>';
				}
				?>
			</div>
		</div>
		<?php
		get_template_part(
			'template-parts/donors/content',
			'donor-list',
			array(
				'donor_names' => array_values( $donor_list )[0],
				'list_id'     => 'donor-list-' . esc_html( sanitize_title( $list_settings[ $index ]['list_label'] ) ),
			)
		);
		if ( $is_even ) {
			echo '</div>';
		}
		?>
</section>
	<?php
endforeach;
