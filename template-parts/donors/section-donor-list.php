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
$is_multi_list   = get_page_template_slug( get_the_ID() ) === 'templates/donors-list-multi-list.php';
$is_multi_column = get_page_template_slug( get_the_ID() ) === 'templates/donors-list-multi-column.php';
$args            = array(
	'donor_names' => $donor_names,
);
if ( $is_multi_list ) {
	get_template_part(
		'template-parts/donors/content',
		'donors-multi-list',
		$args,
	);
} else {
	if ( $is_multi_column ) {
		$args['multi_column'] = true;
	}
	echo '<section class="container">';
	get_template_part(
		'template-parts/donors/content',
		'donor-list',
		$args,
	);
	echo '</section>';
}
