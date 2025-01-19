<?php
/**
 * Template Name: Donors List — Headers
 *
 * @package KJR_Dev
 */

wp_enqueue_style( 'kjr-donor-lookup' );
wp_enqueue_script( 'kjr-donor-lookup' );
get_header();
?>
<main id="post-<?php the_ID(); ?>" <?php post_class( 'd-flex flex-column row-gap-5 mb-5' ); ?>>
	<?php get_template_part( 'template-parts/donors/content', 'donor-page-builder' ); ?>
</main>
<?php
get_footer();
