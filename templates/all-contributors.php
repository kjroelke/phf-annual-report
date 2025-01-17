<?php
/**
 * Template Name: Donors - All Contributors
 *
 * @package KJR_Dev
 */

wp_enqueue_style( 'kjr-donor-utilities' );
get_header();
?>
<main id="post-<?php the_ID(); ?>" <?php post_class( 'd-flex flex-column row-gap-5 mb-5' ); ?>>
	<?php
	$sections = array(
		'header'      => 'section',
		'name-search' => 'aside',
	);
	foreach ( $sections as $template => $prefix ) {
		get_template_part( "template-parts/donors/{$prefix}", $template );
	}
	?>
</main>
<?php
get_footer();