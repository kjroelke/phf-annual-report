<?php
/**
 * The Donor List Template
 *
 * @package KJR_Dev
 */

$donor_names = $args['donor_names'];
if ( is_wp_error( $donor_names ) ) {
	echo '<p class="text-danger">There was an error reading the file. Please try again later.</p>';
	return;
}
$is_multi_list = is_array( $donor_names[0] );
if ( $is_multi_list ) {
	$list_settings = get_field( 'lists' );
	foreach ( $donor_names as $index => $donor_list ) {
		$is_even           = 0 === $index % 2;
		$section_classes   = array( 'd-flex', 'flex-column', 'row-gap-5' );
		$section_classes[] = $is_even ? 'bg-primary text-white py-5' : 'container';
		echo '<section class="' . esc_attr( implode( ' ', $section_classes ) ) . '">';
		echo $is_even ? '<div class="container">' : '';
		echo '<h2 class="text-center mb-5">' . esc_textarea( $list_settings[ $index ]['list_label'] ) . '</h2>';
		get_template_part(
			'template-parts/donors/content',
			'donor-list',
			array(
				'donor_names' => array_values( $donor_list )[0],
				'list_id'     => 'donor-list-' . esc_html( sanitize_title( $list_settings[ $index ]['list_label'] ) ),
			)
		);
		echo $is_even ? '</div>' : '';
		echo '</section>';
	}
} else {
	echo '<section class="container">';
	get_template_part(
		'template-parts/donors/content',
		'donor-list',
		array(
			'donor_names' => $donor_names,
		)
	);
	echo '</section>';
}
