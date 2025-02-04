<?php
/**
 * Donor Template: Section Header
 *
 * @package KJR_Dev
 */

$header_classes = array( 'text-center' );
if ( has_post_thumbnail() ) {
	$header_classes = array_merge( $header_classes, array( 'text-center', 'position-relative' ) );
} else {
	$header_classes = array_merge( $header_classes, array( 'bg-primary', 'py-5' ) );
}
$header_classes = ( implode( ' ', $header_classes ) );
?>
<header class="<?php echo esc_attr( $header_classes ); ?>">
	<?php
	if ( has_post_thumbnail() ) {
		the_post_thumbnail(
			'large',
			array(
				'class'   => 'mx-auto w-100 h-100 object-fit-cover z-n1 position-absolute inset-0',
				'height'  => 600,
				'loading' => 'eager',
				'alt'     => '',
			)
		);
		echo '<div class="overlay bg-primary opacity-50 z-1 position-absolute inset-0"></div>';
	}
	?>
	<div class="container position-relative z-2">
		<?php the_title( '<h1 class="text-white">', '</h1>' ); ?>
	</div>
</header>