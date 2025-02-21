<?php
/**
 * Donor Template: Aside Name Search
 *
 * @package KJR_Dev
 */

?>
<aside class="container py-lg-5">
	<h2 class="text-primary mb-2">Find a Name</h2>
	<div id="form-container" data-post-slug="<?php echo esc_attr( $post->post_name ); ?>">
		<div class="border border-primary text-primary px-3">
			<p>Press <kbd>Control+F</kbd> (or <kbd>⌘+F</kbd> on a Mac) to search for your name.</p>
		</div>
	</div>
	<?php if ( get_page_template_slug( get_the_ID() ) === 'templates/donors-list-multi-column.php' ) : ?>
	<p class="mt-3 px-2 fst-italic mb-0">Names marked “+” are members of our Employee Giving Society. Names marked “◊” have passed. We honor these donors for their contributions.</p>
	<?php endif; ?>
</aside>