<?php
/**
 * Template Name: Donors - 1894 Society
 *
 * @package KJR_Dev
 */

use KJR_Dev\CSV_Handler;

wp_enqueue_style( 'kjr-donor-lookup' );
wp_enqueue_script( 'kjr-donor-lookup' );
get_header();
?>
<main id="post-<?php the_ID(); ?>" <?php post_class( 'd-flex flex-column row-gap-5 mb-5' ); ?>>
	<?php
	$sections = array(
		'header'      => 'section',
		'name-search' => 'aside',
		'donor-list'  => 'section',
	);

	foreach ( $sections as $template => $prefix ) {
		$args = array();
		if ( 'donor-list' === $template ) {
			$file_handler        = new CSV_Handler( get_the_ID() );
			$donor_names         = $file_handler->get_the_list();
			$args['donor_names'] = $donor_names;
		}
		get_template_part( "template-parts/donors/{$prefix}", $template, $args );
	}
	?>
</main>
<?php
get_footer();
