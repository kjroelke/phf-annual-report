<?php
/**
 * Donor Template: Aside Name Search
 *
 * @package KJR_Dev
 */

use KJR_Dev\Donor_Search_Helper;
$search_handler = new Donor_Search_Helper();
?>
<aside class="container py-lg-5">
	<h2 class="text-primary mb-2">Find a Name</h2>
	<div id="form-container" data-post-slug="<?php echo esc_attr( $post->post_name ); ?>">
		<div class="border border-primary text-primary px-3">
			<p>Press <kbd>Control+F</kbd> (or <kbd>⌘+F</kbd> on a Mac) to search for your name.</p>
		</div>
	</div>
	<?php if ( $search_handler->has_message() ) : ?>
	<p class="mt-3 px-2 fst-italic mb-0"><?php $search_handler->the_message(); ?></p>
	<?php endif; ?>
</aside>
